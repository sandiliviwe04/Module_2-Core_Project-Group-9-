# How to Run Your ModernTech HR Project

## 📁 Understanding Your Project Structure

You have **TWO separate folders**:

### 1. Backend (PHP) - No npm needed
```
📂 C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9\Backend\
   ├── api/
   ├── database/
   └── README.md
```
**How to run:** Use XAMPP/WAMP (Apache + MySQL)

### 2. Frontend (Vue.js) - Needs npm
```
📂 C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master\
   ├── src/
   ├── package.json
   └── node_modules/
```
**How to run:** `npm run dev`

---

## 🚀 Step-by-Step: How to Run the Project

### Step 1: Start Backend (XAMPP)

1. **Open XAMPP Control Panel**
2. **Start Apache** (click Start button)
3. **Start MySQL** (click Start button)
4. **Verify backend is running:**
   - Open browser
   - Go to: `http://localhost/m2-core-project-g9/Backend/api/test.php`
   - You should see JSON response

---

### Step 2: Start Frontend (Vue.js)

**Option A: Using Command Prompt**

1. Press `Win + R`
2. Type `cmd` and press Enter
3. Run these commands:
```cmd
cd "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master"
npm run dev
```

**Option B: Using PowerShell**

1. Open PowerShell
2. Run:
```powershell
cd "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master"
npm run dev
```

**Option C: Using VS Code Terminal**

1. Open VS Code
2. File → Open Folder → Select `Module_1_Core_Project-master`
3. Terminal → New Terminal
4. Run:
```bash
npm run dev
```

---

### Step 3: Access the Application

After `npm run dev` starts successfully:

1. You'll see output like:
```
  VITE v7.2.4  ready in 500 ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
```

2. **Open your browser**
3. **Go to:** `http://localhost:5173/`
4. **Login with:**
   - Username: `Admin`
   - Password: `password123`

---

## ❌ Common Errors & Fixes

### Error: "Cannot find package.json"
**Problem:** You're in the wrong folder  
**Fix:** Make sure you're in the frontend folder:
```cmd
cd "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master"
```

### Error: "MySQL shutdown unexpectedly"
**Problem:** MySQL won't start in XAMPP  
**Fix:** See `Backend\MYSQL_TROUBLESHOOTING.md`

### Error: "Network Error" in browser
**Problem:** Backend not running or wrong URL  
**Fix:** 
1. Check XAMPP is running
2. Verify API URL in `src/services/api.js`

---

## 🔧 Quick Troubleshooting

### Backend Not Working?
```bash
# Check if Apache is running
# Open browser: http://localhost/

# Check if MySQL is running
# Open browser: http://localhost/phpmyadmin/

# Test backend API
# Open browser: http://localhost/m2-core-project-g9/Backend/api/test.php
```

### Frontend Not Working?
```bash
# Make sure you're in the right folder
cd "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master"

# Check if node_modules exists
dir node_modules

# If not, install dependencies
npm install

# Then run
npm run dev
```

---

## 📊 Complete Startup Checklist

- [ ] XAMPP Control Panel open
- [ ] Apache running (green)
- [ ] MySQL running (green)
- [ ] Database imported (`moderntech_hr`)
- [ ] Backend test endpoint works
- [ ] Terminal in frontend folder
- [ ] `npm run dev` running
- [ ] Browser open to `http://localhost:5173/`
- [ ] Can login successfully

---

## 🎯 Quick Commands Reference

### Backend (XAMPP)
```
No commands needed - just click Start in XAMPP
Test: http://localhost/m2-core-project-g9/Backend/api/test.php
```

### Frontend (Vue.js)
```bash
# Navigate to frontend
cd "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master"

# Install dependencies (first time only)
npm install

# Run development server
npm run dev

# Access: http://localhost:5173/
```

---

## 💡 Pro Tips

1. **Keep XAMPP running** while developing
2. **Don't close the terminal** running `npm run dev`
3. **Use Ctrl+C** in terminal to stop the dev server
4. **Refresh browser** after making code changes
5. **Check browser console (F12)** for errors

---

## 🆘 Still Having Issues?

### Backend Issues
- See: `Backend\README.md`
- See: `Backend\MYSQL_TROUBLESHOOTING.md`

### Frontend Issues
- Make sure you're in the correct folder
- Try deleting `node_modules` and running `npm install` again
- Check that Node.js is installed: `node --version`

### Integration Issues
- Verify API base URL in `src/services/api.js`
- Check browser console for CORS errors
- Ensure backend is running before starting frontend
