<!DOCTYPE html>
<html>
    <head>
        
        <!-- Importación de la librería de jquery. -->
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
        
        <script type="text/javascript">
            
            function cargando(){
                $("#respuesta").html('Cargando');
            }
            
            function resultadoOk(){
                $("#respuesta").html('El archivo ha sido subido exitosamente.');
            }
            
            function resultadoErroneo(){
                $("#respuesta").html('Ha surgido un error y no se ha podido subir el archivo.');
            }
            
            $(document).ready(function(){
                $("#boton_subir_archivo").click(function(){
                    cargando();
                });
            });
            
        </script>
        
    </head>
    <body>
        
        <form action="subir_archivo.php" class="formularios" method="post" enctype="multipart/form-data" target="contenedor_subir_archivo">
            <label> Archivo </label>
            <input type="file" name="archivo" />
            <input type="submit" id="boton_subir_archivo" />
            <div id="respuesta"></div>
            <iframe width="1" height="1" frameborder="0" name="contenedor_subir_archivo" style="display: none"></iframe>
        </form>
            
    </body>
</html>