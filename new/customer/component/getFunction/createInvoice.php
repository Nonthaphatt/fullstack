<?php
    function createInvoice($cusID, $orderId, $conn , $tax_id)
    {

        $totalAmount = calculateTotalAmount($orderId, $conn);

        $invoiceInsertQuery = "
            INSERT INTO invoice (CusID, total_amount, tax_id)
            VALUES ('$cusID', '$totalAmount',$tax_id)
        ";
        mysqli_query($conn, $invoiceInsertQuery);
        $invoiceId = $conn->insert_id;

        $invoiceUpdateOrders = "
            UPDATE orders
            SET invoice_id = '$invoiceId'
            WHERE order_id = '$orderId';
        ";
        mysqli_query($conn, $invoiceUpdateOrders);

        echo $invoiceId;

        // Insert invoice details
        insertInvoiceDetails($orderId, $invoiceId, $conn);
    }

    function calculateTotalAmount($orderId, $conn)
    {
        $totalAmount = 0;

        $orderDetailsQuery = "SELECT quantity, subtotal_price FROM order_details WHERE order_id = $orderId";
        $orderDetailsResult = mysqli_query($conn, $orderDetailsQuery);

        if ($orderDetailsResult) {
            while ($row = mysqli_fetch_assoc($orderDetailsResult)) {
                $subtotalPrice = $row['subtotal_price'];
                $totalAmount += $subtotalPrice;
            }
            $totalAmount = ($totalAmount * 0.07) + $totalAmount;
        }
        echo $totalAmount;
        return $totalAmount;
    }

    function insertInvoiceDetails($orderId, $invoiceId, $conn)
    {
        $orderDetailsQuery = "SELECT * FROM order_details WHERE order_id = $orderId";
        $orderDetailsResult = mysqli_query($conn, $orderDetailsQuery);

        if ($orderDetailsResult) {
            while ($row = mysqli_fetch_assoc($orderDetailsResult)) {
                $productId = $row['ProID'];
                $quantity = $row['quantity'];
                $pricePerUnit = $row['subtotal_price'] / $quantity;
                $totalPrice = $row['subtotal_price'];

                $invoiceDetailInsertQuery = "
                    INSERT INTO invoice_detail (invoice_id, ProID , quantity, price_per_unit, total_price)
                    VALUES ('$invoiceId', '$productId', '$quantity', '$pricePerUnit', '$totalPrice')
                ";
                mysqli_query($conn, $invoiceDetailInsertQuery);
            }
        }
    }
?>
