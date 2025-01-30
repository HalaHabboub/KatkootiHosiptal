@extends('layouts.app')
@section('title', 'Doctor Schedule')

@section('content')
<x-doctorNavbar />
<div class="doctor-schedule-wrapper">
    <div class="page-banner overlay-dark bg-image" style="background-image: url({{ asset('assets/img/bg_image_1.jpg') }});">
        <div class="banner-section">
            <div class="container text-center wow fadeInUp">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb breadcrumb-dark bg-transparent justify-content-center py-0 mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Schedule</li>
                    </ol>
                </nav>
                <h1 class="font-weight-normal">Dr. {{ Auth::user()->name }}'s Schedule</h1>
            </div>
        </div>
    </div>

    <div class="page-section">
        <div class="container">
            <div class="row">
                <!-- Doctor Info Card -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ asset('assets/img/doctors/doctor_1.jpg') }}" class="rounded-circle mb-3" width="120">
                            <h5>Dr. {{ Auth::user()->name }}</h5>
                            <p class="text-muted">{{ Auth::user()->department->name ?? 'Department' }}</p>
                            <div class="d-flex justify-content-between mt-3">
                                <span>Today's Patients</span>
                                <span class="badge badge-primary">{{ $todayAppointments ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Weekly Calendar and Appointments -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">This Week's Appointments</h5>
                            <div>
                                <button type="button" class="btn btn-secondary btn-sm" id="prevWeek">
                                    <i class="fas fa-chevron-left"></i> Previous Week
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm ms-2" id="currentWeek">
                                    Current Week
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm ms-2" id="nextWeek">
                                    Next Week <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="weeklyAppointments">
                            <!-- Weekly appointments will be loaded here -->
                        </div>
                    </div>

                    <!-- Monthly Calendar -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Mark Unavailable Days</h5>
                                <small class="text-muted">You can mark dates up to 6 months in advance</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary me-2" id="prevMonth">Previous</button>
                                <button type="button" class="btn btn-secondary me-2" id="nextMonth">Next</button>
                                <button type="button" class="btn btn-primary" onclick="saveUnavailableDates()">Save Changes</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="text-center" id="currentMonthLabel"></h6>
                            <div class="simple-calendar" id="calendar-wrapper">
                                <!-- Calendar will be dynamically rendered here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('head-scripts')
<!-- Add debugging alert at the start -->
<script>
    console.log('Script section loaded');
</script>

<script>
    // Remove the FullCalendar initialization code and keep only the simple calendar code
    
    // Define unavailableDates globally so it's accessible to all functions
    const unavailableDates = @json($unavailableDates ?? []);
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script loaded');
        console.log('Unavailable dates:', unavailableDates);
        
        // Initial calendar render
        renderCalendar(currentDate);
        
        // Initialize navigation buttons
        document.getElementById('prevMonth').addEventListener('click', () => {
            const minDate = new Date();
            minDate.setHours(0, 0, 0, 0);
            minDate.setDate(1);
            
            const newDate = new Date(currentDate);
            newDate.setMonth(newDate.getMonth() - 1);
            newDate.setDate(1);
            
            if (newDate.getFullYear() > minDate.getFullYear() || 
                (newDate.getFullYear() === minDate.getFullYear() && 
                 newDate.getMonth() >= minDate.getMonth())) {
                currentDate = newDate;
                renderCalendar(currentDate);
            }
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            const maxDate = new Date();
            maxDate.setMonth(maxDate.getMonth() + maxMonths);
            maxDate.setDate(1);
            
            const newDate = new Date(currentDate);
            newDate.setMonth(newDate.getMonth() + 1);
            newDate.setDate(1);
            
            if (newDate.getFullYear() < maxDate.getFullYear() || 
                (newDate.getFullYear() === maxDate.getFullYear() && 
                 newDate.getMonth() <= maxDate.getMonth())) {
                currentDate = newDate;
                renderCalendar(currentDate);
            }
        });

        // Initialize weekly calendar
        loadWeeklyAppointments(currentWeekStart);

        // Week navigation handlers
        document.getElementById('prevWeek').addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() - 7);
            loadWeeklyAppointments(currentWeekStart);
        });

        document.getElementById('nextWeek').addEventListener('click', () => {
            currentWeekStart.setDate(currentWeekStart.getDate() + 7);
            loadWeeklyAppointments(currentWeekStart);
        });

        document.getElementById('currentWeek').addEventListener('click', () => {
            currentWeekStart = new Date();
            loadWeeklyAppointments(currentWeekStart);
        });
    });

    let currentDate = new Date();
    const maxMonths = 6; // Allow scheduling up to 6 months in advance

    function getFormattedDate(date) {
        // Ensure we're getting the date in local timezone
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function renderCalendar(date) {
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        const startingDay = firstDay.getDay();
        const monthLength = lastDay.getDate();

        // Update month label
        document.getElementById('currentMonthLabel').textContent = 
            new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(date);

        let calendarHtml = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sun</th><th>Mon</th><Tue</th><Wed</th><Thu</th><Fri</th><Sat</th>
                    </tr>
                </thead>
                <tbody>
        `;

        let day = 1;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Create calendar rows
        for (let i = 0; i < 6; i++) {
            let row = '<tr>';
            for (let j = 0; j < 7; j++) {
                if (i === 0 && j < startingDay) {
                    row += '<td></td>';
                } else if (day > monthLength) {
                    row += '<td></td>';
                } else {
                    const currentDate = new Date(date.getFullYear(), date.getMonth(), day);
                    const dateStr = getFormattedDate(currentDate);  // Use the new function instead of toISOString()
                    const isUnavailable = unavailableDates.includes(dateStr);
                    const isPast = currentDate < today;

                    if (isPast) {
                        row += `<td class="text-muted">${day}</td>`;
                    } else {
                        row += `
                            <td class="text-center ${isUnavailable ? 'unavailable-day' : ''}">
                                <div class="day-cell">
                                    <input type="checkbox" 
                                        id="date_${dateStr}"
                                        name="unavailable_dates[]" 
                                        value="${dateStr}"
                                        ${isUnavailable ? 'checked' : ''}>
                                    <label for="date_${dateStr}" 
                                        class="${isUnavailable ? 'unavailable' : ''}">
                                        ${day}
                                    </label>
                                </div>
                            </td>
                        `;
                    }
                    day++;
                }
            }
            row += '</tr>';
            calendarHtml += row;
            if (day > monthLength) break;
        }

        calendarHtml += '</tbody></table>';
        document.getElementById('calendar-wrapper').innerHTML = calendarHtml;
    }

    function saveUnavailableDates() {
        // Get all currently checked dates (new selections)
        const newlyCheckedDates = Array.from(document.querySelectorAll('input[name="unavailable_dates[]"]:checked'))
            .map(input => input.value);
            
        console.log('Saving dates:', newlyCheckedDates); // Debug log
        
        // Combine with existing unavailable dates that aren't shown in the current month view
        const allUnavailableDates = [...new Set([...unavailableDates, ...newlyCheckedDates])];

        fetch('{{ route('doctor.mark-unavailable') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                dates: allUnavailableDates,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone // Send timezone info
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the unavailableDates array with the new complete set
                unavailableDates.length = 0;
                unavailableDates.push(...allUnavailableDates);
                
                // Update UI
                document.querySelectorAll('.day-cell').forEach(cell => {
                    const input = cell.querySelector('input[type="checkbox"]');
                    const label = cell.querySelector('label');
                    const td = cell.closest('td');
                    
                    if (allUnavailableDates.includes(input.value)) {
                        input.checked = true;
                        label.classList.add('unavailable');
                        td.classList.add('unavailable-day');
                    }
                });
                
                alert('Unavailable dates saved successfully!');
            } else {
                alert(data.message || 'Error saving dates');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving dates');
        });
    }

    let currentWeekStart = new Date();
    
    function loadWeeklyAppointments(startDate) {
        const formattedDate = startDate.toISOString().split('T')[0];
        
        fetch(`/doctor/appointments/week/${formattedDate}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('weeklyAppointments').innerHTML = renderWeeklyAppointments(data);
            })
            .catch(error => console.error('Error loading appointments:', error));
    }

    function renderWeeklyAppointments(weekData) {
        let html = '';
        
        Object.entries(weekData).forEach(([date, appointments]) => {
            html += `
                <div class="day-section mb-4">
                    <h6>${new Date(date).toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' })}</h6>
                    ${appointments.length > 0 
                        ? appointments.map(appointment => `
                            <div class="time-slot status-${appointment.status}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <strong>${appointment.time}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <div>${appointment.patient.name}</div>
                                        
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge badge-${appointment.status}">${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}</span>
                                    </div>
                                    <div class="col-md-3 btn-container">
                                        <a href="{{ route('appointment.details', '') }}/${appointment.appointment_id}" 
                                           class="btn btn-sm btn-view-details">
                                            View
                                        </a>
                                        <button class="btn btn-sm btn-view-details bg-success text-white" 
                                            onclick="updateStatus('${appointment.appointment_id}', 'confirmed')">
                                            Approve
                                        </button>
                                        <button class="btn btn-sm btn-view-details bg-danger text-white" 
                                            onclick="updateStatus('${appointment.appointment_id}', 'cancelled')">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')
                        : '<p class="text-muted">No appointments scheduled</p>'
                    }
                </div>
            `;
        });
        
        return html;
    }

    // Add this function after your existing functions
    function updateStatus(appointmentId, status) {
        if (!confirm(`Are you sure you want to ${status} this appointment?`)) {
            return;
        }

        fetch(`/appointments/${appointmentId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the weekly appointments view
                loadWeeklyAppointments(currentWeekStart);
                alert('Appointment status updated successfully!');
            } else {
                alert(data.message || 'Error updating appointment status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating appointment status');
        });
    }

