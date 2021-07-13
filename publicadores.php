<?php require_once("includes/conexion.php");
PermitirAcceso(202);
$IdPublicador="";
$msg_error="";//Mensaje del error

if(isset($_GET['id'])&&($_GET['id']!="")){
	$IdPublicador=base64_decode($_GET['id']);
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
	$Title="Crear publicador";
}else{
	$Title="Editar publicador";
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		if($_POST['tl']==1){//Actualizar
			$IdPublicador=base64_decode($_POST['IdPublicador']);
			$Type=2;
		}else{//Crear
			$IdPublicador="NULL";
			$Type=1;
		}
		
		if($_POST['FechaNac']!=""){
			$FechaNac="'".$_POST['FechaNac']."'";
		}else{
			$FechaNac="NULL";
		}
		if($_POST['FechaBaut']!=""){
			$FechaBaut="'".$_POST['FechaBaut']."'";
		}else{
			$FechaBaut="NULL";
		}
		
		//Eliminar
		if($_POST['P']==24){
			$IdPublicador=base64_decode($_POST['IdPublicador']);
			$Type=3;
		}
		
		$Parametros=array(
			$IdPublicador,
			"'".strtoupper($_POST['Nombre'])."'",
			"'".strtoupper($_POST['SegundoNombre'])."'",
			"'".strtoupper($_POST['Apellido'])."'",
			"'".strtoupper($_POST['SegundoApellido'])."'",
			"'".$_POST['Genero']."'",
			"'".strtoupper($_POST['Direccion'])."'",
			"'".$_POST['Telefono']."'",
			"'".$_POST['Celular']."'",
			$FechaNac,
			$FechaBaut,
			"'".strtoupper($_POST['PersonaCont'])."'",
			"'".$_POST['TelefonoCont']."'",
			"'".$_POST['Grupo']."'",
			"'".$_POST['TipoPublicador']."'",
			"'".$_POST['PrivServicio']."'",
			"'".$_POST['Estado']."'",
			"'".$_POST['TipoEsperanza']."'",
			"'".$_SESSION['NumCong']."'",
			"'".$_SESSION['CodUser']."'",
			"$Type"
		);
		$SQL_Pub=EjecutarSP('usp_tbl_Publicadores',$Parametros,$_POST['P']);
		if($SQL_Pub){
			sqlsrv_close($conexion);
			if($_POST['tl']==0){//Creando Entrega	
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_PubAdd"));
			}else{//Actualizando Entrega
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_PubUpd"));					
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
	$SQL=Seleccionar('uvw_tbl_Publicadores','*',"IDPublicador='".$IdPublicador."'");
	$row=sqlsrv_fetch_array($SQL);
}

//Genero
$SQL_Genero=Seleccionar('uvw_tbl_Genero','*','','NombreGenero');

//Grupos de congregacion
if(PermitirFuncion(205)){
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."'",'NombreGrupo');
}else{
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'",'NombreGrupo');
}


//Tipo publicador
$SQL_TipoPublicador=Seleccionar('uvw_tbl_TipoPublicador','*','','TipoPublicador');

//Tipo privilegio de servicio
$SQL_PrivServicio=Seleccionar('uvw_tbl_PrivilegioServicio','*','','PrivilegioServicio');

//Estado
$SQL_Estado=Seleccionar('uvw_tbl_Estados','*');

//Tipo esperanza
$SQL_TipoEsp=Seleccionar('uvw_tbl_TipoEsperanza','*');
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
                            <a href="gestionar_publicadores.php">Publicadores</a>
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
			   <form action="publicadores.php" method="post" class="form-horizontal" enctype="multipart/form-data" id="frmPublicadores" >   
				<div class="form-group">
					<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-address-book"></i> Datos del publicador</h3></label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Nombre</label>
					<div class="col-lg-3">
                    	<input name="Nombre" type="text" required="required" class="form-control" id="Nombre" maxlength="100" value="<?php if(($edit==1)||($sw_error==1)){echo $row['Nombre'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Segundo nombre</label>
					<div class="col-lg-3">
                    	<input name="SegundoNombre" type="text" class="form-control" id="SegundoNombre" maxlength="100" value="<?php if(($edit==1)||($sw_error==1)){echo $row['SegundoNombre'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Apellido</label>
					<div class="col-lg-3">
                    	<input name="Apellido" type="text" required="required" class="form-control" id="Apellido" maxlength="100" value="<?php if(($edit==1)||($sw_error==1)){echo $row['Apellido'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Segundo apellido</label>
					<div class="col-lg-3">
                    	<input name="SegundoApellido" type="text" class="form-control" id="SegundoApellido" maxlength="100" value="<?php if(($edit==1)||($sw_error==1)){echo $row['SegundoApellido'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Genero</label>
					<div class="col-lg-3">
                    	<select name="Genero" class="form-control m-b" id="Genero" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
							<option value="">Seleccione...</option>
                          <?php while($row_Genero=sqlsrv_fetch_array($SQL_Genero)){?>
								<option value="<?php echo $row_Genero['IDGenero'];?>" <?php if((isset($row['IDGenero']))&&(strcmp($row_Genero['IDGenero'],$row['IDGenero'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Genero['NombreGenero'];?></option>
						  <?php }?>
						</select>
               	  	</div>
					<label class="col-lg-1 control-label">Fecha de nacimiento</label>
				  	<div class="col-lg-2 input-group date">
                    	 <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="FechaNac" type="text" class="form-control" id="FechaNac" value="<?php if($edit==1){if($row['FechaNac']!=""){echo $row['FechaNac']->format('Y-m-d');}}?>" placeholder="YYYY-MM-DD" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Dirección</label>
					<div class="col-lg-4">
                    	<input name="Direccion" type="text" class="form-control" id="Direccion" maxlength="100" value="<?php if($edit==1){echo $row['Direccion'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){echo "readonly='readonly'";}?>>
					</div>					
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Teléfono</label>
					<div class="col-lg-3">
                    	<input name="Telefono" autocomplete="off" type="text" class="form-control" id="Telefono" maxlength="50" value="<?php if(($edit==1)){echo $row['Telefono'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Celular</label>
					<div class="col-lg-3">
                    	<input name="Celular" autocomplete="off" type="text" class="form-control" id="Celular" maxlength="50" value="<?php if(($edit==1)){echo $row['Celular'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Estado</label>
					<div class="col-lg-2">
                    	<select name="Estado" class="form-control m-b" id="Estado" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
                          <?php while($row_Estado=sqlsrv_fetch_array($SQL_Estado)){?>
								<option value="<?php echo $row_Estado['IDEstado'];?>" <?php if((isset($row['IDEstado']))&&(strcmp($row_Estado['IDEstado'],$row['IDEstado'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Estado['NombreEstado'];?></option>
						  <?php }?>
						</select>
               	  	</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-info-circle"></i> Información espiritual</h3></label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Fecha de bautismo</label>
				  	<div class="col-lg-2 input-group date">
                    	 <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="FechaBaut" type="text" class="form-control" id="FechaBaut" value="<?php if($edit==1){if($row['FechaBaut']!=""){echo $row['FechaBaut']->format('Y-m-d');}}?>" placeholder="YYYY-MM-DD" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Grupo</label>
					<div class="col-lg-3">
                    	<select name="Grupo" class="form-control m-b" id="Grupo" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
							<?php if(!PermitirFuncion(205)){?><option value="">Seleccione...</option><?php }?>
                          <?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
								<option value="<?php echo $row_Grupos['IDGrupo'];?>" <?php if((isset($row['IDGrupo']))&&(strcmp($row_Grupos['IDGrupo'],$row['IDGrupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Grupos['NombreGrupo'];?></option>
						  <?php }?>
						</select>
               	  	</div>
					<label class="col-lg-1 control-label">Tipo publicador</label>
					<div class="col-lg-3">
                    	<select name="TipoPublicador" class="form-control m-b" id="TipoPublicador" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
							<option value="">Seleccione...</option>
                          <?php while($row_TipoPublicador=sqlsrv_fetch_array($SQL_TipoPublicador)){?>
								<option value="<?php echo $row_TipoPublicador['IDTipoPublicador'];?>" <?php if((isset($row['IDTipoPublicador']))&&(strcmp($row_TipoPublicador['IDTipoPublicador'],$row['IDTipoPublicador'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_TipoPublicador['TipoPublicador'];?></option>
						  <?php }?>
						</select>
               	  	</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Anciano / SM</label>
					<div class="col-lg-2">
                    	<select name="PrivServicio" class="form-control m-b" id="PrivServicio" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
                          <?php while($row_PrivServicio=sqlsrv_fetch_array($SQL_PrivServicio)){?>
								<option value="<?php echo $row_PrivServicio['IDPrivServicio'];?>" <?php if((isset($row['IDPrivServicio']))&&(strcmp($row_PrivServicio['IDPrivServicio'],$row['IDPrivServicio'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_PrivServicio['PrivilegioServicio'];?></option>
						  <?php }?>
						</select>
               	  	</div>
					<label class="col-lg-1 control-label">Tipo esperanza</label>
					<div class="col-lg-2">
                    	<select name="TipoEsperanza" class="form-control m-b" id="TipoEsperanza" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "disabled='disabled'";}?> required="required">
							<option value="">Seleccione...</option>
                          <?php while($row_TipoEsp=sqlsrv_fetch_array($SQL_TipoEsp)){?>
								<option value="<?php echo $row_TipoEsp['IDTipoEsperanza'];?>" <?php if((isset($row['IDTipoEsperanza']))&&(strcmp($row_TipoEsp['IDTipoEsperanza'],$row['IDTipoEsperanza'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_TipoEsp['TipoEsperanza'];?></option>
						  <?php }?>
						</select>
               	  	</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-database"></i> Información adicional</h3></label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Persona de contacto</label>
					<div class="col-lg-3">
                    	<input name="PersonaCont" autocomplete="off" type="text" class="form-control" id="PersonaCont" maxlength="50" value="<?php if(($edit==1)){echo $row['PersonaCont'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
					<label class="col-lg-1 control-label">Teléfono contacto</label>
					<div class="col-lg-3">
                    	<input name="TelefonoCont" autocomplete="off" type="text" class="form-control" id="TelefonoCont" maxlength="50" value="<?php if(($edit==1)){echo $row['TelefonoCont'];}?>" <?php if(($edit==1)&&(!PermitirFuncion(201))){ echo "readonly='readonly'";}?>>
               	  	</div>
				</div>
				 <br>
				 <?php 			
				   	$EliminaMsg=array("&a=".base64_encode("OK_PubAdd"),"&a=".base64_encode("OK_PubUpd"));//Eliminar mensajes
	
					if(isset($_GET['return'])){
						$_GET['return']=str_replace($EliminaMsg,"",base64_decode($_GET['return']));
					}
					if(isset($_GET['return'])){
						$return=base64_decode($_GET['pag'])."?".$_GET['return'];
					}else{
						$return="gestionar_publicadores.php?";
					}?>
				   
					<input type="hidden" id="P" name="P" value="<?php if($edit==0){echo "20";}else{echo "21";}?>" />
				  	<input type="hidden" id="swError" name="swError" value="<?php echo $sw_error;?>" />
				    <input type="hidden" id="tl" name="tl" value="<?php echo $edit;?>" />
					<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return);?>" />			
					<input type="hidden" id="IdPublicador" name="IdPublicador" value="<?php if($edit==1){ echo base64_encode($row['IDPublicador']);}?>" />
				</form>
				<div class="form-group">
					<div class="col-lg-9">
						<?php if(($edit==1)&&(PermitirFuncion(201))){?> 
							<button class="btn btn-warning" form="frmPublicadores" type="submit" id="Actualizar"><i class="fa fa-refresh"></i> Actualizar publicador</button>
							<button class="btn btn-danger" form="frmPublicadores" type="submit" id="Eliminar" onClick="EnviarFrm('24');"><i class="fa fa-trash"></i> Eliminar</button>
						<?php }?>
						<?php if(($edit==0)&&(PermitirFuncion(201))){?> 
							<button class="btn btn-primary" form="frmPublicadores" type="submit" id="Crear"><i class="fa fa-check"></i> Crear publicador</button>
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
		 $("#frmPublicadores").validate({
			 submitHandler: function(form){
				 var vP=document.getElementById('P');
				 if(vP.value==24){
					 Swal.fire({
						title: "¿Estás seguro que deseas eliminar este publicador?",
						text: "Esta acción no se puede reversar.",
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
		 $('#FechaNac').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
			 	todayHighlight: true,
				format: 'yyyy-mm-dd'
            });
		 $('#FechaBaut').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
			 	todayHighlight: true,
				format: 'yyyy-mm-dd'
            });
		 $(".select2").select2();
		 $('.i-checks').iCheck({
			 checkboxClass: 'icheckbox_square-green',
             radioClass: 'iradio_square-green',
          });
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