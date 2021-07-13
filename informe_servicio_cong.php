<?php require_once("includes/conexion.php");
PermitirAcceso(402);

$sw=0;

//Filtros
$Filtro="";//Filtro
$Grupo="";
$Periodo="";

if(isset($_GET['Periodo'])&&$_GET['Periodo']!=""){
	$Filtro.=" and IDPeriodo='".$_GET['Periodo']."'";
	$Periodo=$_GET['Periodo'];
	$sw=1;
}

//Periodos
$SQL_Periodos=Seleccionar('uvw_tbl_PeriodosInformes','*',"NumCong='".$_SESSION['NumCong']."'",'CodigoPeriodo');

if($sw==1){
	$SQL=EjecutarSP('usp_InformeServicioCongGrupos',$Periodo);
	$SQL_Total=EjecutarSP('usp_InformeServicioCongTotal',$Periodo);
}

//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Informe de servicio de la congregación | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
	
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
                    <h2>Informe de servicio de la congregación</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Reportes</a>
                        </li>
                        <li class="active">
                            <strong>Informe de servicio de la congregación</strong>
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
				  <form action="informe_servicio_cong.php" method="get" id="formBuscar" class="form-horizontal">
					   <div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
					 	<div class="form-group">
							<label class="col-lg-1 control-label">Periodo</label>
							<div class="col-lg-3">
								<select name="Periodo" class="form-control m-b select2" id="Periodo" required>
									<option value="">Seleccione...</option>
								  <?php while($row_Periodos=sqlsrv_fetch_array($SQL_Periodos)){?>
										<option value="<?php echo $row_Periodos['IDPeriodo'];?>" <?php if((isset($_GET['Periodo']))&&(strcmp($row_Periodos['IDPeriodo'],$_GET['Periodo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Periodos['CodigoPeriodo'];?></option>
								  <?php }?>
								</select>
							</div>
							<div class="col-lg-1">
								<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Buscar</button>
							</div>							
						</div>
					  <input type="hidden" id="MM_Buscas" name="MM_Buscar" value="1">
				 </form>
			</div>
			</div>
		  </div>
         <br>
		<?php if($sw==1){?>
		<h3 class="bg-info p-xss b-r-xs"><i class="fa fa-list"></i> Resumen por grupo</h3>
        <div class="row">
			 <div class="col-lg-12">
			    <div class="ibox-content">		
					<?php include("includes/spinner.php"); ?>
					<div class="row m-b-md">
						<div class="col-lg-12">
							<a href="rpt_informe_servicio_cong.php?id=<?php echo base64_encode($Periodo);?>" target="_blank" class="btn btn-outline btn-danger"><i class="fa fa-file-pdf-o"></i> Descargar en PDF</a>
							<a href="exportar_excel.php?exp=10&Cons=<?php echo base64_encode($Periodo);?>&sp=<?php echo base64_encode('usp_InformeServicioCongGrupos');?>" class="btn btn-outline btn-primary"><i class="fa fa-file-excel-o"></i> Exportar a Excel</a>
						</div>
					</div>
					<div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
						<th>Nombre grupo</th>
						<th>Tipo publicador</th>
						<th>Cuántos informan</th>  
						<th>Publicaciones</th>  
                        <th>Presentaciones de video</th>
                        <th>Horas</th>
						<th>Revisitas</th>
						<th>Cursos biblicos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row=sqlsrv_fetch_array($SQL)){?>
						 <tr class="gradeX tooltip-demo">
							<td><?php echo $row['NombreGrupo'];?></td>
							<td><?php echo $row['TipoPublicador'];?></td>
							<td><?php echo $row['Cantidad'];?></td>						
							<td><?php echo $row['Publicaciones'];?></td>
							<td><?php echo $row['Videos'];?></td>
							<td><?php echo $row['Horas'];?></td>
							<td><?php echo $row['Revisitas'];?></td>
							<td><?php echo $row['Cursos'];?></td>
						</tr>
					<?php }?>
                    </tbody>
                    </table>
             		</div>
				</div>
			 </div> 
          </div>
		<br>
		<h3 class="bg-info p-xss b-r-xs"><i class="fa fa-list"></i> Resumen general de la congregación</h3>
 		<div class="row">
			<div class="col-lg-12">
				<div class="ibox-content">		
					<?php include("includes/spinner.php"); ?>
					<div class="row m-b-md">
						<div class="col-lg-12">
							<a href="rpt_informe_servicio_cong.php?id=<?php echo base64_encode($Periodo);?>" target="_blank" class="btn btn-outline btn-danger"><i class="fa fa-file-pdf-o"></i> Descargar en PDF</a>
							<a href="exportar_excel.php?exp=10&Cons=<?php echo base64_encode($Periodo);?>&sp=<?php echo base64_encode('usp_InformeServicioCongTotal');?>" class="btn btn-outline btn-primary"><i class="fa fa-file-excel-o"></i> Exportar a Excel</a>
						</div>
					</div>
					<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover dataTables-example" >
					<thead>
					<tr>
						<th>Tipo publicador</th>
						<th>Cuántos informan</th>  
						<th>Publicaciones</th>  
						<th>Presentaciones de video</th>
						<th>Horas</th>
						<th>Revisitas</th>
						<th>Cursos biblicos</th>
					</tr>
					</thead>
					<tbody>
					<?php while($row_Total=sqlsrv_fetch_array($SQL_Total)){?>
						 <tr class="gradeX tooltip-demo">
							<td><?php echo $row_Total['TipoPublicador'];?></td>
							<td><?php echo $row_Total['Cantidad'];?></td>						
							<td><?php echo $row_Total['Publicaciones'];?></td>
							<td><?php echo $row_Total['Videos'];?></td>
							<td><?php echo $row_Total['Horas'];?></td>
							<td><?php echo $row_Total['Revisitas'];?></td>
							<td><?php echo $row_Total['Cursos'];?></td>
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