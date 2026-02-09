<template>
  <div class="employee-management">
    <div class="page-header">
      <div class="header-content">
        <h1>Employee Management</h1>
        <p>Manage your workforce, add new employees, and update records</p>
      </div>
      <div class="header-actions">
        <button class="btn-add" @click="showForm = true">
          <i class="bi bi-person-plus"></i>
          Add Employee
        </button>
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input 
            v-model="searchQuery" 
            placeholder="Search employees..."
            @input="filterEmployees"
          >
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ filteredEmployees.length }}</h3>
          <p>Total Employees</p>
        </div>
        <i class="bi bi-people-fill"></i>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>R{{ formatCurrency(avgSalary) }}</h3>
          <p>Average Salary</p>
        </div>
        <i class="bi bi-cash-stack"></i>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ deptCount }}</h3>
          <p>Departments</p>
        </div>
        <i class="bi bi-building"></i>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ newThisMonth }}</h3>
          <p>New This Month</p>
        </div>
        <i class="bi bi-graph-up"></i>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="table-responsive desktop-table">
      <table class="employee-table">
        <thead>
          <tr>
            <th @click="sortBy('name')">
              Name
              <i class="bi" :class="sortIcon('name')"></i>
            </th>
            <th @click="sortBy('position')">
              Position
              <i class="bi" :class="sortIcon('position')"></i>
            </th>
            <th @click="sortBy('department')">
              Department
              <i class="bi" :class="sortIcon('department')"></i>
            </th>
            <th @click="sortBy('salary')">
              Salary (ZAR)
              <i class="bi" :class="sortIcon('salary')"></i>
            </th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in paginatedEmployees" :key="emp.id">
            <td>
              <div class="employee-info">
                <div class="avatar">{{ emp.name.charAt(0) }}</div>
                <div>
                  <strong>{{ emp.name }}</strong>
                  <small>{{ emp.email || `${emp.name.toLowerCase().replace(/\s+/g, '.')}@moderntech.co.za` }}</small>
                </div>
              </div>
            </td>
            <td>{{ emp.position }}</td>
            <td>
              <span class="dept-badge" :style="getDeptStyle(emp.department)">
                {{ emp.department }}
              </span>
            </td>
            <td>
              <span class="salary-amount">R{{ formatRand(emp.salary) }}</span>
            </td>
            <td>
              <div class="action-buttons">
                <button class="btn-action edit" @click="editEmployee(emp)" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action view" @click="viewEmployee(emp)" title="View">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn-action delete" @click="deleteEmployee(emp.id)" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div class="pagination" v-if="totalPages > 1">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="page-btn"
        >
          <i class="bi bi-chevron-left"></i>
        </button>
        
        <button 
          v-for="page in pages" 
          :key="page"
          @click="goToPage(page)"
          :class="{ active: currentPage === page }"
          class="page-btn"
        >
          {{ page }}
        </button>
        
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="page-btn"
        >
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Cards -->
    <div class="mobile-cards">
      <div v-for="emp in paginatedEmployees" :key="emp.id" class="employee-card">
        <div class="card-header">
          <div class="employee-avatar">{{ emp.name.charAt(0) }}</div>
          <div class="employee-details">
            <h3>{{ emp.name }}</h3>
            <p>{{ emp.position }}</p>
          </div>
          <div class="employee-salary">
            <span class="salary">R{{ formatRand(emp.salary) }}</span>
            <small class="salary-label">Monthly</small>
          </div>
        </div>
        
        <div class="card-body">
          <div class="info-row">
            <span class="info-label">Department:</span>
            <span class="info-value dept-badge" :style="getDeptStyle(emp.department)">
              {{ emp.department }}
            </span>
          </div>
          <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ emp.email || `${emp.name.toLowerCase().replace(/\s+/g, '.')}@moderntech.co.za` }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Start Date:</span>
            <span class="info-value">{{ emp.startDate || '2023-01-15' }}</span>
          </div>
        </div>
        
        <div class="card-actions">
          <button class="btn-mobile edit" @click="editEmployee(emp)">
            <i class="bi bi-pencil"></i>
            Edit
          </button>
          <button class="btn-mobile view" @click="viewEmployee(emp)">
            <i class="bi bi-eye"></i>
            View
          </button>
          <button class="btn-mobile delete" @click="deleteEmployee(emp.id)">
            <i class="bi bi-trash"></i>
            Delete
          </button>
        </div>
      </div>
      
      <!-- Mobile Pagination -->
      <div class="mobile-pagination" v-if="totalPages > 1">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="mobile-page-btn"
        >
          <i class="bi bi-chevron-left"></i>
        </button>
        
        <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
        
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="mobile-page-btn"
        >
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Employee' : 'Add New Employee' }}</h2>
          <button @click="closeModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <form @submit.prevent="saveEmployee" class="employee-form">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input 
                v-model="formData.name" 
                id="name"
                type="text" 
                placeholder="Enter full name"
                required
              >
            </div>
            <div class="form-group">
              <label for="position">Position *</label>
              <input 
                v-model="formData.position" 
                id="position"
                type="text" 
                placeholder="Enter position"
                required
              >
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="department">Department *</label>
              <select v-model="formData.department" id="department" required>
                <option value="">Select Department</option>
                <option value="Development">Development</option>
                <option value="HR">Human Resources</option>
                <option value="Sales">Sales</option>
                <option value="Marketing">Marketing</option>
                <option value="Finance">Finance</option>
                <option value="Support">Support</option>
                <option value="IT">IT</option>
                <option value="QA">Quality Assurance</option>
                <option value="Design">Design</option>
              </select>
            </div>
            <div class="form-group">
              <label for="salary">Monthly Salary (ZAR) *</label>
              <div class="salary-input">
                <span class="currency">R</span>
                <input 
                  v-model.number="formData.salary" 
                  id="salary"
                  type="number" 
                  placeholder="Enter salary"
                  min="0"
                  required
                >
              </div>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="email">Email Address</label>
              <input 
                v-model="formData.email" 
                id="email"
                type="email" 
                placeholder="employee@moderntech.co.za"
              >
            </div>
            <div class="form-group">
              <label for="startDate">Start Date</label>
              <input 
                v-model="formData.startDate" 
                id="startDate"
                type="date" 
              >
            </div>
          </div>
          
          <div class="form-group full-width">
            <label for="notes">Notes</label>
            <textarea 
              v-model="formData.notes" 
              id="notes"
              placeholder="Additional notes about this employee..."
              rows="3"
            ></textarea>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary">
              {{ isEditing ? 'Update Employee' : 'Add Employee' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Employee Modal -->
    <div v-if="viewingEmployee" class="modal-overlay" @click.self="closeViewModal">
      <div class="modal-content view-modal">
        <div class="modal-header">
          <h2>Employee Details</h2>
          <button @click="closeViewModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <div class="employee-profile">
          <div class="profile-header">
            <div class="profile-avatar">{{ viewingEmployee.name.charAt(0) }}</div>
            <div class="profile-info">
              <h3>{{ viewingEmployee.name }}</h3>
              <p>{{ viewingEmployee.position }}</p>
              <span class="dept-badge" :style="getDeptStyle(viewingEmployee.department)">
                {{ viewingEmployee.department }}
              </span>
            </div>
          </div>
          
          <div class="profile-details">
            <div class="detail-item">
              <span class="detail-label">Salary:</span>
              <span class="detail-value">R{{ (viewingEmployee.salary) }} / month</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email:</span>
              <span class="detail-value">{{ viewingEmployee.email || `${viewingEmployee.name.toLowerCase().replace(/\s+/g, '.')}@moderntech.co.za` }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Start Date:</span>
              <span class="detail-value">{{ viewingEmployee.startDate || 'Not specified' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Employee ID:</span>
              <span class="detail-value">EMP-{{ viewingEmployee.id.toString().padStart(4, '0') }}</span>
            </div>
            <div v-if="viewingEmployee.notes" class="detail-item full-width">
              <span class="detail-label">Notes:</span>
              <span class="detail-value">{{ viewingEmployee.notes }}</span>
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="editEmployee(viewingEmployee)" class="btn-primary">
            <i class="bi bi-pencil"></i>
            Edit Employee
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const USD_TO_ZAR = 1

export default {
  name: 'EmployeeManagement',
  data() {
    return {
      // Load employee data from employee_info.json
      employees: [
        { 
          id: 1, 
          name: 'Sibongile Nkosi', 
          position: 'Software Engineer', 
          department: 'Development', 
          salary: 70000 , 
          email: 'sibongile.nkosi@moderntech.com', 
          startDate: '2015-03-15',
          notes: 'Joined in 2015, promoted to Senior in 2018'
        },
        { 
          id: 2, 
          name: 'Lungile Moyo', 
          position: 'HR Manager', 
          department: 'HR', 
          salary: 80000 * USD_TO_ZAR, 
          email: 'lungile.moyo@moderntech.com', 
          startDate: '2013-08-01',
          notes: 'Joined in 2013, promoted to Manager in 2017'
        },
        { 
          id: 3, 
          name: 'Thabo Molefe', 
          position: 'Quality Analyst', 
          department: 'QA', 
          salary: 55000 * USD_TO_ZAR, 
          email: 'thabo.molefe@moderntech.com', 
          startDate: '2018-01-10',
          notes: 'Joined in 2018'
        },
        { 
          id: 4, 
          name: 'Keshav Naidoo', 
          position: 'Sales Representative', 
          department: 'Sales', 
          salary: 60000 * USD_TO_ZAR, 
          email: 'keshav.naidoo@moderntech.com', 
          startDate: '2020-11-22',
          notes: 'Joined in 2020'
        },
        { 
          id: 5, 
          name: 'Zanele Khumalo', 
          position: 'Marketing Specialist', 
          department: 'Marketing', 
          salary: 58000 * USD_TO_ZAR, 
          email: 'zanele.khumalo@moderntech.com', 
          startDate: '2019-02-28',
          notes: 'Joined in 2019'
        },
        { 
          id: 6, 
          name: 'Sipho Zulu', 
          position: 'UI/UX Designer', 
          department: 'Design', 
          salary: 65000 * USD_TO_ZAR, 
          email: 'sipho.zulu@moderntech.com', 
          startDate: '2016-06-15',
          notes: 'Joined in 2016'
        },
        { 
          id: 7, 
          name: 'Naledi Moeketsi', 
          position: 'DevOps Engineer', 
          department: 'IT', 
          salary: 72000 * USD_TO_ZAR, 
          email: 'naledi.moeketsi@moderntech.com', 
          startDate: '2017-12-05',
          notes: 'Joined in 2017'
        },
        { 
          id: 8, 
          name: 'Farai Gumbo', 
          position: 'Content Strategist', 
          department: 'Marketing', 
          salary: 56000 * USD_TO_ZAR, 
          email: 'farai.gumbo@moderntech.com', 
          startDate: '2021-03-01',
          notes: 'Joined in 2021'
        },
        { 
          id: 9, 
          name: 'Karabo Dlamini', 
          position: 'Accountant', 
          department: 'Finance', 
          salary: 62000 * USD_TO_ZAR, 
          email: 'karabo.dlamini@moderntech.com', 
          startDate: '2018-09-18',
          notes: 'Joined in 2018'
        },
        { 
          id: 10, 
          name: 'Fatima Patel', 
          position: 'Customer Support Lead', 
          department: 'Support', 
          salary: 58000 * USD_TO_ZAR, 
          email: 'fatima.patel@moderntech.com', 
          startDate: '2016-04-30',
          notes: 'Joined in 2016'
        }
      ],
      showForm: false,
      viewingEmployee: null,
      searchQuery: '',
      sortField: 'name',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 5,
      formData: {
        id: null,
        name: '',
        position: '',
        department: 'Development',
        salary: 0,
        email: '',
        startDate: '',
        notes: ''
      },
      isEditing: false
    }
  },
  computed: {
    filteredEmployees() {
      let filtered = this.employees
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(emp => 
          emp.name.toLowerCase().includes(query) ||
          emp.position.toLowerCase().includes(query) ||
          emp.department.toLowerCase().includes(query) ||
          (emp.email && emp.email.toLowerCase().includes(query))
        )
      }
      
      // Sorting
      filtered = filtered.sort((a, b) => {
        let aVal = a[this.sortField]
        let bVal = b[this.sortField]
        
        if (typeof aVal === 'string') {
          aVal = aVal.toLowerCase()
          bVal = bVal.toLowerCase()
        }
        
        if (this.sortDirection === 'asc') {
          return aVal > bVal ? 1 : -1
        } else {
          return aVal < bVal ? 1 : -1
        }
      })
      
      return filtered
    },
    paginatedEmployees() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredEmployees.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredEmployees.length / this.itemsPerPage)
    },
    pages() {
      const pages = []
      const maxPages = 5
      let start = Math.max(1, this.currentPage - Math.floor(maxPages / 2))
      let end = Math.min(this.totalPages, start + maxPages - 1)
      
      if (end - start + 1 < maxPages) {
        start = Math.max(1, end - maxPages + 1)
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      
      return pages
    },
    avgSalary() {
      if (this.employees.length === 0) return 0
      const total = this.employees.reduce((sum, emp) => sum + emp.salary, 0)
      return Math.round(total / this.employees.length)
    },
    deptCount() {
      const departments = new Set(this.employees.map(emp => emp.department))
      return departments.size
    },
    newThisMonth() {
      // Mock data: return random number between 0-3
      return Math.floor(Math.random() * 4)
    }
  },
  methods: {
    formatRand(amount) {
      return amount.toLocaleString('en-ZA', { minimumFractionDigits: 0 })
    },
    formatCurrency(amount) {
      return amount.toLocaleString('en-ZA', { minimumFractionDigits: 0 })
    },
    getDeptStyle(department) {
      const colors = {
        'Development': { bg: 'rgba(177, 156, 217, 0.1)', color: '#b19cd9' },
        'HR': { bg: 'rgba(76, 175, 80, 0.1)', color: '#4caf50' },
        'Sales': { bg: 'rgba(33, 150, 243, 0.1)', color: '#2196f3' },
        'Marketing': { bg: 'rgba(255, 193, 7, 0.1)', color: '#ffc107' },
        'Finance': { bg: 'rgba(244, 67, 54, 0.1)', color: '#f44336' },
        'Support': { bg: 'rgba(156, 39, 176, 0.1)', color: '#9c27b0' },
        'IT': { bg: 'rgba(0, 150, 136, 0.1)', color: '#009688' },
        'QA': { bg: 'rgba(255, 87, 34, 0.1)', color: '#ff5722' },
        'Design': { bg: 'rgba(103, 58, 183, 0.1)', color: '#673ab7' }
      }
      
      const style = colors[department] || { bg: 'rgba(158, 158, 158, 0.1)', color: '#9e9e9e' }
      return {
        backgroundColor: style.bg,
        color: style.color,
        borderColor: style.color
      }
    },
    sortBy(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortField = field
        this.sortDirection = 'asc'
      }
      this.currentPage = 1
    },
    sortIcon(field) {
      if (this.sortField !== field) return 'bi-chevron-expand'
      return this.sortDirection === 'asc' ? 'bi-chevron-up' : 'bi-chevron-down'
    },
    filterEmployees() {
      this.currentPage = 1
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--
        this.scrollToTop()
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++
        this.scrollToTop()
      }
    },
    goToPage(page) {
      this.currentPage = page
      this.scrollToTop()
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    editEmployee(employee) {
      this.formData = { ...employee }
      this.isEditing = true
      this.showForm = true
    },
    viewEmployee(employee) {
      this.viewingEmployee = { ...employee }
    },
    closeViewModal() {
      this.viewingEmployee = null
    },
    saveEmployee() {
      if (this.isEditing) {
        const index = this.employees.findIndex(emp => emp.id === this.formData.id)
        if (index !== -1) {
          this.employees[index] = { ...this.formData }
        }
      } else {
        const newId = Math.max(...this.employees.map(emp => emp.id)) + 1
        this.employees.push({
          id: newId,
          ...this.formData
        })
      }
      
      this.closeModal()
      this.showSuccessMessage()
    },
    deleteEmployee(id) {
      if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        this.employees = this.employees.filter(emp => emp.id !== id)
        // Reset to first page if current page becomes empty
        if (this.paginatedEmployees.length === 0 && this.currentPage > 1) {
          this.currentPage = this.totalPages
        }
      }
    },
    closeModal() {
      this.showForm = false
      this.isEditing = false
      this.resetForm()
    },
    resetForm() {
      this.formData = {
        id: null,
        name: '',
        position: '',
        department: 'Development',
        salary: 0,
        email: '',
        startDate: '',
        notes: ''
      }
    },
    showSuccessMessage() {
      const message = this.isEditing ? 'Employee updated successfully!' : 'Employee added successfully!'
      // In a real app, you might use a toast notification
      alert(message)
    }
  }
}
</script>

<style scoped>
/* ALL STYLING REMAINS EXACTLY THE SAME */
.employee-management {
  padding: 0;
  animation: fadeIn 0.5s ease-out;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, rgba(177, 156, 217, 0.1) 0%, rgba(26, 26, 46, 0.3) 100%);
  border-radius: 16px;
  padding: 1.5rem 2rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid rgba(177, 156, 217, 0.1);
}

