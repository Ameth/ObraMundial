<?php require_once("includes/conexion.php");
PermitirAcceso(404);

$sw=0;

//Filtros
$Filtro="";//Filtro
$Grupo="";
$Periodo="";
if(isset($_GET['Grupo'])&&$_GET['Grupo']!=""){
	$Filtro.=" and IDGrupo='".$_GET['Grupo']."'";
	$Grupo=$_GET['Grupo'];
	$sw=1;
}

if(isset($_GET['Publicador'])&&$_GET['Publicador']!=""){
	$Periodo=$_GET['Publicador'];
	$sw=1;
}

//Grupos de congregacion
if(PermitirFuncion(205)){
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."'",'NombreGrupo');
	
	$SQL_Pub=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."'",'NombrePublicador');
}else{
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'",'NombreGrupo');
}

if($sw==1){
	$SQL_Pub=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_GET['Grupo']."'",'NombrePublicador');
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
		$("#Grupo").change(function(){
			$('.ibox-content').toggleClass('sk-loading',true);
			var Grupo=document.getElementById('Grupo').value;
			$.ajax({
				type: "POST",
				url: "ajx_cbo_select.php?type=2&id="+Grupo,
				success: function(response){
					$('#Publicador').html(response).fadeIn();
					$('#Publicador').trigger('change');
					$('.ibox-content').toggleClass('sk-loading',false);
				}
			});
		});
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
							<label class="col-lg-1 control-label">Grupo</label>
							<div class="col-lg-3">
								<select name="Grupo" class="form-control" id="Grupo">
									<?php if(!PermitirFuncion(205)){?><option value="">(Todos)</option><?php }?>
								  <?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
										<option value="<?php echo $row_Grupos['IDGrupo'];?>" <?php if((isset($_GET['Grupo']))&&(strcmp($row_Grupos['IDGrupo'],$_GET['Grupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Grupos['NombreGrupo'];?></option>
								  <?php }?>
								</select>
							</div>
							<label class="col-lg-1 control-label">Publicador <span class="text-danger">*</span></label>
							<div class="col-lg-3">
								<select name="Publicador" class="form-control select2" id="Publicador" required>
									<option value="">(Seleccione)</option>
								  <?php 
									if($sw==1||PermitirFuncion(205)){
										while($row_Pub=sqlsrv_fetch_array($SQL_Pub)){?>
											<option value="<?php echo $row_Pub['IDPublicador'];?>" <?php if((isset($_GET['Publicador']))&&(strcmp($row_Pub['IDPublicador'],$_GET['Publicador'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Pub['NombrePublicador'];?></option>
								  <?php }
									}?>
								</select>
							</div>
							<div class="col-lg-1">
								<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Consultar</button>
							</div>							
						</div>
					  <input type="hidden" id="MM_Buscas" name="MM_Buscar" value="1">
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
					<div class="form-group">
						<div class="col-lg-6">
							<a href="rpt_informe_registro_publicador.php?id=<?php echo base64_encode($Periodo);?>" target="_blank" class="btn btn-outline btn-danger"><i class="fa fa-file-pdf-o"></i> Descargar en PDF</a>
						</div>
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