@role('admin')
    <!-- Sidenav Menu Heading (Core)-->
    <div class="sidenav-menu-heading">Core</div>

    <!-- Sidenav Accordion (Dashboard)-->
    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseDashboards"
        aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" aria-controls="collapseDashboards">
        <div class="nav-link-icon"><i data-feather="activity"></i></div>
        Users
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="collapseDashboards"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}"
                href="{{ route('admin.users.create') }}">
                Create User
            </a>
            <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                href="{{ route('admin.users.index') }}">
                View Users
            </a>
        </nav>
    </div>


    <!-- Sidenav Accordion (Dashboard)-->
    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAI"
        aria-expanded="{{ request()->routeIs('ai.*') ? 'true' : 'false' }}" aria-controls="collapseAI">
        <div class="nav-link-icon"><i data-feather="activity"></i></div>
        AI Section
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai.*') ? 'show' : '' }}" id="collapseAI" data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link {{ request()->routeIs('ai.content') ? 'active' : '' }}" href="{{ route('ai.content') }}">
                AI Content Generate
            </a>
        </nav>
    </div>
@endrole
