<?php require_once("includes/conexion.php");
PermitirAcceso(404);

$sw=0;

//Filtros
$Filtro="";//Filtro
$Grupo="";
$Periodo="";

//Grupos de congregacion
$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'",'NombreGrupo');

//Tipo publicador
$SQL_TipoPublicador=Seleccionar('uvw_tbl_TipoPublicador','*','','TipoPublicador');

//Tipo privilegio de servicio
$SQL_PrivServicio=Seleccionar('uvw_tbl_PrivilegioServicio','*','','PrivilegioServicio');


if(isset($_GET['Grupo'])&&$_GET['Grupo']!=""){
	$Filtro.=" and IDGrupo='".$_GET['Grupo']."'";
	$Grupo=$_GET['Grupo'];
	$sw=1;
}

if(isset($_GET['TipoPublicador'])&&$_GET['TipoPublicador']!=""){
	$Filtro.=" and IDTipoPublicador='".$_GET['TipoPublicador']."'";
	$sw=1;
}

if(isset($_GET['PrivServicio'])&&$_GET['PrivServicio']!=""){
	$Filtro.=" and IDPrivServicio='".$_GET['PrivServicio']."'";
	$sw=1;
}

//if(isset($_GET['Publicador'])&&$_GET['Publicador']!=""){
//	$Periodo=$_GET['Publicador'];
//	$sw=1;
//}

if(isset($_GET['MM_Buscar'])){
	if(PermitirFuncion(205)){
		$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."' $Filtro",'NombrePublicador');
	}else{
		$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' $Filtro",'NombrePublicador');
	}
	$sw=1;
}

//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Registro de publicador S-21 | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
	
$(document).ready(function() {//Cargar los combos dependiendo de otros

});

