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
        <img src="img/mantenimiento_img.png" class="img-responsive" alt="Mantenimiento" />
    </div>
</body>

</html>
