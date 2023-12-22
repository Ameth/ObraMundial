<?php 
require("includes/conexion.php");
$result=0;
if(isset($_POST['Cambio'])&&($_POST['Cambio']==1)){
	if(isset($_POST['Password'])&&(md5($_POST['Password'])==md5($_POST['ConfPassword']))){
		try{
			$Upd_Clave="EXEC usp_tbl_Usuarios_CambiarClave '".$_SESSION['CodUser']."', '".md5($_POST['Password'])."', '0'";
			$SQL_Clave=sqlsrv_query($conexion,$Upd_Clave);
			if(sqlsrv_query($conexion,$Upd_Clave)){
				header('Location:logout.php?data='.base64_encode("result"));
			}else{//Sino se actualiza la clave
				sqlsrv_close($conexion);
				throw new Exception('Ha ocurrido un error cambiar la clave.');
				echo $Upd_Clave;
			}
		}catch (Exception $e) {
			//InsertarLog(1, 5, $Upd_Clave);
			echo 'Excepción capturada: ',  $e->getMessage(), "\n";
			exit();
		}
	}else{//Si la nueva clave y la confirmación no son iguales
		$result=2;			
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<html lang="en" class="light-style">

<head>
    <title>Cambiar clave | <?php echo NOMBRE_PORTAL; ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="shortcut icon" href="css/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <link rel="stylesheet" href="css/bootstrap.css" class="theme-settings-bootstrap-css">
    <link rel="stylesheet" href="css/appwork.css" class="theme-settings-appwork-css">
    <link rel="stylesheet" href="css/theme-corporate.css" class="theme-settings-theme-css">
    <link rel="stylesheet" href="css/uikit.css">
    <link rel="stylesheet" href="css/authentication.css">
    <link rel="stylesheet" href="css/toastr.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/toastr.js"></script>
    <script src="js/plugins/validate/jquery.validate.min.js"></script>
    <script src="js/funciones.js"></script>
</head>

<body>
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>

    <!-- Content -->

    <div class="authentication-wrapper authentication-2 ui-bg-cover ui-bg-overlay-container px-4" style="background-image: url('img/img_background2.jpg');">
        <div class="ui-bg-overlay bg-dark opacity-25"></div>

        <div class="authentication-inner py-5">

            <div class="card">
                <div class="p-4 px-sm-5 pt-sm-5 pb-0">
                    <!-- Logo -->
                    <div class="d-flex justify-content-center align-items-center pb-2 mb-4">
                        <img src="img/logo_200x200.png" alt="Obra Mundial" />
                    </div>
                    <!-- / Logo -->

                    <!-- <h3 class="text-center text-muted font-weight-normal mb-4">Iniciar sesión</h3>-->

                    <!-- Form -->
                    <form name="frmLogin" id="frmLogin" class="mt-5" role="form" action="login_cambio_clave.php" method="post" enctype="application/x-www-form-urlencoded">
                        <div class="form-group">
                            <label class="form-label">Nueva contrase&ntilde;a</label>
                            <input name="Password" type="password" autofocus required="required" class="form-control" id="Password" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmar</label>
                            <input name="ConfPassword" type="password" required="required" class="form-control" id="ConfPassword" maxlength="50">
                        </div>
                        <div class="d-flex justify-content-between align-items-center m-0">
                            <button type="submit" class="btn btn-primary">Cambiar contrase&ntilde;a</button>
                            <button onClick="javascript:location.href='logout.php'" type="button" class="btn btn-secondary">Salir</button>
                        </div><br>
                        <?php if($result==2){?>
                            <div class="alert alert-danger">
                                <i class="fa fa-times-circle-o"></i> <strong>Error.</strong> Las contrase&ntilde;as no coinciden. <br>Por favor verifique.
                            </div>
                        <?php }?>
                        <input name="Cambio" type="hidden" id="Cambio" value="1">
                    </form>
                    <!-- / Form -->

                </div>
                <div class="card-footer py-3 px-4 px-sm-5">
                    <div class="text-center text-body">
                        <a href="https://github.com/ameth/ObraMundial" target="_blank">
                            <svg width="30px" fill="#000000" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <title>Ver en Github</title>
                                <path d="M16 1.375c-8.282 0-14.996 6.714-14.996 14.996 0 6.585 4.245 12.18 10.148 14.195l0.106 0.031c0.75 0.141 1.025-0.322 1.025-0.721 0-0.356-0.012-1.3-0.019-2.549-4.171 0.905-5.051-2.012-5.051-2.012-0.288-0.925-0.878-1.685-1.653-2.184l-0.016-0.009c-1.358-0.93 0.105-0.911 0.105-0.911 0.987 0.139 1.814 0.718 2.289 1.53l0.008 0.015c0.554 0.995 1.6 1.657 2.801 1.657 0.576 0 1.116-0.152 1.582-0.419l-0.016 0.008c0.072-0.791 0.421-1.489 0.949-2.005l0.001-0.001c-3.33-0.375-6.831-1.665-6.831-7.41-0-0.027-0.001-0.058-0.001-0.089 0-1.521 0.587-2.905 1.547-3.938l-0.003 0.004c-0.203-0.542-0.321-1.168-0.321-1.821 0-0.777 0.166-1.516 0.465-2.182l-0.014 0.034s1.256-0.402 4.124 1.537c1.124-0.321 2.415-0.506 3.749-0.506s2.625 0.185 3.849 0.53l-0.1-0.024c2.849-1.939 4.105-1.537 4.105-1.537 0.285 0.642 0.451 1.39 0.451 2.177 0 0.642-0.11 1.258-0.313 1.83l0.012-0.038c0.953 1.032 1.538 2.416 1.538 3.937 0 0.031-0 0.061-0.001 0.091l0-0.005c0 5.761-3.505 7.029-6.842 7.398 0.632 0.647 1.022 1.532 1.022 2.509 0 0.093-0.004 0.186-0.011 0.278l0.001-0.012c0 2.007-0.019 3.619-0.019 4.106 0 0.394 0.262 0.862 1.031 0.712 6.028-2.029 10.292-7.629 10.292-14.226 0-8.272-6.706-14.977-14.977-14.977-0.006 0-0.013 0-0.019 0h0.001z">
                                </path>
                            </svg>
                        </a>
                        <br>
                        <?php include("includes/copyright.php"); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php if (isset($_POST['data']) && $_POST['data'] == "OK") { ?>
        <script>
            $(document).ready(function() {
                toastr.success('¡Su contraseña ha sido modificada!', 'Felicidades');
            });
        </script>
    <?php } ?>
    <script>
        $(document).ready(function() {
            $("#frmLogin").validate();
        });
    </script>
    <?php include("includes/pie.php"); ?>

</body>

</html>