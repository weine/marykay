<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 客戶相關系統
 * @Author: Weine
 */
class Customer extends CI_Controller
{
	public $data;
	public function __construct()
	{
		parent::__construct();
		$this->data = array();
	}

	public function index()
	{
		$this->data['title'] = '【客戶資料系統】';

		$this->load->view('page_header.html');
		$this->parser->parse('customer.html', $this->data);
		$this->load->view('page_footer.html');
	}
}

//End