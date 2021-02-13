$( document ).ready(function() {
	
	$("#cargar_factura").click(function() {      
		cargaFactura();
    });
    
    $("#cargar_factura").click(function() {      
		DescargarOc();
    });
    
    $("#volver").click(function() {      
		volver();
    });
});

function cargaFactura()
{
	var url = "proveedores/facturas.php";
	window.open(url, '_self');
	
}


function DescargarOc()
{
	//var url = "proveedores/facturas.php";
	//window.open(url, '_self');
	
}

function volver()
{
	var url = "../index.html";
	window.open(url, '_self');
}
