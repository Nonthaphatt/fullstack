<?php

function insertShippingAddress($userId, $shippingAddressData, $conn) {

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

    $recipient_name = $shippingAddressData[0]['recipient_name'];
    $address_line1 = $shippingAddressData[0]['address_line1'];
    $phone_number = $shippingAddressData[0]['phone_number'];

    $query = "
      INSERT INTO shipping_address (CusID , recipient_name, address_line1,  phone_number)
      VALUES (?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $userId, $recipient_name, $address_line1, $phone_number);
    $stmt->execute();
    $stmt->close();

    return mysqli_insert_id($conn);
}
?>
