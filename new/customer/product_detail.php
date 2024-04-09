
<?php include('../logFolder/AccessLog.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<?php include('./component/navbar.php'); ?>

<div class="container">
    <div class="row">
      <div class="col-md-6">
        <?php
        $sql = "SELECT * FROM product WHERE ProID = ".$_POST['id_product'];
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row 
            $row = $result->fetch_assoc();
            $onHandStock = $row['StockQty'] - $row['OnHands'];
            $qty = 1;
            if(isset($_POST['Qty'])){
              $qty=$_POST['Qty'];
            }
            echo "<img src='data:image/*;base64," . base64_encode($row['ImageData']) . "' class='img-fluid' alt='Product Image'>";
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<h1 class="mt-5 mb-4">' . $row["ProName"] . '</h1>';
            echo '<p class="card-text">' . $row["Description"] . '</p>';
            echo '<p>Price: $' . $row["PricePerUnit"] . '</p>';
            echo   "<p style='font-size:20px; color:red;'>จำนวนในสต็อก: {$onHandStock}</p>";
            echo '<form action="accessCart.php" method="post">';
            echo '<input type="hidden" name="id_product" value="' . $row["ProID"] . '">';
            echo '<div class="mb-3">';
            echo '<label for="amount" class="form-label">จำนวน:</label>';
            echo '<input type="number" class="form-control" id="amount" name="amount" min="1" value="'.$qty.'" max="'.$onHandStock.'">';
            echo '</div>';
            echo '<button name="add_to_cart" type="submit" class="btn btn-primary">Add to Cart</button>';
            echo '</form>';
        } else {
            echo "Product not found";
        }
        $conn->close();
        ?>
      </div>
    </div>
  </div>
</body>
</html>