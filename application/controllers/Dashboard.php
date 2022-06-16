<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
		if (!$this->session->has_userdata('logged_in')) {
			redirect('auth/do_logout');
		}

		$data = array(
			'title' => 'Dashboard',
			'nav_id' => 'nav_dashboard',
		);
		
		$this->template->view('VDashboard', $data);
	}
}
