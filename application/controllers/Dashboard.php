<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public function index()
	{
		if (!$this->session->has_userdata('logged_in')) {
			redirect('auth/do_logout');
		}

		$now = date('Y-m-d');

		$awal_bulan = date('Y-m-01');
		$akhir_bulan = date('Y-m-t');

		// jumlah
		$kunjungan = $this->MCore->select_data('id_kunjungan', 'kunjungan')->num_rows();
		// Jumlah Kunjungan Total
		$total_kunjungan = $this->MCore->select_data('tgl_kunjungan', 'riwayat_kunjungan')->num_rows();
		// Daftar kunjungan
		$daftar_kunjungan = $this->MCore->select_data('id_daftar_kunjungan', 'daftar_kunjungan')->num_rows();
		// jumlah kunjungan bulan
		// $jml_kunjungan_bulan = $this->MCore->select_data('tgl_kunjungan', 'riwayat_kunjungan', 'tgl_kunjungan BETWEEN "' . $awal_bulan . '" AND "' . $akhir_bulan . '"')->num_rows();
		// jumlah
		$pengguna = $this->MCore->select_data('id_pengguna', 'pengguna')->num_rows();

		// riwayat
		$option = array(
			'select'    => 'id_riwayat_kunjungan',
			'table'     => 'riwayat_kunjungan',
			'join'      => array(
				array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
				array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
			),
		);

		$jml_riwayat = $this->MCore->join_table($option)->num_rows();

		$option = array(
			'select'    => 'id_riwayat_kunjungan',
			'table'     => 'riwayat_kunjungan',
			'join'      => array(
				array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
				array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
			),
			'where'     => 'riwayat_kunjungan.status = 0'
		);

		$jml_riwayat_0 = $this->MCore->join_table($option)->num_rows();

		$option = array(
			'select'    => 'id_riwayat_kunjungan',
			'table'     => 'riwayat_kunjungan',
			'join'      => array(
				array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
				array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
			),
			'where'     => 'riwayat_kunjungan.status = 1'
		);

		$jml_riwayat_1 = $this->MCore->join_table($option)->num_rows();

		$option = array(
			'select'    => 'id_riwayat_kunjungan',
			'table'     => 'riwayat_kunjungan',
			'join'      => array(
				array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
				array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
			),
			'where'     => 'riwayat_kunjungan.status = 2'
		);

		$jml_riwayat_2 = $this->MCore->join_table($option)->num_rows();

		$data = array(
			'title' => 'Dashboard',
			'nav_id' => 'nav_dashboard',
			'total_kunjungan' => $total_kunjungan,
			'daftar_kunjungan' => $daftar_kunjungan,
			'pengguna' => $pengguna,
			'kunjungan' => $kunjungan,
			'jml_riwayat' => $jml_riwayat,
			'jml_riwayat_0' => $jml_riwayat_0,
			'jml_riwayat_1' => $jml_riwayat_1,
			'jml_riwayat_2' => $jml_riwayat_2,
			'detail_pengguna' => $this->detail_pengguna()
		);

		$this->template->view('VDashboard', $data);
	}

	private function detail_pengguna()
	{
		$option = array(
			'select'	=> 'pengguna.nama, pengguna.username, count(pengguna.id_pengguna) jml',
			'table'		=> 'daftar_kunjungan',
			'join'		=> array('pengguna' => 'daftar_kunjungan.id_pengguna = pengguna.id_pengguna'),
			'group'		=> 'pengguna.id_pengguna'
		);

		$data = $this->MCore->join_table($option)->result_array();
		ob_start();
		foreach ($data as $row) {
?>
			<div class="d-flex">
				<div class="avatar">
					<span class="avatar-title rounded-circle border border-white bg-info"><?= substr($row['nama'], 0, 1); ?></span>
				</div>
				<div class="flex-1 ml-3 pt-1">
					<h4 class="text-uppercase fw-bold mb-1"><?= $row['nama'] ?></h4>
					<span class="text-muted"><?= $row['username'] ?></span>
				</div>
				<div class="float-right pt-1">
					<small class="text-muted"><?= $row['jml'] ?> kunjungan</small>
				</div>
			</div>
			<div class="separator-dashed"></div>
<?php
		}
		$body = ob_get_contents();
		ob_clean();
		return $body;
	}
}
