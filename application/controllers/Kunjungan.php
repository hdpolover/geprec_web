<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet as spreadsheet; // instead PHPExcel
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as xlsx; // Instead PHPExcel_Writer_Excel2007
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing as drawing; // Instead PHPExcel_Worksheet_Drawing
use PhpOffice\PhpSpreadsheet\Style\Alignment as alignment; // Instead PHPExcel_Style_Alignment
use PhpOffice\PhpSpreadsheet\Style\Fill as fill; // Instead PHPExcel_Style_Fill
use PhpOffice\PhpSpreadsheet\Style\Border as border_;
use PhpOffice\PhpSpreadsheet\Style\Color as color_; //Instead PHPExcel_Style_Color
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup as pagesetup; // Instead PHPExcel_Worksheet_PageSetup
use PhpOffice\PhpSpreadsheet\IOFactory as io_factory; // Instead PHPExcel_IOFactory

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
            // untuk reset lokasi
            if ($value['reset_lokasi'] == 1) {

                $status_reset = '<span class="badge badge-primary">Tersedia</span>';
                $button .= '
                <button id="btn-reset-nonaktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Cabut Reset Lokasi">
                    <i class="fa fa-eraser text-danger"></i>
                </button>';
            } else if ($value['reset_lokasi'] == 0) {

                $status_reset = '<span class="badge badge-danger">Tidak tersedia</span>';
                $button .= '
                <button id="btn-reset-aktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-success" data-original-title="Beri Reset Lokasi">
                    <i class="fa fa-check-double text-secondary"></i>
                </button>';
            }

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

            // Button delete kunjungan
            $count_riwayat = $this->MCore->select_data('id_kunjungan', 'riwayat_kunjungan', 'id_kunjungan = ' . $value['id_kunjungan'])->num_rows();

            if ($count_riwayat == 0) {
                $button .= '<button id="btn-delete" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-danger" data-original-title="Hapus Kunjungan">
            <i class="fa fa-trash-alt text-danger"></i>
        </button>';
            }

            // Button export assign

            $count_daftar_kunjungan = $this->MCore->select_data('id_kunjungan', 'daftar_kunjungan', 'id_kunjungan = ' . $value['id_kunjungan'])->num_rows();

            if ($count_daftar_kunjungan > 0) {
                $button .= '<button id="btn-export-assign" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-success" data-original-title="Export">
            <i class="fa fa-file-excel text-success"></i>
        </button>';
            }
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
            'id_pelanggan'      => $this->input->post('id_pelanggan'),
            'nomor_meteran'     => $this->input->post('nomor_meteran'),
            'nama_kunjungan' => $this->input->post('nama_kunjungan'),
            'alamat' => $this->input->post('alamat'),
            'catatan' => $this->input->post('catatan'),
            'latitude_awal' => $this->input->post('latitude_awal'),
            'longitude_awal' => $this->input->post('longitude_awal'),
            'latitude_baru' => $this->input->post('latitude_baru'),
            'longitude_baru' => $this->input->post('longitude_baru')
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
                    Lat dan Long Awal
                </div>
                <div class="col">
                    : <?= $data['latitude_awal'] . ', ' . $data['longitude_awal'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    Lat dan Long Baru
                </div>
                <div class="col">
                    : <?= $data['latitude_baru'] . ', ' . $data['longitude_baru'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    ID Pelanggan
                </div>
                <div class="col">
                    : <?= $data['id_pelanggan'] ?>
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
            <table id="table-assign" class="display table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th style="width: 10%">No</th>
                        <th style="width: 40%">Nama Petugas</th>
                        <th style="width: 50%">Tanggal Ditambahkan</th>
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
            <label for="nama">Pilih Petugas <i class="text-danger fa fa-asterisk fa-sm"></i></label>
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
            if ($this->MCore->get_data('daftar_kunjungan', array('LOWER(id_pengguna)' => strtolower($data['id_pengguna']), 'id_kunjungan' => $data['id_kunjungan']))->num_rows() > 0) {
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
        $spreadsheet = new spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $listInfo = $this->MCore->list_data('kunjungan')->result();

        //set logo
        $objDrawing = new drawing();
        $objDrawing->setName('Logo geprec');
        $objDrawing->setPath('./assets/img/GEPREC.png');
        $objDrawing->setCoordinates('A1');
        //setOffsetX works properly
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(1);
        //set width, height
        $objDrawing->setWidth(50);
        $objDrawing->setHeight(50);
        $objDrawing->setWorksheet($sheet);

        $sheet->getRowDimension('1')->setRowHeight(40);

        // set header
        $sheet->SetCellValue('A5', 'No');
        $sheet->SetCellValue('B5', 'Status');
        $sheet->SetCellValue('C5', 'Nama Kunjungan');
        $sheet->SetCellValue('D5', 'Alamat Kunjungan');
        $sheet->SetCellValue('E5', 'Catatan');
        $sheet->SetCellValue('F5', 'Latitude Longitude Awal');
        $sheet->SetCellValue('G5', 'Latitude Longitude Baru');
        $sheet->SetCellValue('H5', 'ID Pelanggan');
        $sheet->SetCellValue('I5', 'Nomor Meteran');
        $sheet->SetCellValue('J5', 'Foto Kunjungan');
        $sheet->getRowDimension('5')->setRowHeight(20);

        $judul = 'Data Kunjungan';

        $sheet->SetCellValue('A2', $judul);
        $sheet->getStyle("A2")->getFont()->setSize(18);
        $sheet->getStyle("A2")->getAlignment()->setVertical(alignment::VERTICAL_CENTER);
        $sheet->mergeCells('A2:C2');

        $sheet->SetCellValue('A3', "Tanggal : ");
        $sheet->SetCellValue('B3', date('d-m-Y H:i:s'));
        // set Row
        $rowCount = 6;
        $no = 1;

        foreach ($listInfo as $list) {

            $sheet->SetCellValue('A' . $rowCount, $no);
            switch ($list->status) {
                case 0:
                    $status = 'Tidak Aktif';
                    break;
                case 1:
                    $status = "Aktif";
                    break;
            }
            $sheet->SetCellValue('B' . $rowCount, $status);
            $sheet->SetCellValue('C' . $rowCount, $list->nama_kunjungan);
            $sheet->SetCellValue('D' . $rowCount, $list->alamat);
            $sheet->SetCellValue('E' . $rowCount, $list->catatan);
            $sheet->SetCellValue('F' . $rowCount, $list->latitude_awal . ', ' . $list->longitude_awal);
            $sheet->SetCellValue('G' . $rowCount, $list->latitude_baru . ', ' . $list->longitude_baru);
            $sheet->SetCellValue('H' . $rowCount, $list->id_pelanggan);
            $sheet->SetCellValue('I' . $rowCount, $list->nomor_meteran);
            if ($list->foto_kunjungan) {
                $sheet->getCell('J' . $rowCount)->getHyperlink()->setUrl($list->foto_kunjungan);
                $sheet->SetCellValue('J' . $rowCount, $list->foto_kunjungan);
            } else {
                $sheet->SetCellValue('J' . $rowCount, "Tidak ada foto");
            }

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(70);
        $sheet->getColumnDimension('E')->setWidth(70);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => border_::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
        $sheet->getStyle("A5:J" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A5"; // or any value
        $to =  "J5"; // or any value
        //style
        $style_cell = array(
            'alignment' => array(
                'horizontal' => alignment::HORIZONTAL_CENTER,
                'vertical' => alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => fill::FILL_SOLID,
                'color' => array('argb' => '1269db')
            ),
            'font' => [
                'size' => 12,
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ]
        );
        $sheet->getStyle("$from:$to")->applyFromArray($style_cell);

        // create file name
        $filename = $judul . ' #' . date("YmdHis") . ".xlsx";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function export_excel_assign($id = 0)
    {
        //unlimited
        ini_set('max_execution_time', 0);
        // load excel library
        $spreadsheet = new spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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

        if ($listInfo->num_rows() < 1) {
            echo "Maaf, data tidak ada :(";
            die();
        }

        //set logo
        $objDrawing = new drawing();
        $objDrawing->setName('Logo geprec');
        $objDrawing->setPath('./assets/img/GEPREC.png');
        $objDrawing->setCoordinates('A1');
        //setOffsetX works properly
        $objDrawing->setOffsetX(10);
        $objDrawing->setOffsetY(1);
        //set width, height
        $objDrawing->setWidth(50);
        $objDrawing->setHeight(50);
        $objDrawing->setWorksheet($sheet);

        $sheet->getRowDimension('1')->setRowHeight(40);

        // set header
        $sheet->SetCellValue('A14', 'No');
        $sheet->SetCellValue('B14', 'Nama');
        $sheet->SetCellValue('C14', 'Tanggal Ditambahkan');
        $sheet->getRowDimension('14')->setRowHeight(20);
        // set Row
        $rowCount = 15;
        $no = 1;
        $first = true;

        foreach ($listInfo->result() as $list) {

            if ($first) {
                $judul = 'Data Kunjungan ' . $list->nama_kunjungan;

                $sheet->SetCellValue('A2', $judul);
                $sheet->getStyle("A2")->getFont()->setSize(18);
                $sheet->getStyle("A2")->getAlignment()->setVertical(alignment::VERTICAL_CENTER);
                $sheet->mergeCells('A2:C2');

                // Header
                $sheet->SetCellValue('A4', 'Status');
                $sheet->SetCellValue('A5', 'Nama Kunjungan');
                $sheet->SetCellValue('A6', 'Alamat Kunjungan');
                $sheet->SetCellValue('A7', 'Catatan');
                $sheet->SetCellValue('A8', 'Lat dan Long Awal');
                $sheet->SetCellValue('A9', 'Lat dan Long Baru');
                $sheet->SetCellValue('A10', 'ID Pelanggan');
                $sheet->SetCellValue('A11', 'Nomor Meteran');
                $sheet->SetCellValue('A12', 'Foto Kunjungan');

                switch ($list->status) {
                    case 0:
                        $status = 'Tidak Aktif';
                        break;
                    case 1:
                        $status = "Aktif";
                        break;
                }
                $sheet->SetCellValue('B4', " : " . $status);
                $sheet->SetCellValue('B5', " : " . $list->nama_kunjungan);
                $sheet->SetCellValue('B6', " : " . $list->alamat);
                $sheet->SetCellValue('B7', " : " . $list->catatan);
                $sheet->SetCellValue('B8', " : " . $list->latitude_awal . ', ' . $list->longitude_awal);
                $sheet->SetCellValue('B9', " : " . $list->latitude_baru . ', ' . $list->longitude_baru);
                $sheet->SetCellValue('B10', " : " . $list->id_pelanggan);
                $sheet->SetCellValue('B11', " : " . $list->nomor_meteran);
                if ($list->foto_kunjungan) {
                    $sheet->getCell('B12')->getHyperlink()->setUrl($list->foto_kunjungan);
                    $sheet->SetCellValue('B12', $list->foto_kunjungan);
                } else {
                    $sheet->SetCellValue('B12', "Tidak ada foto");
                }

                $first = false;
            }

            $sheet->SetCellValue('A' . $rowCount, $no);
            $sheet->SetCellValue('B' . $rowCount, $list->nama);
            $sheet->SetCellValue('C' . $rowCount, format_indo($list->tgl_ditambahkan));

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => border_::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
        $sheet->getStyle("A14:C" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A14"; // or any value
        $to =  "C14"; // or any value
        //style
        $style_cell = array(
            'alignment' => array(
                'horizontal' => alignment::HORIZONTAL_CENTER,
                'vertical' => alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => fill::FILL_SOLID,
                'color' => array('argb' => '1269db')
            ),
            'font' => [
                'size' => 12,
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ]
        );
        $sheet->getStyle("$from:$to")->applyFromArray($style_cell);

        // create file name
        $filename = $judul . ' #' . date("YmdHis") . ".xlsx";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new xlsx($spreadsheet);
        $writer->save('php://output');
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
    public function delete($id = 0)
    {
        $sql = $this->MCore->delete_data('daftar_kunjungan', array('id_kunjungan' => $id));
        $sql = $this->MCore->delete_data('kunjungan', array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil dihapus';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal dihapus';
        }
        echo json_encode($arr);
    }

}
