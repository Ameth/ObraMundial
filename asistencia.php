<?php require_once("includes/conexion.php");
PermitirAcceso(301);

$sw=0;
$IDPeriodo=0;
//Listado de periodos de la congregacion
$SQL_Periodos=Seleccionar('uvw_tbl_PeriodosInformes','*',"NumCong='".$_SESSION['NumCong']."'",'AnioPeriodo DESC, MesPeriodo DESC');

if(isset($_GET['Periodo'])&&$_GET['Periodo']!=""){
	$IDPeriodo=base64_decode($_GET['Periodo']);
	$sw=1;
}

if(isset($_POST['P'])&&($_POST['P']!="")){//Insertar registro	
	try{
		
		$Count=count($_POST['ReunionSemana']);
		$i=0;
		while($i<$Count){
			
			if($_POST['Metodo'][$i]!="0"){
				$ParamInsert=array(
					"'".$_POST['IDAsistencia'][$i]."'",
					"'".base64_decode($_POST['Periodo'])."'",
					"'".$_POST['Semana'][$i]."'",
					"'".$_POST['ReunionSemana'][$i]."'",
					"'".$_POST['ReunionFinSemana'][$i]."'",
					"'".$_SESSION['NumCong']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_Insert=EjecutarSP('usp_tbl_Asistencia',$ParamInsert,$_POST['P']);
				if(!$SQL_Insert){
					throw new Exception('Ha ocurrido un error al insertar la asistencia');
				}
			}	
			
			$i=$i+1;
			
		}

		sqlsrv_close($conexion);
		header('Location:asistencia.php?Periodo='.$_POST['Periodo'].'&a='.base64_encode("OK_AsistAdd"));
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}

}

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Asistencia a las reuniones | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_AsistAdd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'La asistencia ha sido agregada exitosamente.',
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
                    <h2>Asistencia a las reuniones</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Ingresar informes</a>
                        </li>
                        <li class="active">
                            <strong>Asistencia a las reuniones</strong>
                        </li>
                    </ol>
                </div>
            </div>
         <div class="wrapper wrapper-content">
             <div class="row">
				<div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				  <form action="asistencia.php" method="get" id="formBuscar" class="form-horizontal">
						<div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
					 	<div class="form-group">
							<label class="col-lg-1 control-label">Periodo (Mes) <span class="text-danger">*</span></label>
							<div class="col-lg-3">
								<select name="Periodo" class="form-control select2" id="Periodo" required>
									<option value="">Seleccione...</option>
								  <?php while($row_Periodos=sqlsrv_fetch_array($SQL_Periodos)){?>
										<option value="<?php echo base64_encode($row_Periodos['IDPeriodo']);?>" <?php if((isset($_GET['Periodo']))&&(strcmp($row_Periodos['IDPeriodo'],base64_decode($_GET['Periodo']))==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Periodos['NombreMes']."/".$row_Periodos['AnioPeriodo'];?></option>
								  <?php }?>
								</select>
							</div>
							<div class="col-lg-1">
								<button type="submit" form="formBuscar" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Consultar</button>
							</div>							
						</div>
				 </form>
			</div>
			</div>
		  </div>
         <br>
			 <?php //echo $Cons;?>
		<?php if($sw==1){?>
          <div class="row">
			  <div class="col-lg-12">
				  <div class="ibox-content col-lg-12">
					 <?php include("includes/spinner.php"); ?>
					  
					<div class="col-lg-9">

						<form action="asistencia.php" method="post" class="form-horizontal" enctype="multipart/form-data" id="frmAsistencia">

						<?php for($i=1;$i<=5;$i++){
							$SQL=Seleccionar('uvw_tbl_Asistencia','*',"NumCong='".$_SESSION['NumCong']."' and IDPeriodo='".$IDPeriodo."' and Semana='".$i."'");
							$row=sqlsrv_fetch_array($SQL);?>
						<div class="form-group">
							<div class="col-lg-8 border-bottom">
								<label class="control-label text-danger h4">Semana <?php echo $i;?></label>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-3">
								<label class="control-label">Reunión de entre semana</label>
								<input name="ReunionSemana[]" type="text" onKeyPress="return justNumbers(event,this.value);" onChange="CambiarMetodo('<?php echo $i;?>');" required="required" class="form-control" id="ReunionSemana<?php echo $i;?>" maxlength="5" value="<?php echo $row['AsistSemana'];?>" autocomplete="off">
							</div>
							<div class="col-lg-3">
								<label class="control-label">Reunión del fin de semana</label>
								<input name="ReunionFinSemana[]" type="text" onKeyPress="return justNumbers(event,this.value);" onChange="CambiarMetodo('<?php echo $i;?>');" class="form-control" id="ReunionFinSemana<?php echo $i;?>" maxlength="5" value="<?php echo $row['AsistFinSemana'];?>" autocomplete="off">
							</div>
							<input type="hidden" id="Metodo<?php echo $i;?>" name="Metodo[]" value="0" />
							<input type="hidden" id="Semana<?php echo $i;?>" name="Semana[]" value="<?php echo $i;?>" />
							<input type="hidden" id="IDAsistencia<?php echo $i;?>" name="IDAsistencia[]" value="<?php echo $row['IDAsistencia'];?>" />
						</div>
						<?php }?>
						<div class="form-group m-t-lg">
							<div class="col-lg-12">
								<button class="btn btn-primary" form="frmAsistencia" type="submit" id="Ingresar"><i class="fa fa-check"></i> Ingresar asistencia</button>
								<input type="hidden" id="P" name="P" value="302" />
								<input type="hidden" id="Periodo" name="Periodo" value="<?php echo base64_encode($IDPeriodo);?>" />								
							</div>
						</div>
						</form>
					</div>
					<?php 
						$ParamTotal=array(
							"'".$_SESSION['NumCong']."'",
							"'".$IDPeriodo."'"
						);
						$SQL_Total=EjecutarSP('usp_CalcularTotalesAsistencia',$ParamTotal);
						$row_Total=sqlsrv_fetch_array($SQL_Total);
					  ?>
					<div class="col-lg-3">
						<div class="col-lg-12">
							<div class="ibox border-left-right border-top-bottom">
								<div class="ibox-title">
									<h2 class="font-bold">Total entre semana</h2>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><span class="font-bold text-success"><?php echo $row_Total['TotalSemana'];?></span></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="ibox border-left-right border-top-bottom">
								<div class="ibox-title">
									<h2 class="font-bold">Promedio entre semana</h2>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><span class="font-bold text-navy"><?php echo $row_Total['PromSemana'];?></span></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="ibox border-left-right border-top-bottom">
								<div class="ibox-title">
									<h2 class="font-bold">Total fin de semana</h2>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><span class="font-bold text-success"><?php echo $row_Total['TotalFinSemana'];?></span></h1>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="ibox border-left-right border-top-bottom">
								<div class="ibox-title">
									<h2 class="font-bold">Promedio fin de semana</h2>
								</div>
								<div class="ibox-content">
									<h1 class="no-margins"><span class="font-bold text-navy"><?php echo $row_Total['PromFinSemana'];?></span></h1>
								</div>
							</div>
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
			
			$("#frmAsistencia").validate({
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
			
			$(".select2").select2();
			
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

	 function CambiarMetodo(id){
		 document.getElementById("Metodo"+id).value='1';
	 }
</script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>