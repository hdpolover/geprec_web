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
                            <div class="table-responsive">
                                <table id="table-data" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kunjungan</th>
                                            <th>Nomor Pelanggan</th>
                                            <th>Nomor Meteran</th>
                                            <th>Nama Petugas</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $tbody; ?>
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
        });

        function setDataTable(a, tbody = '') {
            if ($.fn.DataTable.isDataTable(a)) {
                $(a).dataTable().fnDestroy();
            }

            if (tbody != '') {
                $(a).find('tbody').html(tbody);
            }

            $(a).DataTable({
                "responsive": true,
                // "lengthChange": false,
                // "autoWidth": false,
                // "scrollX": true,
                // "scrollY": "400px",
                "scrollCollapse": true,
                "paging": true,
                "order": [
                    [0, 'asc']
                ],
                "columnDefs": [{
                    "targets": [0],
                    "width": "3%",
                }, {
                    "targets": [1],
                    "width": "32%",
                }, {
                    "targets": [2],
                    "width": "25%",
                }, {
                    "targets": [3],
                    "width": "20%",
                }, {
                    "targets": [4],
                    "className": "text-center",
                    "width": "10%",
                }, {
                    "targets": [5],
                    "className": "text-center",
                    "width": "10%",
                    "orderable": false,
                }],
            });
        }

        function previewImage() {
            // document.getElementById("image-preview").style.display = "block";

            if (document.getElementById("foto").files.length == 0) {
                document.getElementById("image-preview").src = "<?= config_item('asset') ?>/img/posting_foto.svg";
            } else {
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById("foto").files[0]);
                oFReader.onload = function(oFREvent) {
                    document.getElementById("image-preview").src = oFREvent.target.result;
                }

            };
        };
    </script>