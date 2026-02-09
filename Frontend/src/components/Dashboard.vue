<template>
  <div class="dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section">
      <div class="welcome-content">
        <h1>Welcome back, Admin</h1>
        <p>Here's what's happening with your HR system today.</p>
      </div>
      <div class="date-display">
        <i class="bi bi-calendar-check"></i>
        <span>{{ currentDate }}</span>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
      <div class="kpi-card" @click="$router.push('/employees')">
        <div class="kpi-icon" style="background: rgba(177, 156, 217, 0.1);">
          <i class="bi bi-people-fill" style="color: var(--primary);"></i>
        </div>
        <div class="kpi-content">
          <h3>{{ totalEmployees }}</h3>
          <p>Total Employees</p>
        </div>
        <div class="kpi-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>12%</span>
        </div>
      </div>

      <div class="kpi-card" @click="$router.push('/payroll')">
        <div class="kpi-icon" style="background: rgba(76, 175, 80, 0.1);">
          <i class="bi bi-cash-stack" style="color: var(--success);"></i>
        </div>
        <div class="kpi-content">
          <h3>R{{ formatCurrency(totalPayroll) }}</h3>
          <p>Monthly Payroll</p>
        </div>
        <div class="kpi-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>8%</span>
        </div>
      </div>

      <div class="kpi-card" @click="$router.push('/timeoff')">
        <div class="kpi-icon" style="background: rgba(255, 193, 7, 0.1);">
          <i class="bi bi-calendar-event" style="color: var(--warning);"></i>
        </div>
        <div class="kpi-content">
          <h3>{{ pendingTimeOff }}</h3>
          <p>Pending Time Off</p>
        </div>
        <div class="kpi-trend trend-down">
          <i class="bi bi-arrow-down-right"></i>
          <span>5%</span>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(33, 150, 243, 0.1);">
          <i class="bi bi-clock-history" style="color: var(--info);"></i>
        </div>
        <div class="kpi-content">
          <h3>{{ avgAttendance }}%</h3>
          <p>Avg. Attendance</p>
        </div>
        <div class="kpi-trend trend-up">
          <i class="bi bi-arrow-up-right"></i>
          <span>3%</span>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-header">
          <h3>Department Distribution</h3>

        </div>
        <div class="chart-container">
          <canvas ref="deptChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3>Salary Trends</h3>

        </div>
        <div class="chart-container">
          <canvas ref="salaryChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="content-grid">
      <div class="activity-card">
        <div class="card-header">
          <h3>Recent Activity</h3>
          <button class="btn-view-all" @click="$router.push('/employees')">View All</button>
        </div>
        <div class="activity-list">
          <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
            <div class="activity-icon" :class="activity.type">
              <i :class="activity.icon"></i>
            </div>
            <div class="activity-content">
              <p>{{ activity.message }}</p>
              <span class="activity-time">{{ activity.time }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="quick-actions-card">
        <div class="card-header">
          <h3>Quick Actions</h3>
        </div>
        <div class="actions-grid">
          <button class="action-btn" @click="$router.push('/employees?add=true')">
            <i class="bi bi-person-plus"></i>
            <span>Add Employee</span>
          </button>
          <button class="action-btn" @click="$router.push('/payroll')">
            <i class="bi bi-file-earmark-text"></i>
            <span>Process Payroll</span>
          </button>
          <button class="action-btn" @click="$router.push('/timeoff')">
            <i class="bi bi-calendar-check"></i>
            <span>Approve Time Off</span>
          </button>
          <button class="action-btn" @click="generateReport">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Generate Report</span>
          </button>
        </div>
      </div>
    </div>


  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Chart from 'chart.js/auto'

const USD_TO_ZAR = 1

export default {
  name: 'Dash-board',
  setup() {
    const deptChart = ref(null)
    const salaryChart = ref(null)
    const chartPeriod = ref('monthly')
    const salaryPeriod = ref('6m')
    const showAllEvents = ref(false)

    // Load employee data from employee_info.json
    const employees = [
      { id: 1, name: 'Sibongile Nkosi', position: 'Software Engineer', department: 'Development', salary: 70000 * USD_TO_ZAR },
      { id: 2, name: 'Lungile Moyo', position: 'HR Manager', department: 'HR', salary: 80000 * USD_TO_ZAR },
      { id: 3, name: 'Thabo Molefe', position: 'QA Analyst', department: 'QA', salary: 55000 * USD_TO_ZAR },
      { id: 4, name: 'Keshav Naidoo', position: 'Sales Rep', department: 'Sales', salary: 60000 * USD_TO_ZAR },
      { id: 5, name: 'Zanele Khumalo', position: 'Marketing Spec', department: 'Marketing', salary: 58000 * USD_TO_ZAR },
      { id: 6, name: 'Sipho Zulu', position: 'UI/UX Designer', department: 'Design', salary: 65000 * USD_TO_ZAR },
      { id: 7, name: 'Naledi Moeketsi', position: 'DevOps Engineer', department: 'IT', salary: 72000 * USD_TO_ZAR },
      { id: 8, name: 'Farai Gumbo', position: 'Content Strategist', department: 'Marketing', salary: 56000 * USD_TO_ZAR },
      { id: 9, name: 'Karabo Dlamini', position: 'Accountant', department: 'Finance', salary: 62000 * USD_TO_ZAR },
      { id: 10, name: 'Fatima Patel', position: 'Support Lead', department: 'Support', salary: 58000 * USD_TO_ZAR }
    ]

    // Load attendance data from attendance.json
    const attendanceData = {
      "attendanceAndLeave": [
        {
          "employeeId": 1,
          "name": "Sibongile Nkosi",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Absent"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-22", "reason": "Sick Leave", "status": "Approved"},
            {"date": "2024-12-01", "reason": "Personal", "status": "Pending"}
          ]
        },
        {
          "employeeId": 2,
          "name": "Lungile Moyo",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Absent"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-15", "reason": "Family Responsibility", "status": "Denied"},
            {"date": "2024-12-02", "reason": "Vacation", "status": "Approved"}
          ]
        },
        {
          "employeeId": 3,
          "name": "Thabo Molefe",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Absent"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-10", "reason": "Medical Appointment", "status": "Approved"},
            {"date": "2024-12-05", "reason": "Personal", "status": "Pending"}
          ]
        },
        {
          "employeeId": 4,
          "name": "Keshav Naidoo",
          "attendance": [
            {"date": "2025-07-25", "status": "Absent"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-20", "reason": "Bereavement", "status": "Approved"}
          ]
        },
        {
          "employeeId": 5,
          "name": "Zanele Khumalo",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Absent"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2024-12-01", "reason": "Childcare", "status": "Pending"}
          ]
        },
        {
          "employeeId": 6,
          "name": "Sipho Zulu",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Absent"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-18", "reason": "Sick Leave", "status": "Approved"}
          ]
        },
        {
          "employeeId": 7,
          "name": "Naledi Moeketsi",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Absent"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-22", "reason": "Vacation", "status": "Pending"}
          ]
        },
        {
          "employeeId": 8,
          "name": "Farai Gumbo",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Absent"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2024-12-02", "reason": "Medical Appointment", "status": "Approved"}
          ]
        },
        {
          "employeeId": 9,
          "name": "Karabo Dlamini",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Present"},
            {"date": "2025-07-28", "status": "Absent"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2025-07-19", "reason": "Childcare", "status": "Denied"}
          ]
        },
        {
          "employeeId": 10,
          "name": "Fatima Patel",
          "attendance": [
            {"date": "2025-07-25", "status": "Present"},
            {"date": "2025-07-26", "status": "Present"},
            {"date": "2025-07-27", "status": "Absent"},
            {"date": "2025-07-28", "status": "Present"},
            {"date": "2025-07-29", "status": "Present"}
          ],
          "leaveRequests": [
            {"date": "2024-12-03", "reason": "Vacation", "status": "Pending"}
          ]
        }
      ]
    }

    const totalEmployees = computed(() => employees.length)
    const totalPayroll = computed(() => employees.reduce((sum, emp) => sum + emp.salary, 0))
    const pendingTimeOff = computed(() => {
      return attendanceData.attendanceAndLeave.reduce((count, employee) => {
        const pendingLeaves = employee.leaveRequests.filter(leave => leave.status === 'Pending')
        return count + pendingLeaves.length
      }, 0)
    })

    const avgAttendance = computed(() => {
      const attendanceRates = attendanceData.attendanceAndLeave.map(employee => {
        const presentDays = employee.attendance.filter(day => day.status === 'Present').length
        const totalDays = employee.attendance.length
        return (presentDays / totalDays) * 100
      })

      const avg = attendanceRates.reduce((sum, rate) => sum + rate, 0) / attendanceRates.length
      return Math.round(avg)
    })

    const currentDate = computed(() => {
      return new Date().toLocaleDateString('en-ZA', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    })

    const recentActivities = [
      { id: 1, type: 'add', icon: 'bi bi-person-plus', message: 'New employee: Thandiwe Mbatha added', time: '2 hours ago' },
      { id: 2, type: 'update', icon: 'bi bi-pencil', message: 'Lungile Moya salary updated', time: '5 hours ago' },
      { id: 3, type: 'timeoff', icon: 'bi bi-calendar-event', message: 'Sipho Zulu requested 3 days off', time: '1 day ago' },
      { id: 4, type: 'payroll', icon: 'bi bi-cash-stack', message: 'Monthly payroll processed successfully', time: '2 days ago' },
      { id: 5, type: 'meeting', icon: 'bi bi-people', message: 'Team meeting scheduled for Friday', time: '3 days ago' }
    ]

    const upcomingEvents = [
      { id: 1, day: '15', month: 'MAR', title: 'Performance Reviews', description: 'Quarterly performance reviews begin', time: '9:00 AM' },
      { id: 2, day: '18', month: 'MAR', title: 'Payroll Deadline', description: 'Submit payroll for March', time: '5:00 PM' },
      { id: 3, day: '22', month: 'MAR', title: 'Team Building', description: 'Company team building event', time: '10:00 AM' },
      { id: 4, day: '25', month: 'MAR', title: 'Training Session', description: 'HR compliance training', time: '2:00 PM' },
      { id: 5, day: '28', month: 'MAR', title: 'Month End', description: 'Monthly reports due', time: '6:00 PM' }
    ]

    const formatCurrency = (amount) => {
      return amount.toLocaleString('en-ZA', { minimumFractionDigits: 0 })
    }

    const generateReport = () => {
      alert('Generating monthly report... This will take a moment.')
      // Simulate report generation
      setTimeout(() => {
        alert('Report generated successfully! Check your downloads.')
      }, 1500)
    }

    const setReminder = (event) => {
      alert(`Reminder set for: ${event.title} on ${event.day} ${event.month}`)
    }

    const initCharts = () => {
      // Destroy existing charts
      if (deptChart.value?.chart) deptChart.value.chart.destroy()
      if (salaryChart.value?.chart) salaryChart.value.chart.destroy()

      // Calculate department distribution from employee data
      const deptCounts = {}
      employees.forEach(emp => {
        deptCounts[emp.department] = (deptCounts[emp.department] || 0) + 1
      })

      // Department Distribution Chart
      const deptCtx = deptChart.value.getContext('2d')
      deptChart.value.chart = new Chart(deptCtx, {
        type: 'doughnut',
        data: {
          labels: Object.keys(deptCounts),
          datasets: [{
            data: Object.values(deptCounts),
            backgroundColor: [
              '#b19cd9',
              '#4caf50',
              '#2196f3',
              '#ffc107',
              '#f44336',
              '#9c27b0',
              '#009688',
              '#ff5722',
              '#673ab7'
            ],
            borderWidth: 2,
            borderColor: '#1a1a2e'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                color: '#a0a0c0',
                font: {
                  family: 'Inter'
                }
              }
            }
          },
          cutout: '70%'
        }
      })

      const salaryCtx = salaryChart.value.getContext('2d')

      salaryChart.value.chart = new Chart(salaryCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Average Salary (ZAR)',
            data: [950000, 980000, 1020000, 1050000, 1080000, 1100000],
            borderColor: '#b19cd9',
            backgroundColor: 'rgba(177, 156, 217, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              grid: {
                color: 'rgba(74, 74, 109, 0.3)'
              },
              ticks: {
                color: '#a0a0c0'
              }
            },
            y: {
              grid: {
                color: 'rgba(74, 74, 109, 0.3)'
              },
              ticks: {
                color: '#a0a0c0',
                callback: (value) => `R${(value / 1000).toFixed(0)}k`
              }
            }
          },
          plugins: {
            legend: {
              labels: {
                color: '#a0a0c0',
                font: {
                  family: 'Inter'
                }
              }
            }
          }
        }
      })
    }

    onMounted(() => {
      setTimeout(() => {
        if (deptChart.value && salaryChart.value) {
          initCharts()
        }
      }, 100)
    })

    onUnmounted(() => {
      if (deptChart.value?.chart) deptChart.value.chart.destroy()
      if (salaryChart.value?.chart) salaryChart.value.chart.destroy()
    })

    return {
      deptChart,
      salaryChart,
      chartPeriod,
      salaryPeriod,
      showAllEvents,
      totalEmployees,
      totalPayroll,
      pendingTimeOff,
      avgAttendance,
      currentDate,
      recentActivities,
      upcomingEvents,
      formatCurrency,
      generateReport,
      setReminder
    }
  }
}
</script>

