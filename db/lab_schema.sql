-- Lab module schema for Telemedicine project
-- Run this file in phpMyAdmin or via mysql CLI to create lab tables and sample data

CREATE TABLE IF NOT EXISTS lab_tests (
  test_id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(32) NOT NULL,
  name VARCHAR(255) NOT NULL,
  sample_type VARCHAR(64),
  default_units VARCHAR(64),
  ref_range VARCHAR(128),
  price DECIMAL(10,2) DEFAULT 0,
  UNIQUE KEY(code)
);

CREATE TABLE IF NOT EXISTS lab_orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor VARCHAR(128),
  ordered_by VARCHAR(128),
  ordered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('PENDING','IN_PROGRESS','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  instructions TEXT,
  INDEX (patient_id),
  FOREIGN KEY (patient_id) REFERENCES patreg(pid) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lab_order_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  test_id INT NOT NULL,
  result_value TEXT,
  units VARCHAR(64),
  ref_range VARCHAR(128),
  remarks TEXT,
  completed_at TIMESTAMP NULL,
  INDEX (order_id),
  INDEX (test_id),
  FOREIGN KEY (order_id) REFERENCES lab_orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (test_id) REFERENCES lab_tests(test_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS lab_results_files (
  file_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  filename VARCHAR(255),
  filepath VARCHAR(1024),
  uploaded_by VARCHAR(128),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES lab_orders(order_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lab_technicians (
  tech_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128),
  email VARCHAR(128) UNIQUE,
  password VARCHAR(255),
  contact VARCHAR(32)
);

-- Sample tests
INSERT INTO lab_tests (code,name,sample_type,default_units,ref_range,price) VALUES
("CBC","Complete Blood Count","Blood","10^9/L","WBC:4.0-11.0;Hgb:12-17",150.00),
("LFT","Liver Function Test","Blood","U/L","AST:0-40;ALT:0-40",300.00),
("RFT","Renal Function Test","Blood","mg/dL","Creatinine:0.6-1.3",250.00),
("LIPID","Lipid Profile","Blood","mg/dL","Cholesterol:<200",300.00)
ON DUPLICATE KEY UPDATE name=VALUES(name);
