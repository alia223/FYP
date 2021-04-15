<a id="home" href="{{ url('home') }}">Home</a>
@if(Auth::check())
	@if (!Gate::denies('admin') && Gate::denies('clubstaff'))
		<a id="bookings" href="{{ url('bookings') }}?ym=<?php echo date('Y-m');?>">Bookings</a>
		<a id="past_bookings" href="{{ url('past-bookings') }}">Past Bookings</a>
		<a id="staff-availability" href="{{ url('staff-availability') }}">Club Staff Availability</a>
		<a id="staff-schedule" href="{{ url('staff-schedule') }}">Club Staff Schedule</a>
		<a id="activity_log" href="{{ url('activity-log') }}">Activity Log</a>
		<a id="control_panel" href="{{ url('control-panel') }}">Control Panel</a>
	@elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
		<a id="bookings" href="{{ url('bookings') }}?ym=<?php echo date('Y-m');?>">Bookings</a>
		<a id="past_bookings" href="{{ url('past-bookings') }}">Past Bookings</a>
		<a id="students" href="{{ url('students') }}">Children</a>
	@elseif (Gate::denies('admin') && !Gate::denies('clubstaff')) 
		<a id="register" href="{{ url('student-register') }}">Attendance Register</a>
		<a id="students" href="{{ url('students') }}">Club Students</a>
		<a id="staff-availability" href="{{ url('staff-availability') }}">Club Staff Availability</a>
		<a id="staff-schedule" href="{{ url('staff-schedule') }}">Club Staff Schedule</a>
	@endif
@endif
<a id="settings" href="{{ url('settings') }}">Settings</a>