<style scoped>
/* ALL STYLING REMAINS EXACTLY THE SAME */
.dashboard {
  padding: 0;
  animation: fadeIn 0.5s ease-out;
}

/* Welcome Section */
.welcome-section {
  background: linear-gradient(135deg, rgba(177, 156, 217, 0.1) 0%, rgba(26, 26, 46, 0.3) 100%);
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid rgba(177, 156, 217, 0.1);
}

.welcome-content h1 {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.welcome-content p {
  color: var(--text-secondary);
  font-size: 1rem;
}

.date-display {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.25rem;
  background: var(--light-dark);
  border-radius: 12px;
  border: 1px solid var(--border);
}

.date-display i {
  font-size: 1.25rem;
  color: var(--primary);
}

.date-display span {
  font-weight: 500;
  color: var(--text);
}

/* KPI Cards */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.kpi-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
  transition: var(--transition);
  border: 1px solid var(--border);
  position: relative;
  overflow: hidden;
}

.kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px var(--shadow);
  border-color: var(--primary);
}

.kpi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
  border-radius: 4px 4px 0 0;
}

.kpi-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.kpi-icon i {
  font-size: 1.5rem;
}

.kpi-content {
  flex: 1;
}

.kpi-content h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 0.25rem;
}

.kpi-content p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

