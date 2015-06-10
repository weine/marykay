<?php
/**
 * API_INPUT模組
 */
class Api_input
{
	public function __construct()
	{

	}

	public function json_input($data)
	{
		if(empty($data))
		{
			return FALSE;
		}

		$arr = json_decode($data,TRUE);

		if(!is_array($arr))
		{
			return FALSE;
		}

		return $arr;
	}
}

//End