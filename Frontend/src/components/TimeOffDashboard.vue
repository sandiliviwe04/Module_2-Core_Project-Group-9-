<template>
  <div class="timeoff-management">
    <div class="page-header">
      <div class="header-content">
        <h1>Time Off Management</h1>
        <p>Manage employee leave requests, track attendance, and approve time off</p>
      </div>
      <div class="header-actions">
        <button class="btn-primary" @click="showNewRequestModal = true">
          <i class="bi bi-plus-circle"></i>
          New Request
        </button>
        <div class="filter-controls">
          <select v-model="filterStatus" class="status-filter">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="denied">Denied</option>
          </select>
          <select v-model="filterType" class="type-filter">
            <option value="all">All Types</option>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Vacation">Vacation</option>
            <option value="Personal">Personal</option>
            <option value="Family Responsibility">Family</option>
            <option value="Medical Appointment">Medical</option>
            <option value="Bereavement">Bereavement</option>
            <option value="Childcare">Childcare</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ pendingRequests }}</h3>
          <p>Pending Requests</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-clock-history"></i>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ approvedThisMonth }}</h3>
          <p>Approved This Month</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-check-circle"></i>
          <span>15%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ avgProcessingTime }}h</h3>
          <p>Avg. Processing Time</p>
        </div>
        <div class="stat-trend trend-down">
          <i class="bi bi-arrow-down-right"></i>
          <span>8%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ leaveBalance }}</h3>
          <p>Avg. Leave Balance (days)</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-calendar-check"></i>
        </div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="table-responsive desktop-table">
      <table class="timeoff-table">
        <thead>
          <tr>
            <th @click="sortBy('employeeName')">
              Employee
              <i class="bi" :class="sortIcon('employeeName')"></i>
            </th>
            <th @click="sortBy('type')">
              Type
              <i class="bi" :class="sortIcon('type')"></i>
            </th>
            <th @click="sortBy('startDate')">
              Dates
              <i class="bi" :class="sortIcon('startDate')"></i>
            </th>
            <th @click="sortBy('duration')">
              Duration
              <i class="bi" :class="sortIcon('duration')"></i>
            </th>
            <th @click="sortBy('status')">
              Status
              <i class="bi" :class="sortIcon('status')"></i>
            </th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="request in paginatedRequests" :key="request.id">
            <td>
              <div class="employee-info">
                <div class="avatar">{{ getInitials(request.employeeName) }}</div>
                <div>
                  <strong>{{ request.employeeName }}</strong>
                  <small>{{ request.department || 'Department' }}</small>
                </div>
              </div>
            </td>
            <td>
              <span class="request-type" :class="getRequestTypeClass(request.type)">
                {{ request.type }}
              </span>
            </td>
            <td>
              <div class="date-range">
                <span class="date-start">{{ formatDate(request.startDate) }}</span>
                <i class="bi bi-arrow-right"></i>
                <span class="date-end">{{ formatDate(request.endDate) }}</span>
              </div>
            </td>
            <td>
              <div class="duration-display">
                <span class="duration-value">{{ request.duration }}</span>
                <span class="duration-label">days</span>
              </div>
            </td>
            <td>
              <span class="status-badge" :class="request.status">
                {{ request.status }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button 
                  v-if="request.status === 'pending'" 
                  class="btn-action approve" 
                  @click="approveRequest(request)"
                  title="Approve"
                >
                  <i class="bi bi-check-lg"></i>
                </button>
                <button 
                  v-if="request.status === 'pending'" 
                  class="btn-action deny" 
                  @click="denyRequest(request)"
                  title="Deny"
                >
                  <i class="bi bi-x-lg"></i>
                </button>
                <button class="btn-action view" @click="viewRequest(request)" title="View">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn-action edit" @click="editRequest(request)" title="Edit">
                  <i class="bi bi-pencil"></i>
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
      <div v-for="request in paginatedRequests" :key="request.id" class="request-card">
        <div class="card-header">
          <div class="employee-avatar">{{ getInitials(request.employeeName) }}</div>
          <div class="employee-details">
            <h3>{{ request.employeeName }}</h3>
            <p>{{ request.type }}</p>
          </div>
          <div class="request-status">
            <span class="status-badge" :class="request.status">
              {{ request.status }}
            </span>
          </div>
        </div>
        
        <div class="card-body">
          <div class="info-row">
            <span class="info-label">Dates:</span>
            <span class="info-value">
              {{ formatDate(request.startDate) }} - {{ formatDate(request.endDate) }}
            </span>
          </div>
          <div class="info-row">
            <span class="info-label">Duration:</span>
            <span class="info-value">{{ request.duration }} days</span>
          </div>
          <div class="info-row">
            <span class="info-label">Reason:</span>
            <span class="info-value reason-text">{{ request.reason || 'No reason provided' }}</span>
          </div>
        </div>
        
        <div class="card-actions">
          <button v-if="request.status === 'pending'" class="btn-mobile approve" @click="approveRequest(request)">
            <i class="bi bi-check-lg"></i>
            Approve
          </button>
          <button v-if="request.status === 'pending'" class="btn-mobile deny" @click="denyRequest(request)">
            <i class="bi bi-x-lg"></i>
            Deny
          </button>
          <button class="btn-mobile view" @click="viewRequest(request)">
            <i class="bi bi-eye"></i>
            Details
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

    <!-- New Request Modal -->
    <div v-if="showNewRequestModal" class="modal-overlay" @click.self="closeNewRequestModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>New Leave Request</h2>
          <button @click="closeNewRequestModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <form @submit.prevent="submitNewRequest" class="request-form">
          <div class="form-row">
            <div class="form-group">
              <label for="employee">Employee *</label>
              <select v-model="newRequest.employeeId" id="employee" required>
                <option value="">Select Employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.name }} ({{ emp.department }})
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="requestType">Leave Type *</label>
              <select v-model="newRequest.type" id="requestType" required>
                <option value="">Select Type</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Vacation">Vacation</option>
                <option value="Personal">Personal</option>
                <option value="Family Responsibility">Family Responsibility</option>
                <option value="Medical Appointment">Medical Appointment</option>
                <option value="Bereavement">Bereavement</option>
                <option value="Childcare">Childcare</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="startDate">Start Date *</label>
              <input 
                v-model="newRequest.startDate" 
                id="startDate"
                type="date" 
                required
                :min="today"
              >
            </div>
            <div class="form-group">
              <label for="endDate">End Date *</label>
              <input 
                v-model="newRequest.endDate" 
                id="endDate"
                type="date" 
                required
                :min="newRequest.startDate || today"
              >
            </div>
          </div>
          
          <div class="form-group full-width">
            <label for="reason">Reason for Leave *</label>
            <textarea 
              v-model="newRequest.reason" 
              id="reason"
              placeholder="Please provide details about your leave request..."
              rows="3"
              required
            ></textarea>
          </div>
          
          <div class="form-group full-width">
            <label for="notes">Additional Notes</label>
            <textarea 
              v-model="newRequest.notes" 
              id="notes"
              placeholder="Any additional information..."
              rows="2"
            ></textarea>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeNewRequestModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary">
              <i class="bi bi-send"></i>
              Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Request Modal -->
    <div v-if="viewingRequest" class="modal-overlay" @click.self="closeViewModal">
      <div class="modal-content view-modal">
        <div class="modal-header">
          <h2>Leave Request Details</h2>
          <button @click="closeViewModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <div class="request-details">
          <div class="detail-header">
            <div class="employee-avatar-large">{{ getInitials(viewingRequest.employeeName) }}</div>
            <div class="employee-info-large">
              <h3>{{ viewingRequest.employeeName }}</h3>
              <p>{{ viewingRequest.department || 'Department' }}</p>
              <span class="request-type-large" :class="getRequestTypeClass(viewingRequest.type)">
                {{ viewingRequest.type }}
              </span>
            </div>
            <div class="status-display">
              <span class="status-badge-large" :class="viewingRequest.status">
                {{ viewingRequest.status }}
              </span>
              <p class="submitted-date">Submitted: {{ formatDate(viewingRequest.submittedDate) }}</p>
            </div>
          </div>
          
          <div class="detail-content">
            <div class="detail-section">
              <h4>Leave Period</h4>
              <div class="period-info">
                <div class="period-item">
                  <span class="period-label">Start Date:</span>
                  <span class="period-value">{{ formatDate(viewingRequest.startDate) }}</span>
                </div>
                <div class="period-item">
                  <span class="period-label">End Date:</span>
                  <span class="period-value">{{ formatDate(viewingRequest.endDate) }}</span>
                </div>
                <div class="period-item">
                  <span class="period-label">Duration:</span>
                  <span class="period-value">{{ viewingRequest.duration }} working days</span>
                </div>
              </div>
            </div>
            
            <div class="detail-section">
              <h4>Request Details</h4>
              <div class="request-info">
                <div class="info-item">
                  <span class="info-label">Reason:</span>
                  <p class="info-value">{{ viewingRequest.reason || 'No reason provided' }}</p>
                </div>
                <div v-if="viewingRequest.notes" class="info-item">
                  <span class="info-label">Additional Notes:</span>
                  <p class="info-value">{{ viewingRequest.notes }}</p>
                </div>
                <div v-if="viewingRequest.approverNotes" class="info-item">
                  <span class="info-label">Approver Notes:</span>
                  <p class="info-value">{{ viewingRequest.approverNotes }}</p>
                </div>
              </div>
            </div>
            
            <div v-if="viewingRequest.status === 'pending'" class="action-section">
              <h4>Approve/Deny Request</h4>
              <div class="action-notes">
                <textarea 
                  v-model="approverNotes" 
                  placeholder="Add notes for the employee (optional)..."
                  rows="2"
                ></textarea>
              </div>
              <div class="action-buttons-large">
                <button @click="denyRequest(viewingRequest)" class="btn-denial">
                  <i class="bi bi-x-lg"></i>
                  Deny Request
                </button>
                <button @click="approveRequest(viewingRequest)" class="btn-approval">
                  <i class="bi bi-check-lg"></i>
                  Approve Request
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TimeOffDashboard',
  data() {
    const today = new Date()
    const tomorrow = new Date(today)
    tomorrow.setDate(tomorrow.getDate() + 1)
    
    return {
      // Load leave requests from attendance.json data
      leaveRequests: [
        { 
          id: 1, 
          employeeId: 1, 
          employeeName: 'Sibongile Nkosi', 
          department: 'Development',
          type: 'Sick Leave', 
          startDate: '2025-07-22', 
          endDate: '2025-07-22', 
          duration: 1,
          reason: 'Sick Leave',
          status: 'approved',
          submittedDate: '2025-07-21',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 2, 
          employeeId: 2, 
          employeeName: 'Lungile Moyo', 
          department: 'HR',
          type: 'Family Responsibility', 
          startDate: '2025-07-15', 
          endDate: '2025-07-15', 
          duration: 1,
          reason: 'Family Responsibility',
          status: 'denied',
          submittedDate: '2025-07-14',
          notes: '',
          approverNotes: 'Denied'
        },
        { 
          id: 3, 
          employeeId: 3, 
          employeeName: 'Thabo Molefe', 
          department: 'QA',
          type: 'Medical Appointment', 
          startDate: '2025-07-10', 
          endDate: '2025-07-10', 
          duration: 1,
          reason: 'Medical Appointment',
          status: 'approved',
          submittedDate: '2025-07-09',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 4, 
          employeeId: 4, 
          employeeName: 'Keshav Naidoo', 
          department: 'Sales',
          type: 'Bereavement', 
          startDate: '2025-07-20', 
          endDate: '2025-07-20', 
          duration: 1,
          reason: 'Bereavement',
          status: 'approved',
          submittedDate: '2025-07-19',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 5, 
          employeeId: 5, 
          employeeName: 'Zanele Khumalo', 
          department: 'Marketing',
          type: 'Childcare', 
          startDate: '2024-12-01', 
          endDate: '2024-12-01', 
          duration: 1,
          reason: 'Childcare',
          status: 'pending',
          submittedDate: '2024-11-30',
          notes: '',
          approverNotes: ''
        },
        { 
          id: 6, 
          employeeId: 6, 
          employeeName: 'Sipho Zulu', 
          department: 'Design',
          type: 'Sick Leave', 
          startDate: '2025-07-18', 
          endDate: '2025-07-18', 
          duration: 1,
          reason: 'Sick Leave',
          status: 'approved',
          submittedDate: '2025-07-17',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 7, 
          employeeId: 7, 
          employeeName: 'Naledi Moeketsi', 
          department: 'IT',
          type: 'Vacation', 
          startDate: '2025-07-22', 
          endDate: '2025-07-22', 
          duration: 1,
          reason: 'Vacation',
          status: 'pending',
          submittedDate: '2025-07-21',
          notes: '',
          approverNotes: ''
        },
        { 
          id: 8, 
          employeeId: 8, 
          employeeName: 'Farai Gumbo', 
          department: 'Marketing',
          type: 'Medical Appointment', 
          startDate: '2024-12-02', 
          endDate: '2024-12-02', 
          duration: 1,
          reason: 'Medical Appointment',
          status: 'approved',
          submittedDate: '2024-12-01',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 9, 
          employeeId: 9, 
          employeeName: 'Karabo Dlamini', 
          department: 'Finance',
          type: 'Childcare', 
          startDate: '2025-07-19', 
          endDate: '2025-07-19', 
          duration: 1,
          reason: 'Childcare',
          status: 'denied',
          submittedDate: '2025-07-18',
          notes: '',
          approverNotes: 'Denied'
        },
        { 
          id: 10, 
          employeeId: 10, 
          employeeName: 'Fatima Patel', 
          department: 'Support',
          type: 'Vacation', 
          startDate: '2024-12-03', 
          endDate: '2024-12-03', 
          duration: 1,
          reason: 'Vacation',
          status: 'pending',
          submittedDate: '2024-12-02',
          notes: '',
          approverNotes: ''
        },
        // Additional requests from attendance.json
        { 
          id: 11, 
          employeeId: 1, 
          employeeName: 'Sibongile Nkosi', 
          department: 'Development',
          type: 'Personal', 
          startDate: '2024-12-01', 
          endDate: '2024-12-01', 
          duration: 1,
          reason: 'Personal',
          status: 'pending',
          submittedDate: '2024-11-30',
          notes: '',
          approverNotes: ''
        },
        { 
          id: 12, 
          employeeId: 2, 
          employeeName: 'Lungile Moyo', 
          department: 'HR',
          type: 'Vacation', 
          startDate: '2024-12-02', 
          endDate: '2024-12-02', 
          duration: 1,
          reason: 'Vacation',
          status: 'approved',
          submittedDate: '2024-12-01',
          notes: '',
          approverNotes: 'Approved'
        },
        { 
          id: 13, 
          employeeId: 3, 
          employeeName: 'Thabo Molefe', 
          department: 'QA',
          type: 'Personal', 
          startDate: '2024-12-05', 
          endDate: '2024-12-05', 
          duration: 1,
          reason: 'Personal',
          status: 'pending',
          submittedDate: '2024-12-04',
          notes: '',
          approverNotes: ''
        },
        { 
          id: 14, 
          employeeId: 10, 
          employeeName: 'Fatima Patel', 
          department: 'Support',
          type: 'Vacation', 
          startDate: '2024-12-03', 
          endDate: '2024-12-03', 
          duration: 1,
          reason: 'Vacation',
          status: 'pending',
          submittedDate: '2024-12-02',
          notes: '',
          approverNotes: ''
        }
      ],
      employees: [
        { id: 1, name: 'Sibongile Nkosi', department: 'Development' },
        { id: 2, name: 'Lungile Moyo', department: 'HR' },
        { id: 3, name: 'Thabo Molefe', department: 'QA' },
        { id: 4, name: 'Keshav Naidoo', department: 'Sales' },
        { id: 5, name: 'Zanele Khumalo', department: 'Marketing' },
        { id: 6, name: 'Sipho Zulu', department: 'Design' },
        { id: 7, name: 'Naledi Moeketsi', department: 'IT' },
        { id: 8, name: 'Farai Gumbo', department: 'Marketing' },
        { id: 9, name: 'Karabo Dlamini', department: 'Finance' },
        { id: 10, name: 'Fatima Patel', department: 'Support' }
      ],
      showNewRequestModal: false,
      viewingRequest: null,
      filterStatus: 'all',
      filterType: 'all',
      sortField: 'submittedDate',
      sortDirection: 'desc',
      currentPage: 1,
      itemsPerPage: 5,
      newRequest: {
        employeeId: '',
        type: '',
        startDate: '',
        endDate: '',
        reason: '',
        notes: ''
      },
      approverNotes: '',
      today: today.toISOString().split('T')[0]
    }
  },
  computed: {
    filteredRequests() {
      let filtered = this.leaveRequests
      
      // Status filter
      if (this.filterStatus !== 'all') {
        filtered = filtered.filter(request => request.status === this.filterStatus)
      }
      
      // Type filter
      if (this.filterType !== 'all') {
        filtered = filtered.filter(request => request.type === this.filterType)
      }
      
      // Sorting
      filtered = filtered.sort((a, b) => {
        let aVal = a[this.sortField]
        let bVal = b[this.sortField]
        
        if (this.sortField.includes('Date')) {
          aVal = new Date(aVal)
          bVal = new Date(bVal)
        }
        
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
    paginatedRequests() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredRequests.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredRequests.length / this.itemsPerPage)
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
    pendingRequests() {
      return this.leaveRequests.filter(r => r.status === 'pending').length
    },
    approvedThisMonth() {
      // Return total approved requests (reactive when status changes)
      return this.leaveRequests.filter(r => r.status === 'approved').length
    },
    avgProcessingTime() {
      const approvedRequests = this.leaveRequests.filter(r => r.status === 'approved')
      if (approvedRequests.length === 0) return 0
      
      const totalHours = approvedRequests.reduce((sum, request) => {
        const submitted = new Date(request.submittedDate)
        // Assume approved 1-2 days after submission for demo
        return sum + (24 + Math.random() * 24)
      }, 0)
      
      return Math.round(totalHours / approvedRequests.length)
    },
    leaveBalance() {
      return 15 // Mock average leave balance
    },
  },
  methods: {
    getInitials(name) {
      return name.split(' ').map(n => n[0]).join('').toUpperCase()
    },
    getRequestTypeClass(type) {
      const classMap = {
        'Sick Leave': 'sick',
        'Vacation': 'vacation',
        'Personal': 'personal',
        'Family Responsibility': 'family',
        'Medical Appointment': 'medical',
        'Bereavement': 'bereavement',
        'Childcare': 'childcare'
      }
      return classMap[type] || 'default'
    },
    formatDate(dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-ZA', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
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
    approveRequest(request) {
      if (confirm(`Approve ${request.employeeName}'s ${request.type} request?`)) {
        request.status = 'approved'
        request.approverNotes = this.approverNotes || 'Approved by HR'
        this.approverNotes = ''
        alert('Request approved successfully!')
        
        if (this.viewingRequest) {
          this.viewingRequest = { ...request }
        }
      }
    },
    denyRequest(request) {
      if (confirm(`Deny ${request.employeeName}'s ${request.type} request?`)) {
        request.status = 'denied'
        request.approverNotes = this.approverNotes || 'Denied by HR'
        this.approverNotes = ''
        alert('Request denied.')
        
        if (this.viewingRequest) {
          this.viewingRequest = { ...request }
        }
      }
    },
    viewRequest(request) {
      this.viewingRequest = { ...request }
    },
    editRequest(request) {
      this.newRequest = { ...request }
      this.showNewRequestModal = true
    },
    closeViewModal() {
      this.viewingRequest = null
      this.approverNotes = ''
    },
    submitNewRequest() {
      const employee = this.employees.find(e => e.id === parseInt(this.newRequest.employeeId))
      if (!employee) {
        alert('Please select an employee')
        return
      }
      
      // Calculate duration
      const start = new Date(this.newRequest.startDate)
      const end = new Date(this.newRequest.endDate)
      const duration = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1
      
      const newId = Math.max(...this.leaveRequests.map(r => r.id)) + 1
      
      this.leaveRequests.push({
        id: newId,
        employeeId: parseInt(this.newRequest.employeeId),
        employeeName: employee.name,
        department: employee.department,
        type: this.newRequest.type,
        startDate: this.newRequest.startDate,
        endDate: this.newRequest.endDate,
        duration: duration,
        reason: this.newRequest.reason,
        status: 'pending',
        submittedDate: new Date().toISOString().split('T')[0],
        notes: this.newRequest.notes,
        approverNotes: ''
      })
      
      this.closeNewRequestModal()
      alert('Leave request submitted successfully!')
    },
    closeNewRequestModal() {
      this.showNewRequestModal = false
      this.resetNewRequest()
    },
    resetNewRequest() {
      this.newRequest = {
        employeeId: '',
        type: '',
        startDate: '',
        endDate: '',
        reason: '',
        notes: ''
      }
    },
    prevMonth() {
      this.calendarDate = new Date(this.calendarDate.getFullYear(), this.calendarDate.getMonth() - 1, 1)
    },
    nextMonth() {
      this.calendarDate = new Date(this.calendarDate.getFullYear(), this.calendarDate.getMonth() + 1, 1)
    },
    getEventsForDate(date) {
      const events = []
      const dateString = date.toISOString().split('T')[0]
      
      this.leaveRequests.forEach(request => {
        const start = new Date(request.startDate)
        const end = new Date(request.endDate)
        
        if (date >= start && date <= end) {
          events.push({
            id: request.id,
            name: request.employeeName.split(' ')[0],
            type: this.getRequestTypeClass(request.type)
          })
        }
      })
      
      return events.slice(0, 2) // Limit to 2 events per day
    }
  }
}
</script>

<style scoped>
/* ALL STYLING REMAINS EXACTLY THE SAME */
.timeoff-management {
  padding: 0;
  animation: fadeIn 0.5s ease-out;
  max-width: 100%;
  overflow-x: hidden;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, rgba(177, 156, 217, 0.1) 0%, rgba(26, 26, 46, 0.3) 100%);
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  border: 1px solid rgba(177, 156, 217, 0.1);
  max-width: 100%;
  box-sizing: border-box;
}

.header-content {
  text-align: center;
}

.header-content h1 {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.2;
}

.header-content p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  line-height: 1.4;
}

