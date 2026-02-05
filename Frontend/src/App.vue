<template>
  <div id="app">
    <div v-if="!isLoggedIn" class="login-screen">
      <div class="login-container">
        <div class="login-header">
          <div class="logo">
            <i class="bi bi-building-fill"></i>
            <h1>ModernTech HR</h1>
          </div>
          <p class="subtitle">Human Resources Management System</p>
        </div>
        
        <div class="login-card">
          <h2>Welcome Back</h2>
          <div class="input-group">
            <label for="username">Username</label>
            <input 
              v-model="username" 
              id="username"
              type="text" 
              placeholder="Enter your username"
              class="form-input"
              @keyup.enter="login"
            >
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input 
              v-model="password" 
              id="password"
              type="password" 
              placeholder="Enter your password"
              class="form-input"
              @keyup.enter="login"
            >
          </div>
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>
          <button @click="login" class="btn-login" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            {{ loading ? 'Signing in...' : 'Sign In' }}
          </button>
          <div class="demo-credentials">
            <p>Demo credentials:</p>
            <p><strong>Admin</strong> / <strong>password123</strong></p>
          </div>
        </div>
        
        <div class="login-footer">
          <p>© 2025 ModernTech HR. All rights reserved.</p>
        </div>
      </div>
    </div>

    <div v-else class="main-app">
      <aside class="sidebar" :class="{ 'sidebar-collapsed': !isSidebarOpen }">
        <div class="sidebar-header">
          <div class="logo-small">
            <i class="bi bi-building"></i>
            <h3 v-if="isSidebarOpen">ModernTech HR</h3>
          </div>
          <button 
            @click="toggleSidebar" 
            class="toggle-btn"
            :aria-label="isSidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
          >
            <i class="bi" :class="isSidebarOpen ? 'bi-chevron-left' : 'bi-chevron-right'"></i>
          </button>
        </div>
        
        <nav class="sidebar-nav">
          <router-link 
            to="/dashboard" 
            class="nav-item"
            :class="{ 'active': $route.path === '/dashboard' }"
            @click="closeMobileSidebar"
          >
            <i class="bi bi-speedometer2"></i>
            <span v-if="isSidebarOpen">Dashboard</span>
          </router-link>
          
          <router-link 
            to="/employees" 
            class="nav-item"
            :class="{ 'active': $route.path === '/employees' }"
            @click="closeMobileSidebar"
          >
            <i class="bi bi-people-fill"></i>
            <span v-if="isSidebarOpen">Employees</span>
          </router-link>
          
          <router-link 
            to="/payroll" 
            class="nav-item"
            :class="{ 'active': $route.path === '/payroll' }"
            @click="closeMobileSidebar"
          >
            <i class="bi bi-cash-stack"></i>
            <span v-if="isSidebarOpen">Payroll</span>
          </router-link>
          
          <router-link 
            to="/timeoff" 
            class="nav-item"
            :class="{ 'active': $route.path === '/timeoff' }"
            @click="closeMobileSidebar"
          >
            <i class="bi bi-calendar-event"></i>
            <span v-if="isSidebarOpen">Time Off</span>
          </router-link>
        </nav>
        
        <div class="sidebar-footer">
          <div class="user-info" v-if="isSidebarOpen">
            <div class="avatar">
              <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
              <strong>{{ currentUser?.username || 'Admin User' }}</strong>
              <small>{{ currentUser?.role || 'System Administrator' }}</small>
            </div>
          </div>
          <button @click="logout" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span v-if="isSidebarOpen">Logout</span>
          </button>
        </div>
      </aside>

      <main class="main-content" :class="{ 'expanded': !isSidebarOpen }">
        <header class="topbar">
          <button @click="toggleSidebar" class="menu-toggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
          </button>
          <div class="topbar-title">
            <h2>{{ currentTitle }}</h2>
            <p class="breadcrumb">{{ getBreadcrumb() }}</p>
          </div>
          <div class="topbar-actions">
          </div>
        </header>
        
        <div class="content-wrapper">
          <router-view></router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useHRStore } from './stores/hrStore'

