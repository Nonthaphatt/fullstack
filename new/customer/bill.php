<?php include('./component/session.php');
include_once '../conn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyXYEy4NotmFXFgAj4DQISO2aHkwKbofjM" crossorigin="anonymous">
    <style>
        img{
            width: 70px;
        }
        td,th{
            vertical-align: middle;
            text-align: center!important;
        }
        .dot {
            height: 40px;
            width: 40px;
            /* margin-left: 5px;
            margin-right: 3px; */
            margin-top: 0px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #9BA4AB;

        }


        .order-date {
            color: #999;
        }

        .connecting-line {
            flex-grow: 1;
            height: 3px;
            background-color: #08abeb;
        }

        /* Define different colors for different statuses */
        .dot.confirm {
            background-color: #08abeb;
        }

        .dot.pending {
            background-color: #08abeb;
        }

        .dot.inprogress {
            background-color: #08abeb;
        }

        .dot.delivered {
            background-color: #08abeb;
        }
    </style>
    
</head>


<body>
        <?php include('./component/navbar.php'); ?>
    <?php
    if (isset($_SESSION['id_username'])) {
        $cusID = $_SESSION['id_username'];
        $orderID = $_POST['id_order'];

        $query_address = "SELECT * FROM shipping_address WHERE shipping_address.CusID = $cusID";
        $result_address = mysqli_query($conn, $query_address);
        if (mysqli_num_rows($result_address) > 0) {
            $row = mysqli_fetch_assoc($result_address);
        }
    }

    $billingQuery = mysqli_query($conn, "SELECT * FROM orders
                    INNER JOIN billing_address ON orders.billing_address_id = billing_address.address_id
                    WHERE orders.order_id = $orderID");
    $billingResult = mysqli_fetch_array($billingQuery);


    $shippingQuery = mysqli_query($conn, "SELECT * FROM orders
                    INNER JOIN shipping_address ON orders.shipping_address_id = shipping_address.address_id
                    WHERE orders.order_id = '$orderID '");
    $shippingResult = mysqli_fetch_array($shippingQuery);


    $orderQuery = mysqli_query($conn, "SELECT * FROM orders
                INNER JOIN customer ON customer.CusID = orders.CusID
                WHERE orders.order_id = '$orderID '");
    $orderResult = mysqli_fetch_array($orderQuery);
    ?>
    <div class="container">
    <!----------------------------------------Check out Header------------------------------------------------->
    
        <h2>Checkout</h2>
        <div id="successForm" class="checkout-form" >
            <?php
                if($orderResult['fullfill_status'] == 'Unfulfilled' && $orderResult['shipping_status'] != 'Canceled') {
                    echo '<h3>Order waiting for confirmation</h3>';
                    echo '<h6>Your order has not been confirmed. Thank you for shopping with us.</h6>';
                } else if($orderResult['fullfill_status'] == 'Fulfilled' && $orderResult['shipping_status'] != 'Canceled'){
                    echo '<h3>Your Order has been placed </h3>';
                    echo '<h6>Your order has been confirmed. Thank you for shopping with us.</h6>';
                } else {
                    echo '<h3>Your Order has been canceled </h3>';
                }
            ?>
        </div>
        <!----------------------------------------Order Tracking------------------------------------------------->
        
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="lead fw-normal">Order Tracking</span>
                                    <span class="text-muted small">by TCP on <?php echo $orderResult['order_date']; ?></span>
                                </div>
                            </div>
                            <hr class="my-4">

                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <?php
                                // Define colors for different order statuses
                                $statusColors = array(
                                    'Pending' => 'pending',
                                    'Inprogress' => 'inprogress',
                                    'Delivered' => 'delivered'
                                );

                                $delivered = false;

                                // Loop through each status and display corresponding dot
                                foreach ($statusColors as $status => $color) {
                                    echo '<div class="d-flex flex-column align-items-center">';
                                    if ($status === $orderResult['shipping_status']) {
                                        if ($status === 'Pending') {
                                            echo '<div class="dot ' . $color . '"><i class="fas fa-clock text-white" ></i></div>';
                                        } else if ($status === 'Inprogress') {
                                            echo '<div class="dot inprogress"><i class="fas fa-spinner text-white"></i></div>';
                                        } else {
                                            echo '<div class="dot delivered"><i class="fas fa-check-circle text-white"></i></div>';
                                        }

                                        echo '<span class="order-date">' . substr($orderResult['order_date'], 0, 10) . '</span><span>' . $status . '</span></div>';
                                    }  else if ($status === 'Pending' && in_array($orderResult['shipping_status'], ['Pending', 'Inprogress', 'Delivered'])) {
                                        // Change color for Pending status
                                        echo '<div class="dot pending"><i class="fas fa-clock text-white"></i></div>';
                                        echo '<span class="order-date">' . substr($orderResult['order_date'], 0, 10) . '</span><span>' . $status . '</span></div>';
                                    } else if ($status === 'Inprogress' && in_array($orderResult['shipping_status'], ['Inprogress', 'Delivered'])) {
                                        // Change color for Inprogress status
                                        echo '<div class="dot inprogress"><i class="fas fa-spinner text-white"></i></div>';

                                        echo '<span class="order-date">' . substr($orderResult['delivery_date'], 0, 10) . '</span><span>' . $status . '</span></div>';
                                    } else {
                                        echo '<div class="dot"></div>';
                                        echo '<span class="order-date">' . substr($orderResult['delivery_date'], 0, 10) . '</span><span>' . $status . '</span></div>';
                                    }


                                    if ($status === 'Delivered') {
                                        $delivered = true;
                                    }
                                    // Add connecting line for all statuses except the first one
                                    if ($delivered === false) {
                                        echo '<div class="connecting-line"></div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        <!-- Bootstrap JS and Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!------------------------------------------------------------------------------------------>
        <?php
        echo '
        <h5 class=" mb-0 mt-3">Thanks for your Order,<span style="color: #08abeb;"> ' . $orderResult['CusFName'] . '!</span></h5>
        <section class="h-100 gradient-custom">
        <div class="container py-4">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header px-4 py-3 d-flex justify-content-between" >
                        <h4 class="text mb-0"><b><span style="color: #08abeb;"> Receipt!</span></b></h4>
                        <div class="action-buttons">';
                        if($orderResult['invoice_id'] !== null && $orderResult['fullfill_status'] == 'Fulfilled' && $orderResult['shipping_status'] != 'Canceled'){          
                           echo '<form class="action-button" action="pdf.php" method="post" target="_blank" style="display: inline-block;">
                                <input type="hidden" name="order_id" value="' . $orderID . '">
                                <input type="hidden" name="id_customer" value="' . $cusID . '">
                                <button type="submit">
                                    <h4><i class="bi bi-filetype-pdf"></i></h4>
                                </button>
                            </form>';
                        }
                        echo '</div>
                    </div>
                    
                        ';


        // <?---------        ส่วนของ detail     -------->
        if (isset($_POST['id_order'])) {

            $orderQuery = mysqli_query($conn, "SELECT product.*, order_details.* , orders.* 
                                        FROM order_details
                                        INNER JOIN orders ON orders.order_id = order_details.order_id
                                        INNER JOIN product ON product.ProID = order_details.ProID            
                                        WHERE orders.order_id = $orderID");

            $totalPriceAllItems = 0;
            $detailsDisplayed = false;


            while ($row = mysqli_fetch_array($orderQuery)) {
                $totalPrice = $row['PricePerUnit'] * $row['quantity'];
                $totalPriceAllItems += $totalPrice;

                echo '<div class="card shadow-0 border mb-0">
                <div class="card-body">
                    <div class="row">       
                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center"> 
                            <img src="data:image/*;base64,' . base64_encode($row["ImageData"]) . '">
                        </div>
                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                            <p class="text-muted mb-0">' . $row['ProName'] . '</p>
                        </div>
                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                            <p class="text-muted mb-0 small">Qty:' . $row['quantity'] . '</p>
                        </div>
                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                            <p class="text-muted mb-0 small">price: ' . $row['PricePerUnit'] . '</p>
                        </div>
                        <div class="col-md-3 text-center d-flex justify-content-center align-items-center">
                            <p class="text-muted mb-0 small">' . $totalPrice . '</p>
                        </div>
                    </div>
                    <hr class="mb-4" style="background-color: #e0e0e0; opacity: 1;">
                    <div class="row d-flex align-items-center"></div>
                </div>';
            }
        }

        echo "</table>";
        $tax = $totalPriceAllItems * 0.07;
        $totalAmount = $tax + $totalPriceAllItems;





        echo '<div class="d-flex justify-content-between pt-2 pb-2">
                    <p class="fw-bold mb-1 mx-3">Order Details</p>
                    <p class="text-muted mb-0" style="font-size: 1.2rem;">
                        <span class="fw-bold" style="margin-right: 17px;">Total</span>
                        <span class="fw-bold mx-5"> ' . $totalPriceAllItems . '฿</span>
                    </p>
                </div>';

        echo '<div class="d-flex justify-content-between mb-3" style="margin-top: -10px;">
                    <div class="flex-col mb-5">
                        <p class="text-muted mb-0 mx-3">Receipt: <span style="font-weight: bold;">' . $orderResult['order_id'] . '</span></p>
                        <p class="text-muted mb-0 mx-3">Order Date: ' . $orderResult['order_date'] . '</p>
                    </div>
                    <div class="flex-col mb-5">
                        <p class="text-muted mb-0" style="font-size: 1rem;"><span class="fw-bold me-4">VAT 7%</span><span class="fw-bold mx-5">' . $tax . '฿</span></p>
                        <p class="text-muted mb-0" style="font-size: 1rem;"><span class="fw-bold me-4">Discount</span><span class="fw-bold mx-5">0.00฿</span></p>
                        <p class="text-muted mb-0" style="font-size: 1rem;"><span class="fw-bold me-4">Delivery Charges</span> Free</p>
                    </div>
                </div>';




        echo   '<div class="card-footer border-0 px-4 py-5"
                        style="background-color: #08abeb; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                        <h5 class="d-flex align-items-center justify-content-end text-white text-uppercase mb-0">Total
                        paid: <span class="h2 mb-0 ms-2">' . $totalAmount . ' ฿</span></h5>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </section>';
        // if (isset($_SESSION['guest'])) {
        //     // Unset session ที่คุณต้องการ
        //     unset($_SESSION['guest']);
        //     unset($_SESSION['id_username']);
        // }
        mysqli_close($conn);
        ?>
    </div>
    <script>
        function getFormId(step) {
            // Map step number to form ID
            return (step === 1) ? 'shippingForm' :
                (step === 2) ? 'paymentForm' :
                (step === 3) ? 'successForm' :
                '';
        }
    </script>
</body>

</html>