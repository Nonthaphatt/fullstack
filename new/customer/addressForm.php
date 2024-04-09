<?php
include ('../logFolder/AccessLog.php');
include ('../logFolder/CallLog.php');
include ('./component/getFunction/getName.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Checkout</title>
    
</head>

<body>
    <div class="mx-5">
        <?php include('./component/navbar.php'); ?>
    </div>
    <form id="profileForm" method="post" action="accessOrder.php" enctype="multipart/form-data">
        <?php
        if (isset($_SESSION['member'])) {
            $uid = $_SESSION['id_username'];
            $row = '';
            $first_name = '';
            $last_name = '';

            include_once '../conn.php';
            $query_address = "SELECT * FROM customer_address
                WHERE customer_address.CusID = '$uid'";
            $result_address = mysqli_query($conn, $query_address);
            if (mysqli_num_rows($result_address) > 0) {
                // Fetch a single row from the result set
                $row = mysqli_fetch_assoc($result_address);
                $recipient_name = $row['recipient_name'];
                $name_parts = explode(' ', $recipient_name);
                $first_name = $name_parts[0];
                $last_name = $name_parts[1];

            }
        }
        ?>



        <div class="container">
            <div class="  mx-auto">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h2>Shipping</h2>
                        <div class="mb-3">
                            <label for="ship_fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="ship_fname" name="ship_fname"
                                value="<?php echo $first_name ?? ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ship_lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="ship_lname" name="ship_lname"
                                value="<?php echo $last_name ?? ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ship_tel" class="form-label">Telephone</label>
                            <input type="tel" class="form-control" id="ship_tel" name="ship_tel"
                                value="<?php echo $row['phone_number'] ?? ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ship_address" class="form-label">Address</label>
                            <textarea class="form-control" id="ship_address" name="ship_address" rows="3"
                                required><?php echo $row['address_line1'] ?? ''; ?></textarea>
                        </div>

                    </div>

                    <div class="col-md-4 mb-4">
                        <h2>Billing</h2>
                        <div class="mb-3">
                            <label for="bill_fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="bill_fname" name="bill_fname" required>
                        </div>
                        <div class="mb-3">
                            <label for="bill_lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="bill_lname" name="bill_lname" required>
                        </div>
                        <div class="mb-3">
                            <label for="bill_tel" class="form-label">Telephone</label>
                            <input type="tel" class="form-control" id="bill_tel" name="bill_tel" required>
                        </div>
                        <div class="mb-5 pb-3">
                            <label for="bill_address" class="form-label">Address</label>
                            <textarea class="form-control" id="bill_address" name="bill_address" rows="3"
                                required></textarea>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <img class="shadow mb-3 w-75"
                            src="https://media.discordapp.net/attachments/950054950388510761/1226677218147700836/CS_qrcode.png?ex=6625a32f&is=66132e2f&hm=bd1c5d0f7edd02d4ca1a0da74f4517a6b5d25aad49f0d41d33530f1eb85cd037&=&format=webp&quality=lossless&width=350&height=350"
                            alt="qr">
                        <br><label for="image">Upload your slip:</label>
                        <input class="form-control" type="file" id="image" name="image" accept="image/*" required />
                        <div class="invalid-feedback"> Valid your slip is required. </div>

                        <?php if(isset($_SESSION['member']) || isset($_SESSION['guest'])): ?>
                                    <div class="mb-3 py-2">                           
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                                Checked Invoice (Optional)
                                            </label>
                                        </div>
                                
                                        <div id="invoiceInput" style="display: none;">
                                            <input type="text" id="tax_id" class="form-control" name="tax_id" placeholder="The tax ID has 13 digits.">
                                        </div>
                                        <div class="invalid-feedback"> Please enter your taxID. </div>
                                    </div>
                                <?php endif; ?>
                    </div>

                    

                </div>
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </div>
                </div>

                <!-- ตรวจสอบว่าเป็น Guest หรือ User และแสดงปุ่ม 'ชำระเงิน' ตามเงื่อนไข -->
                <?php if (isset($_SESSION['guest'])): ?>
                    <input type='hidden' name='cart' value='<?php echo json_encode($_SESSION['cart']); ?>'>
                <?php elseif (isset($_SESSION['member'])): ?>
                    <input type='hidden' name='id_customer' value='<?php echo $uid; ?>'>
                <?php else: ?>
                    <p>Oops Something went wrong</p>
                    <?php echo 'header("Location: ./cart.php")'; ?>
                <?php endif; ?>
            </div>
        </div>
    </form>

</body>

</html>
<script>
    document.getElementById('flexCheckChecked').addEventListener('change', function () {
        var invoiceInput = document.getElementById('invoiceInput');
        if (this.checked) {
            invoiceInput.style.display = 'block';
        } else {
            invoiceInput.style.display = 'none';
        }
    });
    document.getElementById('tax_id').addEventListener('change', function () {
        var taxIDInput = document.getElementById('tax_id');
        var taxIDValue = taxIDInput.value;

        // Check if the tax ID has 13 digits and consists of only digits
        if (taxIDValue.length !== 13 || !(/^\d+$/.test(taxIDValue))) {
            alert('Your tax id is incorrect');
            taxIDInput.value = ''; // Clear the input field
        }
    });
</script>