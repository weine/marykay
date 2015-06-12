<?php
/**
 * API_OUTPUT模組
 */
class Api_output
{
	public $json_data;
	public function __construct()
	{
		$this->json_data['status'] = '100';
	}

	public function set_data($key, $data)
	{
		if(empty($key) || empty($data))
		{
			return FALSE;
		}

		$this->json_data[$key] = $data;

		if(FALSE === $this->json_data)
		{
			return FALSE;
		}
	}

	/* 輸出JSON */
	public function json_output()
	{
		$output = json_encode($this->json_data);
		//$output = FALSE;
		if(FALSE === $output || FALSE === $this->json_data)
		{
			$output = array(
								'status' => '101',
								'info' => 'JSON輸出錯誤'
							);

			$output = json_encode($output);
		}

		echo $output;
		exit;
	}
}

//End