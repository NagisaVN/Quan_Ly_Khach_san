<!-- Top Navigation Bar -->
<nav class="app-header navbar navbar-expand bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <ul class="navbar-nav ms-auto">
            <!-- Branch Switcher -->
            @if(auth()->check() && auth()->user()->branches()->count() > 1)
            <li class="nav-item dropdown me-2">
                <a class="nav-link dropdown-toggle" href="#" id="branchDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-code-branch me-2"></i>
                    {{ session('current_branch')?->name ?? 'Chọn chi nhánh' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="branchDropdown">
                    @foreach(auth()->user()->branches as $branch)
                        <li>
                            <form action="{{ route('branch.switch') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                <button type="submit" class="dropdown-item">
                                    {{ $branch->name }} ({{ $branch->code }})
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endif
            
            <!-- Notifications -->
            <li class="nav-item dropdown me-2">
                <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        0
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;" aria-labelledby="notificationsDropdown">
                    <li class="dropdown-header">Thông báo</li>
                    <li><a class="dropdown-item text-center small text-muted" href="#">Chưa có thông báo</a></li>
                </ul>
            </li>
            
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-2"></i>
                    {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-cog me-2"></i>Cài đặt</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
