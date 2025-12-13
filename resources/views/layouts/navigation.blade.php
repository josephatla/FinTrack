<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
        <i class="fas fa-chart-line me-2"></i> FinTrack
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold text-primary' : '' }}" 
             href="{{ route('dashboard') }}">
            <i class="fas fa-home me-1"></i> {{ __('dashboard.dashboard') }}
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('transactions.index') ? 'active fw-bold text-primary' : '' }}" 
             href="{{ route('transactions.index') }}">
            <i class="fas fa-history me-1"></i> {{ __('dashboard.history') }}
          </a>
        </li>
        
        <li class="nav-item dropdown me-2">
          <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-globe me-1"></i> {{ strtoupper(app()->getLocale()) }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
            <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
            <li><a class="dropdown-item" href="{{ route('lang.switch', 'id') }}">Indonesian</a></li>
          </ul>
        </li>
        
        <li class="nav-item dropdown me-2">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            
            <li>
              <a class="dropdown-item" href="{{ route('accounts.index') }}">
                <i class="fas fa-wallet me-2"></i> {{ __('dashboard.wallets') }} 
              </a>
            </li>
            
            <li>
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="fas fa-user-cog me-2"></i> Profile
              </a>
            </li>
            
            <li><hr class="dropdown-divider"></li>
            
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
              </form>
            </li>
          </ul>
        </li>
        
        @if (!Auth::user()->isPremium())
            <li class="nav-item">
                <a class="btn btn-primary btn-sm fw-bold px-3" 
                   href="{{ route('pricing') }}">
                    <i class="fas fa-crown me-1"></i> {{ __('messages.upgrade_now') }}
                </a>
            </li>
        @endif
      </ul>
    </div>
  </div>
</nav>