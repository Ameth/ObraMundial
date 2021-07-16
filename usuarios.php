<?php require_once("includes/conexion.php");
PermitirAcceso(502);

if(isset($_GET['id'])&&($_GET['id']!="")){
	$IdUsuario=base64_decode($_GET['id']);
}
if(isset($_GET['tl'])&&($_GET['tl']!="")){//0 Si se está creando. 1 Se se está editando.
	$edit=$_GET['tl'];
}elseif(isset($_POST['tl'])&&($_POST['tl']!="")){
	$edit=$_POST['tl'];
}else{
	$edit=0;
}

if($edit==0){
	$Title="Crear usuario";
}else{
	$Title="Editar usuario";
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		if($_POST['tl']==1){//Actualizar
			$IdUsuario=base64_decode($_POST['IDUsuario']);
			$Type=2;
		}else{//Crear
			$IdUsuario="NULL";
			$Type=1;
		}
		if($_POST['tl']==1){
			if($_POST['Password']!=""){//Cambiar clave
				$ParamUpdClave=array(
					"'".$IdUsuario."'",
					"'".md5($_POST['Password'])."'",
					"'".$_POST['CambioPass']."'"
				);
				$SQL_Clave=EjecutarSP('usp_tbl_Usuarios_CambiarClave',$ParamUpdClave,5);
			}
			$Pass="NULL";
			$User="NULL";
			$ChgPass="NULL";
		}else{
			$Pass="'".md5($_POST['Password'])."'";
			$User="'".$_POST['Usuario']."'";
			$ChgPass="'".$_POST['CambioPass']."'";
		}
		
		if(PermitirFuncion(101)){
			$Cong="'".$_POST['Cong']."'";
		}else{
			$Cong="'".$_SESSION['NumCong']."'";
		}
		
		//Eliminar
		if($_POST['P']==6){
			$IdUsuario=base64_decode($_POST['IDUsuario']);
			$Type=3;
		}
		
		$ParamInsUser=array(
			$IdUsuario,
			$User,
			$Pass,
			"'".strtoupper($_POST['Nombre'])."'",
			"'".strtoupper($_POST['SegundoNombre'])."'",
			"'".strtoupper($_POST['Apellido'])."'",
			"'".strtoupper($_POST['SegundoApellido'])."'",
			"'".$_POST['Email']."'",
			"'".$_POST['PerfilUsuario']."'",
			$ChgPass,
			"'".$_POST['TimeOut']."'",
			$Cong,
			"'".$_POST['Publicador']."'",
			"'".$_POST['Estado']."'",
			$Type
		);
		$SQL_InsUser=EjecutarSP('usp_tbl_Usuarios',$ParamInsUser,$_POST['P']);
		
		if($SQL_InsUser){
			sqlsrv_close($conexion);
			if($_POST['tl']==0){//Creando usuario	
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_User"));
			}else{//Actualizando usuario
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_EditUser"));					
			}		
		}else{
			throw new Exception('Ha ocurrido un error al insertar el usuario');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($edit==1){//Editar usuario
	
	$SQL=Seleccionar('uvw_tbl_Usuarios','*',"IDUsuario='".$IdUsuario."'");
	$row=sqlsrv_fetch_array($SQL);
	
	//Publicadores
	$SQL_Publicadores=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$row['NumCong']."'",'Nombre');
		
}
	
//Estados
$SQL_Estados=Seleccionar('uvw_tbl_Estados','*');

//Perfiles
if(PermitirFuncion(101)){
	$SQL_Perfiles=Seleccionar('uvw_tbl_PerfilesUsuarios','*',"NumCong='".$_SESSION['NumCong']."' OR NumCong='0'",'PerfilUsuario');
}else{
	$SQL_Perfiles=Seleccionar('uvw_tbl_PerfilesUsuarios','*',"NumCong='".$_SESSION['NumCong']."'",'PerfilUsuario');
}


if(PermitirFuncion(101)){
	$SQL_Cong=Seleccionar('uvw_tbl_Congregaciones','*','','NombreCongregacion');
}else{
	$SQL_Publicadores=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."'",'Nombre');
}
?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $Title;?> | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
	$(document).ready(function() {//Cargar los combos dependiendo de otros
		$("#Cong").change(function(){
			$('.ibox-content').toggleClass('sk-loading',true);
			var Cong=document.getElementById('Cong').value;
			$.ajax({
				type: "POST",
				url: "ajx_cbo_select.php?type=1&id="+Cong,
				success: function(response){
					$('#Publicador').html(response).fadeIn();
					$('#Publicador').trigger('change');
					$('.ibox-content').toggleClass('sk-loading',false);
				}
			});
		});
	});
