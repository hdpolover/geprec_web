<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MAuth extends CI_Model
{

	function getLogin($data)
	{
		// cek berdasarkan username
		$this->db->where('username', $data["username"]);
		$user = $this->db->get('admin');
		$user_row = $user->row();
		// jika user terdaftar
		if ($user_row) {
			// periksa password-nya

			$isPasswordTrue = $user_row->password == $data['password'];

			// jika password benar 
			if ($isPasswordTrue) {
				$newdata = array(
					'id_admin' => $user_row->id_admin,
					'username' => $user_row->username,
					'nama'  => $user_row->nama,
					'foto_admin' => $user_row->foto_admin,
					'logged_in' => TRUE
				);

				$this->session->set_userdata($newdata);

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
