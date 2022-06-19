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

class Riwayat extends CI_Controller
{

    public function index()
    {

        if (!$this->session->has_userdata('logged_in')) {
            redirect('auth');
        }

        $data = [
            'title' => 'Data Riwayat',
            'nav_id' => 'nav_riwayat',
            'opt_nama' => $this->opt_nama(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
                'plugin/moment/moment.min.js',
                'plugin/datepicker/tempusdominus-bootstrap-4.js',
                'plugin/daterangepicker/daterangepicker.js',
                'plugin/select2/select2.full.min.js',
            ),
            'css' => array(
                'plugin/datepicker/tempusdominus-bootstrap-4.css',
                'plugin/daterangepicker/daterangepicker.css',
                'plugin/select2/select2.min.css',
            )
        ];

        $this->template->view('VRiwayat', $data);
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

    public function list_()
    {
        $tanggal = $this->input->get('filter_tanggal');
        $explode = explode(" ", $tanggal);

        $tgl_awal = date("Y-m-d", strtotime(implode('-', (explode('/', $explode[0])))));
        $tgl_akhir =  date("Y-m-d", strtotime(implode('-', (explode('/', $explode[2])))));

        $filter['tgl_kunjungan >='] = $tgl_awal;
        $filter['tgl_kunjungan <='] = $tgl_akhir;


        $nama = $this->input->get('filter_nama');
        $filter['riwayat_kunjungan.id_pengguna'] = $nama;

        $option = array(
            'select'    => 'riwayat_kunjungan.id_riwayat_kunjungan, riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, pengguna.nama, 
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan,riwayat_kunjungan.status rstatus, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => array_filter($filter),
            'column_order' => array(null, null, 'nama_kunjungan', 'alamat', 'catatan' ,'nama', 'tgl_kunjungan', null),
            'column_search' => array('nama_kunjungan',  'alamat', 'catatan', 'nama', 'tgl_kunjungan'),
            'order'     => array('riwayat_kunjungan.tgl_kunjungan' => 'DESC')
        );

        $list_data = $this->MCore->get_datatables($option);

        header('Content-Type: application/json');
        $data = array();
        $no = $this->input->post('start');
        foreach ($list_data as $value) {

            $no++;
            $button = '<button id="btn-detail" type="button" data-toggle="tooltip" data-id="' . $value->id_riwayat_kunjungan . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Detail">
            <i class="fa fa-ellipsis-h"></i>
        </button><br>';
            if ($value->rstatus == 0) {
                $status = '<span class="badge badge-warning">Menunggu</span>';
                $button .= '<button id="btn-aktif" type="button" data-toggle="tooltip" data-id="' . $value->id_riwayat_kunjungan . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Diterima">
                <i class="fa fa-check"></i>
            </button>
            <button id="btn-nonaktif" type="button" data-toggle="tooltip" data-id="' . $value->id_riwayat_kunjungan . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Ditolak">
                <i class="fa fa-times text-danger"></i>
            </button>';
            } else if ($value->rstatus == 1) {
                $status = '<span class="badge badge-primary">Diterima</span>';
            } else if ($value->rstatus == 2) {
                $status = '<span class="badge badge-danger">Ditolak</span>';
            }
?>
        <?php
            $row = array();
            $row[] = $no;
            $row[] = $status;
            $row[] = $value->nama_kunjungan;
            $row[] = $value->alamat;
            $row[] = $value->catatan;
            $row[] = $value->nama;
            $row[] = format_indo($value->tgl_kunjungan);
            $row[] =  $button;
            $data[] = $row;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->MCore->count_all('riwayat_kunjungan'),
            "recordsFiltered" => $this->MCore->count_filtered($option),
            "data" => array_values($data),
        );
        //output to json format
        $this->output->set_output(json_encode($output));
    }

    public function edit($id = 0)
    {
        $option = array(
            'select'    => 'riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, pengguna.nama,
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, riwayat_kunjungan.status rstatus, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'riwayat_kunjungan.id_riwayat_kunjungan = ' . $id
        );

        $data = $this->MCore->join_table($option)->row_array();

        ob_start();
        ?>
        <div class="row mb-2">
            <div class="col-4">
                <span class="h4">Status</span>
            </div>
            <div class="">
                <?php
                if ($data['rstatus'] == 0) {
                    echo  '<span class="badge badge-warning">Menunggu</span>';
                } else if ($data['rstatus'] == 1) {
                    echo  '<span class="badge badge-primary">Diterima</span>';
                } else if ($data['rstatus'] == 2) {
                    echo '<span class="badge badge-danger">Ditolak</span>';
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <span class="h4">Petugas</span>
            </div>
            <div class="col p-0">
                <div class="font-weight-bold h4"><?= $data['nama']; ?></div>
            </div>
        </div>
        <hr>
        <h4>Data Kunjungan</h4>
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
                Lat-Long Awal
            </div>
            <div class="col">
                : <?= $data['latitude_awal'] . ', ' . $data['longitude_awal'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                Lat-Long Baru
            </div>
            <div class="col">
                : <?= $data['latitude_baru'] . ', ' . $data['longitude_baru'] ?>
            </div>
        </div>
        <hr>
        <h4>Data Riwayat</h4>
        <div class="row">
            <div class="col-4">
                ID Gas Pelanggan
            </div>
            <div class="col">
                : <?= $data['id_gas_pelanggan'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                Pembacaan Meteran
            </div>
            <div class="col">
                : <?= $data['pembacaan_meter'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                Tanggal Kunjungan
            </div>
            <div class="col">
                : <?= format_indo($data['tgl_kunjungan']) ?>
            </div>
        </div>
        <hr>
        <h4>Bukti Dokumen</h4>
        <div class="row">
            <div class="col-12">
                <h5 class="font-weight-bold">Foto Selfie</h5>
                <?php if ($data['foto_selfie']) { ?>
                    <img src="<?= $data['foto_selfie'] ?>" alt="foto selfie" style="max-width: inherit;">
                <?php } else {
                    echo "tidak ada";
                } ?>
            </div>
            <div class="col-12 mt-4">
                <h5 class="font-weight-bold">Foto Meteran</h5>
                <?php if ($data['foto_meteran']) { ?>
                    <img src="<?= $data['foto_meteran'] ?>" alt="foto meteran" style="max-width: inherit;">
                <?php } else {
                    echo "tidak ada";
                } ?>
            </div>
        </div>
<?php
        $body = ob_get_contents();
        ob_clean();
        echo json_encode(array('data' => $body));
    }

    public function export_excel($id = 0)
    {

        //unlimited
        ini_set('max_execution_time', 0);

        $tanggal = $this->input->get('filter_tanggal');
        $explode = explode(" ", $tanggal);

        $tgl_awal = date("Y-m-d", strtotime(implode('-', (explode('/', $explode[0])))));
        $tgl_akhir =  date("Y-m-d", strtotime(implode('-', (explode('/', $explode[2])))));

        $filter['tgl_kunjungan >='] = $tgl_awal;
        $filter['tgl_kunjungan <='] = $tgl_akhir;

        $nama = $this->input->get('filter_nama');
        $filter['riwayat_kunjungan.id_pengguna'] = $nama;

        $filter['riwayat_kunjungan.id_riwayat_kunjungan'] = $id;

        $option = array(
            'select'    => 'riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, pengguna.nama,
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, riwayat_kunjungan.status rstatus, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => array_filter($filter)
        );

        $listInfo = $this->MCore->join_table($option)->result(); // Panggil fungsi filter 

        // load excel library
        $spreadsheet = new spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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
        $sheet->SetCellValue('F5', 'Lat-Long Awal');
        $sheet->SetCellValue('G5', 'Lat-Long Baru');
        $sheet->SetCellValue('H5', 'ID Gas Pelanggan');
        $sheet->SetCellValue('I5', 'Pembacaan Meteran');
        $sheet->SetCellValue('J5', 'Foto Meteran');
        $sheet->SetCellValue('K5', 'Tanggal Kunjungan');
        $sheet->SetCellValue('L5', 'Foto Selfie');
        $sheet->SetCellValue('M5', 'Petugas');
        $sheet->getRowDimension('5')->setRowHeight(20);

        $judul = 'Data Riwayat Kunjungan';

        $sheet->SetCellValue('A2', $judul);
        $sheet->getStyle("A2")->getFont()->setSize(18);
        $sheet->getStyle("A2")->getAlignment()->setVertical(alignment::VERTICAL_CENTER);
        $sheet->mergeCells('A2:C2');

        $sheet->SetCellValue('A3', "Tanggal : ");
        $sheet->SetCellValue('B3', date_indo($tgl_awal) . " - " . date_indo($tgl_akhir));
        // set Row
        $rowCount = 6;
        $no = 1;

        foreach ($listInfo as $list) {

            $sheet->SetCellValue('A' . $rowCount, $no);
            switch ($list->rstatus) {
                case 0:
                    $status = 'Menunggu';
                    break;
                case 1:
                    $status = "Diterima";
                    break;
                case 2:
                    $status = "Ditolak";
                    break;
            }
            $sheet->SetCellValue('B' . $rowCount, $status);
            $sheet->SetCellValue('C' . $rowCount, $list->nama_kunjungan);
            $sheet->SetCellValue('D' . $rowCount, $list->alamat);
            $sheet->SetCellValue('E' . $rowCount, $list->catatan);
            $sheet->SetCellValue('F' . $rowCount, $list->latitude_awal . ', ' . $list->longitude_awal);
            $sheet->SetCellValue('G' . $rowCount, $list->latitude_baru . ', ' . $list->longitude_baru);
            $sheet->SetCellValue('H' . $rowCount, $list->id_gas_pelanggan);
            $sheet->SetCellValue('I' . $rowCount, $list->pembacaan_meter);
            if ($list->foto_meteran) {
                $sheet->getCell('J' . $rowCount)->getHyperlink()->setUrl($list->foto_meteran);
                $sheet->SetCellValue('J' . $rowCount, $list->foto_meteran);
            } else {
                $sheet->SetCellValue('J' . $rowCount, "Tidak ada foto");
            }
            $sheet->SetCellValue('K' . $rowCount, format_indo($list->tgl_kunjungan));
            if ($list->foto_selfie) {
                $sheet->getCell('L' . $rowCount)->getHyperlink()->setUrl($list->foto_selfie);
                $sheet->SetCellValue('L' . $rowCount, $list->foto_selfie);
            } else {
                $sheet->SetCellValue('L' . $rowCount, "Tidak ada foto");
            }
            $sheet->SetCellValue('M' . $rowCount, $list->nama);

            $no++;
            $rowCount++;
        }

        // SIZE WIDTH
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(70);
        $sheet->getColumnDimension('E')->setWidth(70);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(30);
        $sheet->getColumnDimension('M')->setWidth(30);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => border_::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
        $sheet->getStyle("A5:M" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A5"; // or any value
        $to =  "M5"; // or any value
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
        $sql = $this->MCore->save_data('riwayat_kunjungan', array('status' => 1), true, array('id_riwayat_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil diaktifkan';
        } else {
            $arr['message'] = 'Data gagal diaktifkan';
        }
        echo json_encode($arr);
    }

    public function nonaktif($id = 0)
    {
        $sql = $this->MCore->save_data('riwayat_kunjungan', array('status' => 2), true, array('id_riwayat_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil dinonaktifkan';
        } else {
            $arr['message'] = 'Data gagal dinonaktifkan';
        }
        echo json_encode($arr);
    }
}
