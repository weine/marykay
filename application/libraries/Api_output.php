<?php
/**
 * API_OUTPUT模組
 */
class Api_output
{
	public function __construct()
	{

	}

	public function json_output($key, $data)
	{
		if(empty($key) || empty($data))
		{
			return FALSE;
		}

		$arr_key_data = array($key => $data);

		$json_data = json_encode($arr_key_data);

		if(FALSE === $json_data)
		{
			return FALSE;
		}

		echo $json_data;
		exit;
	}
}

//End