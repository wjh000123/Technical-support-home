<?php

class Dashboard extends CI_Controller
{
	public function index()
	{
		
		$data['title'] = 'Dashboard';
		$data['navigator'] = 'Dashboard';
		$this->load->view('templates/header', $data);
		$this->load->view('dashboard/index', $data);
		$this->load->view('templates/footer', $data);
	}
}
