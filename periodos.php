<?php require_once("includes/conexion.php");
PermitirAcceso(206);

if (isset($_GET['id']) && ($_GET['id'] != "")) {
	$IdPeriodo = base64_decode($_GET['id']);
}
if (isset($_GET['tl']) && ($_GET['tl'] != "")) { //0 Si se está creando. 1 Se se está editando.
	$edit = $_GET['tl'];
} elseif (isset($_POST['tl']) && ($_POST['tl'] != "")) {
	$edit = $_POST['tl'];
} else {
	$edit = 0;
}

if ($edit == 0) {
	$Title = "Crear periodo";
} else {
	$Title = "Editar periodo";
}

if (isset($_POST['P']) && ($_POST['P'] != "")) { //Insertar registro	
	try {
		if ($_POST['tl'] == 1) { //Actualizar
			$IdPeriodo = base64_decode($_POST['IDPeriodo']);
			$Type = 2;
		} else { //Crear
			$IdPeriodo = "NULL";
			$Type = 1;
		}

		if (PermitirFuncion(101)) {
			$Cong = "'" . $_POST['Cong'] . "'";
		} else {
			$Cong = "'" . $_SESSION['NumCong'] . "'";
		}

		$ParamInsert = array(
			$IdPeriodo,
			"'" . $_POST['CodPeriodo'] . "'",
			"'" . PrimerDiaMes($_POST['Mes'], $_POST['Anio']) . "'",
			"'" . UltimoDiaMes($_POST['Mes'], $_POST['Anio']) . "'",
			"'" . $_POST['Anio'] . "'",
			"'" . $_POST['Mes'] . "'",
			"'" . $_POST['AServicio'] . "'",
			"'" . $_POST['Estado'] . "'",
			$Cong,
			"'" . $_SESSION['CodUser'] . "'",
			"'" . $_SESSION['CodUser'] . "'",
			$Type
		);
		$SQL_Insert = EjecutarSP('usp_tbl_PeriodosInformes', $ParamInsert, $_POST['P']);

		if ($SQL_Insert) {
			sqlsrv_close($conexion);
			if ($_POST['tl'] == 0) { //Creando Entrega	
				header('Location:' . base64_decode($_POST['return']) . '&a=' . base64_encode("OK_Perd"));
			} else { //Actualizando Entrega
				header('Location:' . base64_decode($_POST['return']) . '&a=' . base64_encode("OK_EditPerd"));
			}
		} else {
			throw new Exception('Ha ocurrido un error al insertar la congregacion');
			sqlsrv_close($conexion);
		}
	} catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
}

if ($edit == 1) { //Editar

	$SQL = Seleccionar('uvw_tbl_PeriodosInformes', '*', "IDPeriodo='" . $IdPeriodo . "' and NumCong='" . $_SESSION['NumCong'] . "'");
	$row = sqlsrv_fetch_array($SQL);
}

//Estados
$SQL_Estados = Seleccionar('uvw_tbl_Estados', '*');

