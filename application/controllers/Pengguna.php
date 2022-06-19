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

class Pengguna extends CI_Controller
{

    public function index()
    {

        if (!$this->session->has_userdata('logged_in')) {
            redirect('auth');
        }

        $data = [
            'title' => 'Data Pengguna',
            'nav_id' => 'nav_pengguna',
            'tbody' => $this->list_(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
                'plugin/bs-custom-file-input/bs-custom-file-input.min.js',
            )
        ];

        $this->template->view('VPengguna', $data);
    }


    private function list_()
    {

        $data_pengguna = $this->MCore->list_data('pengguna');

        $no = 1;
        ob_start();
        foreach ($data_pengguna->result_array() as $value) {

            $status = '<span class="badge badge-primary">Aktif</span>';

            $button = '<button id="btn-edit" type="button" data-toggle="tooltip" data-id="' . $value['id_pengguna'] . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Edit">
            <i class="fa fa-edit"></i>
        </button>';
            if ($value['status'] == 0) {
                $status = '<span class="badge badge-danger">Tidak Aktif</span>';
                $button .= '
                <button id="btn-aktif" data-id="' . $value['id_pengguna'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Aktifkan">
                    <i class="fa fa-check text-success"></i>
                </button>';
            } else {
                $button .= '
                <button id="btn-nonaktif" data-id="' . $value['id_pengguna'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Nonaktifkan">
                    <i class="fa fa-times text-danger"></i>
                </button>';
            }
            $akun = '<span><i class="fa fa-user"></i> ' . $value['username'] . ' <br> <i class="fa fa-key"></i> ' . $value['password'] . '</span>';

?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $status ?></td>
                <td><?= $value['nama'] ?></td>
                <td><?= $akun ?></td>
                <td><img src="<?= $value['foto_pengguna'] ?>" alt="tidak ada" width="50px;"></td>
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
        $data = $this->MCore->get_data('pengguna', 'id_pengguna = ' . $id);

        echo json_encode($data->row_array());
    }

    public function save()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');

        if ($id == '') {
            // untuk create data
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[100]');
        } else {
            // untuk update data ganti password?
            if ($this->input->post('password') != NULL) {
                $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[100]');
            }
        }

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
            'nama' => $this->input->post('nama'),
            'username' => $this->input->post('username')
        );


        if ($id == '') {
            if ($this->MCore->get_data('pengguna', array('LOWER(username)' => strtolower($data['username'])))->num_rows() > 0) {
                $arr['status'] = 0;
                $arr['message'] = 'Username telah digunakan';
                echo json_encode($arr);
                exit();
            }

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['username'] . '_' . time();

                $config['upload_path']          = './upload/profil';
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
                    $config['source_image'] = './upload/profil/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/profil/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_pengguna'] = config_item('upload') . 'profil/' . $image;
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $data['password'] =  $this->input->post('password');
            $sql = $this->MCore->save_data('pengguna', $data);
        } else {

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['username'] . '_' . time();

                $config['upload_path']          = './upload/profil';
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
                    $config['source_image'] = './upload/profil/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/profil/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_pengguna'] = config_item('upload') . 'profil/' . $image;

                    //delete
                    $img_user = $this->MCore->select_data('foto_pengguna', 'pengguna', 'id_pengguna = ' . $id)->row_array();
                    $exp = explode('/', $img_user['foto_pengguna']);
                    $file_name = end($exp);

                    $path = FCPATH . 'upload/profil/' . $file_name;
                    if (isset($path)) {
                        unlink($path);
                    }
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            //jika ada update password
            if ($this->input->post('password') != NULL) {
                $data['password'] = $this->input->post('password');
            }

            $sql = $this->MCore->save_data('pengguna', $data, true, array('id_pengguna' => $id));
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

        $listInfo = $this->MCore->list_data('pengguna')->result();

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
        $sheet->SetCellValue('C5', 'Nama');
        $sheet->SetCellValue('D5', 'Username');
        $sheet->SetCellValue('E5', 'Password');
        $sheet->SetCellValue('F5', 'Foto');
        $sheet->getRowDimension('5')->setRowHeight(20);

        $judul = 'Data Pengguna';

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
            $sheet->SetCellValue('C' . $rowCount, $list->nama);
            $sheet->SetCellValue('D' . $rowCount, $list->username);
            $sheet->SetCellValue('E' . $rowCount, $list->password);
            if ($list->foto_pengguna) {
                $sheet->getCell('F' . $rowCount)->getHyperlink()->setUrl($list->foto_pengguna);
                $sheet->SetCellValue('F' . $rowCount, $list->foto_pengguna);
            } else {
                $sheet->SetCellValue('F' . $rowCount, "Tidak ada foto");
            }

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(40);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => border_::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
        $sheet->getStyle("A5:F" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A5"; // or any value
        $to =  "F5"; // or any value
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
        $sql = $this->MCore->save_data('pengguna', array('status' => 1), true, array('id_pengguna' => $id));

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
        $sql = $this->MCore->save_data('pengguna', array('status' => 0), true, array('id_pengguna' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil dinonaktifkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal dinonaktifkan';
        }
        echo json_encode($arr);
    }
}
