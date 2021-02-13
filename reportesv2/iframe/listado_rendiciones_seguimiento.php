<?
ini_set('memory_limit', "240M");
ini_set('max_execution_time', 600);
ini_set("display_errors", 1);
ini_set("max_input_time ", 600);
set_time_limit(600);
error_reporting(0);

require_once($_SERVER['DOCUMENT_ROOT']."/plugins/excel/Spreadsheet/Writer.php");
global $folio, $empresa, $usuario, $idEstado,$rut_usuario;
include('../../php/conect_rrhh.php');
$link_rrhh = conexion_rrhh();

include('../../php/conect.php');
$link = conexion();

function ExtraerDatosDocConta($docConta)
{
	$Respuesta=array();
	
	if(trim($docConta)!="")
	{
		$PartesDocConta=explode("\n",$docConta);
		
		foreach ($PartesDocConta as $Val) {
			//Quitar espacios
			$Val=trim($Val);
			
			//Validar si no es vacio
			if($Val!="")
			{
				//Buscar 'El documento'
				$pos = strpos($Val, 'El documento');
				
				//Cadena encontrada
				if ($pos !== false) {
					//Buscar 'BKPFF'
					$PosBKPFF = strpos($Val, 'BKPFF');
					//BKPFF encontrado
					if ($PosBKPFF !== false) {
						$Datos = substr($Val,$PosBKPFF,strlen($Val));
						$SoloDato=explode(" ",$Datos);
						//Si contiene el dato necesario
						if(count($SoloDato)>=2)
						{
							//Dividir datos obtenidos
							$Numero=substr($SoloDato[1],0,10);
							$Sociedad=substr($SoloDato[1],10,4);
							$Anio=substr($SoloDato[1],14,4);
							
							$Item=array(
								'Numero'=>$Numero,
								'Anio'=>$Anio,
								'Sociedad'=>$Sociedad);
							
							array_push($Respuesta,$Item);
						}
					}
				}
				else
				{
					//Buscar 'Doc.'
					$pos = strpos($Val, 'Doc.');
					//Cadena encontrada
					if ($pos !== false) {
						$Datos = substr($Val,$pos+strlen('Doc.'),strlen($Val));
						$SoloDato=explode(" ",$Datos);
						
						if(count($SoloDato)==6)
						{
							$Item=array(
								'Numero'=>$SoloDato[0],
								'Anio'=>'',
								'Sociedad'=>$SoloDato[5]);
							
							array_push($Respuesta,$Item);
						}
					}
				}
			}
		}
	}
	return $Respuesta;
}

$usuario = str_replace(chr(13),'',$usuario);
$usuario = str_replace(chr(10),'',$usuario);

$ceco = str_replace(chr(13),'',$ceco);
$ceco = str_replace(chr(10),'',$ceco);

$folio = str_replace(chr(13),'',$folio);
$folio = str_replace(chr(10),'',$folio);

if(trim($usuario<>'')) $usuario = str_replace('*',',',$usuario);
if(trim($ceco<>'')) $ceco = str_replace('*',',',$ceco);
if(trim($folio<>'')) $folio = str_replace('*','',$folio);

