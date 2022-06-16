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
            'tbody' => $this->list_(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
            )
        ];

        $this->template->view('VRiwayat', $data);
    }


    private function list_()
    {
        $option = array(
            'select'    => 'riwayat_kunjungan.id_riwayat_kunjungan, riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, pengguna.nama, 
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'order'     => array('riwayat_kunjungan.tgl_kunjungan' => 'DESC')
        );

        $data = $this->MCore->join_table($option)->result_array();

        $no = 1;
        ob_start();
        foreach ($data as $value) {

?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $value['nama_kunjungan'] ?></td>
                <td><?= $value['nomor_pelanggan'] ?></td>
                <td><?= $value['nomor_meteran'] ?></td>
                <td><?= $value['nama'] ?></td>
                <td class="text-center">
                    <button id="btn-detail" type="button" data-toggle="tooltip" data-id="<?= $value['id_riwayat_kunjungan'] ?>" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Detail">
                        <i class="fa fa-ellipsis-h"></i>
                    </button>
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
