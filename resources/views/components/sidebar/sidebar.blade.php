@role('admin')
    <!-- Sidenav Menu Heading (Core)-->
    <div class="sidenav-menu-heading">Core</div>
    <!-- Sidenav Accordion (Dashboard)-->
    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseDashboards"
        aria-expanded="false" aria-controls="collapseDashboards">
        <div class="nav-link-icon"><i data-feather="activity"></i></div>
        Users
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>
    <div class="collapse" id="collapseDashboards" data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link" href="{{ route('admin.users.create') }}">Create User</a>
            <a class="nav-link" href="{{ route('admin.users.index') }}">View Users</a>
        </nav>
    </div>
@endrole
