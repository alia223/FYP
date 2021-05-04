<div class="col-md-2">
	<div class="sidebar">
		<a id="home" href="{{ url('home') }}">Home</a>
		@if(Auth::check())
			@if (!Gate::denies('admin') && Gate::denies('clubstaff'))
				<a id="admin_bookings" href="{{ url('bookings') }}">Bookings</a>
				<a id="staff_schedule" href="{{ url('staff-schedule') }}">Club Staff Schedule</a>
				<a id="activity_log" href="{{ url('activity-log') }}">Activity Log</a>
				<a id="control_panel" href="{{ url('rules') }}">Control Panel</a>
			@elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
				<a id="clubstaff_bookings" href="{{ url('bookings') }}?ym=<?php echo date('Y-m');?>">Bookings</a>
				<a id="pupils" href="{{ url('pupils') }}">Children</a>
				<a id="injuries" href="{{ url('injuries') }}">Injuries</a>
			@elseif (Gate::denies('admin') && !Gate::denies('clubstaff')) 
				<a id="parent_bookings" href="{{ url('bookings') }}">Bookings</a>
				<a id="register" href="{{ url('pupil-register') }}">Club Attendance Register</a>
				<a id="registered_club_pupils" href="{{ url('pupils') }}">Registered Club Pupils</a>
				<a id="staff_availability" href="{{ url('staff-availability') }}">Club Staff Availability</a>
				<a id="staff_schedule" href="{{ url('staff-schedule') }}">Club Staff Schedule</a>
			@endif
		@endif
		<a id="settings" href="{{ url('settings') }}">Settings</a>            
	</div>
</div>

