import {createStore} from 'vuex'
import employee_info from './employee_info.json'
import attendance from './attendance.json'
import p from './payroll_data.json'
const store = createStore(
    {
        state:{
            employees: employee_info.employeeInformation,
            employeesAttendance : attendance.attendanceAndLeave,
            employeesPayrollData : p.payrollData
        }, 
        getters:{

        },
        mutations:{

        }, 
        actions:{

        }
    }
)

export default store