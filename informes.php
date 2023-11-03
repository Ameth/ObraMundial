<?php
require_once("includes/conexion.php");
PermitirAcceso(301);

if (isset($_GET['id']) && ($_GET['id'] != "")) {
	$IdGrupo = base64_decode($_GET['id']);
	$IdPeriodo = base64_decode($_GET['idped']);
} elseif (isset($_POST['IDGrupo']) && ($_POST['IDGrupo'] != "")) {
	$IdGrupo = base64_decode($_POST['IDGrupo']);
	$IdPeriodo = base64_decode($_POST['IDPeriodo']);
} else {
	header("Location:404.php");
}

//Consultar datos del grupo
$SQL_Grupo = Seleccionar('uvw_tbl_Grupos', '*', "IDGrupo='" . $IdGrupo . "'");
$row_Grupo = sqlsrv_fetch_array($SQL_Grupo);
$NomGrupo = $row_Grupo['NombreGrupo'];

//Datos del Periodo
$SQL_Periodo = Seleccionar('uvw_tbl_PeriodosInformes', '*', "IDPeriodo='" . $IdPeriodo . "'");
$row_Periodo = sqlsrv_fetch_array($SQL_Periodo);

if (isset($_POST['P']) && ($_POST['P'] != "")) { //Insertar registro	
	try {

		$Count = count($_POST['NombrePub']);
		$i = 0;
		while ($i < $Count) {

			//Validar check de si predica
			if (isset($_POST['chkPredica' . $_POST['IdPub'][$i]]) && ($_POST['chkPredica' . $_POST['IdPub'][$i]] == 1)) {
				$chkPredica = 1;
			} else {
				$chkPredica = 0;
			}

			if ($chkPredica === 1 || $_POST['IdInforme'][$i] != "") {

				//Validar check de precursor auxiliar
				if (isset($_POST['chkPrAux' . $_POST['IdPub'][$i]]) && ($_POST['chkPrAux' . $_POST['IdPub'][$i]] == 1)) {
					$chkPrAux = 1;
				} else {
					$chkPrAux = 0;
				}

				//Validar que si no es precursor, no tenga horas ingresadas

				$horas = ($chkPrAux || ($_POST['IdTipoPub'][$i] == 2 || $_POST['IdTipoPub'][$i] == 4)) ? $_POST['Horas'][$i] : 0;

				$ParamInsert = array(
					"'" . $_POST['IdInforme'][$i] . "'",
					"'" . $_POST['IdPub'][$i] . "'",
					"'" . $_POST['NombrePub'][$i] . "'",
					"'" . $_POST['IdTipoPub'][$i] . "'",
					"'" . $_POST['IdPrivServicio'][$i] . "'",
					"'" . $chkPrAux . "'",
					"'" . $chkPredica . "'",
					"'" . $horas . "'",
					"'" . $_POST['Cursos'][$i] . "'",
					"'" . $_POST['Comentarios'][$i] . "'",
					"'" . $IdPeriodo . "'",
					"'" . $IdGrupo . "'",
					"'" . $NomGrupo . "'",
					"'" . $_SESSION['NumCong'] . "'",
					"'" . $_SESSION['CodUser'] . "'"
				);
				$SQL_Insert = EjecutarSP('usp_tbl_Informes', $ParamInsert, $_POST['P']);
				if (!$SQL_Insert) {
					throw new Exception('Ha ocurrido un error al insertar los informes');
				}
			}

			$i = $i + 1;
		}

		sqlsrv_close($conexion);
		header('Location:' . base64_decode($_POST['return']) . '&a=' . base64_encode("OK_InfAdd"));
	} catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
}

