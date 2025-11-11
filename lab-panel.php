<?php
// Simple lab technician panel - view pending orders and enter results
include('include/config.php');
session_start();

$con = $con; // from include/config.php

// technician name from session if exists
$tech = isset($_SESSION['tech_name']) ? $_SESSION['tech_name'] : 'lab-tech';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);
    // set current user for triggers
    mysqli_query($con, "SET @current_user = '".mysqli_real_escape_string($con,$tech)."'");

    // update each item
    if(isset($_POST['item']) && is_array($_POST['item'])){
        $stmt = mysqli_prepare($con, "UPDATE lab_order_items SET result_value = ?, remarks = ?, completed_at = NOW() WHERE item_id = ?");
        foreach($_POST['item'] as $item_id => $vals){
            $val = trim($vals['result']);
            $remark = trim($vals['remark']);
            $iid = intval($item_id);
            mysqli_stmt_bind_param($stmt, 'ssi', $val, $remark, $iid);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    }

    // handle file upload
    if(isset($_FILES['result_file']) && $_FILES['result_file']['error']==0){
        $uploads_dir = __DIR__ . '/uploads/lab_results';
        if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);
        $tmp = $_FILES['result_file']['tmp_name'];
        $name = basename($_FILES['result_file']['name']);
        $target = $uploads_dir . '/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
        if(move_uploaded_file($tmp, $target)){
            $filepath = str_replace('\\','/',$target);
            $stmtf = mysqli_prepare($con, "INSERT INTO lab_results_files (order_id, filename, filepath, uploaded_by) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmtf, 'isss', $order_id, $name, $filepath, $tech);
            mysqli_stmt_execute($stmtf);
            mysqli_stmt_close($stmtf);
        }
    }

    // mark order completed
    $stmt2 = mysqli_prepare($con, "UPDATE lab_orders SET status='COMPLETED' WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt2, 'i', $order_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    echo "<script>alert('Results saved');window.location.href='lab-panel.php';</script>";
    exit;
}

$orders = mysqli_query($con, "SELECT * FROM lab_orders ORDER BY ordered_at DESC");

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Lab Technician Panel</title>
  </head>
  <body style="padding:20px;font-family: 'IBM Plex Sans', sans-serif;">
    <div class="container">
      <h3>Lab Orders</h3>
      <table class="table table-hover">
        <thead>
          <tr><th>Order ID</th><th>Patient ID</th><th>Doctor</th><th>Ordered At</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php while($o = mysqli_fetch_assoc($orders)){ ?>
            <tr>
              <td><?php echo $o['order_id'];?></td>
              <td><?php echo $o['patient_id'];?></td>
              <td><?php echo htmlspecialchars($o['doctor']);?></td>
              <td><?php echo $o['ordered_at'];?></td>
              <td><?php echo $o['status'];?></td>
              <td>
                <a href="lab-panel.php?view=<?php echo $o['order_id'];?>" class="btn btn-sm btn-primary">Open</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php if(isset($_GET['view'])){
        $order_id = intval($_GET['view']);
        $order = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM lab_orders WHERE order_id = $order_id"));
        if($order){
      ?>
      <hr>
      <h4>Order #<?php echo $order['order_id'];?> &mdash; Patient: <?php echo $order['patient_id'];?></h4>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="order_id" value="<?php echo $order['order_id'];?>">
        <table class="table table-bordered">
          <thead><tr><th>Test</th><th>Result</th><th>Remarks</th></tr></thead>
          <tbody>
            <?php $items = mysqli_query($con, "SELECT l.*, t.name FROM lab_order_items l JOIN lab_tests t ON l.test_id=t.test_id WHERE l.order_id=$order_id");
            while($it = mysqli_fetch_assoc($items)){
              ?>
              <tr>
                <td><?php echo htmlspecialchars($it['name']);?></td>
                <td><input class="form-control" name="item[<?php echo $it['item_id'];?>][result]" value="<?php echo htmlspecialchars($it['result_value']);?>"></td>
                <td><input class="form-control" name="item[<?php echo $it['item_id'];?>][remark]" value="<?php echo htmlspecialchars($it['remarks']);?>"></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="form-group">
          <label>Attach result file (PDF / JPG / PNG)</label>
          <input type="file" name="result_file" class="form-control-file">
        </div>
        <button class="btn btn-success">Save Results</button>
        <a class="btn btn-secondary" href="lab-panel.php">Back</a>
      </form>
      <?php }} ?>
    </div>
  </body>
</html>
