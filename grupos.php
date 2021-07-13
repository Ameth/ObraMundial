<?php require_once("includes/conexion.php");
PermitirAcceso(204);
$IdGrupo="";
$msg_error="";//Mensaje del error

if(isset($_GET['id'])&&($_GET['id']!="")){
	$IdGrupo=base64_decode($_GET['id']);
}

if(isset($_GET['tl'])&&($_GET['tl']!="")){//0 Creando publicador. 1 Editando publicador.
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
	$Title="Crear grupo";
}else{
	$Title="Editar grupo";
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		if($_POST['tl']==1){//Actualizar
			$IdGrupo=base64_decode($_POST['IdGrupo']);
			$Type=2;
		}else{//Crear
			$IdGrupo="NULL";
			$Type=1;
		}
		//Eliminar
		if($_POST['P']==24){
			$IdGrupo=base64_decode($_POST['IdGrupo']);
			$Type=3;
		}
		$Parametros=array(
			$IdGrupo,
			"'".strtoupper($_POST['NombreGrupo'])."'",
			"'".$_POST['SuperGrupo']."'",
			"'".$_POST['AuxGrupo']."'",
			"'".strtoupper($_POST['Direccion'])."'",
			"'".$_SESSION['NumCong']."'",
			"'".$_SESSION['CodUser']."'",
			"$Type"
		);
		$SQL_Pub=EjecutarSP('usp_tbl_Grupos',$Parametros,$_POST['P']);
		if($SQL_Pub){
			sqlsrv_close($conexion);
			if($_POST['tl']==0){//Creando	
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_GrpAdd"));
			}else{//Actualizando
				if($Type==3){
					header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_GrpDel"));	
				}else{
					header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_GrpUpd"));	
				}								
			}			
		}else{
			$sw_error=1;
			$msg_error="Ha ocurrido un error al insertar el publicador";
		}
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($edit==1){//Editando
	$SQL=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' AND IDGrupo='".$IdGrupo."'");
	$row=sqlsrv_fetch_array($SQL);
}

//Superintendente
$SQL_Super=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' And IDGenero='H'",'Nombre');

//Auxiliar
$SQL_Auxiliar=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' And IDGenero='H'",'Nombre');
?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $Title;?> | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($sw_error)&&($sw_error==1)){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Ha ocurrido un error!',
                text: '".$msg_error."',
                icon: 'error'
            });
		});		
		</script>";
}
?>
<script type="text/javascript">
	$(document).ready(function() {
		
	});