.kpi-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.75rem;
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

/* Charts */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.chart-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid var(--border);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.chart-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text);
}

.period-select {
  background: var(--secondary);
  color: var(--text);
  border: 1px solid var(--border);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: var(--transition);
}

.period-select:focus {
  outline: none;
  border-color: var(--primary);
}

.chart-container {
  height: 300px;
  position: relative;
}

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.activity-card,
.quick-actions-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid var(--border);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.card-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text);
}

.btn-view-all {
  background: transparent;
  color: var(--primary);
  border: 1px solid var(--primary);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
}

.btn-view-all:hover {
  background: rgba(177, 156, 217, 0.1);
}

/* Activity List */
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background: var(--secondary);
  border-radius: 12px;
  border-left: 4px solid transparent;
  transition: var(--transition);
}

.activity-item:hover {
  transform: translateX(4px);
  background: rgba(177, 156, 217, 0.05);
}

.activity-item.add {
  border-left-color: var(--success);
}

.activity-item.update {
  border-left-color: var(--primary);
}

.activity-item.timeoff {
  border-left-color: var(--warning);
}

.activity-item.payroll {
  border-left-color: var(--info);
}

.activity-item.meeting {
  border-left-color: var(--danger);
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-icon i {
  font-size: 1.25rem;
}

.activity-content {
  flex: 1;
}

.activity-content p {
  color: var(--text);
  margin-bottom: 0.25rem;
  font-weight: 500;
}

.activity-time {
  color: var(--text-secondary);
  font-size: 0.8rem;
}

/* Quick Actions */
.actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.action-btn {
  background: var(--secondary);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  transition: var(--transition);
}

.action-btn:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
  box-shadow: 0 10px 20px var(--shadow);
}

