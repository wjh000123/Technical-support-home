<?php

class KnowledgeBase extends CI_Controller
{
	public function index()
	{
		
		$data['title'] = 'Cases';
		$this->load->view('templates/header', $data);
		$this->load->view('knowledgebase/index', $data);
		$this->load->view('templates/footer', $data);
	}
}