</script>
<!-- InstanceEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-8">
                    <h2><?php echo $Title;?></h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Congregación</a>
                        </li>
                        <li>
                            <a href="gestionar_grupos.php">Grupos de predicación</a>
                        </li>
                        <li class="active">
                            <strong><?php echo $Title;?></strong>
                        </li>
                    </ol>
                </div>
            </div>
           
      <div class="wrapper wrapper-content">
		<div class="ibox-content">
			 <?php include("includes/spinner.php"); ?>
          <div class="row"> 
           <div class="col-lg-12">
			   <form action="grupos.php" method="post" class="form-horizontal" enctype="multipart/form-data" id="frmGrupos" onSubmit="return ComprobarExt()&& ValidarHoras();">   
				<div class="form-group">
					<label class="col-lg-12"><h3 class="bg-muted p-xs b-r-sm"><i class="fa fa-info-circle"></i> Datos del grupo</h3></label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Nombre</label>
					<div class="col-lg-3">
                    	<input name="NombreGrupo" type="text" required="required" class="form-control" id="Nombre" maxlength="100" value="<?php if(($edit==1)||($sw_error==1)){echo $row['NombreGrupo'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(203))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Dirección</label>
					<div class="col-lg-4">
                    	<input name="Direccion" type="text" class="form-control" id="Direccion" maxlength="100" value="<?php if($edit==1){echo $row['Direccion'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(203))){echo "readonly='readonly'";}?>>
					</div>	
				</div>
				<div class="form-group">
					<label class="col-lg-12"><h3 class="bg-muted p-xs b-r-sm"><i class="fa fa-address-card"></i> Encargados</h3></label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Superintendente</label>
					<div class="col-lg-3">
                    	<select name="SuperGrupo" class="form-control m-b select2" id="SuperGrupo" <?php if(($edit==1)&&(!PermitirFuncion(203))){ echo "disabled='disabled'";}?> required="required">
							<option value="">(Ninguno)</option>
                          <?php while($row_Super=sqlsrv_fetch_array($SQL_Super)){?>
								<option value="<?php echo $row_Super['IDPublicador'];?>" <?php if((isset($row['SuperGrupo']))&&(strcmp($row_Super['IDPublicador'],$row['SuperGrupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Super['Nombre']." ".$row_Super['Apellido']." ".$row_Super['SegundoApellido'];?></option>
						  <?php }?>
						</select>
               	  	</div>
					<label class="col-lg-1 control-label">Auxiliar</label>
					<div class="col-lg-3">
                    	<select name="AuxGrupo" class="form-control m-b select2" id="AuxGrupo" <?php if(($edit==1)&&(!PermitirFuncion(203))){ echo "disabled='disabled'";}?>>
							<option value="">(Ninguno)</option>
                          <?php while($row_Auxiliar=sqlsrv_fetch_array($SQL_Auxiliar)){?>
								<option value="<?php echo $row_Auxiliar['IDPublicador'];?>" <?php if((isset($row['AuxGrupo']))&&(strcmp($row_Auxiliar['IDPublicador'],$row['AuxGrupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Auxiliar['Nombre']." ".$row_Auxiliar['Apellido']." ".$row_Auxiliar['SegundoApellido'];?></option>
						  <?php }?>
						</select>
               	  	</div>
				</div>
				 <br>
				 <?php 			
				   	$EliminaMsg=array("&a=".base64_encode("OK_GrpAdd"),"&a=".base64_encode("OK_GrpUpd"));//Eliminar mensajes
	
					if(isset($_GET['return'])){
						$_GET['return']=str_replace($EliminaMsg,"",base64_decode($_GET['return']));
					}
					if(isset($_GET['return'])){
						$return=base64_decode($_GET['pag'])."?".$_GET['return'];
					}else{
						$return="gestionar_grupos.php?";
					}?>
				   
					<input type="hidden" id="P" name="P" value="<?php if($edit==0){echo "22";}else{echo "23";}?>" />
				  	<input type="hidden" id="swError" name="swError" value="<?php echo $sw_error;?>" />
				    <input type="hidden" id="tl" name="tl" value="<?php echo $edit;?>" />
					<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return);?>" />			
					<input type="hidden" id="IdGrupo" name="IdGrupo" value="<?php if($edit==1){ echo base64_encode($row['IDGrupo']);}?>" />
				</form>
				<div class="form-group">
					<div class="col-lg-9">
						<?php if(($edit==1)&&(PermitirFuncion(201))){?> 
							<button class="btn btn-warning" form="frmGrupos" type="submit" id="Actualizar"><i class="fa fa-refresh"></i> Actualizar grupo</button> 
							<button class="btn btn-danger" form="frmGrupos" type="submit" id="Eliminar" onClick="EnviarFrm('24');"><i class="fa fa-trash"></i> Eliminar</button>
						<?php }?>
						<?php if($edit==0){?> 
							<button class="btn btn-primary" form="frmGrupos" type="submit" id="Crear"><i class="fa fa-check"></i> Crear grupo</button>  
							<?php }?>
						<a href="<?php echo $return;?>" class="alkin btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
					</div>
				</div>
				<br><br>
		   </div>
			</div>
          </div>
	</div>
        <!-- InstanceEndEditable -->
        <?php include("includes/footer.php"); ?>

    </div>
</div>
<?php include("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->
<script>
	 $(document).ready(function(){
		 $("#frmGrupos").validate({
			 submitHandler: function(form){
				 var vP=document.getElementById('P');
				 if(vP.value==24){
					 Swal.fire({
						title: "¿Estás seguro que deseas eliminar este grupo?",
						text: "Se quitará la relación de este grupo con los publicadores asociados. Esta acción no se puede reversar.",
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
		 $(".alkin").on('click', function(){
				 $('.ibox-content').toggleClass('sk-loading');
			});
		 $(".select2").select2();
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