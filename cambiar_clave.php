<?php require_once("includes/conexion.php");
$result=0;
if(isset($_POST['Cambio'])&&($_POST['Cambio']==1)){
	$ConsultaClave="Select * From uvw_tbl_Usuarios Where IDUsuario='".$_SESSION['CodUser']."'";
	//echo $ConsultaClave;
	//exit();
	$SQLClave=sqlsrv_query($conexion,$ConsultaClave);
	$rowClave=sqlsrv_fetch_array($SQLClave);
	if(md5($_POST['PasswordActual'])==$rowClave['Password']){
		if(md5($_POST['PasswordNueva'])==md5($_POST['PasswordConfirmacion'])){
			try{
				$Upd_Clave="EXEC usp_tbl_Usuarios_CambiarClave '".$_SESSION['CodUser']."', '".md5($_POST['PasswordNueva'])."', '0'";
				if(sqlsrv_query($conexion,$Upd_Clave)){
					header('Location:index1.php?dt='.base64_encode("result"));
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
	}else{//Si la clave actual no es correcta
			$result=1;
	}
}
?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Cambiar contrase&ntilde;a | <?php echo NOMBRE_PORTAL;?></title>
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
                    <h2>Cambiar contrase&ntilde;a</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Administraci&oacute;n</a>
                        </li>
                        <li class="active">
                            <strong>Cambiar contrase&ntilde;a</strong>
                        </li>
                    </ol>
                </div>
            </div>
           
         <div class="wrapper wrapper-content">
          <div class="row">
           <div class="col-lg-10">
			   <div class="ibox-content">
              <form action="cambiar_clave.php" method="post" class="form-horizontal" id="CambiarClave" enctype="application/x-www-form-urlencoded">
				<div class="form-group">
					<label class="col-sm-3 control-label">Contrase&ntilde;a actual</label>
					<div class="col-sm-3"><input name="PasswordActual" type="password" autofocus required="required" class="form-control" id="PasswordActual" maxlength="50"></div>
				</div>
				<div class="form-group" id="pwd-container1">
					<label class="col-sm-3 control-label">Nueva contrase&ntilde;a</label>
					<div class="col-sm-3"><input name="PasswordNueva" type="password" required="required" class="form-control example1" id="PasswordNueva" maxlength="50"></div>
					<div class="col-sm-5">
						<div class="pwstrength_viewport_progress"></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Reescriba la contrase&ntilde;a</label>
					<div class="col-sm-3"><input name="PasswordConfirmacion" type="password" required="required" class="form-control" id="PasswordConfirmacion" maxlength="50"></div>
				</div>
				<?php if($result==1){?>
                <div class="alert alert-danger">
       				<i class="fa fa-times-circle-o"></i> <strong>Error.</strong> Su contrase&ntilde;a actual no es correcta. Por favor verifique.
       			</div>
               <?php }elseif($result==2){?>
                <div class="alert alert-danger">
       				<i class="fa fa-times-circle-o"></i> <strong>Error.</strong> Las contrase&ntilde;as no coinciden. Por favor verifique.
       			</div>
               <?php }?>
				<div class="form-group">
					<div class="col-sm-9">
						<button class="btn btn-success" type="submit"><i class="fa fa-lock"></i>&nbsp;Cambiar</button> <a href="index1.php" class="btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
					</div>
				</div>
				<input name="Cambio" type="hidden" id="Cambio" value="1">
			  </form>
				<h4>Se recomienda que las contraseñas cumplan con los siguientes requisitos:</h4>
				<ul>					
					<li>Deben tener por lo menos 8 caracteres.</li>
					<li>Deben constar únicamente de caracteres del alfabeto latino que se encuentren en un teclado en inglés (no deben tener acentos ni otros diacríticos).</li>
					<li>Deben ser una combinación de por lo menos tres de los siguientes tipos de carácteres: mayúsculas, minúsculas, números y signos de puntuación.</li>
					<li>No deben basarse en una palabra que pueda encontrarse en un diccionario.</li>
					<li>No pueden estar basadas en su nombre ni en su nombre de usuario.</li>
					<li>No deben contener caracteres repetidos o secuencias de caracteres tales como 1234, 2222, ABCD o letras adyacentes del teclado</li>
				</ul>
		   </div>
          </div>
		 </div>
        </div>
        <!-- InstanceEndEditable -->
        <?php include_once("includes/footer.php"); ?>

    </div>
</div>
<?php include_once("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->
<script>
	 $(document).ready(function(){
		 $("#CambiarClave").validate();
		 // Example 1
            var options1 = {};
            options1.ui = {
                container: "#pwd-container1",
                showVerdictsInsideProgressBar: true,
                viewports: {
                    progress: ".pwstrength_viewport_progress"
                }
            };
            options1.common = {
                debug: false,
            };
            $('.example1').pwstrength(options1);
	});
</script>

<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>