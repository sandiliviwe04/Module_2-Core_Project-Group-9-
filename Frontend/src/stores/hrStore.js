import { defineStore } from 'pinia'
import api from '../services/api'

export const useHRStore = defineStore('hr', {
    state: () => ({
        // Auth
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('auth_token') || null,
        isAuthenticated: !!localStorage.getItem('auth_token'),

        // Data
        employees: [],
        payroll: [],
        attendance: [],
        leaveRequests: [],

        // Loading states
        loading: {
            employees: false,
            payroll: false,
            attendance: false,
            leaveRequests: false,
        },

        // Errors
        errors: {
            employees: null,
            payroll: null,
            attendance: null,
            leaveRequests: null,
        },
    }),

    getters: {
        // Get employee by ID
        getEmployeeById: (state) => (id) => {
            return state.employees.find((emp) => emp.employee_id === id)
        },

        // Get pending leave requests
        pendingLeaveRequests: (state) => {
            return state.leaveRequests.filter((req) => req.status === 'Pending')
        },

        // Get approved leave requests
        approvedLeaveRequests: (state) => {
            return state.leaveRequests.filter((req) => req.status === 'Approved')
        },

        // Total employees
        totalEmployees: (state) => state.employees.length,
    },

    actions: {
        // Authentication
        async login(username, password) {
            try {
                const response = await api.login(username, password)

                if (response.success) {
                    this.user = response.user
                    this.token = response.token
                    this.isAuthenticated = true

                    // Store in localStorage
                    localStorage.setItem('user', JSON.stringify(response.user))
                    localStorage.setItem('auth_token', response.token)

                    return true
                }
                return false
            } catch (error) {
                console.error('Login error:', error)
                return false
            }
        },

        logout() {
            this.user = null
            this.token = null
            this.isAuthenticated = false

            localStorage.removeItem('user')
            localStorage.removeItem('auth_token')
        },

        // Employees
        async fetchEmployees() {
            this.loading.employees = true
            this.errors.employees = null

            try {
                const response = await api.getEmployees()
                if (response.success) {
                    this.employees = response.data
                }
            } catch (error) {
                this.errors.employees = error.message
                console.error('Error fetching employees:', error)
            } finally {
                this.loading.employees = false
            }
        },

        async addEmployee(employeeData) {
            try {
                const response = await api.createEmployee(employeeData)
                if (response.success) {
                    await this.fetchEmployees() // Refresh list
                    return true
                }
                return false
            } catch (error) {
                console.error('Error adding employee:', error)
                return false
            }
        },

        async updateEmployee(employeeData) {
            try {
                const response = await api.updateEmployee(employeeData)
                if (response.success) {
                    await this.fetchEmployees() // Refresh list
                    return true
                }
                return false
            } catch (error) {
                console.error('Error updating employee:', error)
                return false
            }
        },

        async deleteEmployee(id) {
            try {
                const response = await api.deleteEmployee(id)
                if (response.success) {
                    await this.fetchEmployees() // Refresh list
                    return true
                }
                return false
            } catch (error) {
                console.error('Error deleting employee:', error)
                return false
            }
        },

        // Payroll
        async fetchPayroll(employeeId = null) {
            this.loading.payroll = true
            this.errors.payroll = null

            try {
                const response = await api.getPayroll(employeeId)
                if (response.success) {
                    this.payroll = response.data
                }
            } catch (error) {
                this.errors.payroll = error.message
                console.error('Error fetching payroll:', error)
            } finally {
                this.loading.payroll = false
            }
        },

        async addPayroll(payrollData) {
            try {
                const response = await api.createPayroll(payrollData)
                if (response.success) {
                    await this.fetchPayroll()
                    return true
                }
                return false
            } catch (error) {
                console.error('Error adding payroll:', error)
                return false
            }
        },

        async updatePayroll(payrollData) {
            try {
                const response = await api.updatePayroll(payrollData)
                if (response.success) {
                    await this.fetchPayroll()
                    return true
                }
                return false
            } catch (error) {
                console.error('Error updating payroll:', error)
                return false
            }
        },

        // Attendance
        async fetchAttendance(employeeId = null) {
            this.loading.attendance = true
            this.errors.attendance = null

            try {
                const response = await api.getAttendance(employeeId)
                if (response.success) {
                    this.attendance = response.data
                }
            } catch (error) {
                this.errors.attendance = error.message
                console.error('Error fetching attendance:', error)
            } finally {
                this.loading.attendance = false
            }
        },

        async markAttendance(attendanceData) {
            try {
                const response = await api.markAttendance(attendanceData)
                if (response.success) {
                    await this.fetchAttendance()
                    return true
                }
                return false
            } catch (error) {
                console.error('Error marking attendance:', error)
                return false
            }
        },

        // Leave Requests
        async fetchLeaveRequests(employeeId = null) {
            this.loading.leaveRequests = true
            this.errors.leaveRequests = null

            try {
                const response = await api.getLeaveRequests(employeeId)
                if (response.success) {
                    this.leaveRequests = response.data
                }
            } catch (error) {
                this.errors.leaveRequests = error.message
                console.error('Error fetching leave requests:', error)
            } finally {
                this.loading.leaveRequests = false
            }
        },

        async submitLeaveRequest(requestData) {
            try {
                const response = await api.submitLeaveRequest(requestData)
                if (response.success) {
                    await this.fetchLeaveRequests()
                    return true
                }
                return false
            } catch (error) {
                console.error('Error submitting leave request:', error)
                return false
            }
        },

        async updateLeaveRequestStatus(requestData) {
            try {
                const response = await api.updateLeaveRequest(requestData)
                if (response.success) {
                    await this.fetchLeaveRequests()
                    return true
                }
                return false
            } catch (error) {
                console.error('Error updating leave request:', error)
                return false
            }
        },

        // Test connection
        async testConnection() {
            try {
                const response = await api.testConnection()
                console.log('Backend connection test:', response)
                return response.success
            } catch (error) {
                console.error('Backend connection failed:', error)
                return false
            }
        },
    },
})
