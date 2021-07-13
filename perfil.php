<?php require_once("includes/conexion.php");
PermitirAcceso(501);
$IdPerfil="";
$msg_error="";//Mensaje del error

if(isset($_GET['id'])&&($_GET['id']!="")){
	$IdPerfil=base64_decode($_GET['id']);
}

if(isset($_GET['tl'])&&($_GET['tl']!="")){//0 Creando. 1 Editando.
	$edit=$_GET['tl'];
}elseif(isset($_POST['tl'])&&($_POST['tl']!="")){
	$edit=$_POST['tl'];
}else{
	$edit=0;
}

if(isset($_POST['swError'])&&($_POST['swError']!="")){//Para saber si ha ocurrido un error.
	$sw_error=$_POST['swError'];
}else{
	$sw_error=0;
}

if($edit==0){
	$Title="Crear perfil";
}else{
	$Title="Editar perfil";
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		if($_POST['tl']==1){//Actualizar
			$IdPerfil=base64_decode($_POST['IDPerfilUsuario']);
			$Type=2;
		}else{//Crear
			$IdPerfil="NULL";
			$Type=1;
		}		
		$Parametros=array(
			$IdPerfil,
			"'".$_POST['NombrePerfil']."'",
			"'".$_SESSION['NumCong']."'",
			"$Type"
		);
		$SQL_Perfil=EjecutarSP('usp_tbl_PerfilesUsuarios',$Parametros,$_POST['P']);
		if($SQL_Perfil){
			if($_POST['tl']==1){
				$Cons_Delete="Delete From tbl_PermisosPerfiles Where IDPerfilUsuario='".$IdPerfil."'";
				$SQL_Delete=sqlsrv_query($conexion,$Cons_Delete);
			}else{
				$row_InsPerfil=sqlsrv_fetch_array($SQL_Perfil);
				$IdPerfil=$row_InsPerfil[0];
			}
			
			$i=0;
			$Cuenta=count($_POST['Permiso']);
			while($i<$Cuenta){
				$Cons_InsertPer="Insert Into tbl_PermisosPerfiles Values ('".$IdPerfil."','".$_POST['Permiso'][$i]."')";
				$SQL_InsertPer=sqlsrv_query($conexion,$Cons_InsertPer);
				if($SQL_InsertPer){
					$i++;
				}else{
					$sw_error=1;
					$msg_error="Ha ocurrido un error insertando el permiso";
				}			
			}
			sqlsrv_close($conexion);
			if($_POST['tl']==0){//Creando Entrega	
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_Perfil"));
			}else{//Actualizando Entrega
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_EditPerfil"));					
			}			
		}else{
			$sw_error=1;
			$msg_error="Ha ocurrido un error al insertar el perfil";
		}
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

//Listar los nombres de los permisos
if(PermitirFuncion(101)){
	$Cons_Permisos="Select * From uvw_tbl_NombresPermisosPerfiles";
}else{
	$Cons_Permisos="Select * From uvw_tbl_NombresPermisosPerfiles Where SuperAdmin='N'";
}
$SQL_Permisos=sqlsrv_query($conexion,$Cons_Permisos);

if($edit==1){//Editando
	if(PermitirFuncion(101)){
		$SQL=Seleccionar('uvw_tbl_PerfilesUsuarios','*',"IDPerfilUsuario='".$IdPerfil."'");
	}else{
		$SQL=Seleccionar('uvw_tbl_PerfilesUsuarios','*',"NumCong='".$_SESSION['NumCong']."' and IDPerfilUsuario='".$IdPerfil."'");
	}
	
	$row=sqlsrv_fetch_array($SQL);
	
	$SQL_RelPermiso=Seleccionar('uvw_tbl_PermisosPerfiles','*',"IDPerfilUsuario='".$IdPerfil."'");
	$PermisosPerfil=array();
	$i=0;
	while($row_RelPermiso=sqlsrv_fetch_array($SQL_RelPermiso)){
		$PermisosPerfil[$i]=$row_RelPermiso['ID_Permiso'];
		$i++;
	}
}
?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo NOMBRE_PORTAL;?> | <?php echo $Title;?></title>
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
                            <a href="gestionar_perfiles.php">Gestionar perfiles</a>
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
              <form action="perfil.php" method="post" class="form-horizontal" id="FrmPerfil">
				<div class="ibox-content">
					<div class="form-group">
						<label class="col-lg-1 control-label">Nombre perfil</label>
						<div class="col-lg-3"><input name="NombrePerfil" type="text" required="required" class="form-control" id="NombrePerfil" maxlength="100" value="<?php if($edit==1){echo $row['PerfilUsuario'];}?>"></div>
					</div>
					<div class="form-group">
						<div class="col-lg-9">
							<?php if($edit==1){?> 
							<button class="btn btn-warning" form="FrmPerfil" type="submit" id="Actualizar"><i class="fa fa-refresh"></i> Actualizar perfil</button>  
							<?php }else{?>
							<button class="btn btn-primary" form="FrmPerfil" type="submit" id="Crear"><i class="fa fa-check"></i> Crear perfil</button>
							<?php }?>
							<a href="gestionar_perfiles.php" class="btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
						</div>
					</div>
				</div>
				<br>
				<div class="form-group">
					<div class="col-lg-5"><h4>Seleccionar permisos para este perfil</h4></div>
		  		</div>
				 <div class="ibox-content">  
		  		<div class="form-group">
		  			<div class="col-lg-10">
		  			<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
							<tr>
								<th>Seleccionar</th>
								<th>Funci&oacute;n</th>
								<th>Descripci&oacute;n</th>
							</tr>
							</thead>
							<tbody>
						<?php while($row_Permisos=sqlsrv_fetch_array($SQL_Permisos)){
							if($row_Permisos['ID_Padre']==0){ ?>
								<tr class="warning">
									<td colspan="3"><strong><?php echo $row_Permisos['NombreFuncion'];?></strong></td>
								</tr>
						<?php
								$Cons_Padre="Select * From uvw_tbl_NombresPermisosPerfiles Where ID_Padre='".$row_Permisos['ID_Permiso']."'";
								$SQL_Padre=sqlsrv_query($conexion,$Cons_Padre);
								while($row_Padre=sqlsrv_fetch_array($SQL_Padre)){
									if(strlen($row_Padre['ID_Permiso'])==2){ ?>
										<tr class="info">
											<td colspan="3"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row_Padre['NombreFuncion'];?></strong></td>
										</tr>								
						<?php		
										$Cons_Hijo="Select * From uvw_tbl_NombresPermisosPerfiles Where ID_Padre='".$row_Padre['ID_Permiso']."'";
										$SQL_Hijo=sqlsrv_query($conexion,$Cons_Hijo);
										while($row_Hijo=sqlsrv_fetch_array($SQL_Hijo)){ ?>
											<tr>
												<td>
													<div class="switch">
														<div class="onoffswitch">
															<input name="Permiso[]" type="checkbox" class="onoffswitch-checkbox" id="<?php echo $row_Hijo['ID_Permiso'];?>" value="<?php echo $row_Hijo['ID_Permiso'];?>" <?php if($edit==1){if(in_array($row_Hijo['ID_Permiso'],$PermisosPerfil)){ echo "checked";}}?>>
															<label class="onoffswitch-label" for="<?php echo $row_Hijo['ID_Permiso'];?>">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
												</td>
												<td><?php echo $row_Hijo['NombreFuncion'];?></td>
												<td><?php echo $row_Hijo['Descripcion'];?></td>
											</tr>
											<?php
										}
									}else{
										?>
											<tr>
												<td>
													<div class="switch">
														<div class="onoffswitch">
															<input name="Permiso[]" type="checkbox" class="onoffswitch-checkbox" id="<?php echo $row_Padre['ID_Permiso'];?>" value="<?php echo $row_Padre['ID_Permiso'];?>" <?php if($edit==1){if(in_array($row_Padre['ID_Permiso'],$PermisosPerfil)){ echo "checked";}}?>>
															<label class="onoffswitch-label" for="<?php echo $row_Padre['ID_Permiso'];?>">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
													</div>
												</td>
												<td><?php echo $row_Padre['NombreFuncion'];?></td>
												<td><?php echo $row_Padre['Descripcion'];?></td>
											</tr>
											<?php
									}
								}
							}
						}
					?>
							</tbody>
						</table>
						</div>
					</div>
				  </div>
				  </div>
				  <?php 			
				   	$EliminaMsg=array("&a=".base64_encode("OK_Perfil"),"&a=".base64_encode("OK_EditPerfil"));//Eliminar mensajes
	
					if(isset($_GET['return'])){
						$_GET['return']=str_replace($EliminaMsg,"",base64_decode($_GET['return']));
					}
					if(isset($_GET['return'])){
						$return=base64_decode($_GET['pag'])."?".$_GET['return'];
					}else{
						$return="gestionar_perfiles.php?";
					}?>
				 <input type="hidden" name="IDPerfilUsuario" id="IDPerfilUsuario" value="<?php if($edit==1){echo base64_encode($row['IDPerfilUsuario']);}?>">
				 <input type="hidden" id="P" name="P" value="<?php if($edit==0){echo "8";}else{echo "9";}?>" />
				 <input type="hidden" id="tl" name="tl" value="<?php echo $edit;?>" />
				 <input type="hidden" id="swError" name="swError" value="<?php echo $sw_error;?>" />
				 <input type="hidden" id="return" name="return" value="<?php echo base64_encode($return);?>" />
			  </form>
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
		 $("#FrmPerfil").validate();
	});
</script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>