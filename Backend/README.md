# ModernTech HR Backend API Documentation

## Overview

This is the PHP backend API for the ModernTech HR Management System. It provides RESTful JSON endpoints for managing employees, payroll, attendance, and leave requests.

## Features

✅ **Database-Driven Authentication** with JWT tokens  
✅ **Complete CRUD Operations** for all HR entities  
✅ **Secure Password Hashing** using bcrypt  
✅ **CORS Support** for frontend integration  
✅ **Input Validation** and error handling  
✅ **Prepared Statements** to prevent SQL injection  

---

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Import the Database**

```bash
mysql -u root -p < database/mordern_hr.sql
```

This will create the `moderntech_hr` database with all necessary tables and sample data.

2. **Configure Database Connection**

Edit `api/Config/database.php` if your MySQL credentials differ:

```php
private $host = "localhost";
private $db_name = "moderntech_hr";
private $username = "root";
private $password = "";  // Update if you have a password
private $port = 3306;
```

3. **Start the Server**

**Option A: PHP Built-in Server**
```bash
cd Backend/api
php -S localhost:8000
```

**Option B: XAMPP/WAMP**
- Place the project in `htdocs` or `www` folder
- Access via `http://localhost/m2-core-project-g9/Backend/api/`

---

## API Endpoints

### Base URL
```
http://localhost:8000
```

### Authentication

#### Login
**POST** `/Auth/Login.php`

**Request Body:**
```json
{
  "username": "Admin",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "user_id": 1,
    "username": "Admin",
    "email": "admin@moderntech.com",
    "role": "admin"
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "error": "Invalid credentials"
}
```

---

### Employees

**All employee endpoints require authentication. Include the JWT token in the Authorization header:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### Get All Employees
**GET** `/Employees/`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "employee_id": 1,
      "name": "Sibongile Nkosi",
      "position": "Software Engineer",
      "department": "Development",
      "salary": "70000.00",
      "employment_history": "Joined in 2015, promoted to Senior in 2018",
      "contact": "sibongile.nkosi@moderntech.com"
    }
  ],
  "count": 10
}
```

#### Get Single Employee
**GET** `/Employees/?id=1`

#### Create Employee
**POST** `/Employees/`

**Request Body:**
```json
{
  "name": "John Doe",
  "position": "Developer",
  "department": "IT",
  "salary": 65000,
  "employment_history": "Joined in 2026",
  "contact": "john.doe@moderntech.com"
}
```

#### Update Employee
**PUT** `/Employees/`

**Request Body:**
```json
{
  "employee_id": 1,
  "name": "Sibongile Nkosi",
  "position": "Senior Software Engineer",
  "department": "Development",
  "salary": 85000,
  "employment_history": "Joined in 2015, promoted to Senior in 2018, Lead in 2026",
  "contact": "sibongile.nkosi@moderntech.com"
}
```

#### Delete Employee
**DELETE** `/Employees/?id=1`

---

### Payroll

#### Get All Payroll Records
**GET** `/Payroll/`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "hours_worked": "169.00",
      "leave_deductions": 8,
      "final_salary": "76500.00",
      "name": "Sibongile Nkosi",
      "salary": "70000.00"
    }
  ],
  "count": 10
}
```

#### Get Employee Payroll
**GET** `/Payroll/?employee_id=1`

#### Create Payroll Record
**POST** `/Payroll/`

**Request Body:**
```json
{
  "employee_id": 1,
  "hours_worked": 160,
  "leave_deductions": 5,
  "final_salary": 72000
}
```

#### Update Payroll
**PUT** `/Payroll/`

**Request Body:**
```json
{
  "id": 1,
  "hours_worked": 170,
  "leave_deductions": 3,
  "final_salary": 78000
}
```

---

### Attendance

#### Get All Attendance Records
**GET** `/Attendance/`

