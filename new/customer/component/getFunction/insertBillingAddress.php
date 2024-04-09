<?php

function insertBillingAddress($userId, $billingAddressData, $conn) {

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

    $recipient_name = $billingAddressData[0]['recipient_name'];
    $address_line1 = $billingAddressData[0]['address_line1'];
    $address_line2 = $billingAddressData[0]['address_line2'];
    $city = $billingAddressData[0]['city'];
    $postal_code = $billingAddressData[0]['postal_code'];
    $country = $billingAddressData[0]['country'];
    $phone_number = $billingAddressData[0]['phone_number'];

    $query = "
      INSERT INTO billing_address (CusID , recipient_name, address_line1, address_line2, city, postal_code, country, phone_number)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $userId, $recipient_name, $address_line1, $address_line2, $city, $postal_code, $country, $phone_number);
    $stmt->execute();

    return mysqli_insert_id($conn);
    
}
?>