.header-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
  width: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: var(--transition);
  width: 100%;
  max-width: 300px;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(177, 156, 217, 0.3);
}

.filter-controls {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
  width: 100%;
}

.status-filter,
.type-filter {
  background: var(--light-dark);
  color: var(--text);
  border: 1px solid var(--border);
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: var(--transition);
  min-width: 120px;
  flex: 1;
  max-width: 160px;
}

.status-filter:focus,
.type-filter:focus {
  outline: none;
  border-color: var(--primary);
}

/* Stats Cards */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
  max-width: 100%;
}

.stat-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid var(--border);
  transition: var(--transition);
  min-height: 90px;
  box-sizing: border-box;
}

.stat-card:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
}

.stat-content {
  flex: 1;
}

.stat-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 0.25rem;
  line-height: 1;
}

.stat-content p {
  color: var(--text-secondary);
  font-size: 0.8rem;
  line-height: 1.2;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.5rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.stat-trend i {
  font-size: 1.1rem;
}

.trend-up {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.trend-down {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

/* Desktop Table */
.desktop-table {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1rem;
  border: 1px solid var(--border);
  margin-bottom: 2rem;
  width: 100%;
  overflow: hidden;
  box-sizing: border-box;
}

.timeoff-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 1.5rem;
  table-layout: fixed;
}

.timeoff-table th {
  background: var(--secondary);
  color: var(--text-secondary);
  font-weight: 600;
  padding: 0.75rem 0.5rem;
  text-align: left;
  border-bottom: 2px solid var(--border);
  cursor: pointer;
  user-select: none;
  transition: var(--transition);
  font-size: 0.85rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.timeoff-table th:hover {
  color: var(--text);
  background: rgba(177, 156, 217, 0.05);
}

.timeoff-table th i {
  margin-left: 0.25rem;
  font-size: 0.7rem;
}

.timeoff-table td {
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
  font-size: 0.85rem;
  overflow: hidden;
  text-overflow: ellipsis;
}

.timeoff-table tr:hover td {
  background: rgba(177, 156, 217, 0.03);
}

.timeoff-table tr:last-child td {
  border-bottom: none;
}

.employee-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 0;
}

.avatar {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}

.employee-info strong {
  display: block;
  color: var(--text);
  margin-bottom: 0.1rem;
  font-size: 0.85rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.employee-info small {
  color: var(--text-secondary);
  font-size: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.request-type {
  display: inline-block;
  padding: 0.2rem 0.5rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  border: 1px solid;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.request-type.sick {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
  border-color: var(--info);
}

.request-type.vacation {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border-color: var(--success);
}

.request-type.personal {
  background: rgba(255, 193, 7, 0.1);
  color: var(--warning);
  border-color: var(--warning);
}

.request-type.family {
  background: rgba(156, 39, 176, 0.1);
  color: #9c27b0;
  border-color: #9c27b0;
}

.request-type.medical {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border-color: var(--danger);
}

.request-type.bereavement {
  background: rgba(96, 125, 139, 0.1);
  color: #607d8b;
  border-color: #607d8b;
}

.request-type.childcare {
  background: rgba(233, 30, 99, 0.1);
  color: #e91e63;
  border-color: #e91e63;
}

.date-range {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: var(--text);
  font-weight: 500;
  font-size: 0.85rem;
  flex-wrap: wrap;
}

.date-range i {
  color: var(--text-secondary);
  font-size: 0.7rem;
  flex-shrink: 0;
}

.date-start,
.date-end {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.duration-display {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.duration-value {
  font-weight: 700;
  color: var(--text);
  font-size: 1rem;
}

.duration-label {
  color: var(--text-secondary);
  font-size: 0.8rem;
}

.status-badge {
  display: inline-block;
  padding: 0.2rem 0.6rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
  white-space: nowrap;
}

.status-badge.pending {
  background: rgba(255, 193, 7, 0.1);
  color: var(--warning);
  border: 1px solid var(--warning);
}

.status-badge.approved {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid var(--success);
}

.status-badge.denied {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid var(--danger);
}

.action-buttons {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  flex-shrink: 0;
}

.btn-action.approve {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-action.approve:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-action.deny {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

.btn-action.deny:hover {
  background: rgba(244, 67, 54, 0.2);
}

.btn-action.view {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-action.view:hover {
  background: rgba(33, 150, 243, 0.2);
}

.btn-action.edit {
  background: rgba(177, 156, 217, 0.1);
  color: var(--primary);
}

.btn-action.edit:hover {
  background: rgba(177, 156, 217, 0.2);
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.25rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border);
  flex-wrap: wrap;
}

.page-btn {
  width: 36px;
  height: 36px;
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
  font-size: 0.85rem;
  flex-shrink: 0;
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

.request-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1rem;
  margin-bottom: 1rem;
  border: 1px solid var(--border);
  transition: var(--transition);
  max-width: 100%;
  box-sizing: border-box;
}

.request-card:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.employee-avatar {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
  flex-shrink: 0;
}

.employee-details {
  flex: 1;
  min-width: 0;
}

.employee-details h3 {
  font-size: 1rem;
  color: var(--text);
  margin-bottom: 0.2rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.employee-details p {
  color: var(--text-secondary);
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.request-status {
  flex-shrink: 0;
}

.card-body {
  margin-bottom: 1rem;
}

.info-row {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(74, 74, 109, 0.3);
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 500;
}

.info-value {
  color: var(--text);
  font-weight: 500;
  line-height: 1.4;
  word-break: break-word;
}

.reason-text {
  font-style: italic;
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-mobile {
  flex: 1;
  padding: 0.6rem;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  cursor: pointer;
  font-weight: 500;
  transition: var(--transition);
  font-size: 0.85rem;
  min-width: 90px;
}

.btn-mobile.approve {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-mobile.approve:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-mobile.deny {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

.btn-mobile.deny:hover {
  background: rgba(244, 67, 54, 0.2);
}

.btn-mobile.view {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-mobile.view:hover {
  background: rgba(33, 150, 243, 0.2);
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
  max-width: 100%;
}

.mobile-page-btn {
  width: 36px;
  height: 36px;
  border: 1px solid var(--border);
  background: var(--secondary);
  color: var(--text);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  flex-shrink: 0;
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
  font-size: 0.9rem;
  padding: 0 1rem;
  text-align: center;
}

/* Calendar Section */
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
  padding: 1rem;
  box-sizing: border-box;
}

.modal-content {
  background: var(--light-dark);
  border-radius: 16px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideUp 0.3s ease-out;
  box-sizing: border-box;
}

.view-modal {
  max-width: 700px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1rem;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  background: var(--light-dark);
  z-index: 1;
}

.modal-header h2 {
  font-size: 1.35rem;
  color: var(--text);
  font-weight: 600;
  margin: 0;
  line-height: 1.2;
}

.close-btn {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  flex-shrink: 0;
}

.close-btn:hover {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
}

/* Request Form */
.request-form {
  padding: 1.25rem 1rem;
}

.form-row {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  flex: 1;
  width: 100%;
}

.form-group.full-width {
  flex: 0 0 100%;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.85rem;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  background: var(--secondary);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-size: 0.9rem;
  transition: var(--transition);
  box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
}

.form-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-top: 1.25rem;
  border-top: 1px solid var(--border);
  margin-top: 1.25rem;
}

.btn-secondary {
  background: var(--secondary);
  color: var(--text);
  border: 1px solid var(--border);
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  width: 100%;
  box-sizing: border-box;
}

.btn-secondary:hover {
  background: rgba(177, 156, 217, 0.05);
  border-color: var(--primary);
  color: var(--primary);
}

/* Request Details View */
.request-details {
  padding: 1.25rem 1rem;
}

.detail-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid var(--border);
  margin-bottom: 1.25rem;
  text-align: center;
}

.employee-avatar-large {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.75rem;
  flex-shrink: 0;
}

.employee-info-large {
  flex: 1;
  width: 100%;
}

.employee-info-large h3 {
  font-size: 1.35rem;
  color: var(--text);
  margin-bottom: 0.5rem;
  font-weight: 600;
  line-height: 1.2;
}

.employee-info-large p {
  color: var(--text-secondary);
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
}

.request-type-large {
  display: inline-block;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.85rem;
}

.status-display {
  text-align: center;
  width: 100%;
}

.status-badge-large {
  display: inline-block;
  padding: 0.4rem 1.2rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.submitted-date {
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.detail-content {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.detail-section h4 {
  font-size: 1.1rem;
  color: var(--text);
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--border);
  font-weight: 600;
}

.period-info {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
  background: var(--secondary);
  padding: 1rem;
  border-radius: 8px;
}

.period-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.period-label {
  color: var(--text-secondary);
  font-size: 0.85rem;
}

.period-value {
  color: var(--text);
  font-weight: 500;
  font-size: 1rem;
}

.request-info {
  background: var(--secondary);
  padding: 1rem;
  border-radius: 8px;
}

.info-item {
  margin-bottom: 1rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-item .info-label {
  color: var(--text-secondary);
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
  font-size: 0.9rem;
}

.info-item .info-value {
  color: var(--text);
  line-height: 1.6;
  font-size: 0.95rem;
  word-break: break-word;
}

.action-section {
  background: var(--secondary);
  padding: 1.25rem;
  border-radius: 8px;
  border: 1px solid var(--border);
}

.action-notes textarea {
  width: 100%;
  background: var(--light-dark);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem;
  color: var(--text);
  margin-bottom: 1rem;
  resize: vertical;
  font-size: 0.9rem;
  box-sizing: border-box;
  min-height: 80px;
}

.action-buttons-large {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.btn-denial,
.btn-approval {
  flex: 1;
  padding: 0.9rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: var(--transition);
  font-size: 0.95rem;
  box-sizing: border-box;
}

.btn-denial {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid var(--danger);
}

.btn-denial:hover {
  background: rgba(244, 67, 54, 0.2);
}

.btn-approval {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid var(--success);
}

.btn-approval:hover {
  background: rgba(76, 175, 80, 0.2);
}

/* Responsive Design */
@media (min-width: 480px) {
  .page-header {
    padding: 1.5rem;
  }
  
  .header-content h1 {
    font-size: 1.65rem;
  }
  
  .stats-cards {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .filter-controls {
    flex-direction: row;
  }
  
  .form-actions,
  .action-buttons-large {
    flex-direction: row;
  }
  
  .btn-secondary,
  .btn-denial,
  .btn-approval {
    width: auto;
  }
  
  .detail-header {
    flex-direction: row;
    text-align: left;
  }
  
  .status-display {
    text-align: right;
  }
}

@media (min-width: 768px) {
  .page-header {
    flex-direction: row;
    align-items: center;
    text-align: left;
  }
  
  .header-content {
    text-align: left;
    flex: 1;
  }
  
  .header-actions {
    align-items: flex-end;
    width: auto;
  }
  
  .btn-primary {
    width: auto;
    max-width: none;
  }
  
  .filter-controls {
    justify-content: flex-end;
  }
  
  .desktop-table {
    display: block;
  }
  
  .mobile-cards {
    display: none;
  }
  
  .stats-cards {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .form-row {
    flex-direction: row;
  }
  
  .calendar-grid {
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
  }
  
  .calendar-day {
    min-height: 100px;
    padding: 0.75rem;
  }
  
  .period-info {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 992px) {
  .page-header {
    padding: 1.5rem 2rem;
  }
  
  .header-content h1 {
    font-size: 1.75rem;
  }
  
  .modal-content {
    max-width: 600px;
  }
  
  .view-modal {
    max-width: 700px;
  }
  
  .calendar-controls {
    justify-content: space-between;
  }
}

@media (max-width: 767px) {
  .desktop-table {
    display: none;
  }
  
  .mobile-cards {
    display: block;
  }
  
  .calendar-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .timeoff-table th,
  .timeoff-table td {
    padding: 0.75rem 0.25rem;
    font-size: 0.8rem;
  }
  
  .avatar {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
  }
  
  .employee-info strong {
    font-size: 0.8rem;
  }
  
  .employee-info small {
    font-size: 0.7rem;
  }
  
  .request-type {
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
  }
  
  .date-range {
    font-size: 0.8rem;
  }
  
  .duration-value {
    font-size: 0.9rem;
  }
  
  .status-badge {
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
  }
  
  .btn-action {
    width: 28px;
    height: 28px;
  }
}

@media (max-width: 480px) {
  .timeoff-management {
    padding: 0.5rem;
  }
  
  .page-header,
  .stats-cards,
  .desktop-table,
  .mobile-cards,
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .stat-card {
    padding: 1rem;
  }
  
  .stat-content h3 {
    font-size: 1.4rem;
  }
  
  .modal-header {
    padding: 1rem;
  }
  
  .modal-header h2 {
    font-size: 1.25rem;
  }
  
  .request-details,
  .request-form {
    padding: 1rem;
  }
  
  .employee-avatar-large {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .employee-info-large h3 {
    font-size: 1.2rem;
  }
  
  .status-badge-large {
    font-size: 0.85rem;
    padding: 0.3rem 1rem;
  }
  
  .detail-section h4 {
    font-size: 1rem;
  }
  
  .period-value {
    font-size: 0.95rem;
  }
  
  .info-item .info-value {
    font-size: 0.9rem;
  }
  
  .btn-denial,
  .btn-approval {
    padding: 0.75rem;
    font-size: 0.9rem;
  }
  
  .card-actions {
    flex-direction: column;
  }
  
  .btn-mobile {
    min-width: 100%;
  }
}

/* Ensure no horizontal overflow */
* {
  max-width: 100%;
}

.timeoff-table {
  overflow-x: auto;
  display: block;
}

/* Mobile optimization for calendar */
@media (max-width: 360px) {
  .filter-controls select {
    min-width: 100px;
    font-size: 0.8rem;
  }
  
  .stat-card {
    padding: 0.75rem;
  }
  
  .stat-content h3 {
    font-size: 1.3rem;
  }
  
  .stat-content p {
    font-size: 0.75rem;
  }
  
  .modal-overlay {
    padding: 0.5rem;
  }
  
  .modal-content {
    border-radius: 12px;
  }
}

/* Fix for very small screens */
@media (max-width: 320px) {
  .page-header,
  .stats-cards,
  .desktop-table,
  .mobile-cards,
  .btn-primary,
  .btn-secondary,
  .btn-denial,
  .btn-approval {
    padding: 0.6rem 1rem;
    font-size: 0.85rem;
  }
  
  .modal-header h2 {
    font-size: 1.1rem;
  }
}

/* Desktop-only: ensure the desktop table fills its container and columns can auto-size */
@media (min-width: 769px) {
  .desktop-table {
    width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
    box-sizing: border-box;
  }

  .desktop-table .timeoff-table {
    width: 100%;
    table-layout: auto; /* allow columns to size naturally */
    display: table; /* restore table display for proper layout on desktop */
  }

  .timeoff-table th,
  .timeoff-table td {
    white-space: normal; /* allow wrapping so cells can expand */
  }
}
</style>