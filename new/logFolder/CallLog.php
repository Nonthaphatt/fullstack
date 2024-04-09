<?php
class CallLog
{
    public static function callLog($ipAddress, $conn, $uid, $productId, $calledFile, $action)
    {
        // ACCESS LOG
        if ($productId == "") {
            $getPName = '';
        } else {
            $getPName = getProductName($conn, $productId);
        }

        echo "<script>console.log('CHECK LOG');</script>";

        if (isset($_SESSION['id_username'])) { // Make  a copy of checking GUEST!
            // Registered user
            $getCName = getCustomerName($conn, $uid);
            error_log('REG User');
            AccessLog::log($ipAddress, $uid, $getCName, $action, $getPName, $calledFile);
        } else {
            // Guest user
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            error_log('GUEST User');
            AccessLog::logGuest($ipAddress, $action, $getPName, $calledFile);
        }
        //END LOG
    }
}
?>
