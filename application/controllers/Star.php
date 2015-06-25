<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 分析卡入口
 * @Author: Weine
 */
class Star extends CI_Controller
{

	public $data;
	public function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->data['base_url'] = base_url();
		$this->data['js'] = base_url() . 'asset/js';
		$this->data['css'] = base_url() . 'asset/css';
	}

	public function index()
	{
		/* load model */
		$this->load->model('Customer_model');

		/* 取得題目 */
		$res_qu = $this->Customer_model->get_qu();
		if(FALSE === $res_qu)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		else
		{
			$this->data = array_merge($res_qu, $this->data);
		}

		/* 取得答案 */
		$res_ans = $this->Customer_model->get_ans();
		if(FALSE === $res_ans)
		{
			$output['status'] = "101";
			$output['api_msg'] = "資料庫操作失敗!";
		}
		else
		{
			$this->data = array_merge($res_ans, $this->data);
		}

		$this->parser->parse('star.html', $this->data);
	}
}

//End