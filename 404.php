<?php require_once("includes/conexion.php");
sqlsrv_close($conexion);
?>
<!DOCTYPE html>
<html>

<head>
<?php include_once("includes/cabecera.php"); ?>
<title><?php echo NOMBRE_PORTAL;?> | Error 404</title>
</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1 class="logo-name"><img src="img/logo_200x200.png" alt="Obra Mundial" /></h1>
        <h1>404</h1>
        <h3 class="font-bold">P&aacute;gina no encontrada</h3>

        <div class="error-desc">
            Lo sentimos, pero la p&aacute;gina que est&aacute; buscando no ha sido encontrada. Prueba comprobando la URL, luego pulsa el bot&oacute;n de actualizaci&oacute;n en tu navegador o intenta encontrar algo m&aacute;s en nuestra aplicaci&oacute;n.
        </div>
        <br><br>
         <a href="index1.php" class="btn btn-primary btn-outline"><i class="fa fa-home"></i> Volver al Inicio</a>
    </div>
</body>

</html>