//Consultar publicadores del grupo
$SQL = Seleccionar('uvw_tbl_Publicadores', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDGrupo='" . $IdGrupo . "' and IDEstado='1'", 'IDEstado, NombrePublicador');
$Num = sqlsrv_num_rows($SQL);
?>
<!DOCTYPE html>
<html>
<!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<?php include("includes/cabecera.php"); ?>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>Ingresar informes <?php echo $row_Grupo['NombreGrupo']; ?> | <?php echo NOMBRE_PORTAL; ?></title>
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
					<h2><?php echo $row_Grupo['NombreGrupo']; ?></h2>
					<ol class="breadcrumb">
						<li>
							<a href="index1.php">Inicio</a>
						</li>
						<li>
							<a href="#">Ingresar informes</a>
						</li>
						<li>
							<a href="gestionar_informes.php">Informes de predicación</a>
						</li>
						<li class="active">
							<strong><?php echo $row_Grupo['NombreGrupo']; ?></strong>
						</li>
					</ol>
				</div>
			</div>
			<div class="wrapper wrapper-content">
				<div class="ibox-content">
					<?php include("includes/spinner.php"); ?>
					<div class="row">
						<div class="col-lg-12">
							<form action="informes.php" method="post" class="form-horizontal" enctype="multipart/form-data" id="frmInformes">
								<div class="form-group">
									<label class="col-xs-12">
										<h3 class="bg-success p-xs b-r-sm"><i class="fa fa-edit"></i> Ingrese los informes del periodo <?php echo $row_Periodo['CodigoPeriodo'] . " (" . $row_Periodo['NombreMes'] . "/" . $row_Periodo['AnioPeriodo'] . ")"; ?></h3>
									</label>
								</div>
								<div class="table-responsive">
									<table class="table table-striped table-hover dataTables-example">
										<thead>
											<tr>
												<th>Publicador</th>
												<th>Tipo Publicador</th>
												<th>Prec. Auxiliar</th>
												<th>Predicó</th>
												<th>Horas</th>
												<th>Cursos biblicos</th>
												<th>Comentarios</th>
											</tr>
										</thead>
										<tbody>
											<?php while ($row = sqlsrv_fetch_array($SQL)) {
												$SQL_Informes = Seleccionar('uvw_tbl_Informes', '*', "IDGrupo='" . $IdGrupo . "' and IDPeriodo='" . $IdPeriodo . "' and IDPublicador='" . $row['IDPublicador'] . "'");
												$row_Informes = sqlsrv_fetch_array($SQL_Informes);
											?>
												<tr>
													<td <?php if ($row['IDEstado'] == 2) {
															echo "class='text-danger'";
														} ?>><?php echo $row['NombrePublicador']; ?>
														<input name="IdPub[]" type="hidden" id="IdPub<?php echo $row['IDPublicador']; ?>" value="<?php echo $row['IDPublicador']; ?>">
														<input name="NombrePub[]" type="hidden" id="NombrePub<?php echo $row['IDPublicador']; ?>" value="<?php echo $row['NombrePublicador']; ?>">
														<input name="IdInforme[]" type="hidden" id="IdInforme<?php echo $row['IDPublicador']; ?>" value="<?php echo $row_Informes['IDInforme']; ?>">
													</td>
													<td><?php echo $row['TipoPublicadorAbr']; ?>
														<input name="IdTipoPub[]" type="hidden" id="IdTipoPub<?php echo $row['IDPublicador']; ?>" value="<?php echo $row['IDTipoPublicador']; ?>">
														<input name="IdPrivServicio[]" type="hidden" id="IdPrivServicio<?php echo $row['IDPublicador']; ?>" value="<?php echo $row['IDPrivServicio']; ?>">
													</td>
													<td>
														<?php if (($row['IDTipoPublicador'] === 1)) { ?>
															<label class="checkbox-inline i-checks">
																<input data-info="chkPrAux" data-id="<?php echo $row['IDPublicador']; ?>" name="chkPrAux<?php echo $row['IDPublicador']; ?>" type="checkbox" id="chkPrAux<?php echo $row['IDPublicador']; ?>" value="1" <?php if ($row_Informes['PrecAuxiliar'] == 1) {
																																																																			echo "checked='checked'";
																																																																		} ?>>
															</label>
														<?php } ?>
													</td>
													<td>
														<label class="checkbox-inline i-checks">
															<input name="chkPredica<?php echo $row['IDPublicador']; ?>" type="checkbox" id="chkPredica<?php echo $row['IDPublicador']; ?>" value="1" <?php if ($row_Informes['Predica'] == 1) {
																																																			echo "checked='checked'";
																																																		} ?>>
														</label>
													</td>
													<td>
														<input data-typepub="<?php echo $row['IDTipoPublicador']; ?>" name="Horas[]" autocomplete="off" type="text" class="form-control text-right mw-80 txt-resaltado text-13em <?php if (($row_Informes['Horas'] == "") && ($row_Informes['PrecAuxiliar'] == "") && (($row['IDTipoPublicador'] === 1) || ($row['IDTipoPublicador'] === 3))) {
																																																										echo "hidden";
																																																									} ?>" id="Horas<?php echo $row['IDPublicador']; ?>" maxlength="3" onKeyUp="revisaCadena(this);" onKeyPress="return justNumbers(event,this.value);" value="<?php echo $row_Informes['Horas']; ?>">
													</td>
													<td><input name="Cursos[]" data-info="cursos" data-id="<?php echo $row['IDPublicador']; ?>" autocomplete="off" type="text" class="form-control text-right mw-80 txt-resaltado text-13em" id="Cursos<?php echo $row['IDPublicador']; ?>" maxlength="3" onKeyUp="revisaCadena(this);" onKeyPress="return justNumbers(event,this.value);" value="<?php echo $row_Informes['Cursos']; ?>"></td>
													<td><input name="Comentarios[]" autocomplete="off" type="text" class="form-control mw-200 txt-resaltado text-13em" id="Comentarios<?php echo $row['IDPublicador']; ?>" maxlength="50" value="<?php echo $row_Informes['Notas']; ?>"></td>
												</tr>
											<?php } ?>
											<?php if ($Num == 0) { ?>
												<tr class="text-center text-primary">
													<td colspan="9">No hay publicadores.</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
								$EliminaMsg = array("&a=" . base64_encode("OK_InfAdd")); //Eliminar mensajes

								if (isset($_GET['return'])) {
									$_GET['return'] = str_replace($EliminaMsg, "", base64_decode($_GET['return']));
								}
								if (isset($_GET['return'])) {
									$return = base64_decode($_GET['pag']) . "?" . $_GET['return'];
								} else {
									$return = "gestionar_informes.php?";
								} ?>

								<input type="hidden" id="P" name="P" value="301" />
								<input type="hidden" id="return" name="return" value="<?php echo base64_encode($return); ?>" />
								<input type="hidden" id="IDGrupo" name="IDGrupo" value="<?php echo base64_encode($IdGrupo); ?>" />
								<input type="hidden" id="IDPeriodo" name="IDPeriodo" value="<?php echo base64_encode($IdPeriodo); ?>" />
							</form>
							<div class="form-group">
								<div class="col-lg-9">
									<?php if ($Num > 0) { ?>
										<button class="btn btn-primary" form="frmInformes" type="submit" id="Crear"><i class="fa fa-check"></i> Ingresar informes</button>
									<?php } ?>
									<a href="<?php echo $return; ?>" class="alkin btn btn-outline btn-default"><i class="fa fa-arrow-circle-o-left"></i> Regresar</a>
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
		$(document).ready(function() {
			$("#frmInformes").validate({
				submitHandler: function(form) {

					if (verificarRevisitar()) {
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
					} else {
						$('.ibox-content').toggleClass('sk-loading', false);
					}
				}
			});
			$(".alkin").on('click', function() {
				$('.ibox-content').toggleClass('sk-loading');
			});
			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
			});

			$('.dataTables-example').DataTable({
				searching: false,
				info: false,
				paging: false,
				fixedHeader: true,
				ordering: false
			});

			$("[data-info='chkPrAux']").on('ifToggled', function(event) {
				// console.log(event.target.dataset);
				const {
					id
				} = event.target.dataset
				// console.log(`Publicador: ${id}`)
				const horas = document.getElementById(`Horas${id}`)

				const {
					typepub
				} = horas.dataset
				if (typepub == 1 || typepub == 3) {
					horas.classList.toggle("hidden")
				}

			});
		});
	</script>
	<script>
		function verificarRevisitar() {
			const listCursos = document.querySelectorAll("[data-info='cursos']");
			const list = Array.isArray(listCursos) === false ? [...listCursos] : listCursos;
			let alert = true

			list.forEach((curso, index) => {
				const {
					id
				} = curso.dataset
				const horas = document.getElementById(`Horas${id}`)
				const tipoPub = document.getElementById(`IdTipoPub${id}`)	
				const chkPredica = document.getElementById(`chkPredica${id}`).checked
				const chkPrAux = document.getElementById(`chkPrAux${id}`) ? document.getElementById(`chkPrAux${id}`).checked : false

				// console.log('curso', curso.value);
				// console.log('revisita', horas.value);
				// console.log(chkPredica)
				// console.log(chkPrAux)

				//Validar que si tiene cursos biblios, tenga el check de predicación
				if (Number(curso.value) > 0 && (!chkPredica)) {
					// console.log(`Entro con curso en ${curso.value} y horas en ${horas.value}`)
					//Alertar que debe tener horas
					alert = false
					Swal.fire({
						title: "¡Existen estudios sin predicación!",
						text: "Por favor verifique si el publicador predicó o no.",
						icon: "warning",
					});
				}

				//Validar que si tiene cursos biblios y es precursor, tenga horas
				if (Number(curso.value) > 0 && ((Number(horas.value) === 0) || (horas.value === "")) && (chkPrAux)) {
					// console.log(`Entro con curso en ${curso.value} y horas en ${horas.value}`)
					//Alertar que debe tener horas
					alert = false
					Swal.fire({
						title: "¡Existen estudios sin horas!",
						text: "Por favor ingrese las horas que correspondan.",
						icon: "warning",
					});
				}

				//Validar que si es precursor auxiliar, tambien predico
				if ((chkPrAux) && (!chkPredica)) {
					// console.log(`Entro con curso en ${curso.value} y horas en ${horas.value}`)
					//Alertar que debe tener horas
					alert = false
					Swal.fire({
						title: "¡Existen precursores auxiliares sin predicación!",
						text: "Por favor verifique si el publicador predicó o no.",
						icon: "warning",
					});
				}

				//Validar que si es precursor auxiliar, tenga horas
				if (chkPredica && ((Number(horas.value) === 0) || (horas.value === "")) && ((chkPrAux) || (tipoPub.value == 2) || (tipoPub.value == 4))) {
					// console.log(`Entro con curso en ${curso.value} y horas en ${horas.value}`)
					//Alertar que debe tener horas
					alert = false
					Swal.fire({
						title: "¡Existen precursores sin horas!",
						text: "Por favor ingrese las horas que correspondan.",
						icon: "warning",
					});
				}
			})

			return alert
		}
	</script>
	<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd -->

</html>
<?php sqlsrv_close($conexion); ?>