</script>
<script>
function ValidarUsuario(User){
	var spinner=document.getElementById('spinner1');
	spinner.style.visibility='visible';
	$.ajax({
		type: "GET",
		url: "includes/procedimientos.php?type=1&Usuario="+User,
		success: function(response){
			document.getElementById('Validar').innerHTML=response;
			spinner.style.visibility='hidden';
			if(response=="<p class='text-danger'><i class='fa fa-times-circle-o'></i> No disponible</p>"){
				document.getElementById('Crear').disabled=true;
			}else{
				document.getElementById('Crear').disabled=false;
			}
		}
	});
}

function Mostrar(){
	var x = document.getElementById("Password").getAttribute("type");
	if(x=="password"){
		document.getElementById('Password').setAttribute('type','text');
		document.getElementById('VerPass').setAttribute('class','glyphicon glyphicon-eye-close');
		document.getElementById('aVerPass').setAttribute('title','Ocultar contrase'+String.fromCharCode(241)+'a');
	}else{
		document.getElementById('Password').setAttribute('type','password');
		document.getElementById('VerPass').setAttribute('class','glyphicon glyphicon-eye-open');
		document.getElementById('aVerPass').setAttribute('title','Mostrar contrase'+String.fromCharCode(241)+'a');
	}	
}
</script>
<!-- InstanceEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include_once("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include_once("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-8">
                    <h2><?php echo $Title;?></h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Administraci&oacute;n</a>
                        </li>
                        <li>
                            <a href="gestionar_usuarios.php">Gestionar usuarios</a>
                        </li>
                        <li class="active">
                            <strong><?php echo $Title;?></strong>
                        </li>
                    </ol>
                </div>
            </div>
           
         <div class="wrapper wrapper-content">
			<div class="row">
           		<div class="col-lg-12">
					<div class="ibox-content"> 
					<?php include("includes/spinner.php"); ?>
              <form action="usuarios.php" method="post" class="form-horizontal" id="AgregarUsuario">
				  <div class="form-group">
					<label class="col-lg-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-user-circle"></i> Datos de usuario</h3></label>
				  </div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Nombre <span class="text-danger">*</span></label>
					<div class="col-lg-3"><input name="Nombre" type="text" required="required" class="form-control" autocomplete="off" id="Nombre" value="<?php if($edit==1){echo $row['Nombre'];}?>"></div>
					<label class="col-lg-1 control-label">Segundo nombre</label>
					<div class="col-lg-3"><input name="SegundoNombre" type="text" class="form-control" id="SegundoNombre" autocomplete="off" value="<?php if($edit==1){echo $row['SegundoNombre'];}?>"></div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Apellido <span class="text-danger">*</span></label>
					<div class="col-lg-3"><input name="Apellido" type="text" required="required" class="form-control" autocomplete="off" id="Apellido" value="<?php if($edit==1){echo $row['Apellido'];}?>"></div>
					<label class="col-lg-1 control-label">Segundo apellido</label>
					<div class="col-lg-3"><input name="SegundoApellido" type="text" class="form-control" autocomplete="off" id="SegundoApellido" value="<?php if($edit==1){echo $row['SegundoApellido'];}?>"></div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Email</label>
					<div class="col-lg-3"><input name="Email" type="email" class="form-control" autocomplete="off" id="Email" value="<?php if($edit==1){echo $row['Email'];}?>"></div>					
					<?php if(PermitirFuncion(101)){?>
					<label class="col-lg-1 control-label">Congregación <span class="text-danger">*</span></label>
					<div class="col-lg-3">
						<select name="Cong" class="form-control select2" required id="Cong">
							<option value="">(Seleccione)</option>
                          <?php while($row_Cong=sqlsrv_fetch_array($SQL_Cong)){?>
								<option value="<?php echo $row_Cong['NumCong'];?>" <?php if(($edit==1)&&(isset($row['NumCong']))&&(strcmp($row_Cong['NumCong'],$row['NumCong'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Cong['NombreCongregacion'].", ".$row_Cong['Ciudad']." (".$row_Cong['NumCong'].")";?></option>
						  <?php }?>
						</select>
					</div>
					<?php }?>
					<label class="col-lg-1 control-label">Publicador asociado</label>
					<div class="col-lg-3">
						<select name="Publicador" class="form-control select2" id="Publicador">
							<option value="">(Seleccione)</option>
                          <?php if(($edit==1)||(!PermitirFuncion(101))){
									while($row_Publicadores=sqlsrv_fetch_array($SQL_Publicadores)){?>
								<option value="<?php echo $row_Publicadores['IDPublicador'];?>" <?php if(isset($row['IDPublicador'])&&(strcmp($row_Publicadores['IDPublicador'],$row['IDPublicador'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Publicadores['Nombre']." ".$row_Publicadores['Apellido']." ".$row_Publicadores['SegundoApellido'];?></option>
						  <?php 	}
								}?>
						</select>
					</div>
				</div>
				 <div class="form-group">
					<label class="col-lg-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-key"></i> Datos de acceso</h3></label>
				  </div>
				<div class="form-group" id="pwd-container1">
					<label class="col-lg-1 control-label">Usuario <span class="text-danger">*</span></label>
					<div class="col-lg-2"><input name="Usuario" type="text" required="required" class="form-control" autocomplete="off" id="Usuario" onChange="ValidarUsuario(this.value);" value="<?php if($edit==1){echo $row['Usuario'];}?>" <?php if($edit==1){ echo "readonly";}?>></div>
					<div id="Validar" class="col-lg-1">
						<div id="spinner1" style="visibility: hidden;" class="sk-spinner sk-spinner-wave">
							<div class="sk-rect1"></div>
							<div class="sk-rect2"></div>
							<div class="sk-rect3"></div>
							<div class="sk-rect4"></div>
							<div class="sk-rect5"></div>
						</div>
					</div>
					<label class="col-lg-1 control-label">Contrase&ntilde;a <span class="text-danger">*</span></label>
					<div class="col-lg-3"><input name="Password" type="password" class="form-control example1" id="Password" value="" <?php if($edit==0){ echo "required='required'";}?>><a href="#" id="aVerPass" onClick="javascript:Mostrar();" title="Mostrar contrase&ntilde;a" class="btn btn-default btn-xs"><span id="VerPass" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></div>					
					<div class="col-lg-3"><div id="lvlPass" class="pwstrength_viewport_progress"></div></div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Time Out <span class="text-danger">*</span></label>
					<div class="col-lg-3"><input name="TimeOut" type="text" required="required" class="form-control" id="TimeOut" value="900" maxlength="4" value="<?php if($edit==1){echo $row['TimeOut'];}?>"></div>
					<div class="col-lg-1">
						<div class="switch pull-right">
							<div class="onoffswitch">
								<input name="CambioPass" type="checkbox" class="onoffswitch-checkbox" id="CambioPass" value="1">
								<label class="onoffswitch-label" for="CambioPass">
									<span class="onoffswitch-inner"></span>
									<span class="onoffswitch-switch"></span>
								</label>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<p class="text-primary">Solicitar cambio de contrase&ntilde;a al primer inicio de sesi&oacute;n.</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Perfil <span class="text-danger">*</span></label>
					<div class="col-lg-3">
                    	<select name="PerfilUsuario" class="form-control" id="PerfilUsuario" required>
                          <?php while($row_Perfiles=sqlsrv_fetch_array($SQL_Perfiles)){?>
								<option value="<?php echo $row_Perfiles['IDPerfilUsuario'];?>" <?php if(($edit==1)&&(strcmp($row_Perfiles['IDPerfilUsuario'],$row['IDPerfilUsuario'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Perfiles['PerfilUsuario'];?></option>
						  <?php }?>
						</select>
               	  </div>
					<label class="col-lg-1 control-label">Estado <span class="text-danger">*</span></label>
					<div class="col-lg-3">
                    	<select name="Estado" class="form-control" id="Estado" required>
                          <?php while($row_Estado=sqlsrv_fetch_array($SQL_Estados)){?>
								<option value="<?php echo $row_Estado['IDEstado'];?>" <?php if(($edit==1)&&(strcmp($row_Estado['IDEstado'],$row['Estado'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Estado['NombreEstado'];?></option>
						  <?php }?>
						</select>
               	  </div>
				</div>
				<div class="form-group">
					<div class="col-lg-9">
						<?php if($edit==1){?>
						<button class="btn btn-warning" type="submit" id="Crear"><i class="fa fa-refresh"></i> Actualizar usuario</button> 
						<button class="btn btn-danger" type="submit" onClick="EnviarFrm('6');" id="Eliminar"><i class="fa fa-trash"></i> Eliminar usuario</button> 
						<?php }else{?>
						<button class="btn btn-primary" type="submit" id="Crear"><i class="fa fa-check"></i> Crear usuario</button>
						<?php }?>
						<a href="gestionar_usuarios.php" class="btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
					</div>
				</div>
				<?php 
					if(isset($_GET['return'])){
						$return=base64_decode($_GET['pag'])."?".base64_decode($_GET['return']);
					}else{
						$return="gestionar_usuarios.php?";
					}
				  	$return=QuitarParametrosURL($return,array("a"));?>
				  
				<input type="hidden" id="IDUsuario" name="IDUsuario" value="<?php if($edit==1){echo base64_encode($row['IDUsuario']);}?>" />
				<input type="hidden" id="P" name="P" value="<?php if($edit==1){ echo "5";}else{echo "4";}?>" />
				<input type="hidden" id="tl" name="tl" value="<?php echo $edit;?>" />
				<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return);?>" />		
			  </form>
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
		 $("#AgregarUsuario").validate({
			 submitHandler: function(form){
				var vP=document.getElementById('P');
				 if(vP.value==6){
					 Swal.fire({
						title: "¿Estás seguro que deseas eliminar este usuario?",
						icon: "warning",
						showCancelButton: true,
						confirmButtonText: "Si, confirmo",
						cancelButtonText: "No"
					}).then((result) => {
						if (result.isConfirmed) {
							$('.ibox-content').toggleClass('sk-loading',true);
							form.submit();
						}
					});
				 }else{
					Swal.fire({
						title: "¿Está seguro que desea guardar los datos?",
						icon: "question",
						showCancelButton: true,
						confirmButtonText: "Si, confirmo",
						cancelButtonText: "No"
					}).then((result) => {
						if (result.isConfirmed) {
							$('.ibox-content').toggleClass('sk-loading',true);
							form.submit();
						}
					});
				 }
				}
			});
		 $('.chosen-select').chosen({width: "100%"});
		 $(".select2").select2();
		 
		 $(".btn_del").each(function (el){
			$(this).bind("click",delRow);
		});
		 
		document.getElementById('Nombre').focus();
		 
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
	
function EnviarFrm(P){
	var vP=document.getElementById('P');
	vP.value=P;
}
</script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>