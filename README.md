# ModernTech HR Management System

A full-stack Human Resources Management System built with PHP backend and Vue.js frontend.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

ModernTech HR is a comprehensive HR management system designed to streamline employee management, payroll processing, attendance tracking, and leave request management. The system features a secure RESTful API backend with JWT authentication and a modern Vue.js frontend.

**Developed by:** Group 9  
**Project Type:** Module 2 Core Project - Server-Side Development

## ✨ Features

### Core Functionality
- 🔐 **Secure Authentication** - JWT-based login system with bcrypt password hashing
- 👥 **Employee Management** - Complete CRUD operations for employee records
- 💰 **Payroll Processing** - Manage salary calculations and payment records
- 📅 **Attendance Tracking** - Daily attendance monitoring and reporting
- 🏖️ **Leave Management** - Submit and approve time-off requests
- 📊 **Dashboard Analytics** - Real-time HR metrics and insights

### Technical Features
- ✅ RESTful API architecture
- ✅ JWT token authentication (24-hour expiry)
- ✅ SQL injection prevention via prepared statements
- ✅ CORS-enabled for cross-origin requests
- ✅ Responsive Vue.js frontend
- ✅ State management with Pinia
- ✅ Comprehensive error handling
- ✅ Input validation (client & server-side)

## 🛠️ Technology Stack

### Backend
- **Language:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Architecture:** RESTful API
- **Authentication:** JWT (JSON Web Tokens)
- **Database Access:** PDO (PHP Data Objects)

### Frontend
- **Framework:** Vue.js 3.x
- **State Management:** Pinia
- **HTTP Client:** Axios
- **Build Tool:** Vite
- **Styling:** Custom CSS with modern design

### Development Tools
- **Server:** XAMPP/WAMP
- **Version Control:** Git
- **Package Manager:** npm
- **Code Editor:** VS Code (recommended)

## 📁 Project Structure

```
m2-core-project-g9/
├── Backend/
│   ├── api/
│   │   ├── Auth/
│   │   │   └── Login.php              # Authentication endpoint
│   │   ├── Config/
│   │   │   ├── cors.php               # CORS configuration
│   │   │   ├── database.php           # Database connection
│   │   │   └── jwt.php                # JWT utilities
│   │   ├── Middleware/
│   │   │   └── auth.php               # Token verification
│   │   ├── Employees/
│   │   │   └── index.php              # Employee CRUD API
│   │   ├── Payroll/
│   │   │   └── index.php              # Payroll management API
│   │   ├── Attendance/
│   │   │   └── index.php              # Attendance tracking API
│   │   ├── LeaveRequests/
│   │   │   └── index.php              # Leave requests API
│   │   └── test.php                   # API test endpoint
│   ├── database/
│   │   └── mordern_hr.sql             # Database schema
│   ├── README.md                      # Backend documentation
│   └── QUICKSTART.md                  # Quick start guide
├── Frontend/
│   ├── src/
│   │   ├── services/
│   │   │   └── api.js                 # API service module
│   │   ├── stores/
│   │   │   └── hrStore.js             # Pinia store
│   │   └── App.vue                    # Updated main component
│   ├── INTEGRATION_GUIDE.md           # Integration instructions
│   └── ACTION_LIST.md                 # Setup checklist
├── docs/
│   ├── PRESENTATION.md                # Full presentation
│   ├── Group9_Presentation.md         # 5-min presentation
│   └── PRESENTATION_GUIDE.md          # Presentation instructions
├── .gitignore                         # Git ignore rules
├── README.md                          # This file
├── HOW_TO_RUN.md                      # Running instructions
└── LICENSE                            # MIT License
```

## 🚀 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Node.js 16+ and npm
- XAMPP/WAMP (or similar local server)
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/m2-core-project-g9.git
cd m2-core-project-g9
```

### Step 2: Backend Setup

1. **Start XAMPP/WAMP**
   - Start Apache
   - Start MySQL

2. **Import Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `moderntech_hr`
   - Import: `Backend/database/mordern_hr.sql`

3. **Configure Database Connection**
   - Edit `Backend/api/Config/database.php`
   - Update credentials if needed:
   ```php
   private $host = "localhost";
   private $db_name = "moderntech_hr";
   private $username = "root";
   private $password = "";
   private $port = 3306;
   ```

4. **Test Backend**
   - Open browser: `http://localhost/m2-core-project-g9/Backend/api/test.php`
   - Should see JSON response with `"success": true`

### Step 3: Frontend Setup

**Note:** The frontend files in this repo are integration files. You need to copy them to your Vue.js project.

1. **Navigate to your Vue.js project**
   ```bash
   cd path/to/your/vue-project
   ```

2. **Install Dependencies**
   ```bash
   npm install pinia axios
   ```

3. **Copy Integration Files**
   - Copy `Frontend/src/services/api.js` to your project's `src/services/`
   - Copy `Frontend/src/stores/hrStore.js` to your project's `src/stores/`
   - Copy `Frontend/src/App.vue` to your project's `src/`

4. **Update main.js**
   ```javascript
   import { createApp } from 'vue'
   import { createPinia } from 'pinia'
   import App from './App.vue'
   import router from './router'

   const app = createApp(App)
   app.use(createPinia())
   app.use(router)
   app.mount('#app')
   ```

5. **Configure API Base URL**
   - Edit `src/services/api.js`
   - Update line 5:
   ```javascript
   const API_BASE_URL = 'http://localhost/m2-core-project-g9/Backend/api'
   ```

6. **Run Development Server**
   ```bash
   npm run dev
   ```

