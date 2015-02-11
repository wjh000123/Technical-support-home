<?php
//define('VIEWPATH', APPPATH.'views/');

class Pages extends CI_Controller
{
	public function view($page = 'home')
	{
		if (!file_exists(VIEWPATH.'pages/'.$page.'.php'))
		{
			show_404();
		}
		
		$data['title'] = ucfirst($page);
		$this->load->view('templates/header.php', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer.php', $data);
	}
}
