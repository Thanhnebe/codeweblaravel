<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{asset('images/logo_admin.png')}}" alt="" height="67px">
            </span>
            <span class="logo-lg">
                <img src="{{asset('images/logo_admin.png')}}" alt="" height="62px">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{asset('images/logo_admin.png')}}" alt="" height="67px">
            </span>
            <span class="logo-lg">
                <img src="{{asset('images/logo_admin.png')}}" alt="" height="62px">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Bảng điều khiển</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('orders.index') }}">
                        <i class="ri-handbag-fill"></i> <span data-key="t-dashboards">Đơn hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('sliders.index') }}">
                        <i class="ri-image-line"></i> <span data-key="t-sliders">Quản lý Slider</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="{{ in_array(Route::currentRouteName(), ['categories.listCategories' , 'categories.createCategories', 'categories.editCategories', 'products.index', 'products.create', 'products.edit', 'variants.index', 'variants.create', 'variants.edit', 'weights.index', 'weights.create', 'weights.edit']) ? 'true' : 'false' }}" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i> <span data-key="t-apps">Sản phẩm</span>
                    </a>
                    <div class="collapse menu-dropdown {{ in_array(Route::currentRouteName(), ['categories.listCategories' , 'categories.createCategories', 'categories.editCategories', 'products.index', 'products.create', 'products.edit','weights.index', 'weights.create', 'weights.edit']) ? 'show' : '' }}" id="sidebarApps">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('categories.listCategories') }}" style="color: {{ in_array(Route::currentRouteName(), ['categories.listCategories', 'categories.createCategories', 'categories.editCategories']) ? '#fff' : '' }}" class="nav-link" data-key="t-chat"> Danh mục </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('products.index') }}" style="color: {{ in_array(Route::currentRouteName(), ['products.index', 'products.create', 'products.edit']) ? '#fff' : '' }}" class="nav-link" data-key="t-chat"> Sản phẩm </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('weights.index') }}" style="color: {{ in_array(Route::currentRouteName(), ['weights.index', 'weights.create', 'weights.edit']) ? '#fff' : '' }}" class="nav-link" data-key="t-chat"> Trọng lượng </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
