<?php
include('func.php');
// patient-lab-results.php - patient can view their completed lab results

if(!isset($_SESSION['pid'])){
    header('Location: index.php'); exit;
}

$pid = $_SESSION['pid'];
$con = mysqli_connect("localhost","root","","myhmsdb");

$orders = mysqli_query($con, "SELECT * FROM lab_orders WHERE patient_id = " . intval($pid) . " ORDER BY ordered_at DESC");

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>My Lab Results</title>
  </head>
  <body style="padding:20px;font-family: 'IBM Plex Sans', sans-serif;">
    <div class="container">
      <h3>My Lab Results</h3>
      <?php if(mysqli_num_rows($orders) == 0){ ?>
        <div class="alert alert-info">No lab orders found.</div>
      <?php } else { ?>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Ordered At</th>
              <th>Status</th>
              <th>Tests & Results</th>
              <th>Files</th>
            </tr>
          </thead>
          <tbody>
            <?php while($o = mysqli_fetch_assoc($orders)){ ?>
              <tr>
                <td><?php echo $o['order_id'];?></td>
                <td><?php echo $o['ordered_at'];?></td>
                <td><?php echo $o['status'];?></td>
                <td>
                  <ul>
                  <?php
                    $items = mysqli_query($con, "SELECT l.*, t.name FROM lab_order_items l JOIN lab_tests t ON l.test_id=t.test_id WHERE l.order_id=".intval($o['order_id']));
                    while($it = mysqli_fetch_assoc($items)){
                      echo '<li><strong>'.htmlspecialchars($it['name']).'</strong> - ' . htmlspecialchars($it['result_value'] ? $it['result_value'] : 'Pending') . '</li>';
                    }
                  ?>
                  </ul>
                </td>
                <td>
                  <?php
                    $files = mysqli_query($con, "SELECT * FROM lab_results_files WHERE order_id=".intval($o['order_id']));
                    while($f = mysqli_fetch_assoc($files)){
                      $path = htmlspecialchars($f['filepath']);
                      $name = htmlspecialchars($f['filename']);
                      echo '<div><a href="'. $path .'" target="_blank">'. $name .'</a></div>';
                    }
                  ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
      <a class="btn btn-secondary" href="admin-panel.php">Back to Dashboard</a>
    </div>
  </body>
</html>
