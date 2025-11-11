<?php
// admin-audit.php
// View audit_log in admin panel style
require_once __DIR__ . '/include/config.php';
session_start();

// Fetch latest audit entries
$sql = "SELECT * FROM audit_log ORDER BY audit_id DESC LIMIT 200";
$res = mysqli_query($con, $sql);

// Optional: search/filter
$filter_action = '';
$filter_table = '';
if (isset($_POST['filter_submit'])) {
    $filter_action = isset($_POST['filter_action']) ? $_POST['filter_action'] : '';
    $filter_table = isset($_POST['filter_table']) ? $_POST['filter_table'] : '';
    
    $where = "WHERE 1=1";
    if ($filter_action) {
        $filter_action = mysqli_real_escape_string($con, $filter_action);
        $where .= " AND action = '$filter_action'";
    }
    if ($filter_table) {
        $filter_table = mysqli_real_escape_string($con, $filter_table);
        $where .= " AND table_name = '$filter_table'";
    }
    $sql = "SELECT * FROM audit_log $where ORDER BY audit_id DESC LIMIT 200";
    $res = mysqli_query($con, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <title>Audit Log - Telemedicine</title>
    <style>
        body { padding-top: 60px; }
        .navbar { background: -webkit-linear-gradient(left, #3931af, #00c6ff); }
        .btn-primary { background-color: #3c50c1; border-color: #3c50c1; }
        .table-hover tbody tr:hover { background-color: #f5f5f5; }
        .audit-badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-insert { background-color: #28a745; color: white; }
        .badge-update { background-color: #ffc107; color: black; }
        .badge-delete { background-color: #dc3545; color: white; }
        .data-cell { font-size: 12px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="admin-panel1.php"><i class="fa fa-history" aria-hidden="true"></i> Audit Log</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin-panel1.php"><i class="fa fa-arrow-left"></i> Back to Admin Panel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout1.php"><i class="fa fa-sign-out"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top: 50px; margin-bottom: 50px;">
        <h3 style="margin-left: 2%; padding-bottom: 20px; font-family: 'IBM Plex Sans', sans-serif;">
            <i class="fa fa-history"></i> AUDIT LOG - Prescription Changes
        </h3>

        <!-- Filter Section -->
        <div class="col-md-8">
            <form class="form-group" method="post" action="admin-audit.php">
                <div class="row">
                    <div class="col-md-3">
                        <select name="filter_action" class="form-control">
                            <option value="">All Actions</option>
                            <option value="INSERT" <?php echo $filter_action == 'INSERT' ? 'selected' : ''; ?>>INSERT</option>
                            <option value="UPDATE" <?php echo $filter_action == 'UPDATE' ? 'selected' : ''; ?>>UPDATE</option>
                            <option value="DELETE" <?php echo $filter_action == 'DELETE' ? 'selected' : ''; ?>>DELETE</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="filter_table" class="form-control">
                            <option value="">All Tables</option>
                            <option value="prestb" <?php echo $filter_table == 'prestb' ? 'selected' : ''; ?>>Prescriptions (prestb)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="submit" name="filter_submit" class="btn btn-primary" value="Filter">
                    </div>
                    <div class="col-md-3">
                        <a href="admin-audit.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </div>
            </form>
        </div>

        <br>

        <!-- Audit Table -->
        <div class="col-md-12">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width:8%;">#</th>
                        <th scope="col" style="width:12%;">Table</th>
                        <th scope="col" style="width:10%;">Action</th>
                        <th scope="col" style="width:8%;">Record ID</th>
                        <th scope="col" style="width:15%;">Changed By</th>
                        <th scope="col" style="width:18%;">When</th>
                        <th scope="col" style="width:30%;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($res && mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $action = htmlspecialchars($row['action']);
                            $badge_class = 'badge-insert';
                            if ($action == 'UPDATE') $badge_class = 'badge-update';
                            if ($action == 'DELETE') $badge_class = 'badge-delete';
                            
                            $old_data = htmlspecialchars(substr($row['old_data'], 0, 100));
                            $new_data = htmlspecialchars(substr($row['new_data'], 0, 100));
                            
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['audit_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['table_name']) . '</td>';
                            echo '<td><span class="audit-badge ' . $badge_class . '">' . $action . '</span></td>';
                            echo '<td>' . htmlspecialchars($row['record_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['changed_by'] ?: 'System') . '</td>';
                            echo '<td><small>' . htmlspecialchars($row['changed_at']) . '</small></td>';
                            echo '<td><small>';
                            if ($row['old_data']) {
                                echo '<strong>Old:</strong> ' . $old_data;
                                if (strlen($row['old_data']) > 100) echo '...';
                                echo '<br>';
                            }
                            if ($row['new_data']) {
                                echo '<strong>New:</strong> ' . $new_data;
                                if (strlen($row['new_data']) > 100) echo '...';
                            }
                            echo '</small></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" class="text-center text-muted">No audit records found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <br><br>

        <!-- Info Section -->
        <div class="col-md-8">
            <div class="alert alert-info alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Audit Log Information:</strong><br>
                This log tracks all changes to prescriptions. Each entry shows who made the change, when, and what data was modified.
                <br><br>
                <strong>Setup Required:</strong> Make sure <code>db/audit_triggers.sql</code> has been executed and <code>@current_user</code> is set in your PHP code before inserts/updates/deletes.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
</body>
</html>
