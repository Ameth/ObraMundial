<?php require_once("../includes/conexion.php"); ?>
<!DOCTYPE html>
<html>

<head>
<?php include_once("../includes/cabecera.php"); ?>
<!-- TemplateBeginEditable name="doctitle" -->
<title>PortalCopla | </title>
<!-- TemplateEndEditable -->
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include_once("../includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include_once("../includes/menu_superior.php"); ?>
        <!-- TemplateBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-sm-4">
				<h2>Titulo de la pagina</h2>
				<ol class="breadcrumb">
					<li>
						<a href="index1.php">Inicio</a>
					</li>
					<li class="active">
						<strong>Pagina</strong>
					</li>
				</ol>
			</div>
		</div>
            
        <div class="wrapper wrapper-content">
          <div class="row">
            <div class="col-lg-12">
              <div class="text-center m-t-lg">
                <h1> Bienvenido </h1>
              </div>
            </div>
          </div>
        </div>
        <!-- TemplateEndEditable -->
        <?php include_once("../includes/footer.php"); ?>

    </div>
</div>
<?php include_once("../includes/pie.php"); ?>
<!-- TemplateBeginEditable name="EditRegion4" -->

<!-- TemplateEndEditable -->
</body>

</html>
<?php //sqlsrv_close($conexion);?>