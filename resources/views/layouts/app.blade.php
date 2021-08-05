<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>CSM reports</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="{{ asset('datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('bootstrapselect/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
        <!-- Bootstrap 3.3.4 -->
        <link rel="stylesheet" href="{{asset('bootstrap-daterangepicker-master/daterangepicker.css')}}">

        <link rel="stylesheet" href="{{asset('LTE/bootstrap/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('LTE/external/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('LTE/external/ionicons.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('LTE/dist/css/AdminLTE.css')}}">
        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
              page. However, you can choose any other skin. Make sure you
              apply the skin class to the body tag so the changes take effect.
        -->
        <link rel="stylesheet" href="{{asset('LTE/dist/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{asset('LTE/dist/css/bootstrap-rtl.min.css')}}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{asset('LTE/plugins/bootstrap-slider/slider.css')}}">

        <link rel="stylesheet" href="{{asset('LTE/plugins/datatables/dataTables.bootstrap.css')}}">
        <link rel="stylesheet" href="{{asset('LTE/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css')}}">

        <link rel="stylesheet" href="{{asset('css/styles.css')}}">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- REQUIRED JS SCRIPTS -->

        <!-- jQuery 2.1.4 -->
        <script src="{{asset('LTE/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>

        <script src="{{asset('js/jquery.hotkeys.js')}}"></script>

        <script src="{{asset('moment/moment.js')}}"></script>
        <script src="{{asset('moment/moment-with-locales.js')}}"></script>

        <script src="{{asset('bootstrap-daterangepicker-master/daterangepicker.js')}}"></script>
        <!-- Bootstrap 3.3.4 -->
        <script src="{{asset('LTE/plugins/bootstrap-slider/bootstrap-slider.js')}}"></script>

        <script src="{{asset('LTE/bootstrap/js/bootstrap.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('LTE/dist/js/app.min.js')}}"></script>

        <script src="{{asset('datepicker/js/bootstrap-datepicker.min.js')}}"></script>

        <script src="{{asset('bootstrapselect/dist/js/bootstrap-select.min.js')}}"></script>

        <script src="{{asset('highcharts/js/highcharts.js')}}"></script>

        <script src="{{asset('highcharts/js/modules/exporting.js')}}"></script>

        <script src="{{asset('LTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('LTE/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
        <script src="{{asset('LTE/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
        <!-- SlimScroll -->
        <script src="{{asset('LTE/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{asset('LTE/plugins/fastclick/fastclick.min.js')}}"></script>

        <script src="{{asset('js/scripts.js')}}"></script>
        <!-- Optionally, you can add Slimscroll and FastClick plugins.
             Both of these plugins are recommended to enhance the
             user experience. Slimscroll is required when using the
             fixed layout. -->
    </head>
    <!--
    BODY TAG OPTIONS:
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <body class="skin-blue layout-top-nav">
        <div class="full-loader" id="LOADER" style="display: none;"><img src="{{asset("img/ring.gif")}}"/></div>
        <div class="wrapper">

            <!-- Main Header -->
            <header class="main-header">
                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top">
                    <div>
                        <div class="navbar-header">
                            <!-- Logo -->
                            <a href="{{url('/')}}" class="logo">
                                <!-- mini logo for sidebar mini 50x50 pixels -->
                                <span class="logo-mini"><b>CSM</b></span>
                                <!-- logo for regular state and mobile devices -->
                                <span class="logo-lg"><b>CSM</b>reports {{Session::get('current_year')}}</span>
                            </a>
                        </div>
                        <!-- Navbar Right Menu -->
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">                            
                                <!-- User Account Menu -->
                                <li class="dropdown user user-menu">
                                    <!-- Menu Toggle Button -->
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                        <span class="hidden-xs">{{Auth::user()->name}}</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!-- Menu Footer-->
                                        <li class="user-footer">
                                            <a href="{{url('logout')}}" class="btn btn-default btn-flat">تسجيل الخروج</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <!-- Optionally, you can add icons to the links -->
                                <li class="{{Request::segment(1) == ""?" active":""}}"><a href="<?php echo url('/'); ?>"><i class="fa fa-dashboard"></i><span>بيانات</span></a></li>
                                <li class="{{Request::segment(1) == "monthly_reports"?" active":""}}"><a href="<?php echo url('monthly_reports/bulk_create'); ?>"><i class="fa fa-newspaper-o"></i><span>جداول شهرية</span></a></li>
                                <li class="{{Request::segment(1) == "manage_judgement"?" active":""}}"><a href="<?php echo url('manage_judgement/bulk_create'); ?>"><i class="fa fa-newspaper-o"></i><span>جداول تفصيلية</span></a></li>
                                <li class="dropdown{{in_array(Request::segment(1), ['manage_judges','manage_courts','manage_judge_court','manage_court_fields','manage_configs'])?" active":""}}">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gears"></i> <span>إعدادات</span> <i class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li class="{{Request::segment(1) == "manage_judges"?" active":""}}"><a href="<?php echo url('manage_judges/create'); ?>">القضاة</a></li>
                                        <li class="{{Request::segment(1) == "manage_courts"?" active":""}}"><a href="<?php echo url('manage_courts/create'); ?>">المحاكم</a></li> 
                                        <li class="{{Request::segment(1) == "clerk"?" active":""}}"><a href="<?php echo url('clerk/create'); ?>"><span>الكتّاب</span></a></li>
                                        <li class="{{Request::segment(1) == "clerk_courts"?" active":""}}"><a href="<?php echo url('clerk_courts/create'); ?>"><span>كتّاب المحاكم</span></a></li>
                                        <li class="{{Request::segment(1) == "judge_clerks"?" active":""}}"><a href="<?php echo url('judge_clerks/create'); ?>"><span>كتّاب القضاة</span></a></li>
                                        <li class="{{Request::segment(1) == "manage_judge_court"?" active":""}}"><a href="<?php echo url('manage_judge_court/create'); ?>">قضاة المحاكم</a></li> 
                                        <li class="{{Request::segment(1) == "manage_court_fields"?" active":""}}"><a href="<?php echo url('manage_court_fields/0'); ?>">خانات المحكمة </a></li> 
                                        <li class="{{Request::segment(1) == "manage_configs"?" active":""}}"><a href="<?php echo url('manage_configs/create'); ?>">إعداد التشكيلات</a></li>
                                        <li class="{{Request::segment(1) == "user_profile"?" active":""}}"><a href="<?php echo url('user_profile'); ?>">ملف المستخدم</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown{{Request::segment(1) == "reports"?" active":""}}">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gears"></i> <span>إحصاءات</span> <i class="fa fa-angle-left pull-right"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li class="{{Request::segment(2) == "judgments_average"?" active":""}}"><a href="<?php echo url('reports/judgments_average'); ?>">معدّل الأحكام</a></li>
                                        <li class="{{Request::segment(2) == "reports_stats"?" active":""}}"><a href="<?php echo url('reports/reports_stats'); ?>">تقارير شهرية</a></li>
                                        <li class="{{Request::segment(2) == "users_stats"?" active":""}}"><a href="<?php echo url('reports/users_stats'); ?>">أعمال الموظفين</a></li>
                                        <li class="{{Request::segment(2) == "judges_distribution"?" active":""}}"><a href="<?php echo url('reports/judges_distribution'); ?>">توزيع القضاة</a></li>
                                        <li class="{{Request::segment(2) == "user_logs"?" active":""}}"><a href="<?php echo url('reports/user_logs'); ?>">سجل المدخلات</a></li>
                                        <li class="{{Request::segment(2) == "full_report"?" active":""}}"><a href="<?php echo url('reports/full_report'); ?>">إحصاء المناطق</a></li>
                                        <li class="{{Request::segment(2) == "judges_by_occupation"?" active":""}}"><a href="<?php echo url('reports/judges_by_occupation'); ?>">القضاة حسب الوظيفة</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        {{isset($page_title)?$page_title:''}}
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    @yield('content')
                    <!-- Your Page Content Here -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <footer class="main-footer">

            </footer>
        </div>
    </body>
</html>
