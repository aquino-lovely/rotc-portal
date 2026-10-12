<div class="sidebar">
  @if(auth()->user()->role === 'super_admin')
    <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.index') ? 'active' : '' }}"><span class="icon">&#128101;</span> Staff Accounts</a>
    <a href="{{ route('staff.create') }}" class="{{ request()->routeIs('staff.create') ? 'active' : '' }}"><span class="icon">&#10133;</span> Create Account</a>
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="icon">&#128196;</span> Reports</a>
    <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
@elseif(auth()->user()->role === 'cadet')
        <a href="{{ route('dashboard') }}"><span class="icon">&#9638;</span> Dashboard</a>
        <a href="{{ route('cadet.profile') }}"><span class="icon">&#128100;</span> My Profile</a>
        <a href="{{ route('cadet.enrollment') }}"><span class="icon">&#127891;</span> Enrollment</a>
        <a href="{{ route('cadet.attendance') }}"><span class="icon">&#9999;</span> Attendance</a>
        <a href="{{ route('cadet.grades') }}"><span class="icon">&#128202;</span> Grades</a>
        <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
   @elseif(auth()->user()->role === 'GMA')
    <a href="{{ route('gma.dashboard') }}" class="{{ request()->routeIs('gma.dashboard') ? 'active' : '' }}"><span class="icon">&#9638;</span> Dashboard</a>
    <a href="{{ route('admin.performance.index') }}" class="{{ request()->routeIs('admin.performance.*') ? 'active' : '' }}"><span class="icon">&#128202;</span> Performance</a>
     <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="icon">&#128196;</span> Reports</a>
    <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
   
    @elseif(auth()->user()->role === 'S1')
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="icon">&#9638;</span> Dashboard</a>
    <a href="{{ route('admin.cadets.index') }}" class="{{ request()->routeIs('admin.cadets.*') ? 'active' : '' }}"><span class="icon">&#128100;</span> Cadet Profile</a>
    <a href="{{ route('admin.enrollment.index') }}" class="{{ request()->routeIs('admin.enrollment.*') ? 'active' : '' }}"><span class="icon">&#127891;</span> Enrollment</a>
    <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"><span class="icon">&#9999;</span> Attendance</a>
    <a href="{{ route('admin.qr.index') }}" class="{{ request()->routeIs('admin.qr.*') ? 'active' : '' }}"><span class="icon">&#9783;</span> QR Attendance</a>
    <a href="{{ route('admin.performance.index') }}" class="{{ request()->routeIs('admin.performance.*') ? 'active' : '' }}"><span class="icon">&#128202;</span> Performance</a>
    <a href="{{ route('admin.serial.index') }}" class="{{ request()->routeIs('admin.serial.*') ? 'active' : '' }}"><span class="icon">&#127381;</span> Serial Numbers</a>
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="icon">&#128196;</span> Reports</a>
    <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
    @elseif(auth()->user()->role === 'deputy_s1')
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="icon">&#9638;</span> Dashboard</a>
    <a href="{{ route('admin.cadets.index') }}" class="{{ request()->routeIs('admin.cadets.*') ? 'active' : '' }}"><span class="icon">&#128100;</span> Cadet Profile</a>
    <a href="{{ route('admin.enrollment.index') }}" class="{{ request()->routeIs('admin.enrollment.*') ? 'active' : '' }}"><span class="icon">&#127891;</span> Enrollment</a>
    <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"><span class="icon">&#9999;</span> Attendance</a>
    <a href="{{ route('admin.qr.index') }}" class="{{ request()->routeIs('admin.qr.*') ? 'active' : '' }}"><span class="icon">&#9783;</span> QR Attendance</a>
    <a href="{{ route('admin.performance.index') }}" class="{{ request()->routeIs('admin.performance.*') ? 'active' : '' }}"><span class="icon">&#128202;</span> Performance</a>
   <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="icon">&#128196;</span> Reports</a>
    <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
   
    @else
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="icon">&#9638;</span> Dashboard</a>
        <a href="{{ route('admin.cadets.index') }}" class="{{ request()->routeIs('admin.cadets.*') ? 'active' : '' }}"><span class="icon">&#128100;</span> Cadet Profile</a>
        <a href="{{ route('admin.enrollment.index') }}" class="{{ request()->routeIs('admin.enrollment.*') ? 'active' : '' }}"><span class="icon">&#127891;</span> Enrollment</a>
        <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"><span class="icon">&#9999;</span> Attendance</a>
        <a href="{{ route('admin.qr.index') }}" class="{{ request()->routeIs('admin.qr.*') ? 'active' : '' }}"><span class="icon">&#9783;</span> QR Attendance</a>
        <a href="{{ route('admin.performance.index') }}" class="{{ request()->routeIs('admin.performance.*') ? 'active' : '' }}"><span class="icon">&#128202;</span> Performance</a>
        <a href="{{ route('admin.serial.index') }}" class="{{ request()->routeIs('admin.serial.*') ? 'active' : '' }}"><span class="icon">&#127381;</span> Serial Numbers</a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span class="icon">&#128196;</span> Reports</a>
        <a href="{{ route('password.change') }}" class="{{ request()->routeIs('password.change') ? 'active' : '' }}"><span class="icon">&#128274;</span> Change Password</a>
    @endif
</div>