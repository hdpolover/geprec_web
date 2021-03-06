<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Geprec Web | <?= $title; ?></title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="<?= config_item('img') ?>GEPREC-01.ico" type="image/x-icon" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= config_item('css') ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?= config_item('css') ?>atlantis.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= config_item('vendor') ?>sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Datatable -->
    <link rel="stylesheet" href="<?= config_item('vendor') ?>datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= config_item('vendor') ?>datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= config_item('vendor') ?>datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- dari controller -->
    <?php
    if (isset($css)) {
        foreach ($css as $value) { ?>
            <link rel="stylesheet" href="<?= config_item('css') . $value ?>">
    <?php }
    } ?>

    <!-- SCRIPT -->
    <!-- Fonts and icons -->
    <script src="<?= config_item('js') ?>plugin/webfont/webfont.min.js"></script>
    <!--   Core JS Files   -->
    <script src="<?= config_item('js') ?>core/jquery.3.2.1.min.js"></script>
    <script src="<?= config_item('js') ?>core/popper.min.js"></script>
    <script src="<?= config_item('js') ?>core/bootstrap.min.js"></script>

    <!-- jQuery UI -->
    <script src="<?= config_item('js') ?>plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="<?= config_item('js') ?>plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="<?= config_item('js') ?>plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>


    <!-- Chart JS -->
    <script src="<?= config_item('js') ?>plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="<?= config_item('js') ?>plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="<?= config_item('js') ?>plugin/chart-circle/circles.min.js"></script>

    <!-- DataTables -->
    <script src="<?= config_item('vendor') ?>datatables/jquery.dataTables.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?= config_item('vendor') ?>jszip/jszip.min.js"></script>
    <script src="<?= config_item('vendor') ?>pdfmake/pdfmake.min.js"></script>
    <script src="<?= config_item('vendor') ?>pdfmake/vfs_fonts.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?= config_item('vendor') ?>datatables-buttons/js/buttons.colVis.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="<?= config_item('js') ?>plugin/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?= config_item('js') ?>plugin/jqvmap/maps/jquery.vmap.world.js"></script>

    <!-- Sweet Alert2 -->
    <script src="<?= config_item('vendor') ?>sweetalert2/sweetalert2.min.js"></script>

    <!-- Atlantis JS -->
    <script src="<?= config_item('js') ?>atlantis.min.js"></script>

    <script>
        WebFont.load({
            google: {
                "families": ["Lato:300,400,700,900"]
            },
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['assets/css/fonts.min.css']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!-- dari controller -->
    <?php if (isset($js)) {
        foreach ($js as $value) { ?>
            <script src="<?= config_item('js') . $value ?>"></script>
    <?php }
    } ?>
    <script>
        // type = 'success, info, error, warning'
        var sweetMsg = function(type, text) {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: type,
                title: text
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            var nav_active = '<?= $nav_id; ?>';
            $("#" + nav_active).parent().addClass("active");
            if ($("#" + nav_active).parents('.collapse').length == 1) {
                $("#" + nav_active).addClass("active");
                // $("#" + nav_active).parents('.collapse').addClass('show');
                $("#" + nav_active).parents('.collapse').parent().addClass('active');
            }
        });
    </script>
</head>

<body>
    <div class="wrapper sidebar_minimize">
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue">

                <a href="<?= base_url('dashboard') ?>" class="logo">
                    <img src="<?= config_item('img') ?>GEPREC.png" alt="navbar brand" class="navbar-brand" width="120px;">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="icon-menu"></i>
                    </button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

                <div class="container-fluid">
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="<?= $this->session->userdata('foto_admin') ?>" alt="..." class="avatar-img rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg"><img src="<?= $this->session->userdata('foto_admin') ?>" alt="image profile" class="avatar-img rounded"></div>
                                            <div class="u-text">
                                                <h4><?= $this->session->userdata('username'); ?></h4>
                                                <p class="text-muted"><?= $this->session->userdata('nama'); ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?= base_url('Auth/do_logout') ?>">Logout</a>
                                    </li>
                                </div>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2">
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img src="<?= $this->session->userdata('foto_admin'); ?>" alt="..." class="avatar-img rounded-circle">
                        </div>
                        <div class="info">
                            <span class="text-dark"><?= $this->session->userdata('username') ?></span>
                            <span class="user-level text-muted">Administrator</span>
                        </div>
                    </div>
                    <ul class="nav nav-primary">
                        <li class="nav-item">
                            <a href="<?= base_url('Dashboard') ?>" id="nav_dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('Kunjungan') ?>" id="nav_kunjungan">
                                <i class="fas fa-street-view"></i>
                                <p>Kunjungan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('Riwayat') ?>" id="nav_riwayat">
                                <i class="fas fa-history"></i>
                                <p>Riwayat</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('Pengguna') ?>" id="nav_pengguna">
                                <i class="fas fa-users"></i>
                                <p>Pengguna</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->