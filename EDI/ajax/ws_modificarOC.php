<?php
	require_once("../../plugins/nusoap/nusoap.php");
	require_once("../../conf_sap.php");
	include("../../conect/conect.php");
	include_once("SegAjax.php");
	$data	= $_POST["data"];
	$nro_oc = $_POST["nro_oc"];
	
	ini_set('memory_limit', "512M");
	ini_set('max_execution_time', 600); //x saniye
	ini_set("display_errors", 1);
	ini_set("max_input_time ", 600);
	set_time_limit(600);
		
		$filas = split("#",$data);
		$arr_pos = array();
		$all_data = array();
		//OBTENGO POSICIONES //
		foreach($filas as $fila)  
		{
			$arr_fila = split(";",$fila);
			if($arr_fila[0] != "" && $arr_fila[0] != null && $arr_fila[0] != ' ' )
			{
				if(in_array($arr_fila[0], $arr_pos) == false)
					$arr_pos[] = $arr_fila[0];
			}
		}
		// ASIGNO POSICIONES //
		foreach($arr_pos as $pos) 
		{
			$datos 				= array();
			$datos["eBELP"] 	= $pos;
			$datos["mATNR"]  	= "";
			$datos["mATNRgX"] 	= "";
			$datos["mENGE"] 	= "";
			$datos["mENGEgX"] 	= "";
			$datos["nETPR"] 	= "";
			$datos["nETPRgX"] 	= "";	
			$all_data[] = $datos;
		}
		//ASIGNO DATOS AL ARRAY FINAL //
		for($i=0;$i<count($all_data);$i++)
		{
			foreach($filas as $fila)
			{
				$fil = split(";",$fila);

				if($fil[0] == $all_data[$i]["eBELP"]) 
				{
					//modificar checked
					//if($fil[3] == '1' || $fil[3] == 1) 
					//{ 
						if($fil[1] == "PRECIO") {
							$all_data[$i]["nETPR"] = $fil[2];
							$all_data[$i]["nETPRgX"] = "X";
						}
						if($fil[1] == "CANTIDAD") {
							$all_data[$i]["mENGE"] = $fil[2];
							$all_data[$i]["mENGEgX"] = "X";
						}
						if($fil[1] == "MATERIAL") {
							$all_data[$i]["mATNR"] = $fil[2];
							$all_data[$i]["mATNRgX"] = "X";
						}
					//}
				}
			}
		}
		
		function ActualizarEE($ArrayEE,$nro_oc)
		{
			$afectadas=0;
			$vueltasEE=0;
			foreach($ArrayEE as $Row)
			{
				//Verificar si MATERIAL
				if((string)$Row['mATNRgX']=="X")
				{
					$SqlUpdate="update PO_DETAIL set PO_PartNumber='".$Row['mATNR']."',ModifNroParte=1 
								where PO_Item='".$Row['eBELP']."' 
								and PO_Number='".$nro_oc."';";
					//echo $SqlUpdate;
					//die();
					
					$conexion = conectar_srvdev();	
	
					$result = sqlsrv_query($conexion, $SqlUpdate);
					$afectadas=sqlsrv_rows_affected($conexion);
					sqlsrv_close($conexion);	
				}
				
				//Verificar si CANTIDAD
				if((string)$Row['mENGEgX']=="X")
				{
					$SqlUpdate="update PO_DETAIL set PO_Quantity='".$Row['mENGE']."', ModifCantidad=1 
								where PO_Item='".$Row['eBELP']."' 
								and PO_Number='".$nro_oc."';";
					//echo $SqlUpdate;
					//die();
					$conexion = conectar_srvdev();	
	
					$result = sqlsrv_query($conexion, $SqlUpdate);
					$afectadas=sqlsrv_rows_affected($conexion);
					sqlsrv_close($conexion);		
				}
				
				//Verificar si PRECIO
				if((string)$Row['nETPRgX']=="X")
				{
					$SqlUpdate="update PO_DETAIL set PO_PriceOrig='".$Row['nETPR']."', ModifPrecio=1 
								where PO_Item='".$Row['eBELP']."' 
								and PO_Number='".$nro_oc."';";
					//echo $SqlUpdate;
					//die();
					$conexion = conectar_srvdev();	
					$result = sqlsrv_query($conexion, $SqlUpdate);
					$afectadas=sqlsrv_rows_affected($conexion);
					sqlsrv_close($conexion);
				}
				
				$vueltasEE++;
			}
				
			//Ahora se actualizara el Header segun corresponda el procedimiento almacenado					
			$SqlProcedimiento="EXEC [SP_ESTADO_855] '".$nro_oc."';";
			$file = fopen("sql_modificarOC.txt","a+");
			fwrite($file,"\n\r ".$SqlProcedimiento);
			fclose($file);
					
			$conexion = conectar_srvdev();
			$result = sqlsrv_query($conexion, $SqlProcedimiento);
			$resultadoProcedimiento="";
			while( $row = sqlsrv_fetch_array($result) )
			{
				$resultadoProcedimiento=$row["procesado"];
			}
			sqlsrv_close($conexion);
					
			//Ahora se actualizara el Header el Checkid
			$conexion = conectar_srvdev();
			$SqlProcedimiento="EXEC [SP_ESTADO_DIF_855] '".$nro_oc."';";
					
			$file = fopen("sql_modificarOC.txt","a+");
			fwrite($file,"\n\r ".$SqlProcedimiento);
			fclose($file);
		
			$result = sqlsrv_query($conexion, $SqlProcedimiento);
			$resultadoProcedimiento="";
			if($resultadoProcedimiento=="OK")
			{
				$afectadas++;
			}
			sqlsrv_close($conexion);
			
			return $afectadas;
		}
		 
		 		
		//$RetornoActualizacionEE=ActualizarEE($all_data,$nro_oc);
		//die();
		
		//$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:modificacionoc_SOAP_CC&version=3.0";
		$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_modificacionoc_SI&interfaceNamespace=urn:modificacionoc.kcl.cl";
		$client = new nusoap_client($wdsl,false);
		$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;
		
		$err = $client->getError();
		
		if ($err) {
		 echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}
		
		//XML de envio de datos
		$XMLEnvio='<urn:modificacionoc_REQ_MT xmlns:urn="urn:modificacionoc.kcl.cl">
						 <NOC>'.$nro_oc.'</NOC>';
		$vueltasXML=0;
		
		foreach($all_data as $Row)
		{
			$XMLEnvio.='
						<TDETOC>
							<EBELP>'.$Row['eBELP'].'</EBELP>
							<MATNR>'.$Row['mATNR'].'</MATNR>
							<MATNRGX>'.$Row['mATNRgX'].'</MATNRGX>
							<MENGE>'.$Row['mENGE'].'</MENGE>
							<MENGEGX>'.$Row['mENGEgX'].'</MENGEGX>
							<NETPR>'.$Row['nETPR'].'</NETPR>
							<NETPRGX>'.$Row['nETPRgX'].'</NETPRGX>
						 </TDETOC>
						 ';
			$vueltasXML++;
		}
		
		//Cierre de XML
		$XMLEnvio.='</urn:modificacionoc_REQ_MT>';
		
		$file = fopen("ws_modificarOC.txt","a+");
		fwrite($file,"\n\r ".$XMLstring);
		fclose($file);
		
		//print_r($all_data);
		$soapaction = "http://sap.com/xi/WebService/soap1.1";
		
		$mysoapmsg = $client->serializeEnvelope($XMLEnvio,'',array(),'document', 'literal');
		$result = $client->send($mysoapmsg,$soapaction);
		
		$result=json_encode($result);
		
		$file = fopen("ws_modificarOC.txt","a+");
		fwrite($file,"\n\r ".$result);
		fclose($file);
		
		$result=json_decode($result);
		$data = array();
		
		$ErrorEnWs=0;		
		//Validando si hay error de WS		
		try {			
			$Faultcode=$result->faultcode;
			$Faultstring=$result->faultstring;
			$Context=$result->detail->SystemError->context;
			$Code=$result->detail->SystemError->code;
			$TextWS=$result->detail->SystemError->text;
			
			if($Faultcode!="")
			{
				$ErrorEnWs=1;
			}
		} catch (Exception $e) {
			//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
		}
		
		if($ErrorEnWs==0)
		{
			$resultadoReturnSimple = count($result);
			$resultadoReturn = count($result->T_RETORNO);
			if($resultadoReturn>1)
			{
				 foreach($result->T_RETORNO as $retorno)
				 {
					$RetornoActualizacionEE=0;
					//Si fue correcto se ejecutaran los cambio en BD
					 if((string)$retorno->TYPE == "S")
					 {
						$RetornoActualizacionEE=ActualizarEE($all_data,$nro_oc);
					 }
					$item=array("ID"=>$retorno->ID,
								"TYPE"=>$retorno->TYPE,
								"MESSAGE"=>$retorno->MESSAGE,
								"NUMBER"=>$retorno->NUMBER,
								"ERRORWS"=>0,
								"RetornoActualizacionEE"=>$RetornoActualizacionEE
								);
					array_push($data,$item);
				 }
			}
			if($resultadoReturnSimple>0)
			{
				foreach($result as $retorno)
				 {
					$RetornoActualizacionEE=0;
					//Si fue correcto se ejecutaran los cambio en BD
					 if((string)$retorno->TYPE == "S")
					 {
						$RetornoActualizacionEE=ActualizarEE($all_data,$nro_oc);
					 }
					$item=array("ID"=>$retorno->ID,
								"TYPE"=>$retorno->TYPE,
								"MESSAGE"=>$retorno->MESSAGE,
								"NUMBER"=>$retorno->NUMBER,
								"ERRORWS"=>0,
								"RetornoActualizacionEE"=>$RetornoActualizacionEE
								);
					array_push($data,$item);
				 }
			}
		}
		else
		{
			$item=array("ERRORWS"=>1,
						"Faultcode"=>$Faultcode,
						"Faultstring"=>$Faultstring,
						"Context"=>$Context,
						"Code"=>$Code,
						"TextWS"=>$TextWS
						);
			array_push($data,$item);
		}
		
		$data=json_encode($data);
		//print_r($data);
		echo $data;		 
?>
	
