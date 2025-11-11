<?php
include('func1.php');
// doctor-order-lab.php
// Doctor selects lab tests for a patient and creates an order

$doctor = isset($_SESSION['dname']) ? $_SESSION['dname'] : '';
$con = mysqli_connect("localhost","root","","myhmsdb");

if(!$doctor){
    header('Location: index.php'); exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
  $instructions = isset($_POST['instructions']) ? $_POST['instructions'] : '';
  $tests = isset($_POST['tests']) ? $_POST['tests'] : array();

  // Basic validation: pid must be > 0
  if($pid <= 0){
    echo "<script>alert('Invalid patient id. Please open this page from the appointment list.');window.history.back();</script>";
    exit;
  }

  // Ensure patient exists to avoid foreign key errors
  $check = mysqli_prepare($con, "SELECT pid FROM patreg WHERE pid = ? LIMIT 1");
  if($check){
    mysqli_stmt_bind_param($check, 'i', $pid);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if(mysqli_stmt_num_rows($check) === 0){
      mysqli_stmt_close($check);
      echo "<script>alert('Patient not found in the system. Cannot create lab order.');window.history.back();</script>";
      exit;
    }
    mysqli_stmt_close($check);
  } else {
    // If prepare failed, fallback to simple existence check
    $exists = mysqli_query($con, "SELECT pid FROM patreg WHERE pid = " . intval($pid) . " LIMIT 1");
    if(!$exists || mysqli_num_rows($exists) === 0){
      echo "<script>alert('Patient not found in the system. Cannot create lab order.');window.history.back();</script>";
      exit;
    }
  }

  // At least one test should be selected
  if(empty($tests)){
    echo "<script>alert('Please select at least one lab test to order.');window.history.back();</script>";
    exit;
  }

    // set session user for triggers
    $user = $doctor;
    mysqli_query($con, "SET @current_user = '".mysqli_real_escape_string($con,$user)."'");

    // insert order
    $stmt = mysqli_prepare($con, "INSERT INTO lab_orders (patient_id, doctor, ordered_by, instructions) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'isss', $pid, $doctor, $user, $instructions);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($con);
    mysqli_stmt_close($stmt);

    // insert order items
    $stmt2 = mysqli_prepare($con, "INSERT INTO lab_order_items (order_id, test_id) VALUES (?,?)");
    foreach($tests as $t){
        $tid = intval($t);
        mysqli_stmt_bind_param($stmt2, 'ii', $order_id, $tid);
        mysqli_stmt_execute($stmt2);
    }
    mysqli_stmt_close($stmt2);

    echo "<script>alert('Lab order created successfully');window.location.href='doctor-panel.php';</script>";
    exit;
}

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$fname = isset($_GET['fname']) ? htmlspecialchars($_GET['fname']) : '';
$lname = isset($_GET['lname']) ? htmlspecialchars($_GET['lname']) : '';

$tests_res = mysqli_query($con, "SELECT test_id,code,name,price FROM lab_tests ORDER BY name");

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Order Lab Tests</title>
  </head>
  <body style="padding:20px;font-family: 'IBM Plex Sans', sans-serif;">
    <div class="container">
      <h3>Order Lab Tests for <?php echo $fname . ' ' . $lname;?> (PID: <?php echo $pid;?>)</h3>
      <form method="post" action="doctor-order-lab.php">
        <input type="hidden" name="pid" value="<?php echo $pid;?>">
        <div class="form-group">
          <label>Select Tests</label>
          <div class="list-group">
            <?php while($t = mysqli_fetch_assoc($tests_res)){ ?>
              <label class="list-group-item">
                <input type="checkbox" name="tests[]" value="<?php echo $t['test_id'];?>"> <?php echo htmlspecialchars($t['name']);?> (<?php echo htmlspecialchars($t['code']);?>)
              </label>
            <?php } ?>
          </div>
        </div>
        <div class="form-group">
          <label>Instructions (optional)</label>
          <textarea name="instructions" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-primary">Create Order</button>
        <a href="doctor-panel.php" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </body>
</html>
