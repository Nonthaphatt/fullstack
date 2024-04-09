<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>pdf</title>
  <style>
    .container{
      width: 800px!important;
      height: 1000px;
      border: solid 1px #000;
    }
    th,td{
      text-align: center!important;
    }
  </style>
</head>
<body>
<?php 
include('./component/navbar.php');

if (isset($_POST['order_id'])) {
  // $receiptCode = $_SESSION['ReceiptCode'];
  $receiptCode = $_POST['order_id'];
  $sql = "SELECT shipping_address_id FROM orders WHERE order_id = '$receiptCode'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $address_id = $row['shipping_address_id'];
} 

$sql = "SELECT * FROM shipping_address r  
        WHERE  r.address_id = '$address_id'";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);
$cusID = $row["CusID"];
$cusAddress = $row['address_line1'];
$cusFName = $row['recipient_name'];
$cusphone_number = $row['phone_number'];

$sql = "SELECT order_date FROM orders WHERE order_id = '$receiptCode'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$payTime = $row['order_date'];
$payDate = date('Y-m-d', strtotime($payTime));
?>
  <div class="d-flex w-50 mx-auto justify-content-center">
      <div class="container px-5 py-5 mb-5">

    <h1 class="text-center mt-3 mb-0">ใบเสร็จรับเงิน/ใบกำกับภาษี</h1>

    <div class="mx-4">
      <h6 class="text-end ">เลขที่: <?php echo $receiptCode; ?></h6>
      <h6 class="text-end mt-0 mb-4">วันที่: <?php echo $payDate; ?></h6>
    </div>
    <div class="row">
      <div class="col m-4">
          <h6 style="font-weight: bold;">บริษัท เอสมิติช้อป จำกัด(สำนักงานใหญ่)</h6>
          <h6>999 หมู่ 999 ถ.ฉลองกรุง 9999 แขวงลาดกระบัง เขตลาดกระบัง กรุงเทพมหานคร 10500</h6>
          <h6>เลขประจำตัวผู้เสียภาษี 12345678909999</h6>
          <h6>โทร. 0123456789 อีเมล smiti@test.com</h6>
      </div>
      <div class="col m-4">
          <h6>ลูกค้า: <?php echo $cusFName; ?></h6>
          <h6>ที่อยู่: <?php echo $cusAddress; ?></h6>
          <h6>เลขประจำตัวผู้เสียภาษี: 123456789123</h6>
          <h6>โทร: <?php echo $cusphone_number; ?> อีเมล: testuser@test.com</h6>
      </div>
    </div>
    <table  class="col mx-auto my-4 table table-striped">
    <tr>
        <th>ลำดับ</th>
        <th>รายการสินค้า</th>
        <th>จำนวน</th>
        <th>ราคาต่อหน่วย</th>
        <th>จำนวนเงิน</th>
    </tr>
    <?php 
    $sql = "SELECT *
    FROM order_details
    JOIN product ON order_details.ProID = product.ProID 
    WHERE order_details.order_id = '$receiptCode'";
    $result = mysqli_query($conn, $sql);

    $orderProducts = array();
    while ($orderProductRow = mysqli_fetch_array($result)) {
        $orderProducts[] = $orderProductRow;
    }

    $totalPrice = 0;
    $id = 1;


    foreach ($orderProducts as $orderProductRow) {
        $productName = $orderProductRow['ProName'];
        $qty = $orderProductRow['quantity'];
        $pricePerUnit = $orderProductRow['PricePerUnit'];
        $total = $orderProductRow['PricePerUnit'] * $orderProductRow['quantity'];
        echo '<tr>
                  <td>' . $id . '</td>
                  <td>' . $productName . '</td>
                  <td>' . $qty . '</td>
                  <td>' . $pricePerUnit . '฿</td>
                  <td>' . $total . ' บาท</td>
                </tr>';

        $totalPrice += $orderProductRow['PricePerUnit'] * $orderProductRow['quantity'];
        $id++;
    }

    echo  "</table>";

    $vat = $totalPrice * 0.07;

    ?>
    <div class="row m-2 mt-5">
      <div class="col-md-7">
        <h6>หมายเหตุ</h6>
        <h6>ผู้รับเงิน บริษัท เอสมิติช้อป จำกัด(สำนักงานใหญ่)</h6>
      </div>
      <div class="col ms-3 border">
        <table class="w-100">
          <tr>
          <td class="text-start"><h6>ส่วนลด</h6></td>
          <td class="text-end">0 บาท</td>
          </tr>

          <tr>
          <td class="text-start"><h6>รวมเป็นเงิน</h6></td>
          <td class="text-end"><?php echo $totalPrice;?> บาท</td>
          </tr>

          <tr>
          <td class="text-start"><h6>ภาษีมูลค่าเพิ่ม 7%</h6></td>
          <td class="text-end"><?php echo $vat;?> บาท</td>
          </tr>

          <tr>
          <td class="text-start">จำนวนเงินทั้งสิ้น</td>
          <td class="text-end"><?php echo $totalPrice + $vat;?> บาท</td>
          </tr>
        </table>
        
      </div>
      
    </div>

      </div>

      <div class="position-relative ">
      <button id="convertToPDF" class="convertToPDF btn "><h3><i class="bi bi-filetype-pdf"></i></h3></button>
      </div>
  </div>
  

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.getElementById('convertToPDF').addEventListener('click', function() {
        const element = document.querySelector('.container');
        html2canvas(element).then(function(canvas) {
            const imageData = canvas.toDataURL('image/png');
            var doc = new jspdf.jsPDF();
            doc.addImage(imageData, 'PNG', 0, 0,  doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight());
            doc.save('Summary_Report.pdf');
        });
    });
</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
