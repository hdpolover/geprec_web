<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Riwayat extends RestController
{

    public function __construct()
    {
        // code...
        parent::__construct();
    }

    public function list_kunjungan_get($id_user)
    {
        $option = array(
            'select'    => 'riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, 
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'riwayat_kunjungan.id_pengguna = ' . $id_user,
            'order'     => array('riwayat_kunjungan.tgl_kunjungan' => 'DESC')
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
    
    public function detail_kunjungan_get($id_riwayat_kunjungan)
    {
        $option = array(
            'select'    => 'pengguna.nama, pengguna.foto_pengguna, pengguna.username, riwayat_kunjungan.foto_meteran, riwayat_kunjungan.foto_selfie, 
            riwayat_kunjungan.id_gas_pelanggan, riwayat_kunjungan.pembacaan_meter, riwayat_kunjungan.tgl_kunjungan, riwayat_kunjungan.status, kunjungan.*',
            'table'     => 'riwayat_kunjungan',
            'join'      => array(
                array('pengguna' => 'riwayat_kunjungan.id_pengguna = pengguna.id_pengguna'),
                array('kunjungan' => 'riwayat_kunjungan.id_kunjungan = kunjungan.id_kunjungan')
            ),
            'where'     => 'riwayat_kunjungan.id_riwayat_kunjungan = ' . $id_riwayat_kunjungan
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
}