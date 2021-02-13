$( document ).ready(function() {
	
	$("#idproveedor").focus();
	
	$("#img_proveedores").click(function() {  
		$("#idproveedor").focus();
	});
	$("#btn_ingresar").click(function() {      
			validaIngreso();
    });
	
	$("#btn_ingresarEmbarcador").click(function() {      
			validaIngresoEmbarcador();
    });
	
	$('#idproveedor').keypress(function(e){   
               if(e.which == 13){
				$("#btn_ingresar").trigger("click");
          }
	});
	
	$('#pass').keypress(function(e){   
               if(e.which == 13){
				$("#btn_ingresar").trigger("click");
          }
	});
	
	$('#idEmbarcador').keypress(function(e){   
               if(e.which == 13){
				$("#btn_ingresarEmbarcador").trigger("click");
          }
	});
	
	$('#passEmbarcador').keypress(function(e){   
               if(e.which == 13){
				$("#btn_ingresarEmbarcador").trigger("click");
          }
	});
	
});

function validaIngreso()
{
	var user = $("#idproveedor").val();
	var pass = $("#pass").val();
	
	if(user == "" || pass == "") {
		mensaje("mensajeProveedores","Los campos son obligatorios", "error");
		if(user == "")
			$("#idproveedor").focus();
		else 
			$("#pass").focus();
	} else {
		verificaIngreso(user, pass);
	}
}

function validaIngresoEmbarcador()
{
	var user = $("#idEmbarcador").val();
	var pass = $("#passEmbarcador").val();
	
	if(user == "" || pass == "") {
		mensaje("mensajeEmbarcador","Los campos son obligatorios", "error");
		if(user == "")
			$("#idEmbarcador").focus();
		else 
			$("#passEmbarcador").focus();
	} else {
		verificaIngresoEmbarcador(user, pass);
	}
}

function verificaIngresoEmbarcador(user, pass)
{
	$.post('ajax/index.php',{ accion: 'validaIngresoEmbarcador', user:user, pass:pass}, function(response) {
	}).done(function(response) {

		var json = jQuery.parseJSON(response);
		$.each(json, function(i, d) {
			mensaje("mensajeEmbarcador",d.Mensaje, d.status);
			if(d.status == 'ok') {
				$("#idEmbarcador").val("");
				$("#passEmbarcador").val("");
				var url = "embarcadores.php";
				window.open(url, '_self');
			} else {
				$("#idEmbarcador").focus();
			}
		});
	});
}


function verificaIngreso(user, pass)
{
	$.post('ajax/index.php',{ accion: 'validaIngreso', user:user, pass:pass}, function(response) {
	}).done(function(response) {

		var json = jQuery.parseJSON(response);
		$.each(json, function(i, d) {
			mensaje("mensajeProveedores",d.Mensaje, d.status);
			if(d.status == 'ok') {
				$("#idproveedor").val("");
				$("#pass").val("");
				var url = "proveedores.php";
				window.open(url, '_self');
			} else {
				$("#idproveedor").focus();
			}
		});
	});
}


function mensaje(idMensaje,texto, status)
{
	$("#"+idMensaje+"").html("");
	$("#"+idMensaje+"").html(texto);
	if(status == 'ok') {
		$("#"+idMensaje+"").css("color","green");
		setTimeout(function(){$('#'+idMensaje+'').html("");}, 500);
	}
	if(status == 'error') {
		$("#"+idMensaje+"").css("color","red");
		setTimeout(function(){$('#'+idMensaje+'').html("");}, 1500);
	}
}
