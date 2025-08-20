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
    <a href="#resellerSubmenu" class="sidebar-link" data-bs-toggle="collapse">
        <i class="bi bi-diagram-3-fill"></i>
        <span>Reseller Management</span>
    </a>
    <div class="collapse" id="resellerSubmenu">
        <ul class="sidebar-nav">
            <li class="sidebar-item">
                <a href="{{ route('resellers.index') }}" class="sidebar-link">
                    <i class="bi bi-people"></i>
                    <span>Resellers</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('reseller-recharges.index') }}" class="sidebar-link">
                    <i class="bi bi-currency-exchange"></i>
                    <span>Recharges</span>
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
    <a href="{{ route('transactions.index') }}"
        class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i>
        <span>Money Transfer</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="{{ route('payment-reports.index') }}"
        class="sidebar-link {{ request()->routeIs('payment-reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i>
        <span>Payment Reports</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="{{ route('sms.settings') }}" class="sidebar-link">
        <i class="bi bi-gear"></i>
        <span>SMS Settings</span>
    </a>
</li>
