<?php
$InvoiceNumber = $_REQUEST['InvoiceNumber'];
		//include_once("../../conect/conect.php");
		function VerificarEstadoFactura($InvoiceNumber)
		{
			$EstadoEECabecera=8;
			$ValoresNulos=0;
			//Verificaremos los datos EE nulos en la facutura
			$sqlDatosNulos="SELECT COUNT(detalle.InvoiceNumber) as valoresNulos
						FROM InvoiceDetail detalle
						WHERE detalle.InvoiceNumber='$InvoiceNumber'
						AND detalle.EntregaEntrante IS NULL";
			$conexion = conectar_srvdev();

			$resultadoSQL = sqlsrv_query($conexion, $sqlDatosNulos);
			
			while($row = sqlsrv_fetch_array($resultadoSQL) )
			{
				$ValoresNulos=(float)$row[valoresNulos];
			}		
			sqlsrv_close($conexion);
			
			//VERIFICACION VALORES 0
			
			$ValoresCeros=0;
			
			$sqlDatosCeros="SELECT COUNT(detalle.InvoiceNumber) as valoresCeros
						FROM InvoiceDetail detalle
						WHERE detalle.InvoiceNumber='$InvoiceNumber'
						AND (detalle.EntregaEntrante='0'
						OR detalle.EntregaEntrante is null
						OR detalle.EntregaEntrante = '');
						";
			$conexion = conectar_srvdev();
			$resultadoSQL = sqlsrv_query($conexion, $sqlDatosCeros);	
			
			while($row = sqlsrv_fetch_array($resultadoSQL) )
			{
				$ValoresCeros=(float)$row[valoresCeros];
			}		
			sqlsrv_close($conexion);
			
			$ValoresSinEE=$ValoresNulos+$ValoresCeros;
			//echo "Valores sin EE: ".$ValoresSinEE;
			
			
			//Ahora obtendremos el total de registro que tiene que tener la factura
			$ValoresTotalDetalles=0;
			
			$sqlDetalles="SELECT COUNT(detalle.InvoiceNumber) as totalDetalles
							FROM InvoiceDetail detalle
							WHERE detalle.InvoiceNumber='$InvoiceNumber'";
			
			$conexion = conectar_srvdev();
			$resultadoSQL = sqlsrv_query($conexion, $sqlDetalles);	
			
			while($row = sqlsrv_fetch_array($resultadoSQL) )
			{
				$ValoresTotalDetalles=(float)$row[totalDetalles];
			}		
			sqlsrv_close($conexion);
			
			//VALIDACION DE DATOS EN OBTENIDOS
			$DiferenciaRegistro=$ValoresTotalDetalles-$ValoresSinEE;
			
			//echo $DiferenciaRegistro."<br />";
			//echo "Registros totales: ".$ValoresTotalDetalles."<br />";
			//echo "SIN EE: ".$ValoresSinEE."<br />";
			//die();
			//SI TODOS LOS REGISTROS TIENE EE LA DIFERENCIA ES TIENE QUE SER IGUAL AL VALOR DE LA CANTIDAD DE DETALLES
			if($ValoresSinEE==0)
			{
				$EstadoEECabecera=1;
			}
			//SI LA DIFERENCIA ES 0 SIGNIFICA QUE NO TIENE NINGUNA EE CORRECTA
			if($DiferenciaRegistro==0)
			{
				$EstadoEECabecera=3;
			}
			//SI HAY REGISTROS CON EE PERO NO TODOS ESTAN COMPLETOS
			if($DiferenciaRegistro != $ValoresTotalDetalles && $DiferenciaRegistro>0)
			{
				$EstadoEECabecera=2;
			}
			return $EstadoEECabecera;
		}
		//$verificacion= VerificarEstadoFactura('E09-658864');
		//echo $verificacion;
?>