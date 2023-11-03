<?php require_once("includes/conexion.php");
PermitirAcceso(401);

$sw = 0;

//Filtros
$Filtro = ""; //Filtro
$Grupo = "";
$Periodo = "";
if (isset($_GET['Grupo']) && $_GET['Grupo'] != "") {
	$Filtro .= " and IDGrupo='" . $_GET['Grupo'] . "'";
	$Grupo = $_GET['Grupo'];
	$sw = 1;
}

if (isset($_GET['Periodo']) && $_GET['Periodo'] != "") {
	$idPeriodo = explode("-", $_GET['Periodo']);
	$Periodo = $idPeriodo[0];
	$Filtro .= " and IDPeriodo='" . $Periodo . "'";
	$sw = 1;
}

//Periodos
$SQL_Periodos = Seleccionar('uvw_tbl_PeriodosInformes', '*', "NumCong='" . $_SESSION['NumCong'] . "'", 'AnioPeriodo DESC, MesPeriodo DESC');

//Grupos de congregacion
if (PermitirFuncion(205)) {
	$SQL_Grupos = Seleccionar('uvw_tbl_Grupos', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDGrupo='" . $_SESSION['Grupo'] . "'", 'NombreGrupo');
} else {
	$SQL_Grupos = Seleccionar('uvw_tbl_Grupos', '*', "NumCong='" . $_SESSION['NumCong'] . "'", 'NombreGrupo');
}

if ($sw == 1) {
	$Cons = "Select * From uvw_tbl_Informes_Old Where NumCong='" . $_SESSION['NumCong'] . "' $Filtro Order by NombrePublicador";
	$SQL = sqlsrv_query($conexion, $Cons);
}

//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<?php include("includes/cabecera.php"); ?>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>Informe de servicio mensual | <?php echo NOMBRE_PORTAL; ?></title>
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
					<h2>Informe de servicio mensual</h2>
					<ol class="breadcrumb">
						<li>
							<a href="index1.php">Inicio</a>
						</li>
						<li>
							<a href="#">Reportes</a>
						</li>
						<li class="active">
							<strong>Informe de servicio mensual</strong>
						</li>
					</ol>
				</div>
				<?php  //echo $Cons;
				?>
			</div>
			<div class="wrapper wrapper-content">
				<div class="row">
					<div class="col-lg-12">
						<div class="ibox-content">
							<?php include("includes/spinner.php"); ?>
							<form action="informe_servicio_mensual.php" method="get" id="formBuscar" class="form-horizontal">
								<div class="form-group">
									<label class="col-xs-12">
										<h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3>
									</label>
								</div>
								<div class="form-group">
									<label class="col-lg-1 control-label">Periodo <span class="text-danger">*</span></label>
									<div class="col-lg-3">
										<select name="Periodo" class="form-control select2" id="Periodo" required>
											<option value="">Seleccione...</option>
											<?php while ($row_Periodos = sqlsrv_fetch_array($SQL_Periodos)) { ?>
												<option value="<?php echo $row_Periodos['IDPeriodo'] . "-" . $row_Periodos['VersionPeriodo']; ?>" <?php if ((isset($Periodo)) && (strcmp($row_Periodos['IDPeriodo'], $Periodo) == 0)) {
																																					echo "selected=\"selected\"";
																																				} ?>><?php echo $row_Periodos['CodigoPeriodo'] . " (" . $row_Periodos['NombreMes'] . "/" . $row_Periodos['AnioPeriodo'] . ")"; ?></option>
											<?php } ?>
										</select>
									</div>
									<label class="col-lg-1 control-label">Grupo</label>
									<div class="col-lg-3">
										<select name="Grupo" class="form-control" id="Grupo">
											<?php if (!PermitirFuncion(205)) { ?><option value="">(Todos)</option><?php } ?>
											<?php while ($row_Grupos = sqlsrv_fetch_array($SQL_Grupos)) { ?>
												<option value="<?php echo $row_Grupos['IDGrupo']; ?>" <?php if ((isset($_GET['Grupo'])) && (strcmp($row_Grupos['IDGrupo'], $_GET['Grupo']) == 0)) {
																											echo "selected=\"selected\"";
																										} ?>><?php echo $row_Grupos['NombreGrupo']; ?></option>
											<?php } ?>
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
				<?php if ($sw == 1) { ?>
					<div class="row">
						<div class="col-lg-12">
							<div class="ibox-content">
								<?php include("includes/spinner.php"); ?>
								<div class="row m-b-md">
									<div class="col-lg-12">
										<a href="rpt_informe_servicio_mensual_old_version.php?id=<?php echo base64_encode($Filtro); ?>&grp=<?php echo base64_encode($Grupo); ?>&prd=<?php echo base64_encode($Periodo); ?>" target="_blank" class="btn btn-outline btn-danger"><i class="fa fa-file-pdf-o"></i> Descargar en PDF</a>
										<a href="exportar_excel.php?exp=13&Cons=<?php echo base64_encode($Cons); ?>" class="btn btn-outline btn-primary"><i class="fa fa-file-excel-o"></i> Exportar a Excel</a>
									</div>
								</div>
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover dataTables-example">
										<thead>
											<tr>
												<th>Nombre publicador</th>
												<th>Tipo publicador</th>
												<th>Prec. Auxiliar</th>
												<th>Publicaciones</th>
												<th>Presentaciones de video</th>
												<th>Horas</th>
												<th>Revisitas</th>
												<th>Cursos biblicos</th>
												<th>Comentarios</th>
											</tr>
										</thead>
										<tbody>
											<?php while ($row = sqlsrv_fetch_array($SQL)) { ?>
												<tr class="gradeX tooltip-demo">
													<td><?php echo $row['NombrePublicador']; ?></td>
													<td><?php echo $row['TipoPublicadorAbr']; ?></td>
													<td><?php echo $row['DePrecAuxiliar']; ?></td>
													<td><?php echo $row['Publicaciones']; ?></td>
													<td><?php echo $row['Videos']; ?></td>
													<td><?php echo $row['Horas']; ?></td>
													<td><?php echo $row['Revisitas']; ?></td>
													<td><?php echo $row['Cursos']; ?></td>
													<td><?php echo $row['Notas']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<!-- InstanceEndEditable -->
			<?php include("includes/footer.php"); ?>

		</div>
	</div>
	<?php include("includes/pie.php"); ?>
	<!-- InstanceBeginEditable name="EditRegion4" -->
	<script>
		$(document).ready(function() {
			$("#formBuscar").validate({
				submitHandler: function(form) {
					$('.ibox-content').toggleClass('sk-loading');
					//Obtener el valor del periodo
					const {...field} = form.elements
					// console.log(field[0].value)
					const [idPeriodo, verPeriodo] = field[0].value.split("-")
					console.log(verPeriodo)
					if(verPeriodo == 1) {
						form.action = "informe_servicio_mensual_old_version.php"
					}
					// console.log(form.action)					
					form.submit();
				}
			});
			$(".alkin").on('click', function() {
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

			$('.chosen-select').chosen({
				width: "100%"
			});

			$(".select2").select2();

			$('.dataTables-example').DataTable({
				pageLength: 25,
				dom: '<"html5buttons"B>lTfgitp',
				language: {
					"decimal": "",
					"emptyTable": "No se encontraron resultados.",
					"info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
					"infoEmpty": "Mostrando 0 - 0 de 0 registros",
					"infoFiltered": "(filtrando de _MAX_ registros)",
					"infoPostFix": "",
					"thousands": ",",
					"lengthMenu": "Mostrar _MENU_ registros",
					"loadingRecords": "Cargando...",
					"processing": "Procesando...",
					"search": "Filtrar:",
					"zeroRecords": "Ningún registro encontrado",
					"paginate": {
						"first": "Primero",
						"last": "Último",
						"next": "Siguiente",
						"previous": "Anterior"
					},
					"aria": {
						"sortAscending": ": Activar para ordenar la columna ascendente",
						"sortDescending": ": Activar para ordenar la columna descendente"
					}
				},
				buttons: []

			});

		});
	</script>
	<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd -->

</html>
<?php sqlsrv_close($conexion); ?>