function DescargarZIP(){
	Swal.fire({
		title: "Este proceso podría tardar unos minutos",
		text: "¿Desea continuar?",
		icon: "warning",
		showCancelButton: true,
		confirmButtonText: "Si, confirmo",
		cancelButtonText: "No"
	}).then((result) => {
		if (result.isConfirmed) {
			DescargarSAPDownload("filedownload.php", "zip="+btoa('1')+"&file="+btoa("s-21")+"&filtro=<?php echo base64_encode($Filtro);?>", true)
		}
	});
}
	
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
                    <h2>Registro de publicador S-21</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Reportes</a>
                        </li>
                        <li class="active">
                            <strong>Registro de publicador S-21</strong>
                        </li>
                    </ol>
                </div>
               <?php  //echo $Cons;?>
            </div>
         <div class="wrapper wrapper-content">
             <div class="row">
				<div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				  <form action="informe_registro_publicador.php" method="get" id="formBuscar" class="form-horizontal">
					   <div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
					 	<div class="form-group">
							<?php if(!PermitirFuncion(205)){?>
							<label class="col-lg-1 control-label">Grupo</label>
							<div class="col-lg-3">
								<select name="Grupo" class="form-control" id="Grupo">
									<option value="">(Todos)</option>
								  <?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
										<option value="<?php echo $row_Grupos['IDGrupo'];?>" <?php if((isset($_GET['Grupo']))&&(strcmp($row_Grupos['IDGrupo'],$_GET['Grupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Grupos['NombreGrupo'];?></option>
								  <?php }?>
								</select>
							</div>
							<?php }?>
							<label class="col-lg-1 control-label">Tipo publicador</label>
							<div class="col-lg-3">
								<select name="TipoPublicador" class="form-control" id="TipoPublicador">
									<option value="">(Todos)</option>
								  <?php while($row_TipoPublicador=sqlsrv_fetch_array($SQL_TipoPublicador)){?>
										<option value="<?php echo $row_TipoPublicador['IDTipoPublicador'];?>" <?php if((isset($_GET['TipoPublicador']))&&(strcmp($row_TipoPublicador['IDTipoPublicador'],$_GET['TipoPublicador'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_TipoPublicador['TipoPublicador'];?></option>
								  <?php }?>
								</select>
							</div>
							<label class="col-lg-1 control-label">Anciano / SM</label>
							<div class="col-lg-2">
								<select name="PrivServicio" class="form-control" id="PrivServicio">
									<option value="">(Todos)</option>
								  <?php while($row_PrivServicio=sqlsrv_fetch_array($SQL_PrivServicio)){?>
										<option value="<?php echo $row_PrivServicio['IDPrivServicio'];?>" <?php if((isset($_GET['PrivServicio']))&&(strcmp($row_PrivServicio['IDPrivServicio'],$_GET['PrivServicio'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_PrivServicio['PrivilegioServicio'];?></option>
								  <?php }?>
								</select>
							</div>
							<div class="col-lg-1">
								<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Consultar</button>
							</div>							
						</div>
					  <input type="hidden" id="MM_Buscar" name="MM_Buscar" value="1">
				 </form>
			</div>
			</div>
		  </div>
         <br>
		<?php if($sw==1){?>
		<div class="row">
           <div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
					<div class="row m-b-md">
						<div class="col-lg-12">
							<button class="pull-right btn btn-danger btn-outline" id="btnDescargarTodos" name="btnDescargarTodos" type="button" onClick="DescargarZIP();"><i class="fa fa-file-zip-o"></i> Descargar todos</button>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example" >
						<thead>
						<tr>
							<th>Nombre</th>
							<th>Apellidos</th>
							<th>Grupo</th>
							<th>Privilegio</th>
							<th>Anciano/SM</th>
							<th>Acciones</th>
						</tr>
						</thead>
						<tbody>
						<?php while($row=sqlsrv_fetch_array($SQL)){?>
							 <tr class="gradeX tooltip-demo">
								<td><?php echo $row['Nombre']." ".$row['SegundoNombre'];?></td>
								<td><?php echo $row['Apellido']." ".$row['SegundoApellido'];?></td>				
								<td><?php echo $row['NombreGrupo'];?></td>
								<td><?php echo $row['TipoPublicador'];?></td>
								<td><?php echo $row['PrivilegioServicio'];?></td>
								<td><a href="rpt_informe_registro_publicador.php?id=<?php echo base64_encode($row['IDPublicador']);?>" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-download"></i> Descargar tarjeta S-21</a></td>
							</tr>
						<?php }?>
						</tbody>
						</table>
              		</div>
				</div>
			 </div> 
          </div>
		<?php }?>
        </div>
        <!-- InstanceEndEditable -->
        <?php include("includes/footer.php"); ?>

    </div>
</div>
<?php include("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->
 <script>
        $(document).ready(function(){
			$("#formBuscar").validate({
			 submitHandler: function(form){
				 $('.ibox-content').toggleClass('sk-loading');
				 form.submit();
				}
			});
			 $(".alkin").on('click', function(){
					$('.ibox-content').toggleClass('sk-loading');
				});
			 $('#FechaInicial').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
				format: 'yyyy-mm-dd'
            });
			 $('#FechaFinal').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
				format: 'yyyy-mm-dd'
            }); 
			
			$('.chosen-select').chosen({width: "100%"});
			
			$(".select2").select2();
			
            $('.dataTables-example').DataTable({
                pageLength: 25,
                dom: '<"html5buttons"B>lTfgitp',
				language: {
					"decimal":        "",
					"emptyTable":     "No se encontraron resultados.",
					"info":           "Mostrando _START_ - _END_ de _TOTAL_ registros",
					"infoEmpty":      "Mostrando 0 - 0 de 0 registros",
					"infoFiltered":   "(filtrando de _MAX_ registros)",
					"infoPostFix":    "",
					"thousands":      ",",
					"lengthMenu":     "Mostrar _MENU_ registros",
					"loadingRecords": "Cargando...",
					"processing":     "Procesando...",
					"search":         "Filtrar:",
					"zeroRecords":    "Ningún registro encontrado",
					"paginate": {
						"first":      "Primero",
						"last":       "Último",
						"next":       "Siguiente",
						"previous":   "Anterior"
					},
					"aria": {
						"sortAscending":  ": Activar para ordenar la columna ascendente",
						"sortDescending": ": Activar para ordenar la columna descendente"
					}
				},
                buttons: []

            });

        });

    </script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>