if (PermitirFuncion(101)) {
	$SQL_Cong = Seleccionar('uvw_tbl_Congregaciones', '*', '', 'NombreCongregacion');
}
?>
<!DOCTYPE html>
<html>
<!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<?php include_once("includes/cabecera.php"); ?>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title><?php echo $Title; ?> | <?php echo NOMBRE_PORTAL; ?></title>
	<!-- InstanceEndEditable -->
	<!-- InstanceBeginEditable name="head" -->
	<script>
		function ValidarPeriodo(Periodo) {
			<?php if (PermitirFuncion(101)) { ?>
				var Cong = document.getElementById('Cong').value;
			<?php } ?>
			var spinner = document.getElementById('spinner1');
			spinner.style.visibility = 'visible';
			$.ajax({
				type: "GET",
				<?php if (PermitirFuncion(101)) { ?>
					url: "includes/procedimientos.php?type=4&ped=" + Periodo + "&cong=" + Cong,
				<?php } else { ?>
					url: "includes/procedimientos.php?type=4&ped=" + Periodo,
				<?php } ?>
				success: function(response) {
					document.getElementById('Validar').innerHTML = response;
					spinner.style.visibility = 'hidden';
					if (response == "<p class='text-danger'><i class='fa fa-times-circle-o'></i> Ya existe este periodo</p>") {
						document.getElementById('Crear').disabled = true;
					} else {
						document.getElementById('Crear').disabled = false;
					}
				}
			});
		}

		function ValCodPeriodo() {
			var Anio = document.getElementById('Anio');
			var Mes = document.getElementById('Mes');
			var CodPeriodo = document.getElementById('CodPeriodo');
			var AServicio = document.getElementById('AServicio');
			<?php if (PermitirFuncion(101)) { ?>
				var Cong = document.getElementById('Cong');
				if (Anio.value != "" && Mes.value != "" && Cong.value != "") {
				<?php } else { ?>
					if (Anio.value != "" && Mes.value != "") {
					<?php } ?>
					CodPeriodo.value = Anio.value + " - " + Mes.value;
					if (Mes.value >= 9) {
						AServicio.value = parseInt(Anio.value) + 1;
					} else {
						AServicio.value = Anio.value;
					}
					ValidarPeriodo(CodPeriodo.value);
					} else {
						CodPeriodo.value = "";
						document.getElementById('Validar').innerHTML = "";
						document.getElementById('Crear').disabled = false;
					}
				}

				function Mostrar() {
					var x = document.getElementById("Password").getAttribute("type");
					if (x == "password") {
						document.getElementById('Password').setAttribute('type', 'text');
						document.getElementById('VerPass').setAttribute('class', 'glyphicon glyphicon-eye-close');
						document.getElementById('aVerPass').setAttribute('title', 'Ocultar contrase' + String.fromCharCode(241) + 'a');
					} else {
						document.getElementById('Password').setAttribute('type', 'password');
						document.getElementById('VerPass').setAttribute('class', 'glyphicon glyphicon-eye-open');
						document.getElementById('aVerPass').setAttribute('title', 'Mostrar contrase' + String.fromCharCode(241) + 'a');
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
					<h2><?php echo $Title; ?></h2>
					<ol class="breadcrumb">
						<li>
							<a href="index1.php">Inicio</a>
						</li>
						<li>
							<a href="#">Congregación</a>
						</li>
						<li>
							<a href="gestionar_periodos.php">Gestionar periodos</a>
						</li>
						<li class="active">
							<strong><?php echo $Title; ?></strong>
						</li>
					</ol>
				</div>
			</div>

			<div class="wrapper wrapper-content">
				<div class="row">
					<div class="col-lg-12">
						<div class="ibox-content">
							<?php include("includes/spinner.php"); ?>
							<form action="periodos.php" method="post" class="form-horizontal" id="frmPeriodo">
								<div class="form-group">
									<label class="col-xs-12">
										<h3 class="bg-success p-xs b-r-sm"><i class="fa fa-info-circle"></i> Datos del periodo</h3>
									</label>
								</div>
								<div class="form-group">
									<label class="col-lg-1 control-label">Año <span class="text-danger">*</span></label>
									<div class="col-lg-2">
										<select name="Anio" class="form-control" id="Anio" required="required" onChange="ValCodPeriodo();" <?php if ($edit == 1) {
																																				echo "disabled='disabled'";
																																			} ?>>
											<option value="">Seleccione...</option>
											<option value="2019" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2019') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2019</option>
											<option value="2020" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2020') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2020</option>
											<option value="2021" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2021') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2021</option>
											<option value="2022" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2022') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2022</option>
											<option value="2023" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2023') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2023</option>
											<option value="2024" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2024') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2024</option>
											<option value="2025" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2025') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2025</option>
											<option value="2026" <?php if ((isset($row['AnioPeriodo'])) && (strcmp($row['AnioPeriodo'], '2026') == 0)) {
																		echo "selected=\"selected\"";
																	} ?>>2026</option>
										</select>
									</div>
									<label class="col-lg-1 control-label">Mes <span class="text-danger">*</span></label>
									<div class="col-lg-2">
										<select name="Mes" class="form-control" id="Mes" required="required" onChange="ValCodPeriodo();" <?php if ($edit == 1) {
																																				echo "disabled='disabled'";
																																			} ?>>
											<option value="">Seleccione...</option>
											<option value="1" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '1') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>1 - Enero</option>
											<option value="2" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '2') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>2 - Febrero</option>
											<option value="3" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '3') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>3 - Marzo</option>
											<option value="4" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '4') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>4 - Abril</option>
											<option value="5" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '5') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>5 - Mayo</option>
											<option value="6" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '6') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>6 - Junio</option>
											<option value="7" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '7') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>7 - Julio</option>
											<option value="8" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '8') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>8 - Agosto</option>
											<option value="9" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '9') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>9 - Septiembre</option>
											<option value="10" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '10') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>10 - Octubre</option>
											<option value="11" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '11') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>11 - Noviembre</option>
											<option value="12" <?php if ((isset($row['MesPeriodo'])) && (strcmp($row['MesPeriodo'], '12') == 0)) {
																	echo "selected=\"selected\"";
																} ?>>12 - Diciembre</option>
										</select>
									</div>
									<label class="col-lg-1 control-label">Código del periodo <span class="text-danger">*</span></label>
									<div class="col-lg-2"><input name="CodPeriodo" type="text" required="required" readonly class="form-control" id="CodPeriodo" value="<?php if ($edit == 1) {
																																											echo $row['CodigoPeriodo'];
																																										} ?>"></div>
									<div id="Validar" class="col-lg-2"></div>
									<div class="col-lg-1">
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
									<label class="col-lg-1 control-label">Año de servicio <span class="text-danger">*</span></label>
									<div class="col-lg-2"><input name="AServicio" type="text" required="required" readonly class="form-control" id="AServicio" value="<?php if ($edit == 1) {
																																											echo $row['AnioServicio'];
																																										} ?>"></div>
									<?php if (PermitirFuncion(101)) { ?>
										<label class="col-lg-1 control-label">Congregación <span class="text-danger">*</span></label>
										<div class="col-lg-2">
											<select name="Cong" class="form-control select2" required id="Cong" onChange="ValCodPeriodo();">
												<option value="">(Seleccione)</option>
												<?php while ($row_Cong = sqlsrv_fetch_array($SQL_Cong)) { ?>
													<option value="<?php echo $row_Cong['NumCong']; ?>" <?php if (($edit == 1) && (isset($row['NumCong'])) && (strcmp($row_Cong['NumCong'], $row['NumCong']) == 0)) {
																											echo "selected=\"selected\"";
																										} ?>><?php echo $row_Cong['NombreCongregacion'] . ", " . $row_Cong['Ciudad'] . " (" . $row_Cong['NumCong'] . ")"; ?></option>
												<?php } ?>
											</select>
										</div>
									<?php } ?>
									<label class="col-lg-1 control-label">Estado <span class="text-danger">*</span></label>
									<div class="col-lg-2">
										<select name="Estado" class="form-control" id="Estado">
											<?php while ($row_Estado = sqlsrv_fetch_array($SQL_Estados)) { ?>
												<option value="<?php echo $row_Estado['IDEstado']; ?>" <?php if (($edit == 1) && (strcmp($row_Estado['IDEstado'], $row['IDEstado']) == 0)) {
																											echo "selected=\"selected\"";
																										} ?>><?php echo $row_Estado['NombreEstado']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-9">
										<?php if ($edit == 1) { ?>
											<button class="btn btn-warning" type="submit" id="Crear"><i class="fa fa-refresh"></i> Actualizar periodo</button>
										<?php } else { ?>
											<button class="btn btn-primary" type="submit" id="Crear"><i class="fa fa-check"></i> Crear periodo</button>
										<?php } ?>
										<a href="gestionar_periodos.php" class="btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
									</div>
								</div>
								<?php
								if (isset($_GET['return'])) {
									$return = base64_decode($_GET['pag']) . "?" . base64_decode($_GET['return']);
								} else {
									$return = "gestionar_periodos.php?";
								}
								$return = QuitarParametrosURL($return, array("a")); ?>

								<input type="hidden" id="IDPeriodo" name="IDPeriodo" value="<?php if ($edit == 1) {
																								echo base64_encode($row['IDPeriodo']);
																							} ?>" />
								<input type="hidden" id="P" name="P" value="<?php if ($edit == 1) {
																				echo "6";
																			} else {
																				echo "7";
																			} ?>" />
								<input type="hidden" id="tl" name="tl" value="<?php echo $edit; ?>" />
								<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return); ?>" />
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
		$(document).ready(function() {
			$("#frmPeriodo").validate({
				submitHandler: function(form) {
					Swal.fire({
						title: "¿Está seguro que desea guardar los datos?",
						icon: "question",
						showCancelButton: true,
						confirmButtonText: "Si, confirmo",
						cancelButtonText: "No"
					}).then((result) => {
						if (result.isConfirmed) {
							$('.ibox-content').toggleClass('sk-loading', true);
							form.submit();
						}
					});
				}
			});
			$('.chosen-select').chosen({
				width: "100%"
			});
			$(".select2").select2();

			$('.input-daterange').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				format: 'yyyy-mm-dd'
			});

		});
	</script>
	<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd -->

</html>
<?php sqlsrv_close($conexion); ?>