<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('website/homepage');
	}

	public function SignUp($value='')
	{
		if ($this->input->post('SomeThink') !== NULL) {
			$this->load->view('website/signup');
		}else{
			redirect(base_url().'home');
		}
	}

	public function SignIn($value='')
	{
		$this->load->view('website/signin');
	}

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */




?>