@if(extension('PterodactylSSO')->config('enabled'))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Server Management</h5>
        </div>
        <div class="card-body">
            <a href="{{ route('pterodactyl-sso.login') }}" class="btn btn-primary">
                <i class="fas fa-server me-2"></i>
                Manage Server
            </a>
        </div>
    </div>
@endif
