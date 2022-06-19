<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
        // load excel library
        $this->load->library('excel');

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
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Alamat Kunjungan');
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Catatan');
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Latitude Longitude Awal');
        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Latitude Longitude Baru');
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'ID Gas Pelanggan');
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Pembacaan Meteran');
        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Foto Meteran');
        $objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Tanggal Kunjungan');
        $objPHPExcel->getActiveSheet()->SetCellValue('L5', 'Foto Selfie');
        $objPHPExcel->getActiveSheet()->SetCellValue('M5', 'Petugas');
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(20);

        $judul = 'Data Riwayat Kunjungan';

        $objPHPExcel->getActiveSheet()->SetCellValue('A2', $judul);
        $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

        $objPHPExcel->getActiveSheet()->SetCellValue('A3', "Tanggal : ");
        $objPHPExcel->getActiveSheet()->SetCellValue('B3', date_indo($tgl_awal) . " - " . date_indo($tgl_akhir));
        // set Row
        $rowCount = 6;
        $no = 1;

        foreach ($listInfo as $list) {

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $no);
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
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $status);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $list->nama_kunjungan);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $list->alamat);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $list->catatan);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $list->latitude_awal . ', ' . $list->longitude_awal);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $list->latitude_baru . ', ' . $list->longitude_baru);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $list->id_gas_pelanggan);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $list->pembacaan_meter);
            if ($list->foto_meteran) {
                $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount)->getHyperlink()->setUrl($list->foto_meteran);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $list->foto_meteran);
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, "Tidak ada foto");
            }
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, format_indo($list->tgl_kunjungan));
            if ($list->foto_selfie) {
                $objPHPExcel->getActiveSheet()->getCell('L' . $rowCount)->getHyperlink()->setUrl($list->foto_selfie);
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $list->foto_selfie);
            } else {
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, "Tidak ada foto");
            }
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $list->nama);

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
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);

        // TABEL
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A5:N" . ($rowCount - 1))->applyFromArray($styleArray);

        // ini untuk style header
        $from = "A5"; // or any value
        $to =  "N5"; // or any value
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
