# ModernTech HR Management System

A full-stack Human Resources Management System built with a PHP backend and Vue.js frontend.

## 🚀 Quick Start Guide

### 1. Prerequisites
- PHP 7.4+ & MySQL 5.7+ (XAMPP/WAMP recommended)
- Node.js 16+ & npm

### 2. Backend Setup (PHP & MySQL)
1. **Start Apache & MySQL** in your XAMPP/WAMP control panel.
2. **Import Database:**
   - Create a database named `moderntech_hr`.
   - Import `Backend/database/mordern_hr.sql`.
3. **Verify Connection:**
   - Check `Backend/api/Config/database.php` for your MySQL credentials.
   - Test: `http://localhost/m2-core-project-g9/Backend/api/test.php` (Should see success JSON).

### 3. Frontend Setup (Vue.js)
1. **Navigate to the frontend folder:**
   ```bash
   cd Frontend
   ```
2. **Install dependencies:**
   ```bash
   npm install
   ```
3. **Run development server:**
   ```bash
   npm run dev
   ```
4. **Access:** Open `http://localhost:5173/` in your browser.

---

## 🔐 Authentication
- **Username:** `Admin`
- **Password:** `password123`

---

## 📁 Project Structure
- `Backend/` - PHP RESTful API & MySQL Database schema
- `Frontend/` - Vue.js application integrated with the API

---

## 🛠️ Technology Stack
- **Backend:** PHP, MySQL, JWT, PDO
- **Frontend:** Vue.js, Pinia (State Management), Axios (API calls), Vite

---

## 🔒 Security Measures
- ✅ JWT Token Authentication
- ✅ Bcrypt Password Hashing
- ✅ SQL Injection Prevention (Prepared Statements)
- ✅ CORS-enabled for secure cross-origin requests

---

**Built by Group 9**