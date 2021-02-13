<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_carga.php');
	
	ini_set ( 'mssql.connect_timeout' , '600' );
	ini_set ( 'mssql.timeout' , '600' );
	
	$CONNECCION_UNICA_FUNCIONES_EMBARCADORES = conectar_srvdev();
	
	function getEDI856_856BULTO_PrimeraInstanciaEmbarcador()
	{
		$EDI856_856BULTO_PrimeraInstanciaEmbarcador=array(
							'Sociedad'=>'',
							'PONumber'=>'',
							'InvoiceVendor'=>'',
							'Overpack'=>'',
							'idenBulto'=>'',
							'tipoBulto'=>'',
							'tipoCargaAerea'=>'',
							'peso'=>'',
							'unidPeso'=>'',
							'volumen'=>'',
							'unidVolumen'=>'',
							'longitud'=>'',
							'ancho'=>'',
							'alto'=>'',
							'unidDimension'=>'',
							'fechaDespacho'=>'',
							'invNumber'=>'',
							'RegistrarBultoConDetalle'=>'No'
							);
		return $EDI856_856BULTO_PrimeraInstanciaEmbarcador;
	}
	function getEmbarqueSegundaInstancia()
	{
		$EmbarqueSegundaInstancia=array(
							'Sociedad'=>'',
							'PONumber'=>'',
							'InvoiceVendor'=>'',
							'InvoiceNumber'=>'',
							'Ship_trailernumber'=>'',
							'Overpack'=>'',
							'DateReceived'=>'NULL',
							'longitud'=>'NULL',
							'ancho'=>'NULL',
							'alto'=>'NULL',
							'peso'=>'NULL',
							'volumen'=>'NULL',
							'tipoBulto'=>'NULL',
							'InstruccionCompras'=>'',
							'FechaInstruccion'=>'',
							'Comentario'=>'',
							'MawbBL'=>'',
							'PMC_CargoContenedor'=>'',
							'ship_transport'=>'',
							'LCL_FCL'=>'',
							'ID_Contenedor'=>'',
							'Value_Total_BL_AWB'=>'',
							'ETA'=>'',
							'Flight_Vessel'=>'',
							'ETD'=>'',
							'Puerto_Aeropuerto'=>'',
							'FechaArribo'=>'NULL',
							'FechaEntrega'=>'NULL',
							'HoraEntrega'=>'NULL'
							);
		return $EmbarqueSegundaInstancia;
	}
	
	function getEmbarqueTerceraInstancia()
	{
		$EmbarqueTerceraInstancia=array(
							'Sociedad'=>'',
							'PONumber'=>'',
							'InvoiceVendor'=>'',
							'InvoiceNumber'=>'',
							'Ship_trailernumber'=>'',
							'Overpack'=>'',
							'FechaArribo'=>'',
							'FechaEntrega'=>'',
							'HoraEntrega'=>'',
							);
		return $EmbarqueTerceraInstancia;
	}
	function Ingresar856BULTO_Manual($EDI856_856BULTO_PrimeraInstanciaEmbarcador)
	{
		//Factura con bulto
		if($EDI856_856BULTO_PrimeraInstanciaEmbarcador['RegistrarBultoConDetalle']=='No')
		{
			$borrarQuery=" DELETE from [856BULTO] 
							WHERE [idenBulto]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."' 
							AND [invNumber]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."';";
							
			//Borrar query
			$query="Insert into [856BULTO]([idenBulto],[tipoBulto],[peso],[unidPeso],[volumen],[unidVolumen],[longitud],[ancho],[alto],[unidDimension],[fechaDespacho],[invNumber],[estado]) 
					values ('".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoBulto'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['peso'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidPeso'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['volumen'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidVolumen'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['longitud'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['ancho'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['alto'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidDimension'])."',NULL,
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."','R');";
			
			ActualizarBD($borrarQuery);
			ActualizarBD($query);
			
			//return $borrarQuery.'<br />'.$query.'<br /><br />';
			return 'EDI856 Bulto '.$EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'].' ingresado';
		}
		else
		{
			//Factura sin bulto
			$borrarQuery=" DELETE from [856BULTO] 
						WHERE [idenBulto]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."' 
						AND [invNumber]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."';";
						
			//Borrar query
			$query="Insert into [856BULTO]([idenBulto],[tipoBulto],[peso],[unidPeso],[volumen],[unidVolumen],[longitud],[ancho],[alto],[unidDimension],[fechaDespacho],[invNumber],[estado]) 
					values ('".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoBulto'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['peso'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidPeso'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['volumen'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidVolumen'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['longitud'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['ancho'])."','".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['alto'])."',
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidDimension'])."',NULL,
					'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."','R');";
					
			//Actualizar en EDI 810
			$queryActualizarEdi810="UPDATE [InvoiceDetail] 
									SET [idenBulto]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."'
									FROM [InvoiceDetail]
									WHERE InvoiceNumber='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."';";
			
			ActualizarBD($borrarQuery);
			ActualizarBD($query);
			ActualizarBD($queryActualizarEdi810);
			
			//Registrando detalle a bulto nuevo
			//Borrando detalle de bulto anterior
			$QueryBorrarRegistroAnterior="DELETE FROM [856DETAIL]
										WHERE [segm_Id] ='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."'";
			
			ActualizarBD($QueryBorrarRegistroAnterior);
			
			//Registrando detalle de bulto
			$QueryDetalleBulto="INSERT INTO  [856DETAIL]
								SELECT [InvoiceNumber],[ProductID],[ProductQuantity],[ProductMeasure],[PONumber],NULL,'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."',[POPosition],NULL,[InvoicePosition],NULL
								FROM [InvoiceDetail]
								WHERE [InvoiceNumber]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."';";
								
			ActualizarBD($QueryDetalleBulto);
			
			//return $borrarQuery.'<br />'.$query.'<br /><br />';
			return 'EDI856 Bulto '.$EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'].' ingresado';
		}
	}
	function IngresarEmbarquePrimeraInstancia($EDI856_856BULTO_PrimeraInstanciaEmbarcador)
	{
		$borrarQuery=" DELETE from [embarque] 
				  WHERE [Sociedad]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['Sociedad'])."' 
				  AND [PONumber]='".ms_escape_string(NumeroSinExponente($EDI856_856BULTO_PrimeraInstanciaEmbarcador['PONumber']))."' 
				  AND [InvoiceVendor]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['InvoiceVendor'])."' 
				  AND [InvoiceNumber]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."' 
				  AND [Ship_trailernumber]='".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."';";
						
		//Borrar query
		$query="Insert into [embarque]([Sociedad],[PONumber],[InvoiceVendor],[InvoiceNumber],[Ship_trailernumber],[Overpack],[DateReceived],[longitud],[ancho],[alto],[peso],[volumen],[tipoBulto],[tipoCargaAerea]) 
				values ('".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['Sociedad'])."',
				'".ms_escape_string(NumeroSinExponente($EDI856_856BULTO_PrimeraInstanciaEmbarcador['PONumber']))."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['InvoiceVendor'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['Overpack'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['fechaDespacho'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['longitud'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['ancho'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['alto'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['peso'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['volumen'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoBulto'])."',
				'".ms_escape_string($EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoCargaAerea'])."'
				);";
		
		ActualizarBD($borrarQuery);
		ActualizarBD($query);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'Embarque Primera Instancia '.$EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto'].' ingresado';
	}
	function IngresarEmbarqueSegundaInstancia($EmbarqueSegundaInstancia)
	{					
		//Borrar query
		$query="  UPDATE [embarque] 
				  SET [InstruccionCompras]='".ms_escape_string($EmbarqueSegundaInstancia['InstruccionCompras'])."' 
					  ,[FechaInstruccion]='".ms_escape_string($EmbarqueSegundaInstancia['FechaInstruccion'])."' 
					  ,[Comentario]='".ms_escape_string($EmbarqueSegundaInstancia['Comentario'])."' 
					  ,[MawbBL]='".ms_escape_string($EmbarqueSegundaInstancia['MawbBL'])."' 
					  ,[PMC_CargoContenedor]='".ms_escape_string($EmbarqueSegundaInstancia['PMC_CargoContenedor'])."' 
					  ,[ship_transport]='".ms_escape_string($EmbarqueSegundaInstancia['ship_transport'])."' 
					  ,[LCL_FCL]='".ms_escape_string($EmbarqueSegundaInstancia['LCL_FCL'])."' 
					  ,[ID_Contenedor]='".ms_escape_string($EmbarqueSegundaInstancia['ID_Contenedor'])."' 
					  ,[Value_Total_BL_AWB]='".ms_escape_string($EmbarqueSegundaInstancia['Value_Total_BL_AWB'])."' 
					  ,[ETA]='".ms_escape_string($EmbarqueSegundaInstancia['ETA'])."' 
					  ,[Flight_Vessel]='".ms_escape_string($EmbarqueSegundaInstancia['Flight_Vessel'])."' 
					  ,[ETD]='".ms_escape_string($EmbarqueSegundaInstancia['ETD'])."' 
					  ,[Puerto_Aeropuerto]='".ms_escape_string($EmbarqueSegundaInstancia['Puerto_Aeropuerto'])."' 
				  WHERE [Sociedad]='".ms_escape_string($EmbarqueSegundaInstancia['Sociedad'])."' 
				  AND [PONumber]='".ms_escape_string(NumeroSinExponente($EmbarqueSegundaInstancia['PONumber']))."' 
				  AND [InvoiceVendor]='".ms_escape_string($EmbarqueSegundaInstancia['InvoiceVendor'])."' 
				  AND [InvoiceNumber]='".ms_escape_string($EmbarqueSegundaInstancia['InvoiceNumber'])."' 
				  AND [Ship_trailernumber]='".ms_escape_string($EmbarqueSegundaInstancia['Ship_trailernumber'])."';";
		
		ActualizarBD($query);
		
		//Actualizar Bulto
		$query=" UPDATE [856BULTO] SET [fechaDespacho] = '".ms_escape_string($EmbarqueSegundaInstancia['ETD'])."', [estado]='D' 
				WHERE [idenBulto]='".ms_escape_string($EmbarqueSegundaInstancia['Ship_trailernumber'])."' 
				AND [invNumber]='".ms_escape_string($EmbarqueSegundaInstancia['InvoiceNumber'])."';";
		ActualizarBD($query);
		
		//return $query.'<br /><br />';
		return 'Embarque Segunda Instancia '.$EmbarqueSegundaInstancia['Ship_trailernumber'].' ingresado';
	}
	function IngresarEmbarqueTerceraInstancia($EmbarqueSegundaInstancia)
	{					
		//Borrar query
		$query="  UPDATE [embarque] 
				  SET [FechaArribo]='".ms_escape_string($EmbarqueSegundaInstancia['FechaArribo'])."' 
					  ,[FechaEntrega]='".ms_escape_string($EmbarqueSegundaInstancia['FechaEntrega'])."' 
					  ,[HoraEntrega]='".ms_escape_string($EmbarqueSegundaInstancia['HoraEntrega'])."' 
				  WHERE [Sociedad]='".ms_escape_string($EmbarqueSegundaInstancia['Sociedad'])."' 
				  AND [PONumber]='".ms_escape_string(NumeroSinExponente($EmbarqueSegundaInstancia['PONumber']))."' 
				  AND [InvoiceVendor]='".ms_escape_string($EmbarqueSegundaInstancia['InvoiceVendor'])."' 
				  AND [InvoiceNumber]='".ms_escape_string($EmbarqueSegundaInstancia['InvoiceNumber'])."' 
				  AND [Ship_trailernumber]='".ms_escape_string($EmbarqueSegundaInstancia['Ship_trailernumber'])."';";
		
		ActualizarBD($query);
		
		//return $query.'<br /><br />';
		return 'Embarque Tercera Instancia '.$EmbarqueSegundaInstancia['Ship_trailernumber'].' ingresado';
	}
	function InterpretarValidacionNoVacio($Resultado,$FilaExcel)
	{
		$Respuesta='';
		if($Resultado['Respuesta']=='nok')
		{
			switch ($Resultado['Campo_errado']) {
				case 'it_unitshiped':
					$Respuesta= 'No se encontro un valor para el campo '.$Resultado['Campo_errado'].' en la fila '.$FilaExcel.' verifique las posiciones del EDI855 con SAP correspondientes a la factura ingresada';
					break;
				default:
				$Respuesta= 'No se encontro un valor para el campo '.$Resultado['Campo_errado'].' en la fila '.$FilaExcel.'';
			}
		}
		return $Respuesta;
	}
?>