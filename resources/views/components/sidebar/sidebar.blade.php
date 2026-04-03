@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin');
    $canEditRecords = $user?->hasAnyRole(['admin', 'editor']);
    $canViewLists = $user?->hasAnyRole(['admin', 'editor', 'viewer']);
@endphp

@if ($canViewLists)
    <div class="sidenav-menu-heading">Core</div>

    <a class="nav-link" href="{{ route('dashboard') }}">
        <div class="nav-link-icon"><i data-feather="bar-chart"></i></div>
        Dashboard
    </a>

    @if ($isAdmin)
        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
            data-bs-target="#collapseDashboards"
            aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}"
            aria-controls="collapseDashboards">
            <div class="nav-link-icon"><i data-feather="users"></i></div>
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
    @endif

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseAI"
        aria-expanded="{{ request()->routeIs('ai.*') ? 'true' : 'false' }}" aria-controls="collapseAI">
        <div class="nav-link-icon"><i data-feather="file-text"></i></div>
        Content Generation
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai.*') ? 'show' : '' }}" id="collapseAI"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            @if ($isAdmin)
                <a class="nav-link {{ request()->routeIs('ai.content') ? 'active' : '' }}"
                    href="{{ route('ai.content') }}">
                    Content Generation
                </a>
            @endif
            <a class="nav-link {{ request()->routeIs('ai.content.list') ? 'active' : '' }}"
                href="{{ route('ai.content.list') }}">
                Content List
            </a>
        </nav>
    </div>

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
        data-bs-target="#collapseAIEdit" aria-expanded="{{ request()->routeIs('ai_editor.*') ? 'true' : 'false' }}"
        aria-controls="collapseAIEdit">
        <div class="nav-link-icon"><i data-feather="edit-3"></i></div>
        Content Edit
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai_editor.*') ? 'show' : '' }}" id="collapseAIEdit"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            @if ($isAdmin)
                <a class="nav-link {{ request()->routeIs('ai_editor.editor') ? 'active' : '' }}"
                    href="{{ route('ai_editor.editor') }}">
                    Content Edit
                </a>
            @endif
            <a class="nav-link {{ request()->routeIs('ai_editor.list') ? 'active' : '' }}"
                href="{{ route('ai_editor.list') }}">
                Content Edit List
            </a>
        </nav>
    </div>

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
        data-bs-target="#collapseAITranslation"
        aria-expanded="{{ request()->routeIs('ai_translation.*') ? 'true' : 'false' }}"
        aria-controls="collapseAITranslation">
        <div class="nav-link-icon"><i data-feather="languages"></i></div>
        Content Translation
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('ai_translation.*') ? 'show' : '' }}" id="collapseAITranslation"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            @if ($isAdmin)
                <a class="nav-link {{ request()->routeIs('ai_translation.index') ? 'active' : '' }}"
                    href="{{ route('ai_translation.index') }}">
                    Content Translation
                </a>
            @endif
            <a class="nav-link {{ request()->routeIs('ai_translation.list') ? 'active' : '' }}"
                href="{{ route('ai_translation.list') }}">
                Translation List
            </a>
        </nav>
    </div>

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
        data-bs-target="#collapseVersionControl"
        aria-expanded="{{ request()->routeIs('version_control.*') ? 'true' : 'false' }}"
        aria-controls="collapseVersionControl">
        <div class="nav-link-icon"><i data-feather="git-branch"></i></div>
        Version Control
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('version_control.*') ? 'show' : '' }}" id="collapseVersionControl"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            @if ($isAdmin)
                <a class="nav-link {{ request()->routeIs('version_control.index') ? 'active' : '' }}"
                    href="{{ route('version_control.index') }}">
                    Create Content
                </a>
            @endif
            <a class="nav-link {{ request()->routeIs('version_control.list') ? 'active' : '' }}"
                href="{{ route('version_control.list') }}">
                Version List
            </a>
        </nav>
    </div>

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
        data-bs-target="#collapseAnalytics" aria-expanded="{{ request()->routeIs('analytics.*') ? 'true' : 'false' }}"
        aria-controls="collapseAnalytics">
        <div class="nav-link-icon"><i data-feather="bar-chart-2"></i></div>
        Analytics & Insights
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('analytics.*') ? 'show' : '' }}" id="collapseAnalytics"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            @if ($isAdmin)
                <a class="nav-link {{ request()->routeIs('analytics.index') ? 'active' : '' }}"
                    href="{{ route('analytics.index') }}">
                    Analytics Dashboard
                </a>
            @endif
            <a class="nav-link {{ request()->routeIs('analytics.insights_list') ? 'active' : '' }}"
                href="{{ route('analytics.insights_list') }}">
                Insights List
            </a>
        </nav>
    </div>

    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
        data-bs-target="#collapseExportSharing"
        aria-expanded="{{ request()->routeIs('export_sharing.*') ? 'true' : 'false' }}"
        aria-controls="collapseExportSharing">
        <div class="nav-link-icon"><i data-feather="share-2"></i></div>
        Export & Sharing
        <div class="sidenav-collapse-arrow">
            <i class="fas fa-angle-down"></i>
        </div>
    </a>

    <div class="collapse {{ request()->routeIs('export_sharing.*') ? 'show' : '' }}" id="collapseExportSharing"
        data-bs-parent="#accordionSidenav">
        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
            <a class="nav-link {{ request()->routeIs('export_sharing.index') ? 'active' : '' }}"
                href="{{ route('export_sharing.index') }}">
                Export & Sharing
            </a>
        </nav>
    </div>
@endif