export default {
  name: 'App',
  setup() {
    const router = useRouter()
    const hrStore = useHRStore()
    
    const isLoggedIn = ref(hrStore.isAuthenticated)
    const username = ref('')
    const password = ref('')
    const loading = ref(false)
    const errorMessage = ref('')
    const isSidebarOpen = ref(true)
    
    const routes = {
      '/dashboard': 'Dashboard',
      '/employees': 'Employee Management',
      '/payroll': 'Payroll & Attendance',
      '/timeoff': 'Time Off Management'
    }
    
    const currentTitle = computed(() => {
      return routes[router.currentRoute.value.path] || 'Dashboard'
    })
    
    const currentUser = computed(() => hrStore.user)
    
    const login = async () => {
      if (!username.value || !password.value) {
        errorMessage.value = 'Please enter both username and password'
        return
      }
      
      loading.value = true
      errorMessage.value = ''
      
      try {
        const success = await hrStore.login(username.value, password.value)
        
        if (success) {
          isLoggedIn.value = true
          username.value = ''
          password.value = ''
          router.push('/dashboard')
        } else {
          errorMessage.value = 'Invalid credentials. Please try again.'
        }
      } catch (error) {
        console.error('Login error:', error)
        errorMessage.value = 'Login failed. Please check that the backend server is running.'
      }
      
      loading.value = false
    }
    
    const logout = () => {
      if (confirm('Are you sure you want to logout?')) {
        hrStore.logout()
        isLoggedIn.value = false
        router.push('/')
      }
    }
    
    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value
    }
    
    const closeMobileSidebar = () => {
      if (window.innerWidth < 768) {
        isSidebarOpen.value = false
      }
    }
    
    const getBreadcrumb = () => {
      const path = router.currentRoute.value.path
      if (path === '/dashboard') return 'Overview & Analytics'
      if (path === '/employees') return 'Manage your workforce'
      if (path === '/payroll') return 'Salary processing & attendance'
      if (path === '/timeoff') return 'Leave requests & approvals'
      return ''
    }
    
    const handleResize = () => {
      if (window.innerWidth < 768) {
        isSidebarOpen.value = false
      } else {
        isSidebarOpen.value = true
      }
    }
    
    onMounted(() => {
      handleResize()
      window.addEventListener('resize', handleResize)
      
      // Check if user is already logged in
      if (hrStore.isAuthenticated) {
        isLoggedIn.value = true
      }
    })
    
    onUnmounted(() => {
      window.removeEventListener('resize', handleResize)
    })
    
    return {
      isLoggedIn,
      username,
      password,
      loading,
      errorMessage,
      isSidebarOpen,
      currentTitle,
      currentUser,
      login,
      logout,
      toggleSidebar,
      closeMobileSidebar,
      getBreadcrumb
    }
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary: #b19cd9;
  --primary-dark: #8f7ad9;
  --secondary: #1a1a2e;
  --dark: #0f0f1a;
  --light-dark: #2d2d44;
  --text: #e6e6ff;
  --text-secondary: #a0a0c0;
  --success: #4caf50;
  --warning: #ffc107;
  --danger: #f44336;
  --info: #2196f3;
  --border: #4a4a6d;
  --shadow: rgba(0, 0, 0, 0.2);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
  font-family: 'Inter', sans-serif;
  background: var(--dark);
  color: var(--text);
  line-height: 1.6;
  overflow-x: hidden;
}

#app {
  min-height: 100vh;
}

/* Login Screen */
.login-screen {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--secondary) 0%, var(--dark) 100%);
  padding: 1rem;
}

.login-container {
  width: 100%;
  max-width: 420px;
}

.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.logo i {
  font-size: 3rem;
  color: var(--primary);
}

.logo h1 {
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  color: var(--text-secondary);
  font-size: 0.95rem;
}

.login-card {
  background: var(--light-dark);
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 20px 40px var(--shadow);
  border: 1px solid var(--border);
  margin-bottom: 2rem;
  animation: slideUp 0.5s ease-out;
}

.login-card h2 {
  margin-bottom: 2rem;
  color: var(--text);
  font-weight: 600;
  text-align: center;
}

