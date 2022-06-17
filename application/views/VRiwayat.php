<style>
    #image-preview {
        width: 200px;
    }
</style>
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title"><?= $title; ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="#">
                            <i class="flaticon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#"><?= $title; ?></a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Modal -->
                    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header no-bd">
                                    <h5 class="modal-title">
                                        <span class="fw-mediumbold">
                                            Detail</span>
                                        <span class="fw-light">
                                            Riwayat Kunjungan
                                        </span>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="content"></div>
                                </div>
                                <div class="modal-footer no-bd">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">List Data</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="search-data">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" id="filter_tanggal" name="filter_tanggal" class="form-control" autocomplete="off" value="<?= '01/' . date('m/Y') . ' - ' . date('t/m/Y') ?>" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <select class="form-control" id="filter_nama" name="filter_nama">
                                                        <?= $opt_nama ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <button id="btn-search" type="submit" class="btn btn-primary btn-sm w-100"><i class="fa fa-search"></i> Cari</button>
                                                </div>
                                            </div>
                                            <!-- <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-group">
                                                    <button id="btn-export" type="button" class="btn btn-success btn-sm w-100"><i class="fa fa-file-excel"></i> Export Data</button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table id="table-data" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Nama Kunjungan</th>
                                            <th>Nomor Pelanggan</th>
                                            <th>Nomor Meteran</th>
                                            <th>Nama Petugas</th>
                                            <th>Tanggal Kunjungan</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            setDataTable('#table-data', '');
            setDateRange();

        }).on('click', '#btn-detail', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            $.ajax({
                type: 'POST',
                url: '<?= base_url('Riwayat/edit') ?>/' + b.data('id'),
                dataType: 'JSON',
                async: false,
                done: function(r) {},
                beforeSend: function() {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    i.removeClass().addClass(cls);

                    $('#detailModal').modal('show');
                    $('#content').html(r.data);
                },

                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
                    i.removeClass().addClass(cls);
                }
            });
        }).on('submit', '#search-data', function(e) {
            e.preventDefault();
            setDataTable('#table-data', '');
        }).on('click', '#btn-export', function(e) {
            e.preventDefault();
            var input = $('#search-data').serialize();
            window.open('<?php echo base_url('Riwayat/export_excel/') ?>?' + input, '_blank');
        });

        function setDataTable(a, tbody = '') {
            // var card = $(a).parents('.card:first');
            // card.append('<div class="overlay"><i class="fas fa-2x fa-spin fa-sync-alt"></i></div>');
            if ($.fn.DataTable.isDataTable(a)) {
                $(a).dataTable().fnDestroy();
            }
            if (tbody != '') {
                $(a).find('tbody').html(tbody);
            }

            var input = $('#search-data').serialize();

            $(a).DataTable({
                "responsive": true,
                // "lengthChange": false,
                // "autoWidth": false,
                // "scrollY": "400px",
                "scrollCollapse": true,
                "paging": true,
                "processing": true,
                "serverSide": true,
                "order": [
                    [1, 'asc']
                ],
                "ajax": {
                    "url": "<?= base_url('Riwayat/list_') ?>?" + input,
                    "type": "POST"
                },
                "deferRender": true,
                "aLengthMenu": [
                    [10, 25, 50, 100, 500],
                    [10, 25, 50, 100, 500]
                ],
                columnDefs: [{
                        "width": "3%",
                        "targets": 0,
                    },
                    {
                        "targets": 1,
                    },
                    {
                        "targets": 2,
                    },
                    {
                        "targets": 3,
                    },
                    {
                        "targets": 4,
                    },
                    {
                        "targets": 5,
                    },
                    {
                        "targets": 6,
                    },
                    {
                        "targets": 7,
                        "orderable": false
                    },
                ],
            });
            // setTimeout(() => {
            //     card.find('.overlay').remove();
            // }, 1500);
        }

        function setDateRange() {
            $('input[name="filter_tanggal"]').daterangepicker({
                showWeekNumbers: true,
                // autoApply: true,
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        }
    </script>