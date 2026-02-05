<template>
  <div class="payroll-management">
    <div class="page-header">
      <div class="header-content">
        <h1>Payroll & Attendance</h1>
        <p>Manage employee salaries, process payments, and track attendance</p>
      </div>
      <div class="header-actions">
        <button class="btn-secondary" @click="downloadAll">
          <i class="bi bi-download"></i>
          Download All
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-content">
          <h3>R{{ formatCurrency(totalPayroll) }}</h3>
          <p>Total Monthly Payroll</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>8%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>R{{ formatCurrency(averageSalary) }}</h3>
          <p>Average Salary</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>5%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ totalOvertime }}h</h3>
          <p>Total Overtime</p>
        </div>
        <div class="stat-trend trend-down">
          <i class="bi bi-arrow-down-right"></i>
          <span>12%</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-content">
          <h3>{{ avgAttendance }}%</h3>
          <p>Avg. Attendance</p>
        </div>
        <div class="stat-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>3%</span>
        </div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="table-responsive desktop-table">
      <table class="payroll-table">
        <thead>
          <tr>
            <th @click="sortBy('name')">
              Employee
              <i class="bi" :class="sortIcon('name')"></i>
            </th>
            <th @click="sortBy('hours')">
              Hours
              <i class="bi" :class="sortIcon('hours')"></i>
            </th>
            <th @click="sortBy('overtime')">
              Overtime
              <i class="bi" :class="sortIcon('overtime')"></i>
            </th>
            <th @click="sortBy('netPay')">
              Net Pay (ZAR)
              <i class="bi" :class="sortIcon('netPay')"></i>
            </th>
            <th @click="sortBy('status')">
              Status
              <i class="bi" :class="sortIcon('status')"></i>
            </th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="record in paginatedRecords" :key="record.id">
            <td>
              <div class="employee-info">
                <div class="avatar">{{ record.name.charAt(0) }}</div>
                <div>
                  <strong>{{ record.name }}</strong>
                  <small>{{ record.department || 'Department' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div class="hours-display">
                <span class="hours-value">{{ record.hours }}</span>
                <span class="hours-label">hours</span>
              </div>
            </td>
            <td>
              <div class="overtime-display">
                <span class="overtime-value">{{ record.overtime }}</span>
                <span class="overtime-label">hours</span>
              </div>
            </td>
            <td>
              <span class="salary-amount">R{{ formatRand(record.netPay) }}</span>
            </td>
            <td>
              <span class="status-badge" :class="record.status || 'paid'">
                {{ record.status || 'Paid' }}
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <button class="btn-action view" @click="viewPayslip(record)" title="View Payslip">
                  <i class="bi bi-eye"></i>
                </button>
                <!-- <button class="btn-action download" @click="downloadPayslip(record)" title="Download">
                  <i class="bi bi-download"></i>
                </button> -->
                <button class="btn-action edit" @click="editRecord(record)" title="Edit">
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
      <div v-for="record in paginatedRecords" :key="record.id" class="payroll-card">
        <div class="card-header">
          <div class="employee-avatar">{{ record.name.charAt(0) }}</div>
          <div class="employee-details">
            <h3>{{ record.name }}</h3>
            <p>{{ record.department || 'Department' }}</p>
          </div>
          <div class="employee-status">
            <span class="status-badge" :class="record.status || 'paid'">
              {{ record.status || 'Paid' }}
            </span>
          </div>
        </div>
        
        <div class="card-body">
          <div class="info-row">
            <div class="info-item">
              <span class="info-label">Hours</span>
              <span class="info-value">{{ record.hours }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Overtime</span>
              <span class="info-value">{{ record.overtime }}h</span>
            </div>
            <div class="info-item">
              <span class="info-label">Net Pay</span>
              <span class="info-value">R{{ formatRand(record.netPay) }}</span>
            </div>
          </div>
        </div>
        
        <div class="card-actions">
          <button class="btn-mobile view" @click="viewPayslip(record)">
            <i class="bi bi-eye"></i>
            View
          </button>
          <!-- <button class="btn-mobile download" @click="downloadPayslip(record)">
            <i class="bi bi-download"></i>
            Download
          </button> -->
          <button class="btn-mobile edit" @click="editRecord(record)">
            <i class="bi bi-pencil"></i>
            Edit
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

    <!-- Additional Summary -->
    <div class="summary-cards">
      <div class="summary-card">
        <h3>Payroll Summary</h3>
        <div class="summary-content">
          <div class="summary-item">
            <span class="summary-label">Total Employees</span>
            <span class="summary-value">{{ payrolls.length }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Total Hours</span>
            <span class="summary-value">{{ totalHours }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Total Overtime Cost</span>
            <span class="summary-value">R{{ formatCurrency(totalOvertimeCost) }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Tax Deductions</span>
            <span class="summary-value">R{{ formatCurrency(taxDeductions) }}</span>
          </div>
        </div>
      </div>
      
      <div class="summary-card">
        <h3>Payment Status</h3>
        <div class="payment-status">
          <div class="status-item">
            <div class="status-indicator paid"></div>
            <span class="status-label">Paid</span>
            <span class="status-count">{{ paidCount }}</span>
          </div>
          <div class="status-item">
            <div class="status-indicator pending"></div>
            <span class="status-label">Pending</span>
            <span class="status-count">{{ pendingCount }}</span>
          </div>
          <div class="status-item">
            <div class="status-indicator processing"></div>
            <span class="status-label">Processing</span>
            <span class="status-count">{{ processingCount }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Payslip Modal -->
    <div v-if="showPayslip" class="modal-overlay" @click.self="closePayslipModal">
      <div class="modal-content payslip-modal">
        <div class="modal-header">
          <h2>Payslip Details</h2>
          <button @click="closePayslipModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <div class="payslip-content">
          <div class="payslip-header">
            <div class="company-logo">
              <i class="bi bi-building"></i>
              <h3>ModernTech Solutions</h3>
            </div>
            <div class="payslip-title">
              <h2>PAYSLIP</h2>
              <p>Payment Period: {{ currentPayslip.period || 'March 2024' }}</p>
            </div>
          </div>
          
          <div class="payslip-details">
            <div class="detail-section">
              <h4>Employee Information</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Name:</span>
                  <span class="detail-value">{{ currentPayslip.name }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Employee ID:</span>
                  <span class="detail-value">EMP-{{ currentPayslip.id?.toString().padStart(4, '0') || '0001' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Department:</span>
                  <span class="detail-value">{{ currentPayslip.department || 'Development' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Payment Date:</span>
                  <span class="detail-value">{{ currentPayslip.date }}</span>
                </div>
              </div>
            </div>
            
            <div class="detail-section">
              <h4>Earnings & Deductions</h4>
              <div class="earnings-table">
                <div class="earnings-row header">
                  <span>Description</span>
                  <span>Amount (ZAR)</span>
                </div>
                <div class="earnings-row">
                  <span>Basic Salary</span>
                  <span>R{{ formatRand(currentPayslip.basicSalary || currentPayslip.netPay * 0.8) }}</span>
                </div>
                <div class="earnings-row">
                  <span>Overtime Pay</span>
                  <span>R{{ formatRand(currentPayslip.overtimePay || currentPayslip.netPay * 0.1) }}</span>
                </div>
                <div class="earnings-row">
                  <span>Allowances</span>
                  <span>R{{ formatRand(currentPayslip.allowances || currentPayslip.netPay * 0.05) }}</span>
                </div>
                <div class="earnings-row">
                  <span>Tax Deduction</span>
                  <span>-R{{ formatRand(currentPayslip.tax || currentPayslip.netPay * 0.15) }}</span>
                </div>
                <div class="earnings-row total">
                  <span><strong>Net Pay</strong></span>
                  <span><strong>R{{ formatRand(currentPayslip.netPay) }}</strong></span>
                </div>
              </div>
            </div>
            
            <div class="detail-section">
              <h4>Payment Information</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Payment Method:</span>
                  <span class="detail-value">Bank Transfer</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Bank Name:</span>
                  <span class="detail-value">Standard Bank</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Account Number:</span>
                  <span class="detail-value">•••• •••• 1234</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Currency:</span>
                  <span class="detail-value">South African Rand (ZAR)</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-actions">
          <button @click="printPayslip" class="btn-secondary">
            <i class="bi bi-printer"></i>
            Print
          </button>
          <button @click="downloadPayslip(currentPayslip)" class="btn-primary">
            <i class="bi bi-download"></i>
            Download PDF
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Record Modal -->
    <div v-if="showEditModal" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal-content edit-modal">
        <div class="modal-header">
          <h2>Edit Payroll Record</h2>
          <button @click="closeEditModal" class="close-btn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <form @submit.prevent="saveRecord" class="edit-form">
          <div class="form-group">
            <label for="editHours">Hours Worked</label>
            <input 
              v-model.number="editData.hours" 
              id="editHours"
              type="number" 
              min="0"
              max="240"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="editOvertime">Overtime Hours</label>
            <input 
              v-model.number="editData.overtime" 
              id="editOvertime"
              type="number" 
              min="0"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="editNetPay">Net Pay (ZAR)</label>
            <div class="salary-input">
              <span class="currency">R</span>
              <input 
                v-model.number="editData.netPay" 
                id="editNetPay"
                type="number" 
                min="0"
                required
              >
            </div>
          </div>
          
          <div class="form-group">
            <label for="editStatus">Payment Status</label>
            <select v-model="editData.status" id="editStatus">
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="closeEditModal" class="btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn-primary">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
const USD_TO_ZAR = 1

export default {
  name: 'PayrollAttendance',
  data() {
    return {
      // Load payroll data from payroll_data.json
      payrolls: [
        { id: 1, name: 'Sibongile Nkosi', department: 'Development', hours: 160, overtime: 10, netPay: 69500 * USD_TO_ZAR, status: 'paid' },
        { id: 2, name: 'Lungile Moyo', department: 'HR', hours: 150, overtime: 5, netPay: 79000 * USD_TO_ZAR, status: 'paid' },
        { id: 3, name: 'Thabo Molefe', department: 'QA', hours: 170, overtime: 2, netPay: 54800 * USD_TO_ZAR, status: 'processing' },
        { id: 4, name: 'Keshav Naidoo', department: 'Sales', hours: 165, overtime: 8, netPay: 59700 * USD_TO_ZAR, status: 'paid' },
        { id: 5, name: 'Zanele Khumalo', department: 'Marketing', hours: 158, overtime: 15, netPay: 57850 * USD_TO_ZAR, status: 'pending' },
        { id: 6, name: 'Sipho Zulu', department: 'Design', hours: 168, overtime: 6, netPay: 64800 * USD_TO_ZAR, status: 'paid' },
        { id: 7, name: 'Naledi Moeketsi', department: 'IT', hours: 175, overtime: 12, netPay: 71800 * USD_TO_ZAR, status: 'paid' },
        { id: 8, name: 'Farai Gumbo', department: 'Marketing', hours: 160, overtime: 3, netPay: 56000 * USD_TO_ZAR, status: 'cancelled' },
        { id: 9, name: 'Karabo Dlamini', department: 'Finance', hours: 155, overtime: 7, netPay: 61500 * USD_TO_ZAR, status: 'paid' },
        { id: 10, name: 'Fatima Patel', department: 'Support', hours: 162, overtime: 4, netPay: 57750 * USD_TO_ZAR, status: 'paid' }
      ],
      showPayslip: false,
      showEditModal: false,
      currentPayslip: {},
      editData: {},
      sortField: 'name',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 5,
      searchQuery: ''
    }
  },
  computed: {
    filteredRecords() {
      let filtered = this.payrolls
      
      // Search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(record => 
          record.name.toLowerCase().includes(query) ||
          record.department.toLowerCase().includes(query) ||
          record.status.toLowerCase().includes(query)
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
    paginatedRecords() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredRecords.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredRecords.length / this.itemsPerPage)
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
    totalPayroll() {
      return this.payrolls.reduce((sum, record) => sum + record.netPay, 0)
    },
    averageSalary() {
      return Math.round(this.totalPayroll / this.payrolls.length)
    },
    totalHours() {
      return this.payrolls.reduce((sum, record) => sum + record.hours, 0)
    },
    totalOvertime() {
      return this.payrolls.reduce((sum, record) => sum + record.overtime, 0)
    },
    totalOvertimeCost() {
      // Assuming overtime is paid at 1.5x normal rate
      return Math.round(this.totalOvertime * 150) // Simplified calculation
    },
    taxDeductions() {
      return Math.round(this.totalPayroll * 0.15) // 15% tax
    },
    avgAttendance() {
      return 96 // Mock data
    },
    paidCount() {
      return this.payrolls.filter(record => record.status === 'paid').length
    },
    pendingCount() {
      return this.payrolls.filter(record => record.status === 'pending').length
    },
    processingCount() {
      return this.payrolls.filter(record => record.status === 'processing').length
    }
  },
  methods: {
    formatRand(amount) {
      return amount.toLocaleString('en-ZA', { minimumFractionDigits: 2 })
    },
    formatCurrency(amount) {
      return amount.toLocaleString('en-ZA', { minimumFractionDigits: 0 })
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
    // processPayroll removed — button was removed from UI
    downloadAll() {
      alert(`Downloading ${this.payrolls.length} payslips as ZIP archive...`)
      // In a real app, you would generate and download a ZIP file
      setTimeout(() => {
        alert('Download complete! Check your downloads folder.')
      }, 1500)
    },
    viewPayslip(record) {
      this.currentPayslip = {
        ...record,
        date: new Date().toLocaleDateString('en-ZA'),
        period: 'March 2024',
        basicSalary: record.netPay * 0.8,
        overtimePay: record.netPay * 0.1,
        allowances: record.netPay * 0.05,
        tax: record.netPay * 0.15
      }
      this.showPayslip = true
    },
    closePayslipModal() {
      this.showPayslip = false
      this.currentPayslip = {}
    },
    downloadPayslip(record) {
      const payslipContent = `
MODERNTECH SOLUTIONS - PAYSLIP
===============================

Employee Information:
-------------------
Name: ${record.name}
Employee ID: EMP-${record.id.toString().padStart(4, '0')}
Department: ${record.department || 'Development'}
Payment Date: ${new Date().toLocaleDateString('en-ZA')}
Payment Period: March 2024

Earnings & Deductions:
---------------------
Basic Salary: R${this.formatRand(record.netPay * 0.8)}
Overtime Pay: R${this.formatRand(record.netPay * 0.1)}
Allowances: R${this.formatRand(record.netPay * 0.05)}
Tax Deduction: -R${this.formatRand(record.netPay * 0.15)}

Net Pay: R${this.formatRand(record.netPay)}

Payment Information:
------------------
Payment Method: Bank Transfer
Bank: Standard Bank
Account: •••• •••• 1234
Currency: South African Rand (ZAR)
Exchange Rate: 1 USD ≈ ${USD_TO_ZAR} ZAR

Status: ${record.status.toUpperCase()}

This is an official payslip document.
Generated on: ${new Date().toLocaleString('en-ZA')}
      `
      
      const blob = new Blob([payslipContent], { type: 'text/plain' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `Payslip_${record.name.replace(/\s+/g, '_')}_${new Date().getFullYear()}_${(new Date().getMonth() + 1).toString().padStart(2, '0')}.txt`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      window.URL.revokeObjectURL(url)
      
      // Show preview after download
      this.viewPayslip(record)
    },
    printPayslip() {
      const printWindow = window.open('', '_blank')
      printWindow.document.write(`
        <html>
        <head>
          <title>Payslip - ${this.currentPayslip.name}</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #1a1a2e; }
            .header h2 { color: #b19cd9; }
            .details { margin-bottom: 30px; }
            .details h3 { border-bottom: 2px solid #b19cd9; padding-bottom: 5px; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            .detail-item { margin: 5px 0; }
            .detail-label { font-weight: bold; }
            .earnings-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .earnings-table td, .earnings-table th { border: 1px solid #ddd; padding: 10px; text-align: left; }
            .earnings-table th { background: #f5f5f5; }
            .total { font-weight: bold; background: #f9f9f9; }
            .footer { margin-top: 40px; text-align: center; color: #666; font-size: 0.9em; }
          </style>
        </head>
        <body>
          <div class="header">
            <h1>ModernTech Solutions</h1>
            <h2>PAYSLIP</h2>
            <p>Payment Period: ${this.currentPayslip.period || 'March 2024'}</p>
          </div>
          
          <div class="details">
            <h3>Employee Information</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Name:</span>
                <span>${this.currentPayslip.name}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Employee ID:</span>
                <span>EMP-${this.currentPayslip.id?.toString().padStart(4, '0') || '0001'}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Department:</span>
                <span>${this.currentPayslip.department || 'Development'}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Payment Date:</span>
                <span>${this.currentPayslip.date}</span>
              </div>
            </div>
          </div>
          
          <div class="details">
            <h3>Earnings & Deductions</h3>
            <table class="earnings-table">
              <tr>
                <th>Description</th>
                <th>Amount (ZAR)</th>
              </tr>
              <tr>
                <td>Basic Salary</td>
                <td>R${this.formatRand(this.currentPayslip.basicSalary || this.currentPayslip.netPay * 0.8)}</td>
              </tr>
              <tr>
                <td>Overtime Pay</td>
                <td>R${this.formatRand(this.currentPayslip.overtimePay || this.currentPayslip.netPay * 0.1)}</td>
              </tr>
              <tr>
                <td>Allowances</td>
                <td>R${this.formatRand(this.currentPayslip.allowances || this.currentPayslip.netPay * 0.05)}</td>
              </tr>
              <tr>
                <td>Tax Deduction</td>
                <td>-R${this.formatRand(this.currentPayslip.tax || this.currentPayslip.netPay * 0.15)}</td>
              </tr>
              <tr class="total">
                <td><strong>Net Pay</strong></td>
                <td><strong>R${this.formatRand(this.currentPayslip.netPay)}</strong></td>
              </tr>
            </table>
          </div>
          
          <div class="footer">
            <p>This is an official payslip document. Generated on ${new Date().toLocaleString('en-ZA')}</p>
            <p>Currency: South African Rand (ZAR) | Exchange Rate: 1 USD ≈ ${USD_TO_ZAR} ZAR</p>
          </div>
        </body>
        </html>
      `)
      printWindow.document.close()
      printWindow.print()
    },
    editRecord(record) {
      this.editData = { ...record }
      this.showEditModal = true
    },
    closeEditModal() {
      this.showEditModal = false
      this.editData = {}
    },
    saveRecord() {
      const index = this.payrolls.findIndex(record => record.id === this.editData.id)
      if (index !== -1) {
        this.payrolls[index] = { ...this.editData }
        alert('Record updated successfully!')
        this.closeEditModal()
      }
    }
  }
}
</script>

<style scoped>
/* ALL STYLING REMAINS EXACTLY THE SAME */
.payroll-management {
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
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: var(--transition);
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: var(--secondary);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(177, 156, 217, 0.3);
}

.btn-secondary {
  background: var(--light-dark);
  color: var(--text);
  border: 1px solid var(--border);
}

.btn-secondary:hover {
  background: rgba(177, 156, 217, 0.05);
  border-color: var(--primary);
  color: var(--primary);
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

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.5rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
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
  padding: 1.5rem;
  border: 1px solid var(--border);
  margin-bottom: 2rem;
}

.payroll-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 1.5rem;
}

.payroll-table th {
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

.payroll-table th:hover {
  color: var(--text);
  background: rgba(177, 156, 217, 0.05);
}

.payroll-table th i {
  margin-left: 0.5rem;
  font-size: 0.8rem;
}

.payroll-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
}

.payroll-table tr:hover td {
  background: rgba(177, 156, 217, 0.03);
}

.payroll-table tr:last-child td {
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

.hours-display,
.overtime-display {
  display: flex;
  align-items: baseline;
  gap: 0.25rem;
}

.hours-value,
.overtime-value {
  font-weight: 700;
  color: var(--text);
  font-size: 1.1rem;
}

.hours-label,
.overtime-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.salary-display {
  display: flex;
  flex-direction: column;
}

.salary-amount {
  font-weight: 700;
  color: var(--text);
  margin-bottom: 0.25rem;
}



.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.paid {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid var(--success);
}

.status-badge.pending {
  background: rgba(255, 193, 7, 0.1);
  color: var(--warning);
  border: 1px solid var(--warning);
}

.status-badge.processing {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
  border: 1px solid var(--info);
}

.status-badge.cancelled {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid var(--danger);
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

.btn-action.view {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-action.view:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-action.download {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-action.download:hover {
  background: rgba(33, 150, 243, 0.2);
}

.btn-action.edit {
  background: rgba(255, 193, 7, 0.1);
  color: var(--warning);
}

.btn-action.edit:hover {
  background: rgba(255, 193, 7, 0.2);
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

.payroll-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  border: 1px solid var(--border);
  transition: var(--transition);
}

.payroll-card:hover {
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

.employee-status {
  margin-left: auto;
}

.card-body {
  margin-bottom: 1rem;
}

.info-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.info-item {
  background: var(--secondary);
  border-radius: 8px;
  padding: 0.75rem;
  text-align: center;
}

.info-label {
  display: block;
  color: var(--text-secondary);
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.info-value {
  display: block;
  color: var(--text);
  font-weight: 700;
  font-size: 1.1rem;
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

.btn-mobile.view {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
}

.btn-mobile.view:hover {
  background: rgba(76, 175, 80, 0.2);
}

.btn-mobile.download {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
}

.btn-mobile.download:hover {
  background: rgba(33, 150, 243, 0.2);
}

.btn-mobile.edit {
  background: rgba(255, 193, 7, 0.1);
  color: var(--warning);
}

.btn-mobile.edit:hover {
  background: rgba(255, 193, 7, 0.2);
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

/* Summary Cards */
.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.summary-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid var(--border);
}

.summary-card h3 {
  font-size: 1.25rem;
  color: var(--text);
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.summary-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(74, 74, 109, 0.3);
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-label {
  color: var(--text-secondary);
  font-weight: 500;
}

.summary-value {
  color: var(--text);
  font-weight: 700;
  font-size: 1.1rem;
}

.payment-status {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: var(--secondary);
  border-radius: 8px;
}

.status-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.status-indicator.paid {
  background: var(--success);
}

.status-indicator.pending {
  background: var(--warning);
}

.status-indicator.processing {
  background: var(--info);
}

.status-label {
  flex: 1;
  color: var(--text);
  font-weight: 500;
}

.status-count {
  color: var(--text-secondary);
  font-weight: 700;
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

.payslip-modal {
  max-width: 600px;
}

.edit-modal {
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

/* Payslip Content */
.payslip-content {
  padding: 1.5rem;
}

.payslip-header {
  text-align: center;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 2px solid var(--primary);
}

.company-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.company-logo i {
  font-size: 2rem;
  color: var(--primary);
}

.company-logo h3 {
  font-size: 1.5rem;
  color: var(--text);
  font-weight: 600;
}

.payslip-title h2 {
  font-size: 2rem;
  color: var(--primary);
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.payslip-title p {
  color: var(--text-secondary);
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section h4 {
  font-size: 1.1rem;
  color: var(--text);
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--border);
  font-weight: 600;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.detail-value {
  color: var(--text);
  font-weight: 500;
}

.earnings-table {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
}

.earnings-row {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem;
  border-bottom: 1px solid rgba(74, 74, 109, 0.3);
}

.earnings-row.header {
  font-weight: 600;
  color: var(--text-secondary);
  border-bottom: 2px solid var(--border);
}

.earnings-row.total {
  font-weight: 700;
  background: rgba(177, 156, 217, 0.05);
  border-top: 2px solid var(--border);
  border-bottom: none;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid var(--border);
}

/* Edit Form */
.edit-form {
  padding: 1.5rem;
}

.edit-form .form-group {
  margin-bottom: 1.5rem;
}

.edit-form label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

.edit-form input,
.edit-form select {
  width: 100%;
  padding: 0.75rem 1rem;
  background: var(--secondary);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-size: 0.95rem;
  transition: var(--transition);
}

.edit-form input:focus,
.edit-form select:focus {
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

.edit-form .form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border);
  margin-top: 1.5rem;
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
    justify-content: center;
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
  
  .summary-cards {
    grid-template-columns: 1fr;
  }
  
  .info-row {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }
  
  .detail-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .payroll-card {
    padding: 1rem;
  }
  
  .card-header {
    flex-wrap: wrap;
  }
  
  .employee-status {
    width: 100%;
    margin-top: 0.5rem;
  }
  
  .card-actions {
    flex-direction: column;
  }
  
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .header-actions {
    flex-direction: column;
  }
}
</style>