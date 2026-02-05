CREATE DATABASE IF NOT EXISTS moderntech_hr;
USE moderntech_hr;

-- Users table for authentication
CREATE TABLE users(
user_id INT PRIMARY KEY AUTO_INCREMENT,
username VARCHAR(50) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
role ENUM('admin', 'hr', 'employee') DEFAULT 'employee',
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
last_login DATETIME NULL,
INDEX idx_username (username),
INDEX idx_email (email)
) ENGINE=InnoDB;

CREATE TABLE employees(
employee_id INT PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(100) NOT NULL,
position VARCHAR(100),
department VARCHAR(50),
salary DECIMAL(10,2),
employment_history TEXT,
contact VARCHAR(100),
INDEX idx_department (department),
INDEX idx_name (name)
) ENGINE=InnoDB;

CREATE TABLE attendance(
id INT AUTO_INCREMENT PRIMARY KEY,
employee_id INT,
date DATE,
status ENUM('Present', 'Absent'),
FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
INDEX idx_employee_date (employee_id, date),
INDEX idx_date (date)
) ENGINE=InnoDB;

CREATE TABLE leave_requests(
id INT AUTO_INCREMENT PRIMARY KEY,
employee_id INT,
date DATE,
reason VARCHAR(255),
status ENUM('Approved', 'Denied', 'Pending') DEFAULT 'Pending',
FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
INDEX idx_employee (employee_id),
INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE payroll(
id INT AUTO_INCREMENT PRIMARY KEY,
employee_id INT,
hours_worked DECIMAL(5,2),
leave_deductions INT,
final_salary DECIMAL(10,2),
FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
INDEX idx_employee (employee_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS employee_reviews(
review_id INT AUTO_INCREMENT PRIMARY KEY,
employee_id INT NOT NULL,
review_text TEXT NOT NULL,
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
INDEX idx_employee (employee_id)
) ENGINE=InnoDB;

 INSERT INTO employees (employee_id, name, position, department, salary, employment_history, contact)  VALUES
(1, 'Sibongile Nkosi', 'Software Engineer', 'Development', 70000, 'Joined in 2015, promoted to Senior in 2018', 'sibongile.nkosi@moderntech.com'),
(2, 'Lungile Moyo', 'HR Manager', 'HR', 80000, 'Joined in 2013, promoted to Manager in 2017', 'lungile.moyo@moderntech.com'),
(3, 'Thabo Molefe', 'Quality Analyst', 'QA', 55000, 'Joined in 2018', 'thabo.molefe@moderntech.com'),
(4, 'Keshav Naidoo', 'Sales Representative', 'Sales', 60000, 'Joined in 2020', 'keshav.naidoo@moderntech.com'),
(5, 'Zanele Khumalo', 'Marketing Specialist', 'Marketing', 58000, 'Joined in 2019', 'zanele.khumalo@moderntech.com'),
(6, 'Sipho Zulu', 'UI/UX Designer', 'Design', 65000, 'Joined in 2016', 'sipho.zulu@moderntech.com'),
(7, 'Naledi Moeketsi', 'DevOps Engineer', 'IT', 72000, 'Joined in 2017', 'naledi.moeketsi@moderntech.com'),
(8, 'Farai Gumbo', 'Content Strategist', 'Marketing', 56000, 'Joined in 2021', 'farai.gumbo@moderntech.com'),
(9, 'Karabo Dlamini', 'Accountant', 'Finance', 62000, 'Joined in 2018', 'karabo.dlamini@moderntech.com'),
(10, 'Fatima Patel', 'Customer Support Lead', 'Support', 58000, 'Joined in 2016', 'fatima.patel@moderntech.com');

INSERT INTO attendance (employee_id, date, status) VALUES
(1, '2025-07-25', 'Present'), (1, '2025-07-26', 'Present'), (1, '2025-07-27', 'Present'), (1, '2025-07-28', 'Present'), (1, '2025-07-29', 'Present'),
(2, '2025-07-25', 'Present'), (2, '2025-07-26', 'Present'), (2, '2025-07-27', 'Present'), (2, '2025-07-28', 'Present'), (2, '2025-07-29', 'Present'),
(3, '2025-07-25', 'Present'), (3, '2025-07-26', 'Absent'), (3, '2025-07-27', 'Present'), (3, '2025-07-28', 'Absent'), (3, '2025-07-29', 'Present'),
(4, '2025-07-25', 'Absent'), (4, '2025-07-26', 'Present'), (4, '2025-07-27', 'Absent'), (4, '2025-07-28', 'Present'), (4, '2025-07-29', 'Present'),
(5, '2025-07-25', 'Present'), (5, '2025-07-26', 'Absent'), (5, '2025-07-27', 'Absent'), (5, '2025-07-28', 'Present'), (5, '2025-07-29', 'Absent'),
(6, '2025-07-25', 'Present'), (6, '2025-07-26', 'Present'), (6, '2025-07-27', 'Absent'), (6, '2025-07-28', 'Present'), (6, '2025-07-29', 'Present'),
(7, '2025-07-25', 'Absent'), (7, '2025-07-26', 'Present'), (7, '2025-07-27', 'Present'), (7, '2025-07-28', 'Absent'), (7, '2025-07-29', 'Present'),
(8, '2025-07-25', 'Present'), (8, '2025-07-26', 'Absent'), (8, '2025-07-27', 'Absent'), (8, '2025-07-28', 'Present'), (8, '2025-07-29', 'Absent'),
(9, '2025-07-25', 'Present'), (9, '2025-07-26', 'Present'), (9, '2025-07-27', 'Present'), (9, '2025-07-28', 'Absent'), (9, '2025-07-29', 'Present'),
(10, '2025-07-25', 'Present'), (10, '2025-07-26', 'Present'), (10, '2025-07-27', 'Absent'), (10, '2025-07-28', 'Present'), (10, '2025-07-29', 'Absent');

INSERT INTO leave_requests (employee_id, date, reason, status) VALUES
(1, '2025-07-22', 'Sick Leave', 'Approved'), (1, '2024-12-01', 'Personal', 'Pending'),
(2, '2025-07-15', 'Family Responsibility', 'Denied'), (2, '2024-12-02', 'Vacation', 'Approved'),
(3, '2025-07-10', 'Medical Appointment', 'Approved'), (3, '2024-12-05', 'Personal', 'Pending'),
(4, '2025-07-20', 'Bereavement', 'Approved'),
(5, '2024-12-01', 'Childcare', 'Pending'),
(6, '2025-07-18', 'Sick Leave', 'Pending'),
(7, '2025-07-22', 'Vacation', 'Pending'),
(8, '2024-12-02', 'Medical Appointment', 'Approved'),
(9, '2025-07-19', 'Childcare', 'Denied'),
(10, '2024-12-03', 'Vacation', 'Denied');

INSERT INTO payroll (employee_id, hours_worked, leave_deductions, final_salary) VALUES
(1, 169, 8, 76500), (2, 175, 10, 30000), (3, 190, 4, 56800), (4, 135, 6, 99700), (5, 188, 5, 88850),
(6, 168, 2, 88800), (7, 165, 3, 91800), (8, 156, 0, 54000), (9, 155, 5, 61500), (10, 172, 4, 77750);

-- Insert default admin user (password: password123)
INSERT INTO users (username, password_hash, email, role) VALUES
('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@moderntech.com', 'admin');
