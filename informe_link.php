<?php require_once("includes/conexion.php");


$SQL = Seleccionar('uvw_tbl_Congregaciones', 'Token', "NumCong='" . $_SESSION['NumCong'] . "'");
$row = sqlsrv_fetch_array($SQL);

$cong64=base64_encode($row['Token']);

$link = "https://obramundial.net/inf.php?c=".$cong64;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
	<?php include("includes/cabecera.php"); ?>
	<!-- InstanceBeginEditable name="doctitle" -->
	<title>Link para ingresar informes | <?php echo NOMBRE_PORTAL; ?></title>
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
					<h2>Link para ingresar informes</h2>
					<ol class="breadcrumb">
						<li>
							<a href="index1.php">Inicio</a>
						</li>
						<li>
							<a href="#">Administración</a>
						</li>
						<li class="active">
							<strong>Link para ingresar informes</strong>
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
							<form action="" method="get" id="formBuscar" class="form-horizontal">
								<div class="form-group">
									<label class="col-xs-12">
										<h3 class="bg-success p-xs b-r-sm"><i class="fa fa-link"></i> Enlace</h3>
									</label>
								</div>
								<div class="form-group">
									<label class="col-lg-1 control-label">Enlace</label>
									<div class="col-lg-4">
										<input type="text" class="form-control" disabled name="link" id="link" value="<?php echo $link; ?>" />
									</div>
									<div class="col-lg-1">
										<button type="button" class="btn btn-outline btn-success pull-right" id="btnCopy"><i class="fa fa-clipboard"></i> Copiar</button>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-6">
										<h4 class="text-danger">Envie este enlace a los publicadores de su congregación para que ingresen sus informes.</h4>
									</div>
								</div>
								<input type="hidden" id="MM_Buscas" name="MM_Buscar" value="1">
							</form>
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
			
			const btnCopy = document.getElementById("btnCopy")
			btnCopy.addEventListener("click", () => {
				const text = document.getElementById("link")
				navigator.clipboard.writeText(text.value)
				.then(() => {
					// console.log('Texto copiado al portapapeles: ' + text.value)
					toastr.success('Enlace copiado al portapapeles');
				})
				.catch(err => {
					console.error('Error al copiar al portapapeles:', err)
				})
			})
			
			
			$(".alkin").on('click', function() {
				$('.ibox-content').toggleClass('sk-loading');
			});

		});
	</script>
	<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd -->

</html>
<?php sqlsrv_close($conexion); ?>