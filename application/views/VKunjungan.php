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
                <div class="col-md-8">
                    <!-- Modal -->
                    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form id="form-assign">
                                <div class="modal-content">
                                    <div class="modal-header no-bd">
                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">
                                                Detail</span>
                                            <span class="fw-light">
                                                Kunjungan
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
                                        <button type="submit" id="btn-save-assign" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                                        <button type="button" id="btn-batal" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">List Data</h4>
                                <div class="form-group">
                                    <button id="btn-export" type="button" class="btn btn-success btn-sm w-100"><i class="fa fa-file-excel"></i> Export Data</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table-data" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>Nama Kunjungan</th>
                                            <th>Alamat</th>
                                            <th>Catatan</th>
                                            <th>Reset Lokasi</th>
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
                <div class="col-md-4">
                    <form id="form-data">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Form Data</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="id" id="id">
                                <p class="small">Silahkan isi semua form nya</p>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group form-group-default">
                                            <label>Nama Kunjungan</label>
                                            <input id="nama_kunjungan" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group  form-group-default">
                                            <label for="alamat">Alamat</label>
                                            <textarea class="form-control" id="alamat" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group  form-group-default">
                                            <label for="catatan">Catatan</label>
                                            <textarea class="form-control" id="catatan" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-0">
                                        <div class="form-group form-group-default">
                                            <label>Latitude Awal</label>
                                            <input id="latitude_awal" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Longitude Awal</label>
                                            <input id="longitude_awal" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-0">
                                        <div class="form-group form-group-default">
                                            <label>Latitude Baru</label>
                                            <input id="latitude_baru" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Longitude Baru</label>
                                            <input id="longitude_baru" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <img id="image-preview" alt="tidak ada foto" src="<?= config_item('assets') ?>img/undraw_posting_photo.svg" />
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Upload foto</label>
                                            <div class="custom-file">
                                                <input type="file" class="form-control-sm custom-file-input" id="foto" name="foto" onchange="previewImage();">
                                                <label class="custom-file-label" for="customFile">Pilih Foto</label>
                                            </div>
                                            <small class="font-italic text-muted mb-2">JPG / PNG tidak lebih dari 2 MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer no-bd">
                                <button type="submit" id="btn-save" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                                <button type="button" id="btn-cancel" class="btn btn-danger">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $(function() {
                bsCustomFileInput.init();
            });

            setDataTable('#table-data', '');

            $('#btn-cancel').click(function() {
                $('#form-data').find('input#id').val('');
                $('#form-data').find('input.form-control').val('');
                $('#form-data').find('textarea').val('');

                $('#foto').next('label').html('Pilih Foto');
                $("#foto").val('');
                document.getElementById("image-preview").src = "<?= config_item('assets') ?>img/undraw_posting_photo.svg";
            });

            $('#btn-cancel').click();
        }).on('submit', '#form-data', function(e) {
            e.preventDefault();
            var b = $('#btn-save'),
                i = b.find('i'),
                cls = i.attr('class');

            var dt = new FormData();

            dt.append('foto', $('input#foto')[0].files[0]);
            dt.append('id', $('input#id').val());
            dt.append('nama_kunjungan', $('input#nama_kunjungan').val());
            dt.append('alamat', $('textarea#alamat').val());
            dt.append('catatan', $('textarea#catatan').val());
            dt.append('latitude_awal', $('input#latitude_awal').val());
            dt.append('longitude_awal', $('input#longitude_awal').val());
            dt.append('latitude_baru', $('input#latitude_baru').val());
            dt.append('longitude_baru', $('input#longitude_baru').val());

            $.ajax({
                type: 'POST',
                url: '<?= base_url('Kunjungan/save') ?>',
                cache: false,
                contentType: false,
                processData: false,
                data: dt,
                dataType: 'JSON',
                // async: false,
                beforeSend: function() {
                    b.attr("disabled", true);
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    if (r.status) {
                        setDataTable('#table-data', r.tbody);
                        sweetMsg('success', r.message);
                        $('#btn-cancel').trigger('click');
                    } else {
                        sweetMsg('error', r.message);
                    }
                    b.removeAttr("disabled");
                    i.removeClass().addClass(cls);
                },
                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
                    b.removeAttr("disabled");
                    i.removeClass().addClass(cls);
                }
            });
        }).on('click', '#btn-edit', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            $.ajax({
                type: 'POST',
                url: '<?= base_url('Kunjungan/edit') ?>/' + b.data('id'),
                dataType: 'JSON',
                async: false,
                done: function(r) {},
                beforeSend: function() {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    i.removeClass().addClass(cls);

                    $('#form-data').find('#id').val(r.id_kunjungan);
                    $('#form-data').find('#nama_kunjungan').val(r.nama_kunjungan);
                    $('#form-data').find('#alamat').val(r.alamat);
                    $('#form-data').find('#catatan').val(r.catatan);
                    $('#form-data').find('#latitude_awal').val(r.latitude_awal);
                    $('#form-data').find('#longitude_awal').val(r.longitude_awal);
                    $('#form-data').find('#latitude_baru').val(r.latitude_baru);
                    $('#form-data').find('#longitude_baru').val(r.longitude_baru);

                    if ((r.foto_kunjungan)) {
                        $('#form-data').find("#image-preview").attr("src", r.foto_kunjungan);
                    }

                },

                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
                    i.removeClass().addClass(cls);
                }
            });
        }).on('click', '#btn-assign', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            $.ajax({
                type: 'POST',
                url: '<?= base_url('Kunjungan/assign_pengguna') ?>/' + b.data('id'),
                dataType: 'JSON',
                async: false,
                done: function(r) {},
                beforeSend: function() {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    i.removeClass().addClass(cls);

                    $('#detailModal').modal('show');
                    $('#content').html(r.body);

                },

                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
                    i.removeClass().addClass(cls);
                }
            });
        }).on('submit', '#form-assign', function(e) {
            e.preventDefault();
            var b = $('#btn-save-assign'),
                i = b.find('i'),
                cls = i.attr('class');

            $.ajax({
                type: 'POST',
                url: '<?= base_url('Kunjungan/save_assign') ?>',
                data: $(this).serializeArray(),
                dataType: 'JSON',
                // async: false,
                beforeSend: function() {
                    b.attr("disabled", true);
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    if (r.status) {
                        setDataTable('#table-data', r.tbody);
                        sweetMsg('success', r.message);
                        $('#btn-batal').trigger('click');
                    } else {
                        sweetMsg('error', r.message);
                    }
                    b.removeAttr("disabled");
                    i.removeClass().addClass(cls);
                },
                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
                    b.removeAttr("disabled");
                    i.removeClass().addClass(cls);
                }
            });
        }).on('click', '#btn-aktif', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success m-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                text: "Apakah anda yakin akan mengaktifkan data berikut?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('Kunjungan/aktif') ?>/' + b.data('id'),
                        dataType: 'JSON',
                        // async: false,
                        done: function(r) {},
                        beforeSend: function() {
                            i.removeClass().addClass('fa fa-spin fa-spinner');
                        },
                        success: function(r) {
                            if (r.status) {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'success'
                                )
                                setDataTable('#table-data', r.tbody);
                            } else {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'error'
                                )
                            }
                            i.removeClass().addClass(cls);
                        },
                        error: function(e) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan!!',
                                'error'
                            )
                            i.removeClass().addClass(cls);
                        }
                    });
                }
            });
        }).on('click', '#btn-nonaktif', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger m-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                text: "Apakah anda yakin akan menghapus data berikut?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Hapus',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('Kunjungan/nonaktif') ?>/' + b.data('id'),
                        dataType: 'JSON',
                        // async: false,
                        done: function(r) {},
                        beforeSend: function() {
                            i.removeClass().addClass('fa fa-spin fa-spinner');
                        },
                        success: function(r) {
                            if (r.status) {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'success'
                                )
                                setDataTable('#table-data', r.tbody);
                            } else {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'error'
                                )
                            }
                            i.removeClass().addClass(cls);
                        },
                        error: function(e) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan!!',
                                'error'
                            )
                            i.removeClass().addClass(cls);
                        }
                    });
                }
            });
        }).on('click', '#btn-reset-aktif', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success m-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                text: "Apakah anda yakin akan memberikan reset lokasi data berikut?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('Kunjungan/reset_aktif') ?>/' + b.data('id'),
                        dataType: 'JSON',
                        // async: false,
                        done: function(r) {},
                        beforeSend: function() {
                            i.removeClass().addClass('fa fa-spin fa-spinner');
                        },
                        success: function(r) {
                            if (r.status) {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'success'
                                )
                                setDataTable('#table-data', r.tbody);
                            } else {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'error'
                                )
                            }
                            i.removeClass().addClass(cls);
                        },
                        error: function(e) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan!!',
                                'error'
                            )
                            i.removeClass().addClass(cls);
                        }
                    });
                }
            });
        }).on('click', '#btn-reset-nonaktif', function() {
            var b = $(this),
                i = b.find('i'),
                cls = i.attr('class');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger m-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                text: "Apakah anda yakin akan mencabut reset lokasi data berikut?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonText: 'Cabut',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '<?= base_url('Kunjungan/reset_nonaktif') ?>/' + b.data('id'),
                        dataType: 'JSON',
                        // async: false,
                        done: function(r) {},
                        beforeSend: function() {
                            i.removeClass().addClass('fa fa-spin fa-spinner');
                        },
                        success: function(r) {
                            if (r.status) {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'success'
                                )
                                setDataTable('#table-data', r.tbody);
                            } else {
                                Swal.fire(
                                    '',
                                    r.message,
                                    'error'
                                )
                            }
                            i.removeClass().addClass(cls);
                        },
                        error: function(e) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan!!',
                                'error'
                            )
                            i.removeClass().addClass(cls);
                        }
                    });
                }
            });
        }).on('click', '#btn-export', function(e) {
            e.preventDefault();
            window.open('<?php echo base_url('Kunjungan/export_excel/') ?>', '_blank');
        }).on('click', '#btn-export-assign', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            window.open('<?php echo base_url('Kunjungan/export_excel_assign/') ?>' + id, '_blank');
        });

        function setDataTable(a, tbody = '') {
            if ($.fn.DataTable.isDataTable(a)) {
                $(a).dataTable().fnDestroy();
            }

            if (tbody != '') {
                $(a).find('tbody').html(tbody);
            }

            $(a).DataTable({
                // "responsive": true,
                // "lengthChange": false,
                // "autoWidth": false,
                "scrollX": true,
                // "scrollY": "400px",
                "scrollCollapse": true,
                "paging": true,
                "order": [
                    [0, 'asc']
                ],
                "columnDefs": [{
                    "targets": [0],
                    "width": "20px",
                }, {
                    "targets": [1],
                    "width": "60px",
                }, {
                    "targets": [2],
                    "width": "120px",
                }, {
                    "targets": [3],
                    "width": "200px",
                }, {
                    "targets": [4],
                    "width": "200px",
                }, {
                    "targets": [5],
                    "width": "60px",
                    "className": "text-center",
                    "orderable": false,
                }, {
                    "targets": [6],
                    "width": "140px",
                    "className": "text-center",
                    "orderable": false,
                }],
                "initComplete": function() {

                    $('[data-toggle="tooltip"]').tooltip();
                }
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