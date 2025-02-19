<li class="sidebar-item">
    <a href="#clientSubmenu" class="sidebar-link" data-bs-toggle="collapse">
        <i class="bi bi-people-fill"></i>
        <span>Client Management</span>
    </a>
    <div class="collapse" id="clientSubmenu">
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="{{ route('clients.index') }}" class="sidebar-link">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Clients</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('packages.index') }}" class="sidebar-link">
                    <i class="bi bi-box-seam"></i>
                    <span>Packages</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="sidebar-item">
    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i>
        <span>Expenses</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="{{ route('sms.settings') }}" class="sidebar-link">
        <i class="bi bi-gear"></i>
        <span>SMS Settings</span>
    </a>
</li>
