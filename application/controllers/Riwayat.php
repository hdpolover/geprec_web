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
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => array_filter($filter),
            'column_order' => array(null, null, 'nama_kunjungan', 'nomor_pelanggan', 'nomor_meteran', 'nama', 'tgl_kunjungan', null),
            'column_search' => array('nama_kunjungan', 'nomor_pelanggan', 'nomor_meteran', 'nama', 'tgl_kunjungan'),
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
        </button>';
        if ($value->status == 0) {
            $status = '<span class="badge badge-warning">Menunggu</span>';
        } else if($value->status == 1) {
            $status = '<span class="badge badge-primary">Diterima</span>';
        } else if($value->status == 2){
            $status = '<span class="badge badge-danger">Ditolak</span>';
        }
?>
        <?php
            $row = array();
            $row[] = $no;
            $row[] = $status;
            $row[] = $value->nama_kunjungan;
            $row[] = $value->nomor_pelanggan;
            $row[] = $value->nomor_meteran;
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
            'select'    => 'riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, 
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, kunjungan.*',
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
                Nomor Kunjungan
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
                Latitude dan Longitude
            </div>
            <div class="col">
                : <?= $data['latitude'] . ', ' . $data['longitude'] ?>
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
            <div class="col">
                <p>Foto Selfie</p>
                <?php if ($data['foto_selfie']) { ?>
                    <img src="<?= $data['foto_selfie'] ?>" alt="foto selfie" width="200px;">
                <?php } else {
                    echo "tidak ada";
                } ?>
            </div>
            <div class="col">
                <p>Foto Meteran</p>
                <?php if ($data['foto_meteran']) { ?>
                    <img src="<?= $data['foto_meteran'] ?>" alt="foto meteran" width="200px;">
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
}
