<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
</head>
<body class="m-0 p-0">
    
    <?php
        if(isset($_SESSION['guest'])&&isset($_SESSION['id_username'])) {
            unset($_SESSION['id_username']);
        }
    ?>
    <?php include('./component/navbar.php'); ?>
    
    
    <div class="container">
        <h1 class="mt-5 mb-4">Categories</h1>

        <div class="row">
        <?php
            $cur = "SELECT * FROM product";
            $msresults = mysqli_query($conn, $cur);

            if (mysqli_num_rows($msresults) > 0) {
                while ($row = mysqli_fetch_assoc($msresults)) {
                    echo "
                    <div class='col-md-4'>
                        <div class='card mb-4'>
                        <img class='card-img-top' src='data:image/*;base64," . base64_encode($row['ImageData']) . "'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['ProName']}</h5>
                            <p class='card-text'>{$row['PricePerUnit']} ฿</p>
                            <form method='post' action='product_detail.php'>
                                <input type='hidden' name='id_product' value='{$row['ProID']}'>
                                <input class='btn btn-primary' type='submit' value='View Products'>
                            </form>
                        </div>
                        </div>
                    </div>
                    ";
                    
                }
            } else {
                echo "<center><h1>ไม่มีสินค้า</h1></center>";
            }
            ?>
            
            
        </div>
    </div>

</body>
</html>



