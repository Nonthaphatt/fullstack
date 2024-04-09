<?php
include('../Customer/component/callDatabase/sql_connection.php');
// AccessLogger.php
class AccessLog
{
    public static function log($ipAddress,  $uid, $cusName, $action, $productName, $callingFile)
    {
        $cx = Sql_connection::sql_Connection();

        // Escape values to prevent SQL injection
        $ipAddress = mysqli_real_escape_string($cx, $ipAddress);
        $uid = mysqli_real_escape_string($cx, $uid);
        $cusName = mysqli_real_escape_string($cx, $cusName);
        $action = mysqli_real_escape_string($cx, $action);
        $productName = mysqli_real_escape_string($cx, $productName);
        $callingFile = mysqli_real_escape_string($cx, basename($callingFile));

        $sql = "INSERT INTO log (TimeStamp, IPAddress, UserID, CustomerName, Action, ProductName, CallingFile)
                VALUES (NOW(), '$ipAddress', '$uid', '$cusName', '$action', '$productName', '$callingFile')";

        mysqli_query($cx, $sql);
        mysqli_close($cx);
    }
    public static function logGuest($ipAddress, $action, $productName, $callingFile)
    {
        $cx = Sql_connection::sql_Connection();

        $ipAddress = mysqli_real_escape_string($cx, $ipAddress);
        $action = mysqli_real_escape_string($cx, $action);
        $productName = mysqli_real_escape_string($cx, $productName);
        $callingFile = mysqli_real_escape_string($cx, basename($callingFile));

        $sql = "INSERT INTO log (TimeStamp, IPAddress, CustomerName, Action, ProductName, CallingFile)
                VALUES (NOW(), '$ipAddress', 'GUEST', '$action', '$productName', '$callingFile')";

        mysqli_query($cx, $sql);
        mysqli_close($cx);
    }
}
