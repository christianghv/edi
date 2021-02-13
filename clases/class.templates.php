<?php
error_reporting(0);
class templates 
{

	public function __construct($template)
	{
		$this->template = $template;
	}
	
	public function setParams($params)
	{
		$this->params = $params;
	}
	
	public function show($return = false)
	{
		$file = $this->template;
		
		if (!file_exists($file)) die('No existe el template' . $file);
		
		$reader = fopen($file,"r");
		
		$html = fread($reader, filesize($file));
		fclose($reader);
		if (count($this->params) > 0) {
			foreach($this->params as $key => $value)
			{
				$html = str_replace ("{" . $key . "}",$value,$html);
			}
		}
		if ($return)
		    return $html;
		else
			 echo $html;
	}
}
?>