-- Audit triggers and audit_log table for Telemedicine project
-- Save as db/audit_triggers.sql and run in phpMyAdmin (SQL tab) or via MySQL CLI
-- This script creates an `audit_log` table and three triggers on `prestb` (INSERT/UPDATE/DELETE).
-- Triggers record old/new row data and the user who made the change (from session variable @current_user).

-- Create audit table
CREATE TABLE IF NOT EXISTS audit_log (
  audit_id INT NOT NULL AUTO_INCREMENT,
  table_name VARCHAR(64) NOT NULL,
  action ENUM('INSERT','UPDATE','DELETE') NOT NULL,
  record_id INT DEFAULT NULL,
  changed_by VARCHAR(100) DEFAULT NULL,
  changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  old_data TEXT,
  new_data TEXT,
  PRIMARY KEY (audit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DELIMITER $$

-- AFTER INSERT trigger: log new row
CREATE TRIGGER trg_prestb_after_insert
AFTER INSERT ON prestb
FOR EACH ROW
BEGIN
  INSERT INTO audit_log(table_name, action, record_id, changed_by, new_data)
  VALUES(
    'prestb',
    'INSERT',
    NEW.ID,
    @current_user,
    CONCAT(
      'doctor=', NEW.doctor,
      ';pid=', NEW.pid,
      ';ID=', NEW.ID,
      ';fname=', NEW.fname,
      ';lname=', NEW.lname,
      ';appdate=', NEW.appdate,
      ';apptime=', NEW.apptime,
      ';disease=', NEW.disease,
      ';allergy=', NEW.allergy,
      ';prescription=', NEW.prescription
    )
  );
END$$

-- AFTER UPDATE trigger: log old and new
CREATE TRIGGER trg_prestb_after_update
AFTER UPDATE ON prestb
FOR EACH ROW
BEGIN
  INSERT INTO audit_log(table_name, action, record_id, changed_by, old_data, new_data)
  VALUES(
    'prestb',
    'UPDATE',
    NEW.ID,
    @current_user,
    CONCAT(
      'doctor=', OLD.doctor,
      ';pid=', OLD.pid,
      ';ID=', OLD.ID,
      ';fname=', OLD.fname,
      ';lname=', OLD.lname,
      ';appdate=', OLD.appdate,
      ';apptime=', OLD.apptime,
      ';disease=', OLD.disease,
      ';allergy=', OLD.allergy,
      ';prescription=', OLD.prescription
    ),
    CONCAT(
      'doctor=', NEW.doctor,
      ';pid=', NEW.pid,
      ';ID=', NEW.ID,
      ';fname=', NEW.fname,
      ';lname=', NEW.lname,
      ';appdate=', NEW.appdate,
      ';apptime=', NEW.apptime,
      ';disease=', NEW.disease,
      ';allergy=', NEW.allergy,
      ';prescription=', NEW.prescription
    )
  );
END$$

-- AFTER DELETE trigger: log old row
CREATE TRIGGER trg_prestb_after_delete
AFTER DELETE ON prestb
FOR EACH ROW
BEGIN
  INSERT INTO audit_log(table_name, action, record_id, changed_by, old_data)
  VALUES(
    'prestb',
    'DELETE',
    OLD.ID,
    @current_user,
    CONCAT(
      'doctor=', OLD.doctor,
      ';pid=', OLD.pid,
      ';ID=', OLD.ID,
      ';fname=', OLD.fname,
      ';lname=', OLD.lname,
      ';appdate=', OLD.appdate,
      ';apptime=', OLD.apptime,
      ';disease=', OLD.disease,
      ';allergy=', OLD.allergy,
      ';prescription=', OLD.prescription
    )
  );
END$$

DELIMITER ;

-- Usage notes:
-- 1) Set the session variable @current_user before performing INSERT/UPDATE/DELETE so the trigger records the user.
--    From PHP (mysqli):
--      mysqli_query($conn, "SET @current_user = '" . mysqli_real_escape_string($conn, $username) . "'");
--    Make sure the SET and the DML run on the same DB connection.
--
-- 2) To test in phpMyAdmin: run
--      SET @current_user = 'testuser';
--      INSERT INTO prestb (doctor, pid, ID, fname, lname, appdate, apptime, disease, allergy, prescription)
--      VALUES ('TestDr', 1, 999, 'Test', 'Patient', '2025-01-01', '10:00:00', 'Flu', 'None', 'Take rest');
--
-- 3) To view audit records:
--      SELECT * FROM audit_log ORDER BY audit_id DESC LIMIT 50;
--
-- 4) Consider adding an archival or pruning policy for `audit_log` if it grows large.
--
-- Save this file and run it against your `myhmsdb` database.
