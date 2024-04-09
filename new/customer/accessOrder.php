<?php
include('./component/session.php'); 
include('../logFolder/AccessLog.php');
include('../logFolder/CallLog.php');
include('./component/getFunction/getName.php');
include('./component/getFunction/createOrder.php');
include_once '../conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cusID = '';
    if(isset( $_POST['tax_id'])) {
        $tax_id = $_POST['tax_id'];
    } 
    else {
        $tax_id = '';
    }


    if (empty($_SESSION['id_username']) && isset($_SESSION['guest'])) {
        $role = 'guest';
        $stmt = $conn->prepare("INSERT INTO customer (CusFName, CusLName, Tel , role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_POST['ship_fname'], $_POST['ship_lname'], $_POST['ship_tel'], $role);
        $stmt->execute();
        $cusID = mysqli_insert_id($conn);
    } else {
        $cusID = $_SESSION['id_username'];
    }

    /* Shipment */
    $shippingAddressData = array(
        array(
            'recipient_name' => $_POST['ship_fname'] . ' ' . $_POST['ship_lname'],
            'address_line1' => $_POST['ship_address'],
            'address_line2' => '', 
            'city' => '', 
            'postal_code' => '',
            'country' => '',
            'phone_number' => $_POST['ship_tel']
        )
    );

    /* Billing */
    $billingAddressData = array(
        array(
            'recipient_name' => $_POST['bill_fname'] . ' ' . $_POST['bill_lname'],
            'address_line1' => $_POST['bill_address'],
            'address_line2' => '', 
            'city' => '', 
            'postal_code' => '', 
            'country' => '', 
            'phone_number' => $_POST['bill_tel']
        )
    );

    if (isset($_FILES["image"])) {
        $file = $_FILES["image"];
        $imageData = file_get_contents($file["tmp_name"]); // Read image data as binary
    
        // Prepare the SQL query for inserting new image data into the imageslip table
        $insertImageQuery = "INSERT INTO image_slip (image_data) VALUES (?)";
        $stmt = mysqli_prepare($conn, $insertImageQuery); // Prepare the statement
        mysqli_stmt_bind_param($stmt, 's', $imageData); // Bind parameters
        mysqli_stmt_send_long_data($stmt, 0, $imageData); // Send image data
        mysqli_stmt_execute($stmt); // Execute the statement
        $lastInsertedId = mysqli_insert_id($conn); // Get the last inserted ID
    
        // Close the statement
        mysqli_stmt_close($stmt);
    
        // Pass the last inserted imageSlip ID to the createOrder function
        createOrder($shippingAddressData, $billingAddressData, $cusID, $conn, $tax_id, $lastInsertedId);
    } else {
        // If no image is uploaded, call createOrder without the imageSlip ID
        createOrder($shippingAddressData, $billingAddressData, $cusID, $conn, $tax_id, null);
    }
    // Exit after calling createOrder
    exit();
    }
?>
