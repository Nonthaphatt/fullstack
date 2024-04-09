<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <style>
    td img{
        width: 70px;
    }
    td,th{
        vertical-align: middle;
        text-align: center!important;
    }
  </style>
</head>
<body>
<?php include('./component/navbar.php'); ?>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h1 class=" mb-4">Cart</h1>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Picture</th>
              <th scope="col">Product</th>
              <th scope="col">Price</th>
              <th scope="col">Quantity</th>
              <th scope="col">Total</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            
            $totalPriceAllItems = 0;
            $totalPrice = 0;
            $index = 0;
           /* สำหรับ User */
        if (isset($_SESSION['id_username']) && isset($_SESSION['status']) ) {
          // SQL query to fetch products from the database
          $sql = "SELECT product.ProID , product.ProName , product.StockQty  ,product.PricePerUnit , Qty , ImageData  FROM cart
          INNER JOIN product ON cart.ProID = product.ProID";
          $result = $conn->query($sql);
          $uid = "";
          if(isset($_SESSION['id_username'])){
            $uid = $_SESSION['id_username'];
          }
          if ($result->num_rows > 0) {
              $cnt = $result->num_rows;
              while($row = $result->fetch_assoc()) {
                
                  $totalPrice = $row['PricePerUnit'] * $row['Qty'];
                  $totalPriceAllItems += $totalPrice;
                  echo '<tr>';
                  echo '<th scope="row">' . $index+1 . '</th>';
                  echo '<td><img src="data:image/*;base64,' . base64_encode($row["ImageData"]) . '"></td>';
                  echo '<td>' . $row['ProName'] . '</td>';
                  echo '<td>$' . $row['PricePerUnit'] . '</td>';

                  echo '<td>';
                  echo $row['Qty'];
                  echo '</td>';

                  echo '<td>$' . $totalPrice . '</td>';

                  echo "<td>";

                  echo "<div class='d-flex mx-auto justify-content-center'>";
                  echo '<form method="post" action="product_detail.php">
                    <input type="hidden" name="id_product" value="'.$row['ProID'].'">
                    <input type="hidden" name="Qty" value="'.$row['Qty'].'">';
                  echo "<button class='btn btn-sm btn-primary me-2' class='remove-btn'>Edit</button>";
                  echo '</form>';

                  echo '<form method="post" action="accessCart.php">';
                  echo '<input type="hidden" name="CusID" value="' . $uid . '">';
                  echo '<input type="hidden" name="deleteID" value="' . $row['ProID'] . '">';
                  echo "<button class='btn btn-sm btn-danger' class='remove-btn'>Remove</button>";
                  echo '</form>';
                  echo "<div>";
                  echo "</td>";
                  echo "</tr>";
                  
                  $index++;
              }
                  echo '
                  </tbody>
                </table><div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Cart Summary</h5>
                    <p class="card-text">Total Items: ' . $index . ' Unit</p>
                    <p class="card-text">Total Price: '.$totalPrice.' $</p>
                    
                    <form method="post" action="addressForm.php">
                    <input type="hidden" name="id_customer" value="$uid">
                    <input type="submit" class="btn btn-success value="Checkout">
                    </form>
                  </div>
                </div>';
          } else{
              echo "<tr><td colspan='10'>No products in cart</td></tr>";
          }
          $conn->close();
      }
      /* สำหรับ Guest */ 
      elseif(isset($_SESSION['cart']) && isset($_SESSION['guest'])) {
        foreach ($_SESSION['cart'] as $product_id => $product) {
            $cur = "SELECT product.ProID, product.ProName, product.PricePerUnit, product.StockQty , product.StockQty , ImageData FROM product WHERE ProID = '$product_id'";
            $msresults = mysqli_query($conn, $cur);
            $row = mysqli_fetch_array($msresults);

            $totalPrice = $row['PricePerUnit'] * $product['quantity'];
            $totalPriceAllItems += $totalPrice;
            echo '<tr>';
            echo '<th scope="row">' . $index+1 . '</th>';
            echo '<td><img src="data:image/*;base64,' . base64_encode($row["ImageData"]) . '"></td>';
            echo '<td>' . $row['ProName'] . '</td>';
            echo '<td>$' . $row['PricePerUnit'] . '</td>';

            echo '<td>';
            echo '<input type="number" class="form-control w-50 text-center mx-auto" id="amount" name="amount" min="1" value="'.$product['quantity'].'" max="'. $row['StockQty'].'">';
            echo '</td>';

            echo '<td>$' . $totalPrice . '</td>';

            echo "<td>";

            echo '<form method="post" action="accessCart.php">';
            echo '<input type="hidden" name="deleteID" value="' . $row['ProID'] . '">';
            echo "<button class='btn btn-sm btn-danger' class='remove-btn'>Remove</button>";
            echo '</form>';
            
            echo '</td></tr>';
            
            $index++;
          }
            if($index > 0){
              echo '
                  </tbody>
                </table><div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Cart Summary</h5>
                    <p class="card-text">Total Items: ' . $index . ' Unit</p>
                    <p class="card-text">Total Price: '.$totalPrice.' $</p>
                    
                    <form method="post" action="addressForm.php">
                    <input type="hidden" name="cart" value="' . json_encode($_SESSION["cart"]) . '">
                    <input type="submit" class="btn btn-success value="Checkout">
                    </form>
                  </div>
                </div>';
            }else{
              echo "<tr><td colspan='10'>No products in cart</td></tr>";
            }
        
      }
            
            ?>

        

      </div>
      
    </div>
    
  </div>

</body>
</html>