.header-content h1 {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.header-content p {
  color: var(--text-secondary);
  font-size: 0.95rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-add {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: var(--transition);
}

.btn-add:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(177, 156, 217, 0.3);
}

.search-box {
  position: relative;
  width: 300px;
}

.search-box i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.search-box input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  background: var(--light-dark);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: var(--text);
  font-size: 0.95rem;
  transition: var(--transition);
}

.search-box input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
}

/* Stats Cards */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid var(--border);
  transition: var(--transition);
}

.stat-card:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
}

.stat-content h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.stat-content p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.stat-card i {
  font-size: 2rem;
  color: var(--primary);
  opacity: 0.3;
}

/* Desktop Table */
.desktop-table {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid var(--border);
  margin-bottom: 2rem;
}

.employee-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 1.5rem;
}

.employee-table th {
  background: var(--secondary);
  color: var(--text-secondary);
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid var(--border);
  cursor: pointer;
  user-select: none;
  transition: var(--transition);
}

.employee-table th:hover {
  color: var(--text);
  background: rgba(177, 156, 217, 0.05);
}

.employee-table th i {
  margin-left: 0.5rem;
  font-size: 0.8rem;
}

.employee-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
}

.employee-table tr:hover td {
  background: rgba(177, 156, 217, 0.03);
}

