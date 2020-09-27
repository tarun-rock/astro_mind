<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li>
                    <!-- User Profile-->
                    <div class="user-profile dropdown m-t-20">
                        <div class="user-pic">
                            <img src="{{ asset('back/assets/images/users/default.jpg') }}" alt="users" class="rounded-circle img-fluid" />
                        </div>
                        <div class="user-content hide-menu m-t-10">
                            <h5 class="m-b-10 user-name font-medium"></h5>
                            {{--<a href="javascript:void(0)" class="btn btn-circle btn-sm m-r-5" id="Userdd" role="button" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="false">
                                <i class="ti-settings"></i>
                            </a>--}}
                            {{-- <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" title="Logout" class="btn btn-circle btn-sm">
                                <i class="ti-power-off"></i>
                            </a> --}}

                            <form id="logout-form" action="{{-- {{ route('logout') }} --}}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <div class="dropdown-menu animated flipInY" aria-labelledby="Userdd">
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="ti-user m-r-5 m-l-5"></i> My Profile</a>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="ti-wallet m-r-5 m-l-5"></i> My Balance</a>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="ti-email m-r-5 m-l-5"></i> Inbox</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="ti-settings m-r-5 m-l-5"></i> Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)">
                                    <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                            </div>
                        </div>
                    </div>
                    <!-- End User Profile-->
                </li>
                <!-- User Profile-->
                <li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">Personal</span>
                </li>
                

                

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="#"
                       aria-expanded="false">
                        <i class="icon-Car-Wheel"></i>
                        <span class="hide-menu"> Dashboard</span>
                    </a>
                </li>


                <li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">Content</span>
                </li>


                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('contact') }}"
                       aria-expanded="false">
                        <i class="icon-Eye"></i>
                        <span class="hide-menu">Contact Us</span>

                    </a>
                    {{--<a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.prediction.match") }}"
                       aria-expanded="false">
                        <i class="icon-Aim"></i>
                        <span class="hide-menu"> Add Contest </span>

                    </a>--}}
                </li>

                    {{-- <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('category') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">ADD Category</span>

                        </a>
                    </li> --}}

                    <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('view-category') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">View Category</span>

                        </a>
                    </li>

                     <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('view-tag') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">View Tag</span>

                        </a>
                    </li>

                    <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('post') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">View Post</span>

                        </a>
                    </li>

                    <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('view-product') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">View Product</span>

                        </a>
                    </li>

                    <li class="sidebar-item">

                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('view-prodCategory') }}"
                           aria-expanded="false">
                            <i class="icon-Eye"></i>
                            <span class="hide-menu">Product Category</span>

                        </a>
                    </li>


                {{--<li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">General</span>
                </li>--}}

            

             {{--   <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("partner.statistics") }}"
                       aria-expanded="false">
                        <i class="icon-Statistic"></i>
                        <span class="hide-menu"> Statistics</span>
                    </a>
                </li>
                --}}
                



                {{--<li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("partner.leaderboard") }}"
                       aria-expanded="false">
                        <i class="icon-Receipt-3"></i>
                        <span class="hide-menu"> Leaderboard</span>
                    </a>
                </li>--}}


               {{-- <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.countries") }}"
                       aria-expanded="false">
                        <i class="icon-Flag"></i>
                        <span class="hide-menu"> Countries</span>
                    </a>
                </li>--}}

                {{--<li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.partners") }}"
                       aria-expanded="false">
                        <i class="icon-Cool-Guy"></i>
                        <span class="hide-menu"> Partners</span>
                    </a>
                </li>

                    <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.view.users") }}"
                       aria-expanded="false">
                        <i class="icon-Cool-Guy"></i>
                        <span class="hide-menu"> Users</span>
                    </a>
                </li>--}}

             {{--   <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.package.types") }}"
                       aria-expanded="false">
                        <i class="icon-Two-Windows"></i>
                        <span class="hide-menu"> Package Type</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.currencies") }}"
                       aria-expanded="false">
                        <i class="icon-Money"></i>
                        <span class="hide-menu"> Currency </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.languages") }}"
                       aria-expanded="false">
                        <i class="icon-Loudspeaker"></i>
                        <span class="hide-menu"> Language </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route("admin.partner.module") }}"
                       aria-expanded="false">
                        <i class="icon-Money"></i>
                        <span class="hide-menu"> Partner Modules </span>
                    </a>
                </li>--}}

                {{--<li class="nav-small-cap">
                    <i class="mdi mdi-dots-horizontal"></i>
                    <span class="hide-menu">Apps</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="icon-Mailbox-Empty"></i>
                        <span class="hide-menu">Inbox </span>
                    </a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item">
                            <a href="inbox-email.html" class="sidebar-link">
                                <i class="mdi mdi-email"></i>
                                <span class="hide-menu"> Email </span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="inbox-email-detail.html" class="sidebar-link">
                                <i class="mdi mdi-email-alert"></i>
                                <span class="hide-menu"> Email Detail </span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="inbox-email-compose.html" class="sidebar-link">
                                <i class="mdi mdi-email-secure"></i>
                                <span class="hide-menu"> Email Compose </span>
                            </a>
                        </li>
                    </ul>
                </li>--}}
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>