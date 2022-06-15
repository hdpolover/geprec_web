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

    public function saveKunjungan_post()
    {

        $data = array(
            'nomor_pelanggan'   => $this->post('nomor_pelanggan'),
            'nomor_meteran'     => $this->post('nomor_meteran'),
            'nama_kunjungan'    => $this->post('nama_kunjungan'),
            'alamat'            => $this->post('alamat'),
            'catatan'           => $this->post('catatan'),
            'latitude'          => $this->post('latitude'),
            'longitude'         => $this->post('longitude'),
            'foto_kunjungan'    => $this->post('foto_kunjungan'),
        );

        $id = $this->input->post('id');


        //save
        if ($id == '') {
            // $data['id_kunjungan'] = $this->MCore->get_newid('kunjungan', 'id_kunjungan');

            $sql = $this->MCore->save_data('kunjungan', $data);

        } else {

            $sql = $this->MCore->save_data('kunjungan', $data, true, array('us_id' => $id));
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

    public function listKunjungan_get($id_user)
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

    public function detailKunjungan_get($id_kunjungan)
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

    public function saveNgunjungi_post($id_user, $id_kunjungan)
    {

        $data = array(
            'id_pengguna' => $id_user,
            'id_kunjungan' => $id_kunjungan,
            'foto_meteran' => $this->post('foto_meteran'),
            'foto_selfie' => $this->post('foto_selfie'),
            'id_gas_pelanggan' => $this->post('id_gas_pelanggan'),
            'pembacaan_meter' => $this->post('pembacaan_meter'),
            'tgl_kunjungan' => $this->post('tgl_kunjungan'),
            'status' => $this->post('status'),
        );

        $id = $this->input->post('id');

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
}
