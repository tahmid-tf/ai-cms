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
        Content Generation
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai.*') ? 'show' : '' }}" id="collapseAI" data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link {{ request()->routeIs('ai.content') ? 'active' : '' }}" href="{{ route('ai.content') }}">
                Content Generation
            </a>
            <a class="nav-link {{ request()->routeIs('ai.content.list') ? 'active' : '' }}"
                href="{{ route('ai.content.list') }}">
                Content List
            </a>
        </nav>
    </div>

    <!-- Sidenav Accordion (Dashboard)-->
    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAIEdit"
        aria-expanded="{{ request()->routeIs('ai_editor.*') ? 'true' : 'false' }}" aria-controls="collapseAIEdit">
        <div class="nav-link-icon"><i data-feather="activity"></i></div>
        Content Edit
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai_editor.*') ? 'show' : '' }}" id="collapseAIEdit"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link {{ request()->routeIs('ai_editor.editor') ? 'active' : '' }}"
                href="{{ route('ai_editor.editor') }}">
                Content Edit
            </a>
            <a class="nav-link {{ request()->routeIs('ai_editor.list') ? 'active' : '' }}"
                href="{{ route('ai_editor.list') }}">
                Content Edit List
            </a>
        </nav>
    </div>
@endrole
