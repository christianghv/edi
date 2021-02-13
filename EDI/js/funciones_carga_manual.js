function IngresarLog(tipo,respuesta)
{
	switch(tipo) {
		case 'N':
			$("#respuesta").append(respuesta+'<br />');
			break;
		case 'E':
			$("#respuesta").append('<span class="label label-danger">'+respuesta+'</span>'+'<br />');
			break;
		case 'P':
			$("#respuesta").append('<span class="label label-primary">'+respuesta+'</span>'+'<br />');
			break;
		case 'I':
			$("#respuesta").append('<span class="label label-info">'+respuesta+'</span>'+'<br />');
			break;
		case 'W':
			$("#respuesta").append('<span class="label label-warning">'+respuesta+'</span>'+'<br />');
			break;
		case 'S':
			$("#respuesta").append('<span class="label label-success">'+respuesta+'</span>'+'<br />');
			break;
	}
}