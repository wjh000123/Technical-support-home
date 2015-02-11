<?php

class Demo extends CI_Controller
{
	public function pages($page = 'index')
	{
		
		$data['title'] = ucfirst($page);
		switch($page)
		{
			case 'error':
			//case 'login':
				$this->load->view('demo/'.$page, $data);
			break;
			default:
				$this->load->view('templates/header', $data);
				$this->load->view('demo/'.$page, $data);
				$this->load->view('templates/footer', $data);
			break;
		}

	}
}
