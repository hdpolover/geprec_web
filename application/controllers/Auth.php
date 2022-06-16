<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function index()
	{

		if ($this->session->has_userdata('logged_in')) {
			redirect(base_url('dashboard'));
		}

		$data = [
			'title' => 'Login'
		];

		$this->load->view('VAuth', $data);
	}

	public function do_login()
	{
		$data = array(
			'username' => $this->input->post('username'),
			'password' => $this->input->post('password')
		);

		$result = $this->MAuth->getLogin($data);

		if ($result) {
			echo "success";
		} else {
			echo "error";
		}
	}

	public function do_logout()
	{
		// hancurkan semua sesi
		$this->session->sess_destroy();
		redirect(base_url('auth'));
	}
}