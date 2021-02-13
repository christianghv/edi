$( document ).ready(function() {
	ocultarMenuDesabilitados();
});
function ajustarResolucion()
{
		var anchoPag=$(window ).width();
		anchoPag=anchoPag/1000;
		
		if(screen.width!=$(window ).width())
		{
			if(anchoPag<0.9)
			{
				//cambiarResolucion();
				//alert("cambio 1");
				var reso=anchoPag-1;
				reso=1-reso;
				reso=reso-0.9;
				
				cambiarResolucion(reso);
			}
			if(anchoPag>0.9 && anchoPag<1)
			{
				//cambiarResolucion();
				//alert("Cambio 2");
				var reso=anchoPag-1;
				reso=1-reso;
				reso=reso-0.5;
				
				cambiarResolucion(reso);
			}
			if(anchoPag>1)
			{
				//alert("Cambio 3")
				var reso=anchoPag-1;
				reso=1-reso;
				reso=reso-0.2;
				//alert(reso);
				cambiarResolucion(reso);
			}
		}
		else
		{
			cambiarResolucion(1);
		}
}
function cambiarResolucion(resolucion)
{
	var currentZoom = 0;//Zoom inicial
		var currentBckAncho = 1800;//Ancho fondo inicial
		var currentBckAlto = 982;//Alto fondo inicial
		
		currentZoom += resolucion;
		currentBckAnchoFin= Math.round(currentZoom*currentBckAncho);
		currentBckAltoFin= Math.round(currentZoom*currentBckAlto);
				
		document.body.style.zoom=currentZoom; 
		document.body.style.backgroundSize=currentBckAnchoFin+"px " +currentBckAltoFin+ "px";
				
		//Firefox
		document.body.style.MozTransform = 'scale(' + currentZoom + ')';
				
		//Opera
		document.body.style.OTransform = 'scale(' + currentZoom + ')';
		return false;
}
function ocultarMenuDesabilitados()
{
	//Variables
	var EspaciosPrimeraFila=3;
	var opcionesDesabilidatas=0;
	var opciones=0;
	
	//--Menu administracion
	opcionesDesabilidatas=0;
	opciones=0;
	
	$(($('#MenuAdministracion').find('div ul li'))).each(function(){
		var estadoOpcion=$(this).parent().find('li:eq('+opciones+')').css('display');
		if(estadoOpcion=="none")
		{
			opcionesDesabilidatas++;
		}
		opciones++;
	});
	
	//Si todas las opciones estan desabilitadas se ocultara el td admnistracion
	if(opcionesDesabilidatas==opciones)
	{
		$('#MenuAdministracion').css('display','none');
		EspaciosPrimeraFila--;
	}
	
	//--Menu EDI
	opcionesDesabilidatas=0;
	opciones=0;
	
	$(($('#MenuEdi').find('div ul li'))).each(function(){
		var estadoOpcion=$(this).parent().find('li:eq('+opciones+')').css('display');
		if(estadoOpcion=="none")
		{
			opcionesDesabilidatas++;
		}
		opciones++;
	});
	
	//Si todas las opciones estan desabilitadas se ocultara el td admnistracion
	if(opcionesDesabilidatas==opciones)
	{
		$('#MenuEdi').css('display','none');
		EspaciosPrimeraFila--;
	}
	
	//--Menu Proveedor
	opcionesDesabilidatas=0;
	opciones=0;
	
	$(($('#MenuProv').find('div ul li'))).each(function(){
		var estadoOpcion=$(this).parent().find('li:eq('+opciones+')').css('display');
		if(estadoOpcion=="none")
		{
			opcionesDesabilidatas++;
		}
		opciones++;
	});
	
	//Si todas las opciones estan desabilitadas se ocultara el td admnistracion
	if(opcionesDesabilidatas==opciones)
	{
		$('#MenuProv').css('display','none');
		EspaciosPrimeraFila--;
	}
	
	//--Menu Embarcador
	opcionesDesabilidatas=0;
	opciones=0;
	
	$(($('#MenuEmbar').find('div ul li'))).each(function(){
		var estadoOpcion=$(this).parent().find('li:eq('+opciones+')').css('display');
		if(estadoOpcion=="none")
		{
			opcionesDesabilidatas++;
		}
		opciones++;
	});
	
	//Si todas las opciones estan desabilitadas se ocultara el td admnistracion
	if(opcionesDesabilidatas==opciones)
	{
		$('#MenuEmbar').css('display','none');
	}
	
	//Verificar si hay espacion en la primera fila
	var espaciosDisponibles=3-EspaciosPrimeraFila;
	if(espaciosDisponibles>0)
	{
		var divDivision='<div class="division" id="divisionProveedores" style="width: 1px;float: left;"></div>';
		var MenuEmbar=$('#MenuEmbar').parent().html();
		
		MenuEmbar = replaceAll(MenuEmbar, "<br>", "" );
		
		var div=$(MenuEmbar).html();
		
		var NuevoMenu='<td id="MenuEmbar">'+divDivision+div+'</td>';
				
		$('#MenuEmbar').parent().html('');		
		$('#trPrimeraFila').append(NuevoMenu);
		
	}
	
}
function replaceAll( text, busca, reemplaza ){
	  while (text.toString().indexOf(busca) != -1)
	      text = text.toString().replace(busca,reemplaza);
	  return text;
}
function activarPermisos(jsonPermisos)
{
	//alert("Prueba"+hola);
	var json = jQuery.parseJSON(jsonPermisos);
		$.each(json, function(i, d) {	
				var idPermiso=d.id_permiso;
				
				//Se haran visible solo los accesos permitidos
				$("#menu_"+idPermiso+"").css("display","block");
		});
}