.action-btn i {
  font-size: 1.75rem;
  color: var(--primary);
}

.action-btn span {
  font-weight: 500;
  color: var(--text);
  text-align: center;
}

/* Events Card */
.events-card {
  background: var(--light-dark);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid var(--border);
}

.events-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.event-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: var(--secondary);
  border-radius: 12px;
  transition: var(--transition);
}

.event-item:hover {
  background: rgba(177, 156, 217, 0.05);
  transform: translateX(4px);
}

.event-date {
  width: 60px;
  height: 60px;
  background: rgba(177, 156, 217, 0.1);
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.event-day {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
}

.event-month {
  font-size: 0.8rem;
  color: var(--text-secondary);
  font-weight: 600;
  text-transform: uppercase;
}

.event-details {
  flex: 1;
}

.event-details h4 {
  color: var(--text);
  margin-bottom: 0.25rem;
  font-weight: 600;
}

.event-details p {
  color: var(--text-secondary);
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.event-time {
  color: var(--primary);
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.btn-remind {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  flex-shrink: 0;
}

.btn-remind:hover {
  background: rgba(177, 156, 217, 0.1);
  color: var(--primary);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 992px) {
  .kpi-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .content-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .welcome-section {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .kpi-grid {
    grid-template-columns: 1fr;
  }

  .charts-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .dashboard {
    padding: 0.5rem;
  }

  .welcome-section,
  .kpi-card,
  .chart-card,
  .activity-card,
  .quick-actions-card,
  .events-card {
    padding: 1.25rem;
  }

  .welcome-content h1 {
    font-size: 1.5rem;
  }

  .kpi-content h3 {
    font-size: 1.5rem;
  }
}
</style>
