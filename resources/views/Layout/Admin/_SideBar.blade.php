            <div class="main-nav d-flex flex-column h-100">
                <!-- Sidebar Logo -->
                <div class="logo-box">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center justify-content-center py-3">
                        <img
                            src="{{ asset('build/assets/images/homework-konveksi.png') }}"
                            alt="logo"
                            class="rounded-circle"
                            style="height: 150px; width: 150px; object-fit: cover;"
                        />
                    </a>
                </div>


                <div class="scrollbar flex-grow-1" data-simplebar>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title">General</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:home-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Dashboard </span>
                            </a>
                            <a class="nav-link" href="{{ route('team') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:face-with-open-mouth-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Team </span>
                            </a>                            
                        </li>

                        <li class="menu-title">Order</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('order.create') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Create Order </span>
                            </a>
                        </li>                      

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('order') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Orders </span>
                            </a>
                        </li>     

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Customers </span>
                            </a>
                        </li>  


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supplier') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Suppliers </span>
                            </a>
                        </li>                          

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:profile-circle-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Contacts </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invoice') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:invoice-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Invoices </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-3 border-top">
                    <button
                        type="button"
                        class="btn btn-danger w-100 rounded-pill"
                        id="logout-swal"
                    >
                        Logout
                    </button>
                </div>
            </div>
