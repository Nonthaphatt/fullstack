<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History</title>
  <style>
    body{
        background-color: #F0F0F0!important
    }
    .content{
      background-color: #E2E2E2;
      border-radius: 2rem;
      padding: 2rem;
    }
    .container{
      border-radius: 2rem;
      background-color: #FFF;
    }
  </style>
</head>
<body>
<?php 
include('./component/navbar.php');
?>
  <div class="container px-4 py-1 mb-5">
    <h1 class="mt-3 mb-4">Order History</h1>
    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link tablinks active" onclick="openTab(event, 'pending')" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">Pending</a>
      </li>
      <li class="nav-item">
        <a class="nav-link tablinks" onclick="openTab(event, 'inprogress')" data-bs-toggle="tab" href="#inprogress" role="tab" aria-controls="inprogress" aria-selected="false">In Progress</a>
      </li>
      <li class="nav-item">
        <a class="nav-link tablinks" onclick="openTab(event, 'delivered')" data-bs-toggle="tab" href="#delivered" role="tab" aria-controls="delivered" aria-selected="false">Delivered</a>
      </li>
      <li class="nav-item">
        <a class="nav-link tablinks" onclick="openTab(event, 'canceled')" data-bs-toggle="tab" href="#canceled" role="tab" aria-controls="canceled" aria-selected="false">Canceled</a>
      </li>
    </ul>

    <!-- Tab panes -->
        <div id="pending" class="tabcontent">
            <?php includeOrders("SELECT * FROM orders WHERE CusID = '$uid' AND  shipping_status = 'Pending'", $conn); ?>
        </div>

        <div id="inprogress" class="tabcontent">

            
            <?php includeOrders("SELECT * FROM orders WHERE CusID = '$uid' AND   shipping_status = 'Inprogress'", $conn); ?>
        </div>

        <div id="delivered" class="tabcontent">
            <?php includeOrders("SELECT * FROM orders WHERE CusID = '$uid' AND   shipping_status = 'Delivered'", $conn); ?>
        </div>

        <div id="canceled" class="tabcontent">
            <?php includeOrders("SELECT * FROM orders WHERE CusID = '$uid' AND   shipping_status = 'Canceled' AND fullfill_status = 'Fulfilled'", $conn); ?>
        </div>

  </div>
  
  <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        document.getElementById("pendingTab").click();
    </script>
    <script>
        function updateStatus(order_id, new_status) {
            console.log(order_id, new_status);
            $.ajax({
                type:'POST',
                url:'updateStatus.php',
                data: { order_id: order_id, new_status: new_status },
                success: function(response) {
                    // console.log(response); // ล็อกข้อมูลทั้งหมดที่ได้รับกลับมาจากการเรียก Ajax
                    
                    var jsonResponse = JSON.parse(response);
                    console.log(jsonResponse); 
                    console.log(jsonResponse.message); 

                    if (jsonResponse.message === "Add-new-cart") {
                        console.log('check');
                        window.location.assign('./cart.php');
                    }
                    
                }, 
                error: function(error) {
                    console.error('Error fetching filtered data:', error);
                }
            });

            // ตัวอย่างเทส: พิมพ์ข้อความออกมาเพื่อแสดงว่าการอัปเดตเสร็จสมบูรณ์
            console.log('Order ID ' + order_id + ' updated to ' + new_status);
            
        }
    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php


    function includeOrders($query , $conn) { 
        $msresults = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_array($msresults)) {
   
                if($row['shipping_status'] == 'Inprogress') {
                  echo "<div class='content m-4'>
                  <div class='d-flex pt-1'>
                      <h4 class='me-auto'>Order ID: {$row['order_id']}</h4>
                      <div class=''>
                        <form method='post' action='bill.php'>
                            <input type='hidden' name='id_order'value='{$row['order_id']}'>
                            <button type='submit'>
                                <i class='bi bi-file-text'></i>
                            </button>
                        </form>
                      </div>
                    </div>
                    <hr>
                    <p>Total Amount:  {$row['total_price']} ฿</p>
                    <p>Order Date: {$row['order_date']}</p>";
                    if ($row['delivery_date'] != null) {
                      echo "<p>Delivery Date: {$row['delivery_date']}</p>";
                    }
                    echo "<hr>";

                    echo "<h4 id='{$row['shipping_status']}-status' class='text-warning'><i class='bi bi-box-seam'></i> {$row['shipping_status']}</h4>";
                    echo "<hr>";
                    echo "<div class='button-container'>
                    
                </div>";
                }

                else if($row['shipping_status'] == 'Pending') {
                    echo "<div class='content m-4'>
                    <div class='d-flex pt-1'>
                      <h4 class='me-auto'>Order ID: {$row['order_id']}</h4>
                      <div class=''>
                        <form method='post' action='bill.php'>
                            <input type='hidden' name='id_order'value='{$row['order_id']}'>
                            <button type='submit'>
                                <i class='bi bi-file-text'></i>
                            </button>
                        </form>
                      </div>
                    </div>
                    <hr>
                    <p>Total Amount:  {$row['total_price']} ฿</p>
                    <p>Order Date: {$row['order_date']}</p>";
                    if ($row['delivery_date'] != null) {
                      echo "<p>Delivery Date: {$row['delivery_date']}</p>";
                  }
                  
                  echo "<hr>
                    <div >
                    <h4 id='{$row['shipping_status']}-status' class='text-success'><i class='bi bi-clock-history'></i> {$row['shipping_status']}</h4>
                    </div>
                    <hr>
                    <div class='button-container'>
                      <button type='button' class='btn btn-danger' >
                          Canceled
                      </button>
                  </div>";
                    
                }

                else if($row['shipping_status'] == 'Delivered') {
                  echo "<div class='content m-4'>
                  <div class='d-flex pt-1'>
                    <h4 class='me-auto'>Order ID: {$row['order_id']}</h4>
                    <div class=''>
                      <form method='post' action='bill.php'>
                          <input type='hidden' name='id_order'value='{$row['order_id']}'>
                          <button type='submit'>
                              <i class='bi bi-file-text'></i>
                          </button>
                      </form>
                    </div>
                  </div>
                  <hr>
                  <p>Total Amount:  {$row['total_price']} ฿</p>
                  <p>Order Date: {$row['order_date']}</p>";
                  if ($row['delivery_date'] != null) {
                    echo "<p>Delivery Date: {$row['delivery_date']}</p>";
                  }
                  echo "<hr>";

                    echo "<h4 id='{$row['shipping_status']}-status' class='text-primary'><i class='bi bi-truck'></i> {$row['shipping_status']}</h4>";
                    echo "<hr>";
                    echo "<div class='button-container'>
                        <button type='button' class='btn btn-success' onclick='updateStatus({$row['order_id']}, \"Add-new-cart\")'>
                            Order again
                        </button>
                    </div>";
                }

                else if($row['shipping_status'] == 'Canceled') {
                  echo "<div class='content m-4'>
                  <div class='d-flex pt-1'>
                    <h4 class='me-auto'>Order ID: {$row['order_id']}</h4>
                    <div class=''>
                      <form method='post' action='bill.php'>
                          <input type='hidden' name='id_order'value='{$row['order_id']}'>
                          <button type='submit'>
                              <i class='bi bi-file-text'></i>
                          </button>
                      </form>
                    </div>
                  </div>
                  <hr>
                  <p>Total Amount:  {$row['total_price']} ฿</p>
                  <p>Order Date: {$row['order_date']}</p>";
                  if ($row['delivery_date'] != null) {
                    echo "<p>Delivery Date: {$row['delivery_date']}</p>";
                  }
                  echo "<hr>";
                    echo "<h4 id='{$row['shipping_status']}-status' class='text-danger'><i class='bi bi-x-octagon'></i> {$row['shipping_status']} ";
                    echo $row['fullfill_status'] == 'Refund' ? '('.$row['fullfill_status'].')' : '';
                    echo "</h4>";

                    echo "<hr>";
                }
         
                
                echo '</div>';
    
            }          
        }
?>