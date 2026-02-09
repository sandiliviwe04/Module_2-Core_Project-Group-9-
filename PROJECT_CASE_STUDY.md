# CASE STUDY

**Full Name:** [Your Name Here]  
**Project:** ModernTech HR Management System  
**Youth Force Cohort:** [Your Cohort Here]  
**Module/Brief:** Module 2 Core Project - Server-Side Development  

---

## 🏗️ Understanding the Project Brief

### In your own words, explain the project brief:
The project required the development of a professional, fully integrated Human Resources Management System. The core mission was to build a secure PHP-based RESTful API that handles essential business logic (Employees, Payroll, Attendance, and Leave) and seamlessly synchronize it with a modern Vue.js frontend for a dynamic user experience.

### What was the primary objective?
The primary objective was to transition a mock, file-based frontend into a production-ready, database-driven full-stack application. This meant moving away from static JSON files and hardcoded logic toward a secure, scalable system with real-time data persistence and authentication.

### Core requirements:
*   **Secure Authentication:** Implementing JWT tokens and bcrypt password hashing for user protection.
*   **Relational Database:** Designing a normalized MySQL database schema with proper relationships.
*   **RESTful API:** Building complete CRUD endpoints for Employees, Payroll, Attendance, and Leave.
*   **Frontend Integration:** Connecting the Vue.js interface to the API using Pinia and Axios.
*   **Security & Validation:** Ensuring all data is validated on the server and protected against SQL injection.

### Developer Role:
I acted as the Lead Full-Stack Developer, managing the end-to-end technical lifecycle of the project—from database normalization and backend API design to frontend state management and deployment.

---

## 🛠️ The Process

### Project Planning:
My planning phase focused on auditing the existing infrastructure. I identified critical naming inconsistencies in the database and planned a modular API structure. I used a step-by-step implementation plan to ensure that security (Authentication) was established before building out the feature-rich HR modules.

### Implementation Strategies:
I chose a modular backend architecture to keep the code maintainable and extensible. On the frontend, I migrated the state management to Pinia to ensure high performance and clean data flow. This strategy allowed me to build, test, and integrate each module (Employees, Payroll, etc.) independently before final consolidation.

### Technologies Used:
*   **Backend:** PHP 7.4+, MySQL
*   **Frontend:** Vue.js 3, Pinia, Axios, Vite
*   **Security:** JWT (JSON Web Tokens), bcrypt hashing, PDO Prepared Statements
*   **Environment:** XAMPP, Git/GitHub

### Challenges and Debugging:
One of the main challenges was resolving CORS (Cross-Origin Resource Sharing) conflicts between the PHP backend and the Vite dev server. I debugged this by creating a centralized CORS configuration file. I also spent considerable time fixing schema errors in the original SQL file, such as duplicate tables and missing foreign key constraints, to ensure data integrity.

### Successes and Achievements:
I achieved 100% full-stack integration across all modules. Seeing the login screen transition into a live dashboard filled with database-driven employee data was a major success. Additionally, I successfully deployed the entire project to GitHub with professional documentation.

---

## 🚀 Project Showcase

### Solution Impact:
The ModernTech HR system provides a secure and automated alternative to manual record-keeping. It eliminates data silos, ensures that payroll calculations are consistent, and provides management with a real-time overview of attendance and leave—directly impacting organizational efficiency and data security.

### Visual showcase (GitHub Link):
**[ModernTech HR - GitHub Repository](https://github.com/sandiliviwe04/Module_2-Core_Project-Group-9-)**

### Personal Reflection:
This project was a transformative learning experience. It taught me how to bridge the gap between complex server-side logic and interactive client-side interfaces. I gained a deep appreciation for application security and the importance of thorough documentation, which I now consider essential to any successful software project.