#### Get Employee Attendance
**GET** `/Attendance/?employee_id=1`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "date": "2025-07-29",
      "status": "Present",
      "name": "Sibongile Nkosi"
    }
  ],
  "count": 5
}
```

#### Mark Attendance
**POST** `/Attendance/`

**Request Body:**
```json
{
  "employee_id": 1,
  "date": "2026-02-05",
  "status": "Present"
}
```

**Status values:** `Present` or `Absent`

#### Update Attendance
**PUT** `/Attendance/`

**Request Body:**
```json
{
  "id": 1,
  "status": "Absent"
}
```

---

### Leave Requests

#### Get All Leave Requests
**GET** `/LeaveRequests/`

#### Get Employee Leave Requests
**GET** `/LeaveRequests/?employee_id=1`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "date": "2025-07-22",
      "reason": "Sick Leave",
      "status": "Approved",
      "name": "Sibongile Nkosi"
    }
  ],
  "count": 2
}
```

#### Submit Leave Request
**POST** `/LeaveRequests/`

**Request Body:**
```json
{
  "employee_id": 1,
  "date": "2026-02-10",
  "reason": "Medical Appointment"
}
```

#### Update Leave Request Status
**PUT** `/LeaveRequests/`

**Request Body:**
```json
{
  "id": 1,
  "status": "Approved"
}
```

**Status values:** `Pending`, `Approved`, or `Denied`

---

## Testing the API

### Using cURL

**Test Database Connection:**
```bash
curl http://localhost:8000/test.php
```

**Login:**
```bash
curl -X POST http://localhost:8000/Auth/Login.php \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"Admin\",\"password\":\"password123\"}"
```

**Get Employees (with token):**
```bash
curl http://localhost:8000/Employees/ \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Browser

1. Start the PHP server
2. Open `http://localhost:8000/test.php` in your browser
3. Open Developer Tools (F12) → Network tab
4. Verify JSON response with proper headers

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request (validation error) |
| 401 | Unauthorized (invalid/missing token) |
| 404 | Not Found |
| 405 | Method Not Allowed |
| 500 | Internal Server Error |

---

## Security Features

- ✅ **Password Hashing**: All passwords stored using `password_hash()` with bcrypt
- ✅ **JWT Authentication**: Stateless token-based authentication
- ✅ **Prepared Statements**: All SQL queries use PDO prepared statements
- ✅ **Input Validation**: Server-side validation for all inputs
- ✅ **CORS Configuration**: Proper cross-origin resource sharing setup
- ✅ **Error Handling**: Comprehensive try-catch blocks with JSON error responses

---

## Default Credentials

**Username:** `Admin`  
**Password:** `password123`

---

## Troubleshooting

### Database Connection Failed
- Verify MySQL is running
- Check credentials in `api/Config/database.php`
- Ensure database `moderntech_hr` exists

### CORS Errors
- Check that `api/Config/cors.php` is included
- Verify frontend URL is allowed (currently set to `*`)

### Token Errors
- Ensure Authorization header is formatted: `Bearer TOKEN`
- Check token hasn't expired (24-hour expiry)
- Verify JWT secret key matches in `api/Config/jwt.php`

---

## Project Structure

```
Backend/
├── api/
│   ├── Auth/
│   │   └── Login.php          # Authentication endpoint
│   ├── Config/
│   │   ├── cors.php           # CORS configuration
│   │   ├── database.php       # Database connection
│   │   └── jwt.php            # JWT utilities
│   ├── Middleware/
│   │   └── auth.php           # Token verification
│   ├── Employees/
│   │   └── index.php          # Employee CRUD
│   ├── Payroll/
│   │   └── index.php          # Payroll management
│   ├── Attendance/
│   │   └── index.php          # Attendance tracking
│   ├── LeaveRequests/
│   │   └── index.php          # Leave request management
│   └── test.php               # Test endpoint
└── database/
    └── mordern_hr.sql         # Database schema
```

---

## Next Steps

1. ✅ Backend API is complete and ready
2. 🔄 Integrate frontend Vue.js application
3. 🔄 Test all endpoints with frontend
4. 🔄 Deploy to production server

For frontend integration, see the implementation plan for details on connecting the Vue.js application to these API endpoints.