.input-group {
  margin-bottom: 1.5rem;
}

.input-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  background: var(--secondary);
  border: 2px solid var(--border);
  border-radius: 10px;
  color: var(--text);
  font-size: 1rem;
  transition: var(--transition);
}

.form-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
}

.error-message {
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: rgba(244, 67, 54, 0.1);
  border: 1px solid rgba(244, 67, 54, 0.3);
  border-radius: 8px;
  color: var(--danger);
  font-size: 0.9rem;
  text-align: center;
}

.btn-login {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  margin-top: 1rem;
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(177, 156, 217, 0.3);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(26, 26, 46, 0.3);
  border-radius: 50%;
  border-top-color: var(--secondary);
  animation: spin 1s ease-in-out infinite;
  margin-right: 8px;
}

.demo-credentials {
  margin-top: 1.5rem;
  padding: 1rem;
  background: rgba(177, 156, 217, 0.1);
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
}

.demo-credentials p {
  margin: 0.25rem 0;
}

.login-footer {
  text-align: center;
  color: var(--text-secondary);
  font-size: 0.85rem;
}

/* Main App Layout */
.main-app {
  display: flex;
  min-height: 100vh;
  background: var(--dark);
}

/* Sidebar */
.sidebar {
  width: 260px;
  background: var(--secondary);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  z-index: 1000;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-collapsed {
  width: 70px;
}

.sidebar-header {
  padding: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--border);
}

.logo-small {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-small i {
  font-size: 1.5rem;
  color: var(--primary);
  flex-shrink: 0;
}

.logo-small h3 {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.toggle-btn {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  flex-shrink: 0;
}

.toggle-btn:hover {
  background: rgba(177, 156, 217, 0.1);
  color: var(--primary);
}

/* Navigation */
.sidebar-nav {
  flex: 1;
  padding: 1.5rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1.5rem;
  color: var(--text-secondary);
  text-decoration: none;
  transition: var(--transition);
  margin: 0.25rem 0;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background: rgba(177, 156, 217, 0.05);
  color: var(--text);
}

.nav-item.active {
  background: rgba(177, 156, 217, 0.1);
  color: var(--primary);
  border-left-color: var(--primary);
}

.nav-item i {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.nav-item span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-weight: 500;
}

/* Sidebar Footer */
.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid var(--border);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.avatar {
  width: 40px;
  height: 40px;
  background: rgba(177, 156, 217, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  font-size: 1.5rem;
  flex-shrink: 0;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-details strong {
  display: block;
  font-size: 0.9rem;
  color: var(--text);
}

.user-details small {
  color: var(--text-secondary);
  font-size: 0.8rem;
}

.btn-logout {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid rgba(244, 67, 54, 0.2);
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
  font-weight: 500;
}

.btn-logout:hover {
  background: rgba(244, 67, 54, 0.2);
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 260px;
  transition: var(--transition);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content.expanded {
  margin-left: 70px;
}

.topbar {
  background: var(--secondary);
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
}

.menu-toggle {
  display: none;
  background: transparent;
  border: none;
  color: var(--text);
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: var(--transition);
}

.menu-toggle:hover {
  background: rgba(177, 156, 217, 0.1);
}

.topbar-title h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.breadcrumb {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.content-wrapper {
  flex: 1;
  padding: 1.5rem;
  overflow-y: auto;
}

/* Animations */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }
  
  .main-content {
    margin-left: 240px;
  }
  
  .main-content.expanded {
    margin-left: 70px;
  }
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
    box-shadow: 5px 0 30px rgba(0, 0, 0, 0.3);
  }
  
  .sidebar:not(.sidebar-collapsed) {
    transform: translateX(0);
  }
  
  .main-content {
    margin-left: 0 !important;
  }
  
  .menu-toggle {
    display: block;
  }
  
  .login-card {
    padding: 2rem;
  }
  
  .content-wrapper {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .login-card {
    padding: 1.5rem;
  }
  
  .logo h1 {
    font-size: 2rem;
  }
  
  .logo i {
    font-size: 2.5rem;
  }
  
  .topbar-title h2 {
    font-size: 1.25rem;
  }
}
</style>
