<?php

class Members extends CI_Controller
{
	public function index()
	{
		
		$data['title'] = 'Knowledge Base';
		$this->load->view('templates/header', $data);
		$this->load->view('members/index', $data);
		$this->load->view('templates/footer', $data);
	}
}
