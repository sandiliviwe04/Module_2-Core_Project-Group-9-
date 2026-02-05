import axios from 'axios'

// Base URL for your backend API
// Update this to match your server setup
const API_BASE_URL = 'http://localhost/m2-core-project-g9/Backend/api'
// Alternative for PHP built-in server: 'http://localhost:8000'

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Add token to requests if available
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Handle response errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid - clear auth and redirect to login
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/'
    }
    return Promise.reject(error)
  }
)

// API Service
export default {
  // Authentication
  async login(username, password) {
    const response = await apiClient.post('/Auth/Login.php', {
      username,
      password,
    })
    return response.data
  },

  // Employees
  async getEmployees() {
    const response = await apiClient.get('/Employees/')
    return response.data
  },

  async getEmployee(id) {
    const response = await apiClient.get(`/Employees/?id=${id}`)
    return response.data
  },

  async createEmployee(employeeData) {
    const response = await apiClient.post('/Employees/', employeeData)
    return response.data
  },

  async updateEmployee(employeeData) {
    const response = await apiClient.put('/Employees/', employeeData)
    return response.data
  },

  async deleteEmployee(id) {
    const response = await apiClient.delete(`/Employees/?id=${id}`)
    return response.data
  },

  // Payroll
  async getPayroll(employeeId = null) {
    const url = employeeId ? `/Payroll/?employee_id=${employeeId}` : '/Payroll/'
    const response = await apiClient.get(url)
    return response.data
  },

  async createPayroll(payrollData) {
    const response = await apiClient.post('/Payroll/', payrollData)
    return response.data
  },

  async updatePayroll(payrollData) {
    const response = await apiClient.put('/Payroll/', payrollData)
    return response.data
  },

  // Attendance
  async getAttendance(employeeId = null) {
    const url = employeeId ? `/Attendance/?employee_id=${employeeId}` : '/Attendance/'
    const response = await apiClient.get(url)
    return response.data
  },

  async markAttendance(attendanceData) {
    const response = await apiClient.post('/Attendance/', attendanceData)
    return response.data
  },

  async updateAttendance(attendanceData) {
    const response = await apiClient.put('/Attendance/', attendanceData)
    return response.data
  },

  // Leave Requests
  async getLeaveRequests(employeeId = null) {
    const url = employeeId ? `/LeaveRequests/?employee_id=${employeeId}` : '/LeaveRequests/'
    const response = await apiClient.get(url)
    return response.data
  },

  async submitLeaveRequest(requestData) {
    const response = await apiClient.post('/LeaveRequests/', requestData)
    return response.data
  },

  async updateLeaveRequest(requestData) {
    const response = await apiClient.put('/LeaveRequests/', requestData)
    return response.data
  },

  // Test endpoint
  async testConnection() {
    const response = await apiClient.get('/test.php')
    return response.data
  },
}
