<?php
require_once("../../conect/conect.php");
include_once("SegAjax.php");
$sociedadEnCSV="";
function ProcesarCSV($RutaArchivoCSV, $sociedadEnArchivo){

	$ValidacionSociedad=0;
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
			//Validaremos que la sociedad sea valida
			if((string)$data[7]!=(string)$sociedadEnArchivo)
			{
				$sociedadEnCSV=(string)$data[7];
				$ValidacionSociedad=1;
			}
			$H_NumeroFactura =$data[0];
			$H_FechaFactura  =$data[1];
			$H_currency		 =$data[2];
			$H_MontoNeto	 =$data[3];
			$H_GrossValue	 =$data[4];
			$H_Gastos		 =$data[5];
			$H_Vendedor		 =$data[6];
			$H_sociedad      =$data[7];
			$H_Enviado		 =$data[8];
			$H_RespIntercomex=$data[9];
			$H_EE			 =$data[10];
			$H_TipoIngreso	 =$data[11];
			
			$sqlHeader="Insert into InvoiceHeader ([InvoiceNumber],[InvoiceDate],[InvoiceCurrency],[InvoiceNetValue],[InvoiceGrossValue],[InvoiceGastos],[InvoiceVendor],[Sociedad],[Enviado],[RespIntercomex],[EntregaEntrante],[tipoIngresp]) ";
			$sqlHeader.="values('$H_NumeroFactura','$H_FechaFactura','$H_currency','$H_MontoNeto','$H_GrossValue','$H_Gastos','$H_Vendedor','$H_sociedad','$H_Enviado','$H_RespIntercomex','$H_EE','$H_TipoIngreso');";
			//Si NO hubo error con la sociedad ingresada
			if($ValidacionSociedad==0)
			{
				$conexion = conectar_srvdev();
				$result = sqlsrv_query($sqlHeader, $conexion);
				$afectadas=sqlsrv_rows_affected($conexion);
				$NumeroFacuturaParaDetalle=$rowFactura[NumeroFactura];
				sqlsrv_close($conexion);
				
					if($afectadas>0)
					{
						$RespuestasFacturas.="<br /><br /> -------------- Factura $H_NumeroFactura -------------- ";
						$RespuestasFacturas.="<br /> --- InvoiceDate: $H_FechaFactura -----";
						$RespuestasFacturas.="<br /> --- InvoiceCurrency : $H_currency -----";
						$RespuestasFacturas.="<br /> --- InvoiceNetValue: $H_MontoNeto -----";
						$RespuestasFacturas.="<br /> --- InvoiceGrossValue $H_GrossValue -----";
						$RespuestasFacturas.="<br /> --- InvoiceGastos $H_Gastos -----";			
						$RespuestasFacturas.="<br /> --- InvoiceVendor $H_Vendedor -----";			
						$RespuestasFacturas.="<br /> --- Sociedad $H_sociedad -----";
						$RespuestasFacturas.="<br /> --- Enviado $H_Enviado -----";			
						$RespuestasFacturas.="<br /> --- RespIntercomex $H_RespIntercomex -----";
						$RespuestasFacturas.="<br /> --- EntregaEntrante $H_EE -----";
						$RespuestasFacturas.="<br /> --- tipoIngresp $H_TipoIngreso -----";
					}
			}
		}
		else
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
		/**
		foreach($data as $row) {

			echo "Campo $i: $row<br>"; // Muestra todos los campos de la fila actual
			$i++ ;

		}*/
		//echo "<br><br>";
		$linea++;
	}
	fclose ( $fp );
	
	foreach($Detalle_Factura as $producto)
	{
		//Si NO hubo error con la sociedad ingresada
		if($ValidacionSociedad==0)
		{
			//echo "Detalle: ".$producto[NumeroFactura];
			$QueryProducto="Insert into InvoiceDetail ([InvoiceNumber],[InvoicePosition],[PONumber],[POPosition],[ProductID],[ProductDesciption],[ProductMeasure],[ProductQuantity],[PorductPrice],[EntregaEntrante],[PaisOrigen]) ";
			$QueryProducto.="values('".$producto[NumeroFactura]."','".$producto[InvoicePosition]."','".$producto[PONumber]."','".$producto[POPosition]."',";
			$QueryProducto.="'".$producto[ProductID]."','".$producto[ProductDescription]."','".$producto[ProductMeasure]."','".$producto[ProductQuantity]."','".$producto[ProductPrice]."'";
			$QueryProducto.=",'".$producto[EntregaEntrante]."','".$producto[PaisOrigen]."');";
			$conexion = conectar_srvdev();
			$result = sqlsrv_query($QueryProducto, $conexion);
			$afectadas=sqlsrv_rows_affected($conexion);
			if($afectadas>0)
				{
					$RespuestasFacturas.="<br /><br /> -- Producto ".$producto[ProductID]." --";
					$RespuestasFacturas.="<br /> -- PONumber: ".$producto[PONumber]." --";
					$RespuestasFacturas.="<br /> -- POPosition: ".$producto[POPosition]." --";
					$RespuestasFacturas.="<br /> -- ProductDesciption: ".$producto[ProductDescription]." --";
					$RespuestasFacturas.="<br /> -- ProductMeasure: ".$producto[ProductMeasure]." --";
					$RespuestasFacturas.="<br /> -- ProductQuantity: ".$producto[ProductQuantity]." --";
					$RespuestasFacturas.="<br /> -- ProductPrice: ".$producto[ProductPrice]." --";
					$RespuestasFacturas.="<br /> -- Entrega Entrante: ".$producto[EntregaEntrante]." --";
					$RespuestasFacturas.="<br /> -- Pais Origen: ".$producto[PaisOrigen]." --";
				}
			sqlsrv_close($conexion);
		}
	}
	//Si hubo error con la sociedad ingresada
	if($ValidacionSociedad==1)
	{
		$RespuestasFacturas="(!)- El archivo ingresado no corresponde a la sociedad ingresada -(!)";
	}
	else
	{
		//Se actualizar el estado EE
		$sqlProcec="exec SP_EE_FACTURA '$H_NumeroFactura';";
		$conexion = conectar_srvdev();
		$result = sqlsrv_query($sqlProcec, $conexion);
		$afectadas=sqlsrv_rows_affected($conexion);
	}
	return $RespuestasFacturas;
}
	$sociedadObtenida= $_POST["sociedad"];
	$rutaCSV= $_POST["rutaCSV"];
	
	$rutaCSV='archivos/csv/'.$rutaCSV.'.csv';
	$respuestaIngreso=ProcesarCSV($rutaCSV, $sociedadObtenida);
	
	echo $respuestaIngreso;
	//echo $rutaCSV;
?>