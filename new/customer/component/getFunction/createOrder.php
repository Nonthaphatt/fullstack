<?php

include_once 'insertShippingAddress.php'; 
include_once 'insertBillingAddress.php'; 
include_once 'createInvoice.php'; 

function createOrder($shippingAddressData, $billingAddressData, $cusID ,$conn, $tax_id , $slipID) {
    /* Gobal */
    $finalAmount = 0;
    $orderId = '';

    // Insert shipping address
    $shippingAddressId = insertShippingAddress($cusID, $shippingAddressData, $conn);

    // Insert billing address   เทสเเล้วเลข id ไม่ส่งมา เเต่ใน db มีการบันทึกเเล้ว
    $billingAddressId = insertBillingAddress($cusID, $billingAddressData, $conn);

 
    if(isset($_SESSION['member'])) {

        echo 'inn';

        $userCartData = mysqli_query($conn, "SELECT Product.ProID, Qty, Product.PricePerUnit FROM cart 
        INNER JOIN Product ON Cart.ProID = Product.ProID WHERE CusID = $cusID");

        $orderInsertQuery = "
        INSERT INTO orders (CusID, order_date, shipping_status, fullfill_status, total_price, shipping_address_id, billing_address_id, image_slip_id)
        VALUES (?, NOW(), 'Pending', 'Unfulfilled', '0.0', ?, ?, ?)
        ";

        // Prepare the statement
        $stmt = mysqli_prepare($conn, $orderInsertQuery);

        // Bind parameters to the placeholders
        mysqli_stmt_bind_param($stmt, "iiii", $cusID, $shippingAddressId, $billingAddressId, $slipID);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the ID of the newly inserted order
        $orderId = $conn->insert_id;

        echo $orderId;

       
        if ($userCartData) {
            while ($item = mysqli_fetch_assoc($userCartData)) {
                $product_id = $item['ProID'];
                $quantity = $item['Qty'];
                $price = $item['PricePerUnit'];
                $finalAmount += $quantity * $price;

                $orderDetailsInsertQuery = "
                    INSERT INTO order_details (order_id, ProID , quantity, subtotal_price)
                    VALUES ($orderId, $product_id, $quantity, " . ($quantity * $price) . ")
                ";
                mysqli_query($conn, $orderDetailsInsertQuery);

                // Update Stock and OnHands
                $stmt2 = mysqli_query($conn,"SELECT StockQty from product where ProID = '$product_id'");
                $stockQtyRow = mysqli_fetch_assoc($stmt2);
                $stockQty = $stockQtyRow['StockQty'];
                
                if(($stockQty-$quantity) >= 0 ){
                    mysqli_query($conn, "UPDATE product SET StockQty = StockQty - '$quantity', OnHands = OnHands WHERE ProID ='$product_id'");
                } else {
                    mysqli_query($conn, "UPDATE product SET StockQty = StockQty - '$quantity', OnHands = OnHands - '$quantity' WHERE ProID ='$product_id'");
                }    
            }
            mysqli_query($conn,"UPDATE orders SET total_price = $finalAmount WHERE order_id = $orderId");
        }

    } elseif(isset($_SESSION['guest']) && !empty($_SESSION['cart'])) {
        // $orderInsertQuery = "
        //     INSERT INTO orders (CusID, order_date, shipping_status, fullfill_status , total_price , shipping_address_id  , billing_address_id , image_slip_id)
        //     VALUES ($cusID, NOW(), 'Pending', 'Unfulfilled', 0.0 , $shippingAddressId, $billingAddressId , $slipID)
        // ";

        // mysqli_query($conn, $orderInsertQuery); 
        // $orderId = $conn->insert_id;

        $orderInsertQuery = "
        INSERT INTO orders (CusID, order_date, shipping_status, fullfill_status, total_price, shipping_address_id, billing_address_id, image_slip_id)
        VALUES (?, NOW(), 'Pending', 'Unfulfilled', '0.0', ?, ?, ?)
        ";

        // Prepare the statement
        $stmt = mysqli_prepare($conn, $orderInsertQuery);

        // Bind parameters to the placeholders
        mysqli_stmt_bind_param($stmt, "iiii", $cusID, $shippingAddressId, $billingAddressId, $slipID);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the ID of the newly inserted order
        $orderId = $conn->insert_id;



        foreach ($_SESSION['cart'] as $product_id => $item) {
            $cur = "SELECT product.ProID, product.ProName, product.PricePerUnit , ImageData FROM product WHERE ProID = '$product_id'";
            $msresults = mysqli_query($conn, $cur);
            $row = mysqli_fetch_array($msresults);

            // $product_id = $product_id;
            $quantity = $item['quantity'];
            $price = $row['PricePerUnit'];
 
            $finalAmount += $quantity * $price;
         

            $orderDetailsInsertQuery = "
                INSERT INTO order_details (order_id, ProID , quantity, subtotal_price)
                VALUES ($orderId, $product_id, $quantity, " . ($quantity * $price) . ")
            ";
            mysqli_query($conn, $orderDetailsInsertQuery);

            // Update Stock and OnHands
            $stmt2 = mysqli_query($conn,"SELECT StockQty from product where ProID = '$product_id'");
            $stockQtyRow = mysqli_fetch_assoc($stmt2);
            $stockQty = $stockQtyRow['StockQty'];
            
            if(($stockQty-$quantity) >= 0 ){
                mysqli_query($conn, "UPDATE product SET StockQty = StockQty - '$quantity', OnHands = OnHands WHERE ProID ='$product_id'");
            } else {
                mysqli_query($conn, "UPDATE product SET StockQty = StockQty - '$quantity', OnHands = OnHands + '$quantity' WHERE ProID ='$product_id'");
            }    
        }

        mysqli_query($conn,"UPDATE orders SET total_price = $finalAmount WHERE order_id = $orderId");
        // init vaule session
        $_SESSION['id_username'] = $cusID;
        unset($_SESSION['cart']);
    }

  


    /*------------------ACCESS LOG------------------*/
    $productId = "";
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    }
    $callingFile = __FILE__;
    $action = 'INSERT'; 
    CallLog::callLog($ipAddress, $conn, $cusID , $productId, $callingFile, $action);
    /*----------------------------------------------*/


    
    // Create Invoice if member needed
    if($tax_id !== null && $tax_id !== ''){
      createInvoice($cusID, $orderId , $conn , $tax_id);
      echo 'Im in!';
    }

    mysqli_query($conn, "DELETE FROM cart WHERE CusID = '$cusID'");

    // Form submission with hidden values
    echo "<form id='auto_submit_form' method='post' action='bill.php'>
    <input type='hidden' name='id_order' value='$orderId'>
    </form>";
     
    // Use JavaScript to trigger form submission
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('auto_submit_form').submit();
        });
    </script>";
    exit();
}

?>
