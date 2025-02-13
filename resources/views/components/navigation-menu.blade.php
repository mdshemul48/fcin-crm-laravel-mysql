<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-people-fill me-1"></i>Clients Management
    </a>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('clients.index') }}">
                <i class="bi bi-person-lines-fill me-2"></i>Clients
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('packages.index') }}">
                <i class="bi bi-box-seam me-2"></i>Packages
            </a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="bi bi-diagram-3 me-1"></i>Sub Reseller
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="bi bi-cash-stack me-1"></i>Expenses
    </a>
</li>
<li>
    <a class="nav-link" href="{{ route('sms.settings') }}">
        <i class="bi bi-gear me-2"></i>SMS Settings
    </a>
</li>
