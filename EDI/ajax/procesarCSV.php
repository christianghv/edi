<?php
require_once("../../conect/conect.php");
include_once("SegAjax.php");
function ValidarSociedad($Sociedad)
{
	$sociedadEncontrada=0;
	$conexion = conectar_srvdev();
	$sql = "SELECT [id_sociedad],[desc_sociedad] FROM [cfg_sociedades_desc];";
			
	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$sociedadEncontrada++;
	}
	sqlsrv_close($conexion);
	
	return $sociedadEncontrada;
}
function ValidarInvoiceNumber($InvoiceNumber)
{
	$InvoiceNumberEncontrados=0;
	$conexion = conectar_srvdev();
	$sql = "SELECT [InvoiceNumber] FROM [InvoiceHeader] WHERE [InvoiceNumber]='$InvoiceNumber';";
			
	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$InvoiceNumberEncontrados++;
	}
	sqlsrv_close($conexion);
	
	return $InvoiceNumberEncontrados;
}

function ValidarFecha($FechaRecibida)
{
	$nuevaFecha = date('Y-m-d',$fecha);
	$fechaServidor= date('Y-m-d');
	
	if($nuevaFecha>$fechaServidor)
	{
		//fecha es superior a la actual
		$ErrorEnfecha++;
	}
	
	return $FechaRecibida;
}

function ProcesarCSV($RutaArchivoCSV, $sociedadEnArchivo,$JsonDeProveedores){

	$Header=array();
	$ErrorEnCSV="";
	$ValidacionesMalas=0;

	$RespuestasFacturas="";
	
	//810 header
	$Facturas=array();
	$H_NumeroFactura="";
	$H_FechaFactura="";
	$H_currency="";
	$H_MontoNeto="";
	$H_GrossValue="";
	$H_Gastos="";
	$H_Vendedor="";
	$H_sociedad="";
	$H_Enviado="";
	$H_RespIntercomex="";
	$H_EE="";
	$H_TipoIngreso="";
	
	//810 detail
	$Detalle_Factura=array();

	$fp = fopen ( "$RutaArchivoCSV" , "r" );
	$linea=0;
	while (( $data = fgetcsv ( $fp , 1000 , ";" )) !== FALSE ) { // Mientras hay líneas que leer...

		$i = 0;
		//Si la linea es 0 estamos en la cabecera de la factura 810
		if($linea==0)
		{
			
			//Validacion de sociedad valida
			/**
			$ValSociedad=ValidarSociedad((string)$data[7]);			
			if($ValSociedad==0)
			{
				$ValidacionesMalas++;
				$ErrorEnCSV.=" Error: La sociedad ingresada ".(string)$data[7]." no esta registrada";
			}
			*/
			//Validacion de InvoiceNumber
			$ValInvoiceNumber=ValidarInvoiceNumber((string)$data[0]);			
			if($ValInvoiceNumber>0)
			{
				$ValidacionesMalas++;
				$ErrorEnCSV.="<br /> Error: El InvoiceNumber ingresado ".(string)$data[0]." ya esta registrado->".$ValInvoiceNumber;
			}
			
			//Validacion de Fecha
			$fechaExtraida=(string)$data[1];
			$FechaRecibida = substr($fechaExtraida, 0, 10);
			$fechaFactura = strtotime($FechaRecibida);
			$fechaFacturaFormateada = date('d/m/Y',$fechaFactura);
			
			$ValFecha=ValidarFecha($fechaFacturaFormateada);
			if($ValFecha==0)
			{
				$ValidacionesMalas++;
				$ErrorEnCSV.="<br /> Error: La fecha  ingresada ".$fechaFacturaFormateada." no es valida";
			}
			
			//Si NO hubo error con la sociedad ingresada
			if($ValidacionesMalas==0)
			{
				//Si no es un detalle
				$HeaderFactura=array( 
										NumeroFactura=> $data[0],
										FechaFactura => $fechaFacturaFormateada,
										currency=>$data[2],
										NetValue => $data[3],
										GrossValue=>$data[4],
										Gastos=>$data[5],
										Vendedor=>$data[6],
										sociedad=>$data[7],
										Enviado=>$data[8],
										RespIntercomex=>$data[9],
										EE=>$data[10],
										TipoIngreso=>$data[11]
										);
				array_push($Header,$HeaderFactura);
			}
		}
		else
		{
			if($ValidacionesMalas==0)
			{
				//Si no es un detalle
				$ProductoFactura=array( 
										NumeroFactura=> $data[0],
										InvoicePosition => $data[1],
										PONumber=>$data[2],
										POPosition => $data[3],
										ProductID=>$data[4],
										ProductDescription=>$data[5],
										ProductMeasure=>$data[6],
										ProductQuantity=>$data[7],
										ProductPrice=>$data[8],
										EntregaEntrante=>$data[9],
										PaisOrigen=>$data[10]
										);
				array_push($Detalle_Factura,$ProductoFactura);
			}
		}
		$linea++;
	}
	fclose ( $fp );
	
	$ArregloRespuesta=array();
	
	if($ValidacionesMalas>0)
	{
		$ArregloRespuesta=array(
								Resultado=>$ErrorEnCSV
		);
	}
	else
	{
		$ArregloRespuesta=array(
								Resultado=>"Correcto",
								Header=>json_encode($Header),
								Detalle=>json_encode($Detalle_Factura)
		);
	}
	$RespuestaFinal=array(RespuestaFinal=>$ArregloRespuesta);
	$RespuestasFacturas=json_encode($RespuestaFinal);
	
	return $RespuestasFacturas;
}
	
	$idUsuario= $_POST["idUsuario"];
	$rutaCSV= $_POST["rutaCSV"];
	$JsonDeProveedores=$_POST["JsonDeProveedores"];
	
	if($idUsuario!="" || $rutaCSV!="" || $JsonDeProveedores!="")
	{
		//$rutaCSV='archivos/csv/'.$rutaCSV.'.csv';
		$rutaCSV='archivos/csv/'.$rutaCSV.'.csv';
		$respuestaIngreso=ProcesarCSV($rutaCSV, $idUsuario,$JsonDeProveedores);
	}
	else
	{
		$respuestaIngreso="(!) Error no se recibio una o mas varibles de validacion (!)";
	}
	

	
	echo $respuestaIngreso;
	//echo $rutaCSV;
?>