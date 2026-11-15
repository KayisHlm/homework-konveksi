            <div class="main-nav d-flex flex-column h-100">
                <!-- Sidebar Logo -->
                <div class="logo-box">
                    <a href="{{ auth()->user()->homeRoute() }}" class="d-flex align-items-center justify-content-center py-3">
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
                        @if (auth()->user()->isPemilik())
                        <li class="menu-title">Pemilik</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:home-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Dashboard </span>
                            </a>
                            <a class="nav-link" href="{{ route('report') }}">
                                <span class="nav-icon">
                                    <i class="bx bx-printer"></i>
                                </span>
                                <span class="nav-text"> Rekap Keuangan </span>
                            </a>
                            <a class="nav-link" href="{{ route('team') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:face-with-open-mouth-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Tim </span>
                            </a>
                            <a class="nav-link" href="{{ route('expense') }}">
                                <span class="nav-icon">
                                    <i class="bx bx-wallet-alt"></i>
                                </span>
                                <span class="nav-text"> Pengeluaran </span>
                            </a>
                        </li>
                        @endif

                        <li class="menu-title">Operasional</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('order.create') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Buat Pesanan </span>
                            </a>
                        </li>                      

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('order') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Pesanan </span>
                            </a>
                        </li>     

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('procurement') }}">
                                <span class="nav-icon">
                                    <iconify-icon icon="iconamoon:delivery-fast-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Pengadaan </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Pelanggan </span>
                            </a>
                        </li>  


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('supplier') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:shopping-bag-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Pemasok </span>
                            </a>
                        </li>                          

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invoice') }}">
                                <span class="nav-icon">
                                    <iconify-icon
                                        icon="iconamoon:invoice-duotone"
                                    ></iconify-icon>
                                </span>
                                <span class="nav-text"> Invoice </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-3 border-top">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
