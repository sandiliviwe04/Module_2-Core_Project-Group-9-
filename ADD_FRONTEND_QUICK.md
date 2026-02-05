# Quick Frontend Addition Guide

## 🚀 Copy Frontend to Your Project

Run these commands in PowerShell:

```powershell
# Navigate to your project
cd "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9"

# Remove the current Frontend folder
Remove-Item -Recurse -Force Frontend

# Copy the complete Vue.js project
Copy-Item -Recurse "C:\Users\Student\Downloads\Core_Project_Module_1\Module_1_Core_Project-master\*" "Frontend\"

# Copy the integration files we created
Copy-Item "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9\Frontend\src\services\api.js" "Frontend\src\services\" -Force
Copy-Item "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9\Frontend\src\stores\hrStore.js" "Frontend\src\stores\" -Force
Copy-Item "C:\Users\Student\Desktop\Module_2 Core_Project(G9)\m2-core-project-g9\Frontend\src\App.vue" "Frontend\src\" -Force
```

## 📝 Then Update Git

```bash
# Add the new files
git add .

# Commit
git commit -m "Add complete Vue.js frontend application"

# Push to GitHub
git push origin main
```

## ✅ Done!

Your GitHub repo will now have the complete full-stack application!
