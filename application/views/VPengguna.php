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
                                            <th>Status</th>
                                            <th>Nama</th>
                                            <th>Akun</th>
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
                                            <label>Nama</label>
                                            <input id="nama" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-0">
                                        <div class="form-group form-group-default">
                                            <label>Username</label>
                                            <input id="username" type="text" class="form-control" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default">
                                            <label>Password</label>
                                            <input id="password" type="text" class="form-control" autocomplete="off">
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
            dt.append('nama', $('input#nama').val());
            dt.append('username', $('input#username').val());
            dt.append('password', $('input#password').val());

            $.ajax({
                type: 'POST',
                url: '<?= base_url('Pengguna/save') ?>',
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
                url: '<?= base_url('Pengguna/edit') ?>/' + b.data('id'),
                dataType: 'JSON',
                async: false,
                done: function(r) {},
                beforeSend: function() {
                    i.removeClass().addClass('fa fa-spin fa-spinner');
                },
                success: function(r) {
                    i.removeClass().addClass(cls);

                    $('#form-data').find('#id').val(r.id_pengguna);
                    $('#form-data').find('#nama').val(r.nama);
                    $('#form-data').find('#username').val(r.username);
                    $('#form-data').find('#password').val(r.password);

                    if ((r.foto_pengguna)) {
                        $('#form-data').find("#image-preview").attr("src", r.foto_pengguna);
                    }

                },

                error: function(e) {
                    sweetMsg('error', 'Terjadi kesalahan!');
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
                        url: '<?= base_url('Pengguna/aktif') ?>/' + b.data('id'),
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
                        url: '<?= base_url('Pengguna/nonaktif') ?>/' + b.data('id'),
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
                    "className": "text-center",
                    "width": "10%",
                    "orderable": false,
                }, {
                    "targets": [2],
                    "width": "25%",
                }, {
                    "targets": [3],
                    "width": "32%",
                }, {
                    "targets": [4],
                    "className": "text-center",
                    "width": "20%",
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