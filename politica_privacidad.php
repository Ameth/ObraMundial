<?php require_once("includes/conexion.php"); ?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo NOMBRE_PORTAL;?> | Pol&iacute;tica de privacidad</title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include_once("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include_once("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-4">
                    <h2>Pol&iacute;tica de privacidad</h2>
                </div>
            </div>
        <div class="row wrapper wrapper-content animated fadeInRight">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<h3>Datos personales</h3>
				<p>La información personal que envíe al sitio se usará únicamente con el fin que usted indicó en el momento del envío. Nosotros no pasamos datos personales a nadie a menos que sea necesario para proporcionar los servicios que usted solicitó, como se lo hemos hecho saber, o a menos que sea necesario cumplir con determinadas normas o leyes, o con el fin de detectar y prevenir fraudes, seguridad o problemas técnicos. Al usar este sitio de Internet, usted nos da el consentimiento para divulgar su información personal a terceras personas únicamente con estos fines. Ningún dato personal recibido en este sitio será vendido, intercambiado ni alquilado bajo ningún concepto.</p><br>
				<h3>Seguridad</h3>
				<p>Nos tomamos muy en serio la seguridad de sus datos personales protegiéndolos durante el tránsito usando la encriptación, como la Seguridad de la capa de transporte (TSL). Usamos sistemas informáticos de acceso limitado que se alojan en instalaciones que cuentan con medidas de seguridad físicas, electrónicas y procedimentales con el fin de proteger la confidencialidad y seguridad de la información que se nos transmite.</p><br>
				<h3>Cuentas</h3>
				<p>La dirección de correo electrónico que proporcione al crear su cuenta en este sitio de internet la usaremos para comunicarnos con usted en lo relacionado con la misma. Por ejemplo, si olvida su nombre de usuario o contraseña y pide ayuda para registrarse, le ayudaremos enviando un mensaje al correo electrónico que nos proporcionó en su perfil de usuario.</p>
			</div>
		</div>		   
        <!-- InstanceEndEditable -->
        <?php include_once("includes/footer.php"); ?>

    </div>
</div>
<?php include_once("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->

<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>