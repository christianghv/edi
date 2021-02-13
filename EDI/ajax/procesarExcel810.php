<?php 
	require_once('../../plugins/Excel_LIB/reader.php');
	
	$ArchivoExcel=$_REQUEST["ArchivoExcel"].".xls";
	$ArchivoExcel='archivos/xls/'.$ArchivoExcel;
	
	if($ArchivoExcel!="")
	{	
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		
		////////////////////////////////
		$data->read($ArchivoExcel);				
		error_reporting(E_ALL ^ E_NOTICE);
		
		$filas   = $data->sheets[0]['numRows'];
		$columnas   = $data->sheets[0]['numCols'];
		
		//echo "Filas ".$filas."<br />";
		//echo "Columnas ".$columnas."<br />";
		
		
		$datos = array();
		
		//Recorriendo filas
		for ($i = 2; $i <= $filas+1; $i++) 
		{
			$Invoice_Number="";
			$Invoice_Position="";
			$PO_Number="";
			$PO_Position="";
			$Product_ID="";
			$Product_Description="";
			$Product_Measure="";
			$Product_Quantity="";
			$Porduct_Price="";
			$Pais_Origen="";
			
			
			
			//Recorriendo columna
			for ($j = 1; $j <= $columnas; $j++)
			{
				$dato=("".$data->sheets[0]['cells'][$i][$j]);

				switch ($j) 
							{
								case 1:
									$Invoice_Number=$dato;
									break;
									
								case 2:
									$Invoice_Position=$dato;
									break;
									
								case 3:
									$PO_Number=$dato;
									break;
									
								case 4:
									$PO_Position=$dato;
									break;
									
								case 5:
									$Product_ID=$dato;
									break;
									
								case 6:
									$Product_Description=$dato;
									break;
									
								case 7:
									$Product_Measure=$dato;
									break;
									
								case 8:
									$Product_Quantity=$dato;
									break;
									
								case 9:
									$Porduct_Price=$dato;
									break;
									
								case 10:
									$Pais_Origen=$dato;
									break;
							}
			}
			if($Invoice_Number!="")
			{
				$item=array("Invoice_Number"=>$Invoice_Number,
							"Invoice_Position"=>$Invoice_Position,
							"PO_Number"=> $PO_Number,
							"PO_Position"=>$PO_Position,
							"Product_ID"=>$Product_ID,
							"Product_Description"=>$Product_Description,
							"Product_Measure"=>$Product_Measure,
							"Product_Quantity"=>$Product_Quantity,
							"Porduct_Price"=>$Porduct_Price,
							"Pais_Origen"=>$Pais_Origen);
							
				array_push($datos,$item);
			}
		}
		
		echo json_encode($datos);
	}
	else
	{
		echo "[]";
	}
	
?>