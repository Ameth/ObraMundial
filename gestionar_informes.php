<?php require_once("includes/conexion.php");
PermitirAcceso(301);

$sw=0;

//Periodo mas reciente abierto
$ParamPed=array(
	"'".$_SESSION['NumCong']."'"
);
$SQL_Periodo=EjecutarSP('usp_ConsultarUltimoPeriodoCong',$ParamPed);
$row_Periodo=sqlsrv_fetch_array($SQL_Periodo);

$TotalPub=0;
$TotalPR=0;
$TotalInf=0;
$TotalFalt=0;

if(PermitirFuncion(205)){
	$Param=array(
		"'".$_SESSION['NumCong']."'",
		"'".$row_Periodo['IDPeriodo']."'",
		"'".$_SESSION['Grupo']."'"
	);
}else{
	$Param=array(
		"'".$_SESSION['NumCong']."'",
		"'".$row_Periodo['IDPeriodo']."'"
	);
}

$SQL=EjecutarSP('usp_ConsultarGruposInformes',$Param);

//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Informes de predicación | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_InfAdd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'Los informes han sido agregados exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
?>
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
                    <h2>Informes de predicación</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Ingresar informes</a>
                        </li>
                        <li class="active">
                            <strong>Informes de predicación</strong>
                        </li>
                    </ol>
                </div>
            </div>
         <div class="wrapper wrapper-content">
             <div class="row">
				<div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				  <form action="gestionar_informes.php" method="get" id="formBuscar" class="form-horizontal">
					<?php if($row_Periodo['IDPeriodo']!=""){?>
					  <div class="form-group">
						<div class="col-lg-1 control-label">
							<h2><strong>Periodo: </strong></h2>
						</div>
						<div class="col-lg-3 p-xxs">
							<h2><?php echo $row_Periodo['CodigoPeriodo'];?></h2>
						</div>
					</div>
					<?php }else{?>
					<div class="form-group">
						<div class="col-xs-12 ">
							<h3><div class="alert alert-danger"><i class="fa fa-times-circle"></i> No hay periodos abiertos para ingresar informes.</div></h3>
						</div>
					</div>	  
					<?php }?>
				 </form>
			</div>
			</div>
		  </div>
         <br>
			 <?php //echo $Cons;?>
		<?php if($row_Periodo['IDPeriodo']!=""){?>
          <div class="row">
           <div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				<div class="table-responsive">
                    <table class="table table-striped table table-hover table-responsive" >
                    <thead>
                    <tr>
						<th>Grupo</th>
						<th>Superintendente</th>
						<th>Auxiliar</th>  
						<th>Publicadores</th>  
						<th>Informes ingresados</th>
						<th>Informes faltantes</th>
						<th>Estado</th>
						<th>Completado</th>
						<th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row=sqlsrv_fetch_array($SQL)){
						$TotalPub=$TotalPub+$row['CantPub'];
						//$TotalPR=$TotalPR+$row['CantPR'];
						$TotalFalt=$TotalFalt+$row['CantFalt'];
						$TotalInf=$TotalInf+$row['TotalInf'];
						if($row['CantPub']>0){
							$PrcComp=number_format((($row['TotalInf']/$row['CantPub'])*100),1);
						}else{
							$PrcComp=0;
						}
						?>
						 <tr>
							<td><?php echo $row['NombreGrupo'];?></td>
							<td><?php echo $row['NombreSuperGrupo'];?></td>
							<td><?php if($row['NombreAuxGrupo']!=""){echo $row['NombreAuxGrupo'];}else{echo "(Ninguno)";};?></td>
							<td><?php echo $row['CantPub'];?></td>
							<td><?php echo $row['TotalInf'];?></td>
							<td <?php if($row['CantFalt']>0){echo "class='text-danger'";}?>><?php echo $row['CantFalt'];?></td>
							<td>
								<?php if($row['CantPub']==$row['CantFalt']){?>
									<span class="badge badge-danger">No empezado</span>
								<?php }elseif(($row['CantPub']>$row['CantFalt'])&&($row['CantFalt']>0)){?>
									<span class="badge badge-warning">Pendientes</span>
								<?php }else{?>
									<span class="badge badge-primary">Completado</span>
								<?php }?>
							</td>
							<td>					
								<?php if($row['CantPub']==$row['CantFalt']){?>
									<span data-peity='{"fill": ["#ed5565", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
									<span class="text-danger"><?php echo "0%";?></span>
								<?php }elseif(($row['CantPub']>$row['CantFalt'])&&($row['CantFalt']>0)){?>
									<span data-peity='{"fill": ["#f8ac59", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
									<span class="text-warning"><?php echo $PrcComp."%";?></span>
								<?php }else{?>
									<span data-peity='{"fill": ["#1ab394", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
									<span class="text-navy"><?php echo $PrcComp."%";?></span>
								<?php }?>												
							</td>
							<td><a href="informes.php?id=<?php echo base64_encode($row['IDGrupo']);?>&idped=<?php echo base64_encode($row_Periodo['IDPeriodo']);?>&return=<?php echo base64_encode($_SERVER['QUERY_STRING']);?>&pag=<?php echo base64_encode('gestionar_informes.php');?>&tl=1" class="alkin btn btn-success btn-xs"><i class="fa fa-folder-open"></i> Abrir</a></td>
						</tr>
					<?php }?>
						<tr>
							<td colspan="3" class="text-center font-bold">TOTAL</td>
							<td class="font-bold"><?php echo $TotalPub;?></td>
							<td class="font-bold"><?php echo $TotalInf;?></td>
							<td class="font-bold <?php if($TotalFalt>0){echo "text-danger";}?>"><?php echo $TotalFalt;?></td>
							<td colspan="3">&nbsp;</td>
						</tr>
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
			
			$("span.pie").peity("pie");
			
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