<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Kunjungan extends RestController
{

    public function __construct()
    {
        // code...
        parent::__construct();
    }

    public function save_kunjungan_post()
    {

        $data = array(
            'id_pelanggan'      => $this->post('id_pelanggan'),
            'nomor_meteran'     => $this->post('nomor_meteran'),
            'nama_kunjungan'    => $this->post('nama_kunjungan'),
            'alamat'            => $this->post('alamat'),
            'catatan'           => $this->post('catatan'),
            'latitude_awal'     => $this->post('latitude_awal'),
            'longitude_awal'    => $this->post('longitude_awal'),
            'latitude_baru'     => $this->post('latitude_baru'),
            'longitude_baru'    => $this->post('longitude_baru')
        );

        $id = $this->post('id');

        if ($this->post('foto_kunjungan')) {
            // $url_param = rtrim($this->post('foto_kunjungan'), '=');
            // // and later:
            // $base_64 = $url_param . str_repeat('=', strlen($url_param) % 4);
            // $img = base64_decode($base_64);
            $file = $this->post('foto_kunjungan');
            $pos = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $mime = explode('/', $type);

            $pathImage = "./upload/foto/" . time() . "." . $mime[1];
            $file = substr($file, strpos($file, ',') + 1, strlen($file));
            $dataBase64 = base64_decode($file);

            if (file_put_contents($pathImage, $dataBase64)) {
                $data['foto_kunjungan'] = base_url() . 'upload/foto/' . time() . "." . $mime[1];
            } else {
                // echo $error;
                $this->response([
                    'status' => '404',
                    'message' => "Terjadi Kesalahan saat upload"
                ], 404);

                exit();
            }
        }

        //save
        if ($id == '') {
            // $data['id_kunjungan'] = $this->MCore->get_newid('kunjungan', 'id_kunjungan');

            $sql = $this->MCore->save_data('kunjungan', $data);

            $d_kunjungan = array(
                'id_pengguna'   => $this->post('id_pengguna'),
                'id_kunjungan'  => $this->MCore->get_lastid('kunjungan', 'id_kunjungan'),
                'tgl_ditambahkan' => date('Y-m-d H:i:s')
            );

            $this->MCore->save_data('daftar_kunjungan', $d_kunjungan);
        } else {

            $sql = $this->MCore->save_data('kunjungan', $data, true, array('id' => $id));
        }

        // CHECK
        if ($sql) {
            $this->response([
                'status' => '200',
                'message' => 'Data Berhasil Ditambahkan'
            ], 200);
        } else {
            $this->response([
                'status' => '404',
                'message' => 'Terjadi Kesalahan!'
            ], 404);
        }
    }

    public function list_kunjungan_get($id_user)
    {

        $option = array(
            'select'    => 'pengguna.*, daftar_kunjungan.tgl_ditambahkan, kunjungan.*',
            'table'     => 'pengguna',
            'join'      => array(
                array('daftar_kunjungan' => 'daftar_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'daftar_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'daftar_kunjungan.id_pengguna = ' . $id_user,
            'order'     => array('tgl_ditambahkan' => 'DESC')
        );

        $data = $this->MCore->join_table($option)->result_array();


        // Check if the users data store contains users
        if ($data) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => $data
            ], 200);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => NULL
            ], 404);
        }
    }

    public function check_kunjungan_get(){
        
        $id_user = $this->get('id_pengguna');
        $id_kunjungan = $this->get('id_kunjungan');

        $option = array(
            'select'    => 'riwayat_kunjungan.tgl_kunjungan',
            'table'     => 'pengguna',
            'join'      => array(
                array('riwayat_kunjungan' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'riwayat_kunjungan.id_pengguna = ' . $id_user . ' AND riwayat_kunjungan.id_kunjungan = ' . $id_kunjungan . ' AND DATE(riwayat_kunjungan.tgl_kunjungan) = "'.date('Y-m-d').'"',
        );

        $data = $this->MCore->join_table($option)->num_rows();


        // Check if the users data store contains users
        if ($data > 0) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => 1
            ], 200);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => 0
            ], 404);
        }
    }

    public function detail_kunjungan_get($id_kunjungan)
    {

        $option = array(
            'select'    => 'pengguna.*, daftar_kunjungan.tgl_ditambahkan, kunjungan.*',
            'table'     => 'pengguna',
            'join'      => array(
                array('daftar_kunjungan' => 'daftar_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'daftar_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'kunjungan.id_kunjungan = ' . $id_kunjungan,
            'order'     => array('tgl_ditambahkan' => 'DESC')
        );

        $data = $this->MCore->join_table($option)->result_array();


        // Check if the users data store contains users
        if ($data) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => $data
            ], 200);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => NULL
            ], 404);
        }
    }

    public function save_ngunjungi_post()
    {
        $data = array(
            'id_pengguna' => $this->post('id_pengguna'),
            'id_kunjungan' => $this->post('id_kunjungan'),
            'id_gas_pelanggan' => $this->post('id_gas_pelanggan'),
            'pembacaan_meter' => $this->post('pembacaan_meter'),
            'tgl_kunjungan' => $this->post('tgl_kunjungan'),
            'status' => $this->post('status'),
        );

        $id = $this->input->post('id');

        if ($this->post('foto_meteran')) {

            // untuk watermark
            $lat = $this->post('latitude');
            $long = $this->post('longitude');

            $file = $this->post('foto_meteran');
            $pos = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $mime = explode('/', $type);

            $pathImage = "./upload/foto/" . "meteran_" . time() . "." . $mime[1];
            $file = substr($file, strpos($file, ',') + 1, strlen($file));
            $dataBase64 = base64_decode($file);

            if (file_put_contents($pathImage, $dataBase64)) {
                $data['foto_meteran'] = base_url() . 'upload/foto/' . "meteran_" . time() . "." . $mime[1];

                $imgConfig = array();
                $imgConfig['image_library']   = 'GD';
                $imgConfig['source_image']    = $pathImage;
                $imgConfig['wm_text']         = format_indo(date('Y-m-d H:i:s'));
                $imgConfig['wm_type']         = 'text';
                $imgConfig['wm_font_color'] = 'ffff00';
                $imgConfig['wm_font_path'] = realpath('./assets/fonts/Roboto-Regular.ttf');
                $imgConfig['wm_font_size']    = '20';
                $imgConfig['wm_vrt_alignment'] = 'bottom';
                $imgConfig['wm_hor_alignment'] = 'right';
                $imgConfig['wm_padding'] = '-10';
                $this->load->library('image_lib', $imgConfig);
                $this->image_lib->initialize($imgConfig);
                if (!$this->image_lib->watermark()) {
                    echo $this->image_lib->display_errors();
                }

                $imgConfig = array();
                $imgConfig['image_library']   = 'GD2';
                $imgConfig['source_image']    = $pathImage;
                $imgConfig['wm_text']         = 'LAT : ' . $lat . ', LONG : ' . $long;
                $imgConfig['wm_type']         = 'text';
                $imgConfig['wm_font_color'] = 'ffff00';
                $imgConfig['wm_font_path'] = realpath('./assets/fonts/Roboto-Regular.ttf');
                $imgConfig['wm_font_size']    = '26';
                $imgConfig['wm_vrt_alignment'] = 'bottom';
                $imgConfig['wm_hor_alignment'] = 'right';
                $imgConfig['wm_padding'] = '-56';
                $this->load->library('image_lib', $imgConfig);
                $this->image_lib->initialize($imgConfig);
                if (!$this->image_lib->watermark()) {
                    echo $this->image_lib->display_errors();
                }
            } else {
                // echo $error;
                $this->response([
                    'status' => '404',
                    'message' => "Terjadi Kesalahan saat upload"
                ], 404);

                exit();
            }
        }

        if ($this->post('foto_selfie')) {
            // $url_param = rtrim($this->post('foto_kunjungan'), '=');
            // // and later:
            // $base_64 = $url_param . str_repeat('=', strlen($url_param) % 4);
            // $img = base64_decode($base_64);
            $file = $this->post('foto_selfie');
            $pos = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $mime = explode('/', $type);

            $pathImage = "./upload/foto/" . "selfie_" . time() . "." . $mime[1];
            $file = substr($file, strpos($file, ',') + 1, strlen($file));
            $dataBase64 = base64_decode($file);

            if (file_put_contents($pathImage, $dataBase64)) {
                $data['foto_selfie'] = base_url() . 'upload/foto/' . "selfie_"  . time() . "." . $mime[1];
            } else {
                // echo $error;
                $this->response([
                    'status' => '404',
                    'message' => "Terjadi Kesalahan saat upload"
                ], 404);

                exit();
            }
        }
        //save
        if ($id == '') {
            // $data['id_kunjungan'] = $this->MCore->get_newid('kunjungan', 'id_kunjungan');

            $sql = $this->MCore->save_data('riwayat_kunjungan', $data);
        } else {

            $sql = $this->MCore->save_data('riwayat_kunjungan', $data, true, array('us_id' => $id));
        }

        // CHECK
        if ($sql) {
            $this->response([
                'status' => '200',
                'message' => 'Data Berhasil Ditambahkan'
            ], 200);
        } else {
            $this->response([
                'status' => '404',
                'message' => 'Terjadi Kesalahan!'
            ], 404);
        }
    }

    public function last_kunjungan_get()
    {

        $id_user = $this->get('id_pengguna');
        $id_kunjungan = $this->get('id_kunjungan');

        $option = array(
            'select'    => 'MAX(riwayat_kunjungan.tgl_kunjungan) tgl_kunjungan',
            'table'     => 'pengguna',
            'join'      => array(
                array('riwayat_kunjungan' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'riwayat_kunjungan.id_pengguna = ' . $id_user . ' AND riwayat_kunjungan.id_kunjungan = ' . $id_kunjungan,
        );

        $data = $this->MCore->join_table($option)->row_array();


        // Check if the users data store contains users
        if ($data) {
            // Set the response and exit
            $this->response([
                'status' => true,
                'message' => 'Berhasil',
                'data'  => $data
            ], 200);
        } else {
            // Set the response and exit
            $this->response([
                'status' => false,
                'message' => 'Tidak ada data',
                'data' => NULL
            ], 404);
        }
    }

    public function update_kunjungan_post($id_kunjungan)
    {

        $data = array(
            'latitude_baru' => $this->post('latitude_baru'),
            'longitude_baru' => $this->post('longitude_baru')
        );

        $sql = $this->MCore->save_data('kunjungan', $data, true, array('id_kunjungan' => $id_kunjungan));

        // CHECK
        if ($sql) {
            $this->response([
                'status' => '200',
                'message' => 'Data Berhasil Diperbarui'
            ], 200);
        } else {
            $this->response([
                'status' => '404',
                'message' => 'Terjadi Kesalahan!'
            ], 404);
        }
    }

    public function delete_kunjungan_post()
    {

        $id = $this->post('id_kunjungan');

        $count_riwayat = $this->MCore->select_data('id_kunjungan', 'riwayat_kunjungan', 'id_kunjungan = ' . $id)->num_rows();

        // CHECK
        if ($count_riwayat == 0) {

            $sql = $this->MCore->delete_data('daftar_kunjungan', array('id_kunjungan' => $id));
            $sql = $this->MCore->delete_data('kunjungan', array('id_kunjungan' => $id));

            if ($sql) {
                $this->response([
                    'status' => '200',
                    'message' => 'Data Berhasil Dihapus'
                ], 200);
            } else {
                $this->response([
                    'status' => '404',
                    'message' => 'Terjadi Kesalahan!'
                ], 404);
            }
        } else {
            $this->response([
                'status' => '404',
                'message' => 'Tidak bisa dihapus karena sudah ada riwayat kunjungan'
            ], 404);
        }
    }
}
