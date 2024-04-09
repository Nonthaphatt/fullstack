<?php
function logAction($ipAddress, $uid, $customerName, $action, $productName, $callingFile)
{
    AccessLog::log($ipAddress, $uid, $customerName, $action, $productName, $callingFile);
}

function getCustomerName($cx, $uid)
{
    if ($uid) {
        $cur = "SELECT CusFName FROM customer WHERE CusID = '$uid'";
        $getCustomerNameQuery = mysqli_query($cx, $cur);
        if ($getCustomerNameQuery) {
            $customerData = mysqli_fetch_assoc($getCustomerNameQuery);
            return $customerData['CusFName'];
        } else {
            return "GUEST";
        }
    } else {
        return "GUEST";
    }
}

function getProductName($cx, $code)
{
    $cur = "SELECT * FROM product WHERE ProID = $code ";
    $result = mysqli_query($cx, $cur);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['ProName'];
    } else {
        // Handle the case where the product is not found or an error occurs
        return "Unknown Product";
    }
}
?>
