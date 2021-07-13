<?php require_once("includes/conexion.php");
PermitirAcceso(101);

if(isset($_GET['id'])&&($_GET['id']!="")){
	$NumCong=base64_decode($_GET['id']);
}
if(isset($_GET['tl'])&&($_GET['tl']!="")){//0 Si se está creando. 1 Se se está editando.
	$edit=$_GET['tl'];
}elseif(isset($_POST['tl'])&&($_POST['tl']!="")){
	$edit=$_POST['tl'];
}else{
	$edit=0;
}

if($edit==0){
	$Title="Crear congregación";
}else{
	$Title="Editar congregación";
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		if($_POST['tl']==1){//Actualizar
			$Type=2;
		}else{//Crear
			$Type=1;
		}
		
		$ParamInsert=array(
			"'".base64_decode($_POST['ID'])."'",
			"'".$_POST['NumCong']."'",
			"'".strtoupper($_POST['NombreCong'])."'",
			"'".strtoupper($_POST['Ciudad'])."'",
			"'".strtoupper($_POST['Municipio'])."'",
			"'".strtoupper($_POST['Pais'])."'",
			"'".strtoupper($_POST['Direccion'])."'",
			"'".$_POST['CorreoJW']."'",
			"'".$_SESSION['CodUser']."'",
			$Type
		);
		$SQL_InsUser=EjecutarSP('usp_tbl_Congregaciones',$ParamInsert,$_POST['P']);
		
		if($SQL_InsUser){
			sqlsrv_close($conexion);
			if($_POST['tl']==0){//Creando Entrega	
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_Cong"));
			}else{//Actualizando Entrega
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_EditCong"));					
			}		
		}else{
			throw new Exception('Ha ocurrido un error al insertar la congregacion');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($edit==1){//Editar
	
	$SQL=Seleccionar('uvw_tbl_Congregaciones','*',"NumCong='".$NumCong."'");
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
<script>
function ValidarUsuario(Cong){
	var spinner=document.getElementById('spinner1');
	spinner.style.visibility='visible';
	$.ajax({
		type: "GET",
		url: "includes/procedimientos.php?type=3&cong="+Cong,
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
                            <a href="gestionar_congregacion.php">Gestionar congregación</a>
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
              <form action="congregacion.php" method="post" class="form-horizontal" id="frmCong">
				  <div class="form-group">
					<label class="col-lg-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-info-circle"></i> Datos de la congregación</h3></label>
				  </div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Número de congregación</label>
					<div class="col-lg-2"><input name="NumCong" type="text" required="required" class="form-control" id="NumCong" value="<?php if($edit==1){echo $row['NumCong'];}?>" onChange="ValidarUsuario(this.value);"></div>
					<label class="col-lg-2 control-label">Nombre de la congregación</label>
					<div class="col-lg-3"><input name="NombreCong" type="text" required="required" class="form-control" id="NombreCong" value="<?php if($edit==1){echo $row['NombreCongregacion'];}?>"></div>
					<div id="Validar" class="col-lg-1">
						<div id="spinner1" style="visibility: hidden;" class="sk-spinner sk-spinner-wave">
							<div class="sk-rect1"></div>
							<div class="sk-rect2"></div>
							<div class="sk-rect3"></div>
							<div class="sk-rect4"></div>
							<div class="sk-rect5"></div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Ciudad</label>
					<div class="col-lg-3"><input name="Ciudad" type="text" required="required" class="form-control" id="Ciudad" value="<?php if($edit==1){echo $row['Ciudad'];}?>"></div>
					<label class="col-lg-1 control-label">Municipio</label>
					<div class="col-lg-3"><input name="Municipio" type="text" class="form-control" id="Municipio" value="<?php if($edit==1){echo $row['Municipio'];}?>"></div>
					<label class="col-lg-1 control-label">Pais</label>
					<div class="col-lg-3"><input name="Pais" type="text" class="form-control" id="Pais" value="<?php if($edit==1){echo $row['Pais'];}?>"></div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Dirección</label>
					<div class="col-lg-3"><input name="Direccion" type="text" required="required" class="form-control" id="Direccion" value="<?php if($edit==1){echo $row['Direccion'];}?>"></div>
					<label class="col-lg-1 control-label">Correo JW</label>
					<div class="col-lg-3"><input name="CorreoJW" type="text" class="form-control" id="CorreoJW" value="<?php if($edit==1){echo $row['CorreoJW'];}?>"></div>
				</div>
				<div class="form-group">
					<div class="col-lg-9">
						<?php if($edit==1){?>
						<button class="btn btn-warning" type="submit" id="Crear"><i class="fa fa-refresh"></i> Actualizar congregación</button> 
						<?php }else{?>
						<button class="btn btn-primary" type="submit" id="Crear"><i class="fa fa-check"></i> Crear congregación</button>
						<?php }?>
						<a href="gestionar_congregacion.php" class="btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
					</div>
				</div>
				<?php 			
				   	$EliminaMsg=array("&a=".base64_encode("OK_Cong"),"&a=".base64_encode("OK_EditCong"));//Eliminar mensajes
	
					if(isset($_GET['return'])){
						$_GET['return']=str_replace($EliminaMsg,"",base64_decode($_GET['return']));
					}
					if(isset($_GET['return'])){
						$return=base64_decode($_GET['pag'])."?".$_GET['return'];
					}else{
						$return="gestionar_congregacion.php?";
					}?>
				  
				<input type="hidden" id="P" name="P" value="<?php if($edit==1){ echo "1";}else{echo "2";}?>" />
				<input type="hidden" id="tl" name="tl" value="<?php echo $edit;?>" />
				<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return);?>" />
				<input type="hidden" id="ID" name="ID" value="<?php if($edit==1){ echo base64_encode($row['ID']);}?>" />
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
		 $("#frmCong").validate({
			 submitHandler: function(form){
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
		});
		 $('.chosen-select').chosen({width: "100%"});
		 $(".select2").select2();
	
	});
</script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>