7. **Access Application**
   - Open browser: `http://localhost:5173/`
   - Login: `Admin` / `password123`

## ⚙️ Configuration

### Backend Configuration

**Database Settings** (`Backend/api/Config/database.php`):
```php
private $host = "localhost";
private $db_name = "moderntech_hr";
private $username = "root";
private $password = "";
private $port = 3306;
```

**JWT Secret** (`Backend/api/Config/jwt.php`):
```php
private static $secret_key = "your-secret-key-here";
```
⚠️ **Important:** Change this in production!

### Frontend Configuration

**API Base URL** (`src/services/api.js`):
```javascript
const API_BASE_URL = 'http://localhost/m2-core-project-g9/Backend/api'
```

Update this based on your server setup:
- Local XAMPP: `http://localhost/m2-core-project-g9/Backend/api`
- PHP built-in server: `http://localhost:8000`
- Production: `https://yourdomain.com/api`

## 📖 Usage

### Default Login Credentials

- **Username:** `Admin`
- **Password:** `password123`

⚠️ **Change these credentials in production!**

### Creating New Users

1. Hash your password:
   ```php
   echo password_hash("your_password", PASSWORD_BCRYPT);
   ```

2. Insert into database:
   ```sql
   INSERT INTO users (username, password_hash, email, role)
   VALUES ('newuser', 'hashed_password_here', 'user@example.com', 'admin');
   ```

### Common Operations

**Add Employee:**
- Navigate to Employees page
- Click "Add Employee"
- Fill in details
- Submit

**Process Payroll:**
- Go to Payroll page
- Select employee
- Enter hours and deductions
- Calculate and save

**Track Attendance:**
- Open Attendance page
- Select date and employee
- Mark Present/Absent
- Submit

**Manage Leave Requests:**
- Navigate to Time Off page
- Submit new request or approve pending ones

## 📚 API Documentation

### Authentication

**Login**
```http
POST /Auth/Login.php
Content-Type: application/json

{
  "username": "Admin",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "user_id": 1,
    "username": "Admin",
    "role": "admin"
  }
}
```

### Employees

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/Employees/` | Get all employees |
| GET | `/Employees/?id=1` | Get single employee |
| POST | `/Employees/` | Create employee |
| PUT | `/Employees/` | Update employee |
| DELETE | `/Employees/?id=1` | Delete employee |

### Payroll

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/Payroll/` | Get all payroll records |
| GET | `/Payroll/?employee_id=1` | Get employee payroll |
| POST | `/Payroll/` | Create payroll record |
| PUT | `/Payroll/` | Update payroll |

### Attendance

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/Attendance/` | Get all attendance |
| GET | `/Attendance/?employee_id=1` | Get employee attendance |
| POST | `/Attendance/` | Mark attendance |
| PUT | `/Attendance/` | Update attendance |

### Leave Requests

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/LeaveRequests/` | Get all requests |
| GET | `/LeaveRequests/?employee_id=1` | Get employee requests |
| POST | `/LeaveRequests/` | Submit request |
| PUT | `/LeaveRequests/` | Update status |

**Full API Documentation:** See `Backend/README.md`

## 🔒 Security

### Implemented Security Measures

1. **Password Security**
   - Bcrypt hashing (cost factor 10)
   - No plaintext storage
   - Secure verification

2. **Authentication**
   - JWT tokens with HMAC-SHA256
   - 24-hour token expiry
   - Stateless authentication

3. **SQL Injection Prevention**
   - PDO prepared statements
   - Parameter binding
   - No string concatenation in queries

4. **Input Validation**
   - Server-side validation
   - Type checking
   - Required field validation
   - Enum validation for status fields

5. **CORS Configuration**
   - Proper cross-origin headers
   - Preflight request handling

### Security Best Practices

- ✅ Change default credentials
- ✅ Update JWT secret key
- ✅ Use HTTPS in production
- ✅ Implement rate limiting
- ✅ Regular security audits
- ✅ Keep dependencies updated

## 🧪 Testing

### Backend Testing

**Test Database Connection:**
```bash
curl http://localhost/m2-core-project-g9/Backend/api/test.php
```

**Test Login:**
```bash
curl -X POST http://localhost/m2-core-project-g9/Backend/api/Auth/Login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"Admin","password":"password123"}'
```

**Test Employee API:**
```bash
curl http://localhost/m2-core-project-g9/Backend/api/Employees/ \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Frontend Testing

1. Start dev server: `npm run dev`
2. Open browser: `http://localhost:5173/`
3. Test login functionality
4. Verify all CRUD operations
5. Check browser console for errors

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 for PHP code
- Use ESLint for JavaScript/Vue.js
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation for new features

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

**Group 9**
- [Your Name] - Backend Development
- [Team Member 2] - Frontend Development
- [Team Member 3] - Database Design
- [Team Member 4] - Testing & Documentation

## 🙏 Acknowledgments

- PHP Documentation
- Vue.js Documentation
- MySQL Documentation
- MDN Web Docs
- Stack Overflow Community

## 📞 Support

For issues, questions, or contributions:
- **Issues:** [GitHub Issues](https://github.com/yourusername/m2-core-project-g9/issues)
- **Email:** your.email@example.com
- **Documentation:** See `docs/` folder

## 🗺️ Roadmap

### Version 2.0 (Planned)
- [ ] Email notifications
- [ ] Advanced reporting and analytics
- [ ] File upload functionality
- [ ] Multi-tenant support
- [ ] Role-based access control
- [ ] Mobile application
- [ ] Calendar integration
- [ ] Export to PDF/Excel

---

**Built with ❤️ by Group 9**