<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Asset Management</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('img/' . Auth::user()->roles[0]->name.'.png') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block text-truncate" style="max-width: 150px">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                @role('admin')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endrole

                <li class="nav-header">SUBMISSION</li>
                <li class="nav-item">
                    <a href="{{ route('submission.index') }}" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>Submission Form</p>
                    </a>
                </li>

                <li class="nav-header">ASSET DATA</li>
                <li class="nav-item">
                    <a href="{{ route('asset.physical.index') }}" class="nav-link">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Physical Asset</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('asset.license.index') }}" class="nav-link">
                        <i class="fas fa-list nav-icon"></i>
                        <p>License Asset</p>
                    </a>
                </li>

                @role('staff')
                    <li class="nav-header">ACCOUNT</li>
                    <li class="nav-item">
                        <a href="{{ route('my-account.show') }}" class="nav-link">
                            <i class="fas fa-user nav-icon"></i>
                            <p>My Account</p>
                        </a>
                    </li>
                @endrole

                @role('admin')
                    <li class="nav-header">MASTER DATA</li>
                    <li class="nav-item">
                        <a href="{{ route('master.manufacture.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>
                                Manufacture
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.brand.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-tag"></i>
                            <p>
                                Brand
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.category.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>
                                Category Asset
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.division.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>
                                Division Staff
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('master.user.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                @endrole
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
