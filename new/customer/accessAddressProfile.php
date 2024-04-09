<?php
include('./component/session.php');

include_once '../conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id_customer'])) {
        // Delete operation
        $uid = $_POST['delete_id_customer'];
        $address_id = $_POST['delete_address_id'];

        $delete_result_address = mysqli_query($conn, "DELETE FROM shipping_address WHERE address_id = '$address_id' AND CusID = '$uid'");

        if (!$delete_result_address) {
            echo "Error deleting address: " . mysqli_error($conn);
        } else {
            header("Location: ./profile.php");
            exit();
        }
    } else {
        $recv_id = $_POST['id_receiver'];
        $uid = $_POST['id_customer'];

        if ($recv_id !== null && $recv_id !== '' && !isset($_POST['delete_id_customer'])) {
            $uid = $_POST['id_customer'];
            $new_fname = $_POST['fname'];
            $new_lname = $_POST['lname'];
            $new_tel = $_POST['tel'];
            $new_address = $_POST['address'];

            $query_address = "SELECT * FROM receiver 
                    INNER JOIN receiver_detail ON receiver.RecvID = receiver_detail.RecvID  
                    WHERE receiver_detail.CusID = '$uid' AND receiver.RecvID = '$recv_id'";
            $result_address = mysqli_query($conn, $query_address);

            if (mysqli_num_rows($result_address) > 0) {
                $update_query = "UPDATE receiver SET RecvFName = '$new_fname', RecvLName = '$new_lname' ,Tel = '$new_tel' , Address = '$new_address' WHERE RecvID = '$recv_id'";
                $update_result = mysqli_query($conn, $update_query);

                $update_query = "UPDATE  receiver_detail SET CusID = '$uid' , RecvID = '$recv_id' WHERE CusID = '$uid'";
                $update_result = mysqli_query($conn, $update_query);

                header("Location: ./profile.php");
            } else {
                // Handle the case where $recv_id is not found
                echo "RecvID not found.";
            }
        } elseif ($recv_id === null || $recv_id === '' && !isset($_POST['delete_id_customer'])) {
            $uid = $_POST['id_customer'];
            $new_fname = $_POST['fname'];
            $new_lname = $_POST['lname'];
            $new_tel = $_POST['tel'];
            $new_address = $_POST['address'];

            $insert_query_head = "INSERT INTO shipping_address (RecvFName , RecvLName  , Tel , Address) 
                    VALUES('$new_fname', '$new_lname', '$new_tel' , '$new_address')";
            $insert_result_head = mysqli_query($conn, $insert_query_head);

            if ($insert_result_head) {
                $recv_id = mysqli_insert_id($conn);
            } else {
                die("Error inserting into receiver: " . mysqli_error($conn));
            }

            // Generate new NumID
            $resultDetail = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(NumID, 4) AS UNSIGNED)) AS num_id FROM receiver_detail WHERE CusID = '$uid'");
            $latestID = mysqli_fetch_assoc($resultDetail);
            $lastID = $latestID['num_id'];

            // Increment the numeric part
            $newNumericPart = $lastID + 1;

            // Format the complete NumID
            $NumID = 'Num' . str_pad($newNumericPart, 3, '0', STR_PAD_LEFT);

            echo $NumID;
            echo $recv_id;

            $insert_query_detail = "INSERT INTO receiver_detail (CusID, RecvID, NumID) VALUES('$uid', '$recv_id', '$NumID')";
            $insert_result_detail = mysqli_query($conn, $insert_query_detail);

            if (!$insert_result_detail) {
                die("Error inserting receiver_detail: " . mysqli_error($conn));
            }

            header("Location: ./profile.php");
        }
    }
}
mysqli_close($conn);
