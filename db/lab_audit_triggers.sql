-- Audit triggers for lab module
-- These triggers write to existing audit_log table when lab orders/items change
DELIMITER $$
CREATE TRIGGER trg_lab_orders_after_insert
AFTER INSERT ON lab_orders
FOR EACH ROW
BEGIN
  DECLARE usr VARCHAR(128);
  SET usr = IFNULL(@current_user, 'system');
  INSERT INTO audit_log (table_name, action, record_id, changed_by, changed_at, new_data)
  VALUES ('lab_orders', 'INSERT', NEW.order_id, usr, NOW(), CONCAT('patient_id=',NEW.patient_id,'; doctor=',NEW.doctor,'; status=',NEW.status));
END$$

CREATE TRIGGER trg_lab_orders_after_update
AFTER UPDATE ON lab_orders
FOR EACH ROW
BEGIN
  DECLARE usr VARCHAR(128);
  SET usr = IFNULL(@current_user, 'system');
  INSERT INTO audit_log (table_name, action, record_id, changed_by, changed_at, old_data, new_data)
  VALUES ('lab_orders', 'UPDATE', NEW.order_id, usr, NOW(), CONCAT('status=',OLD.status,';instructions=',IFNULL(OLD.instructions,'')), CONCAT('status=',NEW.status,';instructions=',IFNULL(NEW.instructions,'')));
END$$

CREATE TRIGGER trg_lab_order_items_after_update
AFTER UPDATE ON lab_order_items
FOR EACH ROW
BEGIN
  DECLARE usr VARCHAR(128);
  SET usr = IFNULL(@current_user, 'system');
  INSERT INTO audit_log (table_name, action, record_id, changed_by, changed_at, old_data, new_data)
  VALUES ('lab_order_items', 'UPDATE', NEW.item_id, usr, NOW(), CONCAT('result=',IFNULL(OLD.result_value,'')), CONCAT('result=',IFNULL(NEW.result_value,'')));
END$$

DELIMITER ;