.employee-table tr:last-child td {
  border-bottom: none;
}

.employee-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  flex-shrink: 0;
}

.employee-info strong {
  display: block;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.employee-info small {
  color: var(--text-secondary);
  font-size: 0.8rem;
}

.dept-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid;
}





.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}

.btn-action.edit {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-action.edit:hover {
  background: rgba(33, 150, 243, 0.2);
}

.btn-action.view {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-action.view:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-action.delete {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

.btn-action.delete:hover {
  background: rgba(244, 67, 54, 0.2);
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border);
}

.page-btn {
  width: 40px;
  height: 40px;
  border: 1px solid var(--border);
  background: var(--secondary);
  color: var(--text);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  font-weight: 500;
}

.page-btn:hover:not(:disabled) {
  border-color: var(--primary);
  color: var(--primary);
}

.page-btn.active {
  background: var(--primary);
  color: var(--secondary);
  border-color: var(--primary);
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Mobile Cards */
.mobile-cards {
  display: none;
}

.employee-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  border: 1px solid var(--border);
  transition: var(--transition);
}

.employee-card:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.employee-avatar {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.employee-details h3 {
  font-size: 1.1rem;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.employee-details p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.employee-salary {
  margin-left: auto;
  text-align: right;
}

.employee-salary .salary {
  display: block;
  font-weight: 700;
  color: var(--text);
  font-size: 1.1rem;
  margin-bottom: 0.25rem;
}

.employee-salary .salary-label {
  color: var(--text-secondary);
  font-size: 0.8rem;
}

.card-body {
  margin-bottom: 1rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(74, 74, 109, 0.3);
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.info-value {
  color: var(--text);
  font-weight: 500;
  text-align: right;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-mobile {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  cursor: pointer;
  font-weight: 500;
  transition: var(--transition);
}

.btn-mobile.edit {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-mobile.edit:hover {
  background: rgba(33, 150, 243, 0.2);
}

.btn-mobile.view {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-mobile.view:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-mobile.delete {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

.btn-mobile.delete:hover {
  background: rgba(244, 67, 54, 0.2);
}

.mobile-pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  margin-top: 1rem;
  background: var(--light-dark);
  border-radius: 16px;
  border: 1px solid var(--border);
}

.mobile-page-btn {
  width: 40px;
  height: 40px;
  border: 1px solid var(--border);
  background: var(--secondary);
  color: var(--text);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}

.mobile-page-btn:hover:not(:disabled) {
  border-color: var(--primary);
  color: var(--primary);
}

.mobile-page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: var(--text);
  font-weight: 500;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 15, 26, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

.modal-content {
  background: var(--light-dark);
  border-radius: 16px;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease-out;
}

.view-modal {
  max-width: 500px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
}

.modal-header h2 {
  font-size: 1.5rem;
  color: var(--text);
  font-weight: 600;
}

.close-btn {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}

.close-btn:hover {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

.employee-form {
  padding: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  flex: 1;
}

.form-group.full-width {
  flex: 0 0 100%;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  background: var(--secondary);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-size: 0.95rem;
  transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
}

.salary-input {
  position: relative;
}

.salary-input .currency {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
  font-weight: 500;
}

.salary-input input {
  padding-left: 2.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border);
  margin-top: 1.5rem;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(177, 156, 217, 0.3);
}

.btn-secondary {
  background: var(--secondary);
  color: var(--text);
  border: 1px solid var(--border);
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
}

.btn-secondary:hover {
  background: rgba(177, 156, 217, 0.05);
  border-color: var(--primary);
  color: var(--primary);
}

/* Employee Profile */
.employee-profile {
  padding: 1.5rem;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 2rem;
  flex-shrink: 0;
}

.profile-info h3 {
  font-size: 1.5rem;
  color: var(--text);
  margin-bottom: 0.5rem;
}

.profile-info p {
  color: var(--text-secondary);
  margin-bottom: 0.75rem;
}

.profile-details {
  background: var(--secondary);
  border-radius: 12px;
  padding: 1.5rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(74, 74, 109, 0.3);
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-item.full-width {
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5rem;
}

.detail-label {
  color: var(--text-secondary);
  font-weight: 500;
}

.detail-value {
  color: var(--text);
  text-align: right;
}

.detail-item.full-width .detail-value {
  text-align: left;
  line-height: 1.6;
}

.modal-actions {
  padding: 1.5rem;
  border-top: 1px solid var(--border);
}

/* Responsive Design */
@media (max-width: 992px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .header-actions {
    width: 100%;
    flex-direction: column;
  }
  
  .search-box {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .desktop-table {
    display: none;
  }
  
  .mobile-cards {
    display: block;
  }
  
  .stats-cards {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .form-row {
    flex-direction: column;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .employee-card {
    padding: 1rem;
  }
  
  .card-header {
    flex-wrap: wrap;
  }
  
  .employee-salary {
    width: 100%;
    text-align: left;
    margin-top: 0.5rem;
  }
  
  .card-actions {
    flex-direction: column;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
}
</style>