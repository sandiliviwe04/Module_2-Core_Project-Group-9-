import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../components/Dashboard.vue'
import EmployeeManagement from '../components/EmployeeManagement.vue'
import PayrollAttendance from '../components/PayrollAttendance.vue'
import TimeOffDashboard from '../components/TimeOffDashboard.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { title: 'Dashboard' }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: EmployeeManagement,
    meta: { title: 'Employees' }
  },
  {
    path: '/payroll',
    name: 'Payroll',
    component: PayrollAttendance,
    meta: { title: 'Payroll' }
  },
  {
    path: '/timeoff',
    name: 'TimeOff',
    component: TimeOffDashboard,
    meta: { title: 'Time Off' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})

// Update page title
router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} | ModernTech HR` : 'ModernTech HR'
  next()
})

export default router