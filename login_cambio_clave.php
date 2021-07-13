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
<head>
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
<script src="js/bootstrap.js"></script>
<script src="js/toastr.js"></script>
<script src="js/plugins/validate/jquery.validate.min.js"></script>
<style>
#myVideo {
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
}
</style>
<script>
	document.oncontextmenu = function(){return false;}
</script>
</head>

<body>
  <div class="page-loader">
    <div class="bg-primary"></div>
  </div>

  <!-- Content -->

  <div class="authentication-wrapper authentication-3">
    <div class="authentication-inner">

      <!-- Side container -->
      <!-- Do not display the container on extra small, small and medium screens -->
      <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5">
        <div class="ui-bg-overlay bg-dark opacity-50"></div>
		<video autoplay muted loop id="myVideo">
		  <source src="img/vid_background.mp4" type="video/mp4">
		  Tu navegador debe soportar HTML5
		</video>
        <!-- Text -->
        <div class="w-100 text-white px-5">
          <h1 class="display-2 font-weight-bolder mb-4">BIENVENIDO
            <br><small><?php echo NOMBRE_PORTAL;?></small></h1>
          <div class="text-large font-weight-light">
           Sistema de gestión.
          </div>
        </div>
        <!-- /.Text -->
      </div>
      <!-- / Side container -->

      <!-- Form container -->
      <div class="d-flex col-lg-4 align-items-center bg-white p-5">
        <!-- Inner container -->
        <!-- Have to add `.d-flex` to control width via `.col-*` classes -->
        <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
          <div class="w-100">

            <!-- Logo -->
			   <div class="d-flex justify-content-center align-items-center">
				<img src="img/logo_200x200.png" alt="Obra Mundial" />
			  </div>
            <!-- / Logo -->

            <h4 class="text-center font-weight-normal mt-5 mb-0">Inicio de sesi&oacute;n</h4>
			<h5 class="text-center font-weight-normal mt-5 mb-0">
				Cambiar su contrase&ntilde;a
			</h5>
            <!-- Form -->
			<form name="frmLogin" id="frmLogin" class="my-5" role="form" action="login_cambio_clave.php" method="post" enctype="application/x-www-form-urlencoded">
					<div class="form-group">
						<label class="form-label">Contrase&ntilde;a</label>
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
			  <div class="text-center small mt-4">
               <?php include("includes/copyright.php"); ?>
              </div>
          </div>
        </div>
      </div>
      <!-- / Form container -->

    </div>
  </div>
<?php include("includes/pie.php"); ?>
</body>

</html>