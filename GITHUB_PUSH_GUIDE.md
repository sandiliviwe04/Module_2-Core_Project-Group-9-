# Push to GitHub - Quick Guide

## 🚀 Step-by-Step Instructions

### Step 1: Initialize Git (if not already done)

```bash
cd "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9"
git init
```

### Step 2: Add All Files

```bash
git add .
```

### Step 3: Create Initial Commit

```bash
git commit -m "Initial commit: ModernTech HR Management System

- Complete PHP backend API with JWT authentication
- RESTful endpoints for employees, payroll, attendance, leave requests
- MySQL database schema with normalized design
- Vue.js frontend integration files
- Comprehensive documentation and presentations
- Security features: bcrypt, prepared statements, CORS
"
```

### Step 4: Create GitHub Repository

1. Go to [GitHub.com](https://github.com)
2. Click the **+** icon → **New repository**
3. Repository name: `moderntech-hr-system` (or your choice)
4. Description: `Full-stack HR Management System with PHP backend and Vue.js frontend`
5. Choose **Public** or **Private**
6. **Do NOT** initialize with README (we already have one)
7. Click **Create repository**

### Step 5: Link to GitHub

Copy the commands GitHub shows you, or use these:

```bash
git remote add origin https://github.com/YOUR_USERNAME/moderntech-hr-system.git
git branch -M main
git push -u origin main
```

**Replace `YOUR_USERNAME`** with your actual GitHub username!

---

## 📋 What's Included in Your Repository

### ✅ Backend Files
- Complete PHP API (`Backend/api/`)
- Database schema (`Backend/database/mordern_hr.sql`)
- Configuration files
- API documentation

### ✅ Frontend Files
- Integration files (`Frontend/src/`)
- API service and Pinia store
- Updated App.vue
- Setup guides

### ✅ Documentation
- Main README.md
- Backend README.md
- HOW_TO_RUN.md
- CONTRIBUTING.md
- Presentations (in `docs/`)

### ✅ Configuration
- .gitignore
- LICENSE (MIT)

---

## 🔧 Before Pushing - Security Checklist

### ⚠️ Important: Remove Sensitive Data

1. **Check database.php**
   ```bash
   # Make sure no real passwords are committed
   # Use placeholder values
   ```

2. **Check jwt.php**
   ```bash
   # Ensure secret key is generic
   # Add note to change in production
   ```

3. **Review .gitignore**
   ```bash
   # Verify sensitive files are ignored
   cat .gitignore
   ```

---

## 📝 Recommended Repository Settings

### After Pushing

1. **Add Topics** (on GitHub):
   - `php`
   - `vue`
   - `mysql`
   - `rest-api`
   - `hr-management`
   - `jwt-authentication`

2. **Add Description**:
   ```
   Full-stack HR Management System with PHP RESTful API, 
   MySQL database, and Vue.js frontend. Features employee 
   management, payroll, attendance tracking, and leave requests.
   ```

3. **Enable Issues** (for bug reports and feature requests)

4. **Add Website** (if you deploy it):
   - Your live demo URL

---

## 🌿 Git Workflow for Future Updates

### Making Changes

```bash
# 1. Check status
git status

# 2. Add specific files
git add Backend/api/Employees/index.php

# Or add all changes
git add .

# 3. Commit with meaningful message
git commit -m "Fix: Employee update validation"

# 4. Push to GitHub
git push origin main
```

### Creating Feature Branches

```bash
# Create and switch to new branch
git checkout -b feature/email-notifications

# Make your changes...

# Commit changes
git commit -m "Add: Email notification system"

# Push branch to GitHub
git push origin feature/email-notifications

# Create Pull Request on GitHub
```

---

## 📊 Repository Structure on GitHub

```
moderntech-hr-system/
├── .github/              (optional: workflows, templates)
├── Backend/
│   ├── api/
│   ├── database/
│   └── README.md
├── Frontend/
│   ├── src/
│   └── INTEGRATION_GUIDE.md
├── docs/
│   ├── PRESENTATION.md
│   ├── Group9_Presentation.md
│   └── PRESENTATION_GUIDE.md
├── .gitignore
├── CONTRIBUTING.md
├── HOW_TO_RUN.md
├── LICENSE
└── README.md
```

---

## ✅ Final Checklist

Before pushing to GitHub:

- [ ] All sensitive data removed/placeholders used
- [ ] README.md is complete and accurate
- [ ] .gitignore includes all necessary exclusions
- [ ] LICENSE file is present
- [ ] All documentation is up to date
- [ ] Code is tested and working
- [ ] Commit messages are clear
- [ ] GitHub repository is created
- [ ] Remote origin is set correctly

---

## 🎯 Quick Commands Summary

```bash
# Navigate to project
cd "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9"

# Initialize (if needed)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit: ModernTech HR Management System"

# Add remote (replace YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/moderntech-hr-system.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## 🆘 Common Issues

### Issue: "fatal: remote origin already exists"
```bash
# Solution: Remove and re-add
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/moderntech-hr-system.git
```

### Issue: "Permission denied (publickey)"
```bash
# Solution: Use HTTPS instead of SSH
git remote set-url origin https://github.com/YOUR_USERNAME/moderntech-hr-system.git
```

### Issue: "Updates were rejected"
```bash
# Solution: Pull first, then push
git pull origin main --allow-unrelated-histories
git push origin main
```

---

## 🎉 After Successful Push

Your repository is now live! Share it:

1. **Add to your portfolio**
2. **Share on LinkedIn**
3. **Add to your resume**
4. **Show to potential employers**

**Repository URL:**
```
https://github.com/YOUR_USERNAME/moderntech-hr-system
```

---

## 📞 Need Help?

- [GitHub Docs](https://docs.github.com)
- [Git Documentation](https://git-scm.com/doc)
- Create an issue in your repository

**Good luck! 🚀**
