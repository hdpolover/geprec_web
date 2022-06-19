<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kunjungan extends CI_Controller
{

    public function index()
    {

        if (!$this->session->has_userdata('logged_in')) {
            redirect('auth');
        }

        $data = [
            'title' => 'Data Kunjungan',
            'nav_id' => 'nav_kunjungan',
            'tbody' => $this->list_(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
                'plugin/bs-custom-file-input/bs-custom-file-input.min.js',
            )
        ];

        $this->template->view('VKunjungan', $data);
    }


    private function opt_nama()
    {

        $data  = $this->MCore->list_data('pengguna', 'status');
        // $opt = "";
        $opt = '<option value="">Pilih Nama</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['id_pengguna'] . '">' . $value['nama'] . '</option>';
        }
        return $opt;
    }

    private function list_()
    {

        $data = $this->MCore->list_data('kunjungan');

        $no = 1;
        ob_start();
        foreach ($data->result_array() as $value) {

            $status = '<span class="badge badge-primary">Aktif</span>';

            $button = '<button id="btn-assign" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-secondary" data-original-title="Assign Petugas">
            <i class="fa fa-sign-in-alt text-secondary"></i>
            <button id="btn-edit" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-primary" data-original-title="Edit">
            <i class="fa fa-edit"></i>
        </button>';
            if ($value['status'] == 0) {
                $status = '<span class="badge badge-danger">Tidak Aktif</span>';
                $button .= '
                <button id="btn-aktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Aktifkan">
                    <i class="fa fa-check text-success"></i>
                </button>';
            } else {
                $button .= '
                <button id="btn-nonaktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Nonaktifkan">
                    <i class="fa fa-times text-danger"></i>
                </button>';
            }

            // untuk reset lokasi
            if ($value['reset_lokasi'] == 1) {

                $status_reset = '<span class="badge badge-primary">Tersedia</span>';
                $button .= '
                <button id="btn-reset-nonaktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Cabut Reset Lokasi">
                    <i class="fa fa-trash text-danger"></i>
                </button>';
            } else if ($value['reset_lokasi'] == 0) {

                $status_reset = '<span class="badge badge-danger">Tidak tersedia</span>';
                $button .= '
                <button id="btn-reset-aktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-success" data-original-title="Beri Reset Lokasi">
                    <i class="fa fa-check-double text-secondary"></i>
                </button>';
            }
            $button .= '<button id="btn-export-assign" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-success" data-original-title="Export">
            <i class="fa fa-file-excel text-success"></i>
        </button>';
?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $status ?></td>
                <td><?= $value['nama_kunjungan'] ?></td>
                <td><?= $value['alamat'] ?></td>
                <td><?= $value['catatan'] ?></td>
                <td><?= $status_reset ?></td>
                <td class="text-center">
                    <?= $button; ?>
                </td>
            </tr>
        <?php
            $no++;
        }
        $tbody = ob_get_contents();
        ob_clean();
        return $tbody;
    }

    public function edit($id = 0)
    {
        $data = $this->MCore->get_data('kunjungan', 'id_kunjungan = ' . $id);

        echo json_encode($data->row_array());
    }

    public function save()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_kunjungan', 'Nama Kunjungan', 'trim|required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|required');
        $this->form_validation->set_rules('catatan', 'Catatan', 'trim|required');
        $this->form_validation->set_rules('latitude_awal', 'Latitude', 'trim|required');
        $this->form_validation->set_rules('longitude_awal', 'Longitude', 'trim|required');

        $this->form_validation->set_message('required', '{field} tidak boleh kosong.');
        $this->form_validation->set_message('matches', 'Password harus sama.');
        $this->form_validation->set_message('max_length', '{field} melebihi {param} karakter.');

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $arr['status'] = 0;
            $arr['message'] = reset($errors);
            echo json_encode($arr);
            exit();
        }

        $data = array(
            'nama_kunjungan' => $this->input->post('nama_kunjungan'),
            // 'nomor_pelanggan' => $this->input->post('nomor_pelanggan'),
            // 'nomor_meteran' => $this->input->post('nomor_meteran'),
            'alamat' => $this->input->post('alamat'),
            'catatan' => $this->input->post('catatan'),
            'latitude_awal' => $this->input->post('latitude_awal'),
            'longitude_awal' => $this->input->post('longitude_awal')
        );


        if ($id == '') {
            // if ($this->MCore->get_data('kunjungan', array('LOWER(nomor_pelanggan)' => strtolower($data['nomor_pelanggan'])))->num_rows() > 0) {
            //     $arr['status'] = 0;
            //     $arr['message'] = 'Nomor Pelanggan telah ada. Periksa lagi.';
            //     echo json_encode($arr);
            //     exit();
            // }

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['nama_kunjungan'] . '_' . time();

                $config['upload_path']          = './upload/foto';
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 5000;
                $config['file_name']            = $new_name;
                // $config['max_width']            = 1024;
                // $config['max_height']           = 768;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload("foto")) {
                    $img = array('upload_data' => $this->upload->data());

                    $image = $img['upload_data']['file_name'];
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './upload/foto/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/foto/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_kunjungan'] = config_item('upload') . 'foto/' . $image;
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $sql = $this->MCore->save_data('kunjungan', $data);
        } else {

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['nama_kunjungan'] . '_' . time();

                $config['upload_path']          = './upload/foto';
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 5000;
                $config['file_name']            = $new_name;
                // $config['max_width']            = 1024;
                // $config['max_height']           = 768;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload("foto")) {
                    $img = array('upload_data' => $this->upload->data());

                    $image = $img['upload_data']['file_name'];
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './upload/foto/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/foto/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_kunjungan'] = config_item('upload') . 'foto/' . $image;

                    //delete
                    $img_user = $this->MCore->select_data('foto_kunjungan', 'kunjungan', 'id_kunjungan = ' . $id)->row_array();
                    $exp = explode('/', $img_user['foto_kunjungan']);
                    $file_name = end($exp);

                    $path = FCPATH . 'upload/foto/' . $file_name;
                    if (isset($path)) {
                        unlink($path);
                    }
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $sql = $this->MCore->save_data('kunjungan', $data, true, array('id_kunjungan' => $id));
        }
        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil disimpan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal disimpan';
        }
        echo json_encode($arr);
    }

    public function assign_pengguna($id = 0)
    {

        $data = $this->MCore->get_data('kunjungan', 'id_kunjungan = ' . $id)->row_array();

        ob_start();
        ?>
        <input type="hidden" id="id_kunjungan" name="id_kunjungan" value="<?= $data['id_kunjungan']; ?>">
        <h4>Data Kunjungan</h4>
        <div class="">
            <div class="row">
                <div class="col-4">
                    Nama Kunjungan
                </div>
                <div class="col">
                    : <?= $data['nama_kunjungan'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Nomor Pelanggan
                </div>
                <div class="col">
                    : <?= $data['nomor_pelanggan'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Nomor Meteran
                </div>
                <div class="col">
                    : <?= $data['nomor_meteran'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Alamat Kunjungan
                </div>
                <div class="col">
                    : <?= $data['alamat'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Catatan
                </div>
                <div class="col">
                    : <?= $data['catatan'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Latitude dan Longitude Awal
                </div>
                <div class="col">
                    : <?= $data['latitude_awal'] . ', ' . $data['longitude_awal'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Latitude dan Longitude Baru
                </div>
                <div class="col">
                    : <?= $data['latitude_baru'] . ', ' . $data['longitude_baru'] ?>
                </div>
            </div>
        </div>
        <hr>
        <h4>Bukti Dokumen</h4>
        <div class="row">
            <div class="col">
                <p>Foto Kunjungan</p>
                <?php if ($data['foto_kunjungan']) { ?>
                    <img src="<?= $data['foto_kunjungan'] ?>" alt="tidak foto kunjungan" width="200px;">
                <?php } else {
                    echo "tidak ada";
                } ?>
            </div>
        </div>
        <hr>
        <h4>Data Petugas</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Petugas</th>
                        <th>Tanggal Ditambahkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $option = array(
                        'select' => 'pengguna.nama, daftar_kunjungan.tgl_ditambahkan',
                        'table'  => 'kunjungan',
                        'join'   => array(
                            array('daftar_kunjungan' => 'daftar_kunjungan.id_kunjungan = kunjungan.id_kunjungan'),
                            array('pengguna' => 'daftar_kunjungan.id_pengguna = pengguna.id_pengguna')
                        ),
                        'where'  => 'kunjungan.id_kunjungan = ' . $id,
                        'order' => array('daftar_kunjungan.tgl_ditambahkan' => 'DESC')
                    );

                    $detail = $this->MCore->join_table($option)->result_array();
                    $no = 1;
                    foreach ($detail as $row) {
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= format_indo($row['tgl_ditambahkan']) ?></td>
                        </tr>
                    <?php
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <hr>
        <div class="form-group">
            <label for="nama">Pilih Petugas</label>
            <select class="form-control" id="nama" name="nama">
                <?= $this->opt_nama() ?>
            </select>
        </div>
<?php
        $body = ob_get_contents();
        ob_clean();
        echo json_encode(array('body' => $body));
    }

    public function save_assign()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Nama Petugas', 'trim|required|is_natural_no_zero');

        $this->form_validation->set_message('required', '{field} tidak boleh kosong.');
        $this->form_validation->set_message('is_natural_no_zero', '{field} tidak boleh kosong.');
        $this->form_validation->set_message('max_length', '{field} melebihi {param} karakter.');

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $arr['status'] = 0;
            $arr['message'] = reset($errors);
            echo json_encode($arr);
            exit();
        }

        $data = array(
            'id_kunjungan' => $this->input->post('id_kunjungan'),
            'id_pengguna' => $this->input->post('nama'),
            'tgl_ditambahkan' => date('Y-m-d H:i:s')
        );


        if ($id == '') {
            if ($this->MCore->get_data('daftar_kunjungan', array('LOWER(id_pengguna)' => strtolower($data['id_pengguna'])))->num_rows() > 0) {
                $arr['status'] = 0;
                $arr['message'] = 'Petugas sudah ada.';
                echo json_encode($arr);
                exit();
            }

            $sql = $this->MCore->save_data('daftar_kunjungan', $data);
        } else {

            $sql = $this->MCore->save_data('daftar_kunjungan', $data, true, array('id_daftar_kunjungan' => $id));
        }
        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil disimpan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal disimpan';
        }
        echo json_encode($arr);
    }

    public function export_excel()
    {
        //unlimited
        ini_set('max_execution_time', 0);
        // load excel library
        $this->load->library('excel');

        $listInfo = $this->MCore->list_data('kunjungan')->result();

        // echo "<pre>";
        // print_r($listInfo);
        // die();

        // excdel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        //set logo
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo geprec');
        $objDrawing->setPath('./assets/img/GEPREC.png');
        $objDrawing->setCoordinates('A1');
        //setOffsetX works properly
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(1);
        //set width, height
        $objDrawing->setWidth(50);
        $objDrawing->setHeight(50);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);

        // set header
        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Nama Kunjungan');
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Nomor Pelanggan');
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Nomor Meteran');
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Alamat Kunjungan');
        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Catatan');
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Latitude Longitude Awal');
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Latitude Longitude Baru');
        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Foto Kunjungan');
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(20);

        $judul = 'Data Kunjungan';

        $objPHPExcel->getActiveSheet()->SetCellValue('A2', $judul);
        $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

        $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Tanggal : ");
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', date('d-m-Y H:i:s'));
        // set Row
        $rowCount = 6;
        $no = 1;

        foreach ($listInfo as $list) {

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $no);
            switch ($list->status) {
                case 0:
                    $status = 'Tidak Aktif';
                    break;
                case 1:
                    $status = "Aktif";
                    break;
            }
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $status);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list->nama_kunjungan);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list->nomor_pelanggan);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list->nomor_meteran);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list->alamat);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list->catatan);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list->latitude_awal . ', ' . $list->longitude_awal);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list->latitude_baru . ', ' . $list->longitude_baru);
            if ($list->foto_kunjungan) {
                $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount)->getHyperlink()->setUrl($list->foto_kunjungan);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $list->foto_kunjungan);
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, "Tidak ada foto");
            }

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A5:J" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A5"; // or any value
        $to =  "J5"; // or any value
        //style
        $style_cell = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '1269db')
            ),
            'font' => [
                'size' => 12,
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ]
        );
        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->applyFromArray($style_cell);

        // create file name
        $filename = $judul . ' #' . date("YmdHis") . ".xlsx";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function export_excel_assign($id = 0)
    {
        //unlimited
        ini_set('max_execution_time', 0);
        // load excel library
        $this->load->library('excel');

        $option = array(
            'select' => 'pengguna.nama, daftar_kunjungan.tgl_ditambahkan, kunjungan.*',
            'table'  => 'kunjungan',
            'join'   => array(
                array('daftar_kunjungan' => 'daftar_kunjungan.id_kunjungan = kunjungan.id_kunjungan'),
                array('pengguna' => 'daftar_kunjungan.id_pengguna = pengguna.id_pengguna')
            ),
            'where'  => 'kunjungan.id_kunjungan = ' . $id,
            'order' => array('daftar_kunjungan.tgl_ditambahkan' => 'DESC')
        );

        $listInfo = $this->MCore->join_table($option);

        // echo $this->db->last_query();
        // echo "<pre>";
        // print_r($listInfo);
        // die();

        if ($listInfo->num_rows() < 1) {
            echo "Maaf, data tidak ada :(";
            die();
        }
        // excdel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        //set logo
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo geprec');
        $objDrawing->setPath('./assets/img/GEPREC.png');
        $objDrawing->setCoordinates('A1');
        //setOffsetX works properly
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(1);
        //set width, height
        $objDrawing->setWidth(50);
        $objDrawing->setHeight(50);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);

        // set header
        $objPHPExcel->getActiveSheet()->SetCellValue('A13', 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('B13', 'Nama');
        $objPHPExcel->getActiveSheet()->SetCellValue('C13', 'Tanggal Ditambahkan');
        $objPHPExcel->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
        // set Row
        $rowCount = 15;
        $no = 1;
        $first = true;

        foreach ($listInfo->result() as $list) {

            if ($first) {
                $judul = 'Data Kunjungan ' . $list->nama_kunjungan;

                $objPHPExcel->getActiveSheet()->SetCellValue('A2', $judul);
                $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(18);
                $objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

                // Header
                $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Status');
                $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'Nama Kunjungan');
                $objPHPExcel->getActiveSheet()->SetCellValue('A6', 'Nomor Pelanggan');
                $objPHPExcel->getActiveSheet()->SetCellValue('A7', 'Nomor Meteran');
                $objPHPExcel->getActiveSheet()->SetCellValue('A8', 'Alamat Kunjungan');
                $objPHPExcel->getActiveSheet()->SetCellValue('A9', 'Catatan');
                $objPHPExcel->getActiveSheet()->SetCellValue('A10', 'Latitude Longitude Awal');
                $objPHPExcel->getActiveSheet()->SetCellValue('A11', 'Latitude Longitude Baru');
                $objPHPExcel->getActiveSheet()->SetCellValue('A12', 'Foto Kunjungan');

                switch ($list->status) {
                    case 0:
                        $status = 'Tidak Aktif';
                        break;
                    case 1:
                        $status = "Aktif";
                        break;
                }
                $objPHPExcel->getActiveSheet()->SetCellValue('B4', " : " . $status);
                $objPHPExcel->getActiveSheet()->SetCellValue('B5', " : " . $list->nama_kunjungan);
                $objPHPExcel->getActiveSheet()->SetCellValue('B6', " : " . $list->nomor_pelanggan);
                $objPHPExcel->getActiveSheet()->SetCellValue('B7', " : " . $list->nomor_meteran);
                $objPHPExcel->getActiveSheet()->SetCellValue('B8', " : " . $list->alamat);
                $objPHPExcel->getActiveSheet()->SetCellValue('B9', " : " . $list->catatan);
                $objPHPExcel->getActiveSheet()->SetCellValue('B10', " : " . $list->latitude_awal . ', ' . $list->longitude_awal);
                $objPHPExcel->getActiveSheet()->SetCellValue('B11', " : " . $list->latitude_baru . ', ' . $list->longitude_baru);
                if ($list->foto_kunjungan) {
                    $objPHPExcel->getActiveSheet()->getCell('B12')->getHyperlink()->setUrl($list->foto_kunjungan);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B12', $list->foto_kunjungan);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue('B12', "Tidak ada foto");
                }

                $first = false;
            }

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $no);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $list->nama);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, format_indo($list->tgl_ditambahkan));

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A14:C" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A14"; // or any value
        $to =  "C14"; // or any value
        //style
        $style_cell = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '1269db')
            ),
            'font' => [
                'size' => 12,
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ]
        );
        $objPHPExcel->getActiveSheet()->getStyle("$from:$to")->applyFromArray($style_cell);

        // create file name
        $filename = $judul . ' #' . date("YmdHis") . ".xlsx";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
    public function aktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('status' => 1), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil diaktifkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal diaktifkan';
        }
        echo json_encode($arr);
    }

    public function nonaktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('status' => 0), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil dinonaktifkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal dinonaktifkan';
        }
        echo json_encode($arr);
    }

    public function reset_aktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('reset_lokasi' => 1), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Reset Lokasi berhasil Ditambahkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal diaktifkan';
        }
        echo json_encode($arr);
    }

    public function reset_nonaktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('reset_lokasi' => 0), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Reset Lokasi berhasil Dicabut';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal dinonaktifkan';
        }
        echo json_encode($arr);
    }
}
