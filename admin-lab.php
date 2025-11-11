<?php
include('include/config.php');
session_start();
// Simple admin summary for lab orders
$con = $con;

$total = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM lab_orders"))[0];
$pending = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM lab_orders WHERE status='PENDING'"))[0];
$completed = mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM lab_orders WHERE status='COMPLETED'"))[0];

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Lab Admin Summary</title>
  </head>
  <body style="padding:20px;font-family: 'IBM Plex Sans', sans-serif;">
    <div class="container">
      <h3>Lab Summary</h3>
      <div class="row">
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Total Orders</h5><p><?php echo $total;?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Pending</h5><p><?php echo $pending;?></p></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h5>Completed</h5><p><?php echo $completed;?></p></div></div></div>
      </div>
      <br>
      <a class="btn btn-primary" href="lab-panel.php">Open Lab Panel</a>
      <a class="btn btn-secondary" href="admin-panel1.php">Back to Admin</a>
    </div>
  </body>
</html>
