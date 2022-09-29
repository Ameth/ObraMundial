<?php require_once("includes/conexion.php");
PermitirAcceso(206);
$sw = 0; //Verificar que hay datos
$And = 0; //Agregar mas filtros a la busqueda
$Filtro = "";

//Años periodo
$SQL_AnioPeriodo = Seleccionar('uvw_tbl_PeriodosInformes', 'DISTINCT AnioPeriodo', "NumCong='" . $_SESSION['NumCong'] . "'", 'AnioPeriodo');

//Filtros
if (isset($_GET['AnioPeriodo']) && $_GET['AnioPeriodo'] != "") {
	$Filtro .= " and AnioPeriodo='" . $_GET['AnioPeriodo'] . "'";
	$sw = 1;
}

if (isset($_GET['BuscarDato']) && $_GET['BuscarDato'] != "") {
	$Filtro .= " and (CodigoPeriodo LIKE '%" . $_GET['BuscarDato'] . "%')";
	$sw = 1;
}

$Cons = "Select * From uvw_tbl_PeriodosInformes Where NumCong='" . $_SESSION['NumCong'] . "' $Filtro Order by FechaInicioPeriodo DESC";
$SQL = sqlsrv_query($conexion, $Cons);

?>
<!DOCTYPE html>
<html>
<!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<?php include_once("includes/cabecera.php"); ?>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>Gestionar periodos | <?php echo NOMBRE_PORTAL; ?></title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<?php
	if (isset($_GET['a']) && ($_GET['a'] == base64_encode("OK_Perd"))) {
		echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El periodo ha sido creado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
	}
	if (isset($_GET['a']) && ($_GET['a'] == base64_encode("OK_EditPerd"))) {
		echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El periodo ha sido editado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
	}
	?>

	<script>
		function Activar_Inactivar(ID) {
			$.ajax({
				type: "GET",
				url: "includes/procedimientos.php?type=5&ID_Periodo=" + ID,
				success: function(response) {
					if (response == 1) { //Lo activo
						document.getElementById('LinkActive' + ID).setAttribute('title', 'Inactivar');
						document.getElementById('LinkActive' + ID).setAttribute('class', 'btn btn-danger btn-xs');
						document.getElementById('LinkActive' + ID).innerHTML = "<i class='fa fa-times-circle'></i> Inactivar";
						document.getElementById('rowAct' + ID).innerHTML = "<span class='badge badge-primary'>Activo</span>";
					}
					if (response == 2) { //Lo desactivo
						document.getElementById('LinkActive' + ID).setAttribute('title', 'Activar');
						document.getElementById('LinkActive' + ID).setAttribute('class', 'btn btn-primary btn-xs');
						document.getElementById('LinkActive' + ID).innerHTML = "<i class='fa fa-check-circle'></i> Activar";
						document.getElementById('rowAct' + ID).innerHTML = "<span class='badge badge-danger'>Inactivo</span>";
					}
				}
			});
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
					<h2>Gestionar periodos</h2>
					<ol class="breadcrumb">
						<li>
							<a href="index1.php">Inicio</a>
						</li>
						<li>
							<a href="#">Congregación</a>
						</li>
						<li class="active">
							<strong>Gestionar periodos</strong>
						</li>
					</ol>
				</div>
				<div class="col-sm-4">
					<div class="title-action">
						<a href="periodos.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Agregar periodo</a>
					</div>
				</div>
			</div>
			<div class="wrapper wrapper-content">
				<div class="row">
					<div class="col-lg-12">
						<div class="ibox-content">
							<?php include("includes/spinner.php"); ?>
							<form action="gestionar_periodos.php" method="get" id="formBuscar" class="form-horizontal">
								<div class="form-group">
									<label class="col-xs-12">
										<h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3>
									</label>
								</div>
								<div class="form-group">
									<label class="col-lg-1 control-label">Año</label>
									<div class="col-lg-3">
										<select name="AnioPeriodo" class="form-control" id="AnioPeriodo">
											<option value="">(Todos)</option>
											<?php while ($row_AnioPeriodo = sqlsrv_fetch_array($SQL_AnioPeriodo)) { ?>
												<option value="<?php echo $row_AnioPeriodo['AnioPeriodo']; ?>" <?php if ((isset($_GET['AnioPeriodo'])) && (strcmp($row_AnioPeriodo['AnioPeriodo'], $_GET['AnioPeriodo']) == 0)) {
																													echo "selected=\"selected\"";
																												} ?>><?php echo $row_AnioPeriodo['AnioPeriodo']; ?></option>
											<?php } ?>
										</select>
									</div>
									<label class="col-lg-1 control-label">Buscar dato</label>
									<div class="col-lg-3">
										<input name="BuscarDato" type="text" class="form-control" id="BuscarDato" maxlength="100" value="<?php if (isset($_GET['BuscarDato']) && ($_GET['BuscarDato'] != "")) {
																																				echo $_GET['BuscarDato'];
																																			} ?>">
									</div>
									<div class="col-lg-2">
										<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-lg-12">
						<div class="ibox-content">
							<?php include("includes/spinner.php"); ?>
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example">
									<thead>
										<tr>
											<th>Fecha inicial</th>
											<th>Fecha final</th>
											<th>Código de periodo</th>
											<th>Año</th>
											<th>Mes</th>
											<th>Año de servicio</th>
											<th>Estado</th>
											<th>Usuario creación</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
										<?php while ($row = sqlsrv_fetch_array($SQL)) { ?>
											<tr>
												<td><?php echo $row['FechaInicioPeriodo']->format('Y-m-d'); ?></td>
												<td><?php echo $row['FechaFinalPeriodo']->format('Y-m-d'); ?></td>
												<td><?php echo $row['CodigoPeriodo']; ?></td>
												<td><?php echo $row['AnioPeriodo']; ?></td>
												<td><?php echo $row['MesPeriodo'] . " - " . $row['NombreMes']; ?></td>
												<td><?php echo $row['AnioServicio']; ?></td>
												<td id="rowAct<?php echo $row['IDPeriodo']; ?>"><?php if ($row['IDEstado'] == 1) { ?><span class="badge badge-primary"><?php echo $row['NombreEstado']; ?></span><?php } else { ?><span class="badge badge-danger"><?php echo $row['NombreEstado']; ?></span><?php } ?></td>
												<td><?php echo $row['NombreUsuario']; ?></td>
												<td>
													<a href="periodos.php?id=<?php echo base64_encode($row['IDPeriodo']); ?>&tl=1" class="alkin btn btn-success btn-xs" title="Editar"><i class="fa fa-edit"></i> Editar</a>
													<?php if ($row['IDEstado'] == 1) { ?>
														<a href="#" id="LinkActive<?php echo $row['IDPeriodo']; ?>" onClick="Activar_Inactivar(<?php echo $row['IDPeriodo']; ?>);" class="btn btn-danger btn-xs" title="Inactivar"><i class="fa fa-times-circle"></i> Inactivar</a>
													<?php } else { ?>
														<a href="#" id="LinkActive<?php echo $row['IDPeriodo']; ?>" onClick="Activar_Inactivar(<?php echo $row['IDPeriodo']; ?>);" class="btn btn-primary btn-xs" title="Activar"><i class="fa fa-check-circle"></i> Activar</a>
													<?php } ?>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
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
		$(document).ready(function() {
			$("#formBuscar").validate({
				submitHandler: function(form) {
					$('.ibox-content').toggleClass('sk-loading');
					form.submit();
				}
			});
			$(".alkin").on('click', function() {
				$('.ibox-content').toggleClass('sk-loading');
			});
			$(".select2").select2();
			$('.dataTables-example').DataTable({
				pageLength: 25,
				order: [
					[0, "desc"]
				],
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