$sql = "SELECT distinct h1.nombreSolicitante, h1.empresaSolicitante, h1.folio, convert(char(10),h1.fecha,104) as fecha_creacion, usuarioSolicitante, convert(varchar(250),objetivoGasto) as objetivo, gastos, (SELECT TOP 1 enviadoPor FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as enviadoPor, (SELECT TOP 1 nombreEnviadoPor FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as nombreEnviadoPor, (SELECT TOP 1 enviadoA FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as enviadoA, (SELECT TOP 1 estado FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as estado, (SELECT TOP 1 convert(char(10),fechaEnvio,103) as fechaEnvio FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as fechaEnvio, (SELECT TOP 1 enviadoPor FROM HistorialAprobacionesSFRG WHERE (folio = h1.folio) AND (tipo = 'RENDICION') ORDER BY fechaEnvio DESC) as Ucajero, codCentroCosto, centroCosto, nombreSolicitante, rutSolicitante, codEmpresaSolicitante, empresaSolicitante, convert(varchar(250),objetivoGasto), montoSaldo as saldo_x_devolver, montoSaldo as saldo_x_cobrar, gastos as total_rendicion, h1.folio, '' as persona_autoriza, convert(char(10),h1.fecha,103) as fecha_reg, moneda, visa, amex as orden_servicio, archivoAdjunto as url, oficinaVentas as sucursal, '' as montoDolar, '' as montoPesos, vales as folio1, ufp as fondoPermanente, visa, (SELECT convert(char(10),max(fechaEnvio),103) FROM HistorialAprobacionesSFRG WHERE (folio = h1.folio) AND (tipo = 'RENDICION') and estado NOT LIKE '%pagado%') as fechaUltEnvio, (SELECT docConta FROM DatosContaRG WHERE folio = h1.folio) as docConta, h1.textoSaldo, Resumen.posicion,CONVERT(char(10), Resumen.fecha, 103) AS fecha, Resumen.tipoDocumento, Resumen.subTipoFactura, Resumen.numeroFactura, Resumen.posDet, Resumen.montoRendido, Resumen.naturaleza, ListadoDisResRG.imputacionGasto, ListadoDisResRG.nombreDetalleGasto, ListadoDisResRG.idObjetoImputacion, ListadoDisResRG.valorObjetoImputacion, ListadoDisResRG.idImporteAsignado, ListadoDisResRG.cuenta, (SELECT viaPago FROM DatosContaRG WHERE folio = h1.folio) as viaPago, h1.codEmpresaSolicitante,h1.empresaRendicion FROM dbo.CabeceraRG h1 INNER JOIN dbo.ResumenRG Resumen ON Resumen.folio=h1.folio INNER JOIN ListadoDistribucionResumenRG ListadoDisResRG ON ListadoDisResRG.folio=h1.folio AND ListadoDisResRG.numeroFactura= Resumen.numeroFactura AND ListadoDisResRG.tipoFactura=Resumen.tipoDocumento AND ListadoDisResRG.subTipoFactura=Resumen.subTipoFactura AND ListadoDisResRG.rutEmisor=Resumen.posDet WHERE h1.id IN(select max(h2.id) from CabeceraRG h2 where h2.folio = h1.folio) and (convert(datetime,convert(char(10),h1.fecha,103),103) between convert(datetime,'01/03/2018',103) and convert(datetime,'31/03/2018',103)) UNION SELECT distinct h1.nombreSolicitante, h1.empresaSolicitante, h1.folio, convert(char(10),h1.fecha,104) as fecha_creacion, usuarioSolicitante, convert(varchar(250),objetivoGasto) as objetivo, gastos, (SELECT TOP 1 enviadoPor FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as enviadoPor, (SELECT TOP 1 nombreEnviadoPor FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as nombreEnviadoPor, (SELECT TOP 1 enviadoA FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as enviadoA, (SELECT TOP 1 estado FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as estado, (SELECT TOP 1 convert(char(10),fechaEnvio,103) as fechaEnvio FROM dbo.HistorialAprobacionesSFRG WHERE (folio = h1.folio) ORDER BY fechaEnvio DESC) as fechaEnvio, (SELECT TOP 1 enviadoPor FROM HistorialAprobacionesSFRG WHERE (folio = h1.folio) AND (tipo = 'RENDICION') ORDER BY fechaEnvio DESC) as Ucajero, codCentroCosto, centroCosto, nombreSolicitante, rutSolicitante, codEmpresaSolicitante, empresaSolicitante, convert(varchar(250),objetivoGasto), montoSaldo as saldo_x_devolver, montoSaldo as saldo_x_cobrar, gastos as total_rendicion, h1.folio, '' as persona_autoriza, convert(char(10),h1.fecha,103) as fecha_reg, moneda, visa, amex as orden_servicio, archivoAdjunto as url, oficinaVentas as sucursal, '' as montoDolar, '' as montoPesos, vales as folio1, ufp as fondoPermanente, visa, (SELECT convert(char(10),max(fechaEnvio),103) FROM HistorialAprobacionesSFRG WHERE (folio = h1.folio) AND (tipo = 'RENDICION') and estado NOT LIKE '%pagado%') as fechaUltEnvio, (SELECT docConta FROM DatosContaRG WHERE folio = h1.folio) as docConta, h1.textoSaldo, Resumen.posicion,CONVERT(char(10), Resumen.fecha, 103) AS fecha, Resumen.tipoDocumento, Resumen.subTipoFactura, Resumen.numeroFactura, Resumen.posDet, Resumen.montoRendido, Resumen.naturaleza, '' as imputacionGasto, '' as nombreDetalleGasto, '' as idObjetoImputacion, '' as valorObjetoImputacion, '' as idImporteAsignado, '' as cuenta, (SELECT viaPago FROM DatosContaRG WHERE folio = h1.folio) as viaPago, h1.codEmpresaSolicitante,h1.empresaRendicion FROM dbo.CabeceraRG h1 INNER JOIN dbo.ResumenRG Resumen ON Resumen.folio=h1.folio WHERE h1.id IN(select max(h2.id) from CabeceraRG h2 where h2.folio = h1.folio) AND Resumen.resumen='Kilometraje' and (convert(datetime,convert(char(10),h1.fecha,103),103) between convert(datetime,'01/03/2018',103) and convert(datetime,'31/03/2018',103)) ORDER BY h1.folio ASC;";
	$rec_sql = @sqlsrv_query($sql);
	
	
	//////////// F O R M A T O //////////////////
	$nombrearchivo = "Rendicion";
	$fecha = date("Ymd_his"); 
	$NombreFinal=$nombrearchivo.'_'.$fecha.'.xls';
	
	$workbook = new Spreadsheet_Excel_Writer("archivos/".$NombreFinal);
	$num_format =& $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 =& $workbook->addFormat();
	$num_format1->setNumFormat('#');
	$num_format1->setBorder(1);
	
	$workbook->setCustomColor(12, 204, 204, 204);
	$format_our_green =& $workbook->addFormat();
	$format_our_green->setFgColor(12);
	
	$format_bold =& $workbook->addFormat();
	$format_bold->setBold();
	$format_bold->setBorder(1);
	$format_bold->setFgColor(12);
	$format_bold->setColor('black');
	$format_bold->setTextWrap();
	
	$celda =& $workbook->addFormat();
	$celda->setBorder(1);
	//////////////////////////////////////////	
	$worksheet =& $workbook->addWorksheet('Detalle');
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Folio', $format_bold);
	$worksheet->write(0, 1, 'Fecha', $format_bold);
	$worksheet->write(0, 2, 'Solicitante', $format_bold);
	$worksheet->write(0, 3, 'Rut', $format_bold);
	$worksheet->write(0, 4, 'Centro de Costo', $format_bold);
	$worksheet->write(0, 5, 'Cod. Centro Costo', $format_bold);
	$worksheet->write(0, 6, 'Empresa', $format_bold);
	$worksheet->write(0, 7, 'Moneda', $format_bold);
	$worksheet->write(0, 8, 'Visa', $format_bold);
	$worksheet->write(0, 9, 'Orden de Servicio', $format_bold);
	$worksheet->write(0, 10, 'Sucursal', $format_bold);
	$worksheet->write(0, 11, 'Monto USD', $format_bold);
	$worksheet->write(0, 12, 'Estado', $format_bold);
	$worksheet->write(0, 13, 'Total Gastos', $format_bold);
	$worksheet->write(0, 14, 'Anticipo Fondos', $format_bold);
	$worksheet->write(0, 15, 'Saldo por Devolver a Empresa', $format_bold);
	$worksheet->write(0, 16, 'Saldo por Cobrar a Empresa', $format_bold);
	$worksheet->write(0, 17, 'Fecha de Pago', $format_bold);
	$worksheet->write(0, 18, 'Folio Solicitud de Fondos', $format_bold);
	$worksheet->write(0, 19, 'Objetivo del Gasto', $format_bold);
	$worksheet->write(0, 20, utf8_decode('Fecha Última Aprobación'), $format_bold);
	$worksheet->write(0, 21, 'U. Cajero', $format_bold);
	$worksheet->write(0, 22, utf8_decode('Tipo de rendición'), $format_bold);
	$worksheet->write(0, 23, 'Documentos', $format_bold);
	$worksheet->write(0, 24, 'Pos.', $format_bold);
	$worksheet->write(0, 25, 'Fecha', $format_bold);
	$worksheet->write(0, 26, 'Tipo Documento', $format_bold);
	$worksheet->write(0, 27, 'Subtipo', $format_bold);
	$worksheet->write(0, 28, utf8_decode('N° Documento'), $format_bold);
	$worksheet->write(0, 29, 'Rut Emisor', $format_bold);
	$worksheet->write(0, 30, 'Monto Rendido', $format_bold);
	$worksheet->write(0, 31, utf8_decode('Descripción del Gasto'), $format_bold);
	$worksheet->write(0, 32, 'Concepto Gasto', $format_bold);
	$worksheet->write(0, 33, 'Detalle Gasto', $format_bold);
	$worksheet->write(0, 34, utf8_decode('Objeto de Imputación'), $format_bold);
	$worksheet->write(0, 35, utf8_decode('Valor Objeto Imputación'), $format_bold);
	$worksheet->write(0, 36, 'Importe Asignado', $format_bold);
	$worksheet->write(0, 37, 'Cuenta', $format_bold);
	$worksheet->write(0, 38, 'Via de pago', $format_bold);
	$worksheet->write(0, 39, 'Empresa Solicitante', $format_bold);
	$worksheet->write(0, 40, utf8_decode('Empresa Rendición'), $format_bold);
	
	$fil = 1;
	while($row = sqlsrv_fetch_array($rec_sql))
	{
		$vales_arr=array();

		if(trim($row[folio1])!="")
		{
			$vales_arr = explode("--",$row[folio1]);
		}

		$total_vales = 0;
		$vales = "";

		foreach ($vales_arr as $value) {
			$value = trim($value);
			if (!empty($value))
			{
				$value_arr = explode(",",$value);
				$vales .= $value_arr[0] . " - ";
				$total_vales += $value_arr[2];
			}
		}
		
		$DocumentosConta=ExtraerDatosDocConta($row['docConta']);

		$tipo = "";
		if(trim($row['fondoPermanente'])=='SI') $tipo = "Permanente";
		elseif(trim($row['visa'])=='SI') $tipo = "Visa";
		else $tipo = "Normal";
		
		/**
		if($num%2 == 0) $color = "#E8E8E8";
		else $color = "";
		*/
		
		$env = explode(':',$row['enviadoA']);
		if(count($env)>1) $lotiene = $env[1];
		else $lotiene = "";
		//******************************************** FIN LLAMADA A WEBSERVICE *********************************************
		$worksheet->write($fil, 0, $row["folio"], $celda);
		$worksheet->write($fil, 1, str_replace('/','-',$row['fecha_creacion']), $celda);
		$worksheet->write($fil, 2, $row['nombreSolicitante'], $celda);
		$worksheet->write($fil, 3, $row['rutSolicitante'], $celda);
		$worksheet->write($fil, 4, $row['centroCosto'], $celda);
		$worksheet->write($fil, 5, $row['codCentroCosto'], $celda);
		$worksheet->write($fil, 6, $row['empresaSolicitante'], $celda);
		$worksheet->write($fil, 7, $row['moneda'], $celda);
		
		if(trim($row['moneda'])!="CLP")
		{
			$decimales=2;
		}
		else
		{
			$decimales=0;
		}
		
		$worksheet->write($fil, 8, $row['visa'], $celda);
		$worksheet->write($fil, 9, $row['orden_servicio'], $celda);
		$worksheet->write($fil, 10, $row['sucursal'], $celda);
		$worksheet->write($fil, 11, $row['montoDolar'], $celda);
		$worksheet->write($fil, 12, $row['estado'], $celda);
		$worksheet->write($fil, 13, number_format($row['total_rendicion'],$decimales,",","."), $celda);
		$worksheet->write($fil, 14, number_format($total_vales,$decimales,'','.'), $celda);
		
		
		$Devolver=0;
		$Cobrar=0;
		
		if(trim($row['textoSaldo'])=='Saldo por pagar al trabajador')
		{
			$Devolver=0;
			$Cobrar=round($row['saldo_x_devolver']);
		}
		
		if(trim($row['textoSaldo'])=='Saldo por pagar a la empresa')
		{
			$Devolver=round($row['saldo_x_devolver']);
			$Cobrar=0;
		}
		
		$worksheet->write($fil, 15, number_format($Devolver,0,'','.'), $celda);
		$worksheet->write($fil, 16, number_format($Cobrar,0,'','.'), $celda);
		$worksheet->write($fil, 17, $row['fechaEnvio'], $celda);
		
		if(trim($row['folio1'])!="")
		{
			$vales_arr = explode("--",$row['folio1']);
			
			$Vales='';
			
			foreach ($vales_arr as $value) {
				$value = trim($value);
				if (!empty($value))
				{
					$value_arr = explode(",",$value);
					$Vales.= $value_arr[0];
				}
			}
		}
		
		$worksheet->write($fil, 18, $Vales, $celda);
		$worksheet->write($fil, 19, $row['objetivo'], $celda);
		$worksheet->write($fil, 20, $row['fechaUltEnvio'], $celda);
		$worksheet->write($fil, 21, $row['Ucajero'], $celda);
		$worksheet->write($fil, 22, $tipo, $celda);
		
		$DocNumero='';
		foreach ($DocumentosConta as $Doc)
		{
			$DocNumero.=$Doc['Numero']."\n";
		}
		
		$DocNumero=substr($DocNumero, 0, strlen($DocNumero)-2);
		
		$multipleLineDataFormat = &$workbook->addFormat(array('Align' => 'left'));
		$multipleLineDataFormat->setTextWrap();
		
		$worksheet->write($fil, 23, $DocNumero, $multipleLineDataFormat );
		
		//$worksheet->write($fil, 23, $DocNumero, $celda);
		$worksheet->write($fil, 24, $row['posicion'], $celda);
		$worksheet->write($fil, 25, $row['fecha'], $celda);
		$worksheet->write($fil, 26, $row['tipoDocumento'], $celda);
		$worksheet->write($fil, 27, $row['subTipoFactura'], $celda);
		$worksheet->write($fil, 28, $row['numeroFactura'], $celda);
		$worksheet->write($fil, 29, $row['posDet'], $celda);
		
		
		$pos = strpos($row['tipoDocumento'],'Nota de Cr');   
		
		if ($pos === false)
			$factor = 1;
		else
			$factor = -1;
					
		$pos = strpos($row['tipoDocumento'],'Kilometr');
		if ($row['tipoDocumento'] == 'Kilometraje' )
		{
			$sql_kms = "SELECT [conceptoGasto],[cuenta],[descripcionGasto],[detalleGasto],[fechaViaje]
											,[kilometros],[numero],[totalPagar],[totalPagar2]
									FROM [KilometrajeRG]
									WHERE [folio] = ". $row['folio'] 
								." and numero = ". $row['numeroFactura'];
						
			$rec_kms = sqlsrv_query($sql_kms);
						
			if ($rowK = sqlsrv_fetch_array($rec_kms)) 
			{
				$concepto = $rowK['conceptoGasto'];
				$detalle = $rowK['detalleGasto'];
				$importe = $rowK['totalPagar'];
				$cuenta = $rowK['cuenta'];
			}
		}
		else
		{
			$concepto = $row['imputacionGasto'];
			$detalle = $row['nombreDetalleGasto'];
			$importe = $row['idImporteAsignado'];
			$cuenta  = $row['cuenta'];
		}
		
		$worksheet->write($fil, 30, number_format($row['montoRendido']*$factor,$decimales,",","."), $celda);
		$worksheet->write($fil, 31, $row['naturaleza'], $celda);
		$worksheet->write($fil, 32, $concepto, $celda);
		$worksheet->write($fil, 33, $detalle, $celda);
		$worksheet->write($fil, 34, $row['idObjetoImputacion'], $celda);
		$worksheet->write($fil, 35, $row['valorObjetoImputacion'], $celda);
		$worksheet->write($fil, 36, number_format($importe * $factor,$decimales,",","."), $celda);
		$worksheet->write($fil, 37, $cuenta, $celda);
		$worksheet->write($fil, 38, $row['viaPago'], $celda);
		$worksheet->write($fil, 39, $row['codEmpresaSolicitante'], $celda);
		$worksheet->write($fil, 40, $row['empresaRendicion'], $celda);
		
		$fil++;
	}
	$workbook->close();
	
	echo $sql;
	echo "<br/><br/>";
	echo $NombreFinal;
	echo "<br/><br/>";
	echo '<a href="http://d-edi20.kcl.cl/reportesv2/iframe/archivos/'.$NombreFinal.'" target="_blank">'.$NombreFinal.'</a>';
?>