</script>

<style>
// Remove FullCalendar styles and keep only the simple calendar styles
.simple-calendar {
    width: 100%;
    margin-top: 20px;
}

.day-cell {
    position: relative;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.day-cell input[type="checkbox"] {
    position: absolute;
    opacity: 0;
}

.day-cell label {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    margin: 0;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
}

.day-cell label.unavailable,
.day-cell input[type="checkbox"]:checked + label {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
}

.unavailable-day {
    background-color: rgba(220, 53, 69, 0.1);
}

.table td {
    height: 60px;
    padding: 5px !important;
}

#prevMonth, #nextMonth {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>



<!-- Add flash message display -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<style>
.fc-event {
    cursor: pointer;
}
#calendar {
    margin-bottom: 20px;
}
.unavailable-date {
    opacity: 0.6;
}
.calendar-tooltip {
    background: #f8f9fa;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
    color: #666;
    font-size: 0.9em;
    text-align: center;
    border: 1px dashed #ddd;
}

.fc-day-grid-event {
    cursor: pointer;
}

.fc-day {
    cursor: pointer;
}

.fc-day.fc-past {
    cursor: not-allowed;
    opacity: 0.7;
}

.unavailable-date {
    opacity: 0.8 !important;
}

.simple-calendar {
    width: 100%;
    margin-top: 20px;
}

.day-cell {
    position: relative;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.day-cell input[type="checkbox"] {
    position: absolute;
    opacity: 0;
}

.day-cell label {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    margin: 0;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
}

.day-cell label.unavailable,
.day-cell input[type="checkbox"]:checked + label {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
}

.unavailable-day {
    background-color: rgba(220, 53, 69, 0.1);
}

.table td {
    height: 60px;
    padding: 5px !important;
}

// Remove any conflicting styles

.unavailable-day {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.day-cell label.unavailable {
    background-color: #dc3545 !important;
    color: white !important;
    font-weight: bold !important;
}

#prevMonth, #nextMonth {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

</style>

@endsection


