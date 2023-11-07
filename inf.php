<?php
$swCong = 0;
$swPeriodo = 0;
$msg = "";

if (isset($_REQUEST['c']) && ($_REQUEST['c'] != "")) {
    try {
        require_once("includes/conect_srv.php");
        include_once("includes/funciones.php");
        include_once("includes/LSiqml.php");
        include_once("includes/definicion.php");

        $Token = base64_decode($_REQUEST['c']);

        $SQL = Seleccionar('uvw_tbl_Congregaciones', 'NumCong', "Token='" . $Token . "'");
        $row = sqlsrv_fetch_array($SQL);

        if (isset($row['NumCong']) && $row['NumCong'] !== "") {
            $NumCong = $row['NumCong'];
            $swCong = 1;

            //Periodo mas reciente abierto
            $ParamPed = array(
                "'" . $NumCong . "'"
            );
            $SQL_Periodo = EjecutarSP('usp_ConsultarUltimoPeriodoCong', $ParamPed);
            $row_Periodo = sqlsrv_fetch_array($SQL_Periodo);

            $swPeriodo = isset($row_Periodo['IDPeriodo']) ? 1 : 0;

            //Grupos de congregacion
            $SQL_Grupos = Seleccionar('uvw_tbl_Grupos', 'IDGrupo, NombreGrupo', "NumCong='" . $NumCong . "'", 'NombreGrupo');

            //Datos congregación
            $SQL_Cong = Seleccionar('uvw_tbl_Congregaciones', 'NombreCongregacion', "NumCong='" . $NumCong . "'");
            $row_Cong = sqlsrv_fetch_array($SQL_Cong);
        }
    } catch (Exception $e) {
        echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
    }
}

// $root = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];

if (isset($_POST['P']) && ($_POST['P'] != "")) { //Insertar registro
    try {

        $SQL_Grupo = Seleccionar('uvw_tbl_Grupos', '*', "IDGrupo='" . $Grupo . "' and NumCong='" . $NumCong . "'");
        $row_Grupo = sqlsrv_fetch_array($SQL_Grupo);
        $NomGrupo = $row_Grupo['NombreGrupo'];

        $horas = ($_POST['PrAux'] == 1 || ($_POST['IdTipoPub'] == 2 || $_POST['IdTipoPub'] == 4)) ? $_POST['Horas'] : 0;

        $ParamInsert = array(
            "''",
            "'" . $_POST['Publicador'] . "'",
            "'" . $_POST['NombrePub'] . "'",
            "'" . $_POST['IdTipoPub'] . "'",
            "'" . $_POST['IdPrivServicio'] . "'",
            "'" . $_POST['PrAux'] . "'",
            "'" . $_POST['Predica'] . "'",
            "'" . $horas . "'",
            "'" . $_POST['Cursos'] . "'",
            "'" . $_POST['Comentarios'] . "'",
            "'" . $row_Periodo['IDPeriodo'] . "'",
            "'" . $_POST['Grupo'] . "'",
            "'" . $_POST['NombreGrupo'] . "'",
            "'" . $NumCong . "'",
            "'0'"
        );
        $SQL_Insert = EjecutarSP('usp_tbl_Informes', $ParamInsert, $_POST['P']);
        if (!$SQL_Insert) {
            throw new Exception('Ha ocurrido un error al insertar los informes');
        }

        sqlsrv_close($conexion);
        header('Location:inf.php?c=' . base64_encode($Token) . '&a=' . base64_encode("OK_InfAdd"));
    } catch (Exception $e) {
        echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
    }
}

$msg = $swPeriodo == 0 ? "No hay datos para ingresar informes" : "";
$msg = $swCong == 0 ? "Link inválido. Por favor verifique." : "";

?>
<!DOCTYPE html>
<html>

<head>
    <?php include("includes/cabecera.php"); ?>

    <title>Registro de informe</title>
    <?php
    if (isset($_GET['a']) && ($_GET['a'] == base64_encode("OK_InfAdd"))) {
        echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Muchas gracias!',
                text: 'Su informe fue guardado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function() { //Cargar los combos dependiendo de otros
            $("#Grupo").change(function() {
                $('.ibox-content').toggleClass('sk-loading', true);
                const grupo = document.getElementById('Grupo').value;
                $.ajax({
                    type: "POST",
                    url: "ajx_cbo_select_ext.php?type=3&id=" + grupo + "&c=<?php echo $NumCong; ?>",
                    success: function(response) {
                        $('#Publicador').html(response).fadeIn();
                        $('#Publicador').trigger('change');
                        $('.ibox-content').toggleClass('sk-loading', false);
                    }
                });
                $.ajax({
                    url: "ajx_buscar_datos_json_ext.php",
                    data: {
                        type: 2,
                        id: grupo,
                        c: '<?php echo $NumCong; ?>'
                    },
                    dataType: 'json',
                    success: function(data) {
                        document.getElementById('NombreGrupo').value = data.NombreGrupo;
                        $('.ibox-content').toggleClass('sk-loading', false);
                    }
                });
            });

            $("#Publicador").change(function() {
                $('.ibox-content').toggleClass('sk-loading', true);
                const publicador = document.getElementById('Publicador').value;
                $.ajax({
                    url: "ajx_buscar_datos_json_ext.php",
                    data: {
                        type: 1,
                        id: publicador,
                        c: '<?php echo $NumCong; ?>'
                    },
                    dataType: 'json',
                    success: function(data) {
                        document.getElementById('NombrePub').value = data.NombrePublicador;
                        document.getElementById('IdTipoPub').value = data.IDTipoPublicador;
                        document.getElementById('IdPrivServicio').value = data.IDPrivServicio;
                        $('.ibox-content').toggleClass('sk-loading', false);
                    }
                });
            });
        });
    </script>
</head>

<body>

    <div id="wrapper">
        <div id="" class="gray-bg">
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-xs-12 text-center">
                    <div class="d-flex justify-content-center align-items-center pb-2 mb-4">
                        <img src="img/logo_150x150.png" alt="Obra Mundial" />
                    </div>
                    <h2 class="font-bold">Registre su informe</h2>
                    <?php if ($swCong == 1 && $swPeriodo == 1) { ?>
                        <h2 class="text-primary">Congregación: <?php echo $row_Cong['NombreCongregacion']; ?></h2>
                        <h2 class="text-success">Mes: <?php echo $row_Periodo['NombreMes'] . "/" . $row_Periodo['AnioPeriodo']; ?></h2>
                    <?php } else { ?>
                        <h2 class="text-danger"><?php echo $msg; ?></h2>
                    <?php } ?>
                </div>
            </div>
            <?php if ($swCong == 1 && $swPeriodo == 1) { ?>
                <div class="wrapper wrapper-content">
                    <div class="ibox-content">
                        <?php include("includes/spinner.php"); ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="inf.php" method="post" class="form" enctype="multipart/form-data" id="frmInforme">
                                    <div class="form-group">
                                        <label>Grupo de predicación</label>
                                        <select name="Grupo" class="form-control" id="Grupo" required="required">
                                            <option value="">Seleccione...</option>
                                            <?php while ($row_Grupos = sqlsrv_fetch_array($SQL_Grupos)) { ?>
                                                <option value="<?php echo $row_Grupos['IDGrupo']; ?>"><?php echo $row_Grupos['NombreGrupo']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <select name="Publicador" class="form-control select2" id="Publicador" required="required">
                                            <option value="">Seleccione...</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>¿Partició en alguna faceta de la predicación este mes?</label>
                                        <select name="Predica" class="form-control" id="Predica" required="required">
                                            <option value="">Seleccione...</option>
                                            <option value="1">SI</option>
                                            <option value="0">NO</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>¿Fue precursor auxiliar este mes?</label>
                                        <select name="PrAux" class="form-control" id="PrAux" required="required">
                                            <option value="0">NO</option>
                                            <option value="1">SI</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Número de <span class="font-italic">diferentes</span> cursos bíblicos dirigidos</label>
                                        <input name="Cursos" type="number" maxlength="2" class="form-control" id="Cursos" value="" onKeyUp="revisaCadena(this);" onKeyPress="return justNumbers(event,this.value);">
                                    </div>
                                    <div class="form-group">
                                        <label>Horas (solo para precursores auxiliares y regulares)</label>
                                        <input name="Horas" type="number" maxlength="2" class="form-control" id="Horas" value="" onKeyUp="revisaCadena(this);" onKeyPress="return justNumbers(event,this.value);">
                                    </div>
                                    <div class="form-group">
                                        <label>Comentarios</label>
                                        <textarea name="Comentarios" rows="3" class="form-control" id="Comentarios"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input type="hidden" id="P" name="P" value="301" />
                                            <input type="hidden" id="c" name="c" value="<?php echo base64_encode($Token); ?>" />
                                            <input type="hidden" id="NombrePub" name="NombrePub" value="" />
                                            <input type="hidden" id="IdTipoPub" name="IdTipoPub" value="" />
                                            <input type="hidden" id="IdPrivServicio" name="IdPrivServicio" value="" />
                                            <input type="hidden" id="NombreGrupo" name="NombreGrupo" value="" />
                                            <button class="btn btn-success btn-block btn-lg" form="frmInforme" type="submit" id="Crear"><i class="fa fa-user-circle" aria-hidden="true"></i> Registrar mi informe</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
        </div>
    </div>
    <?php include("includes/pie.php"); ?>
    <script>
        $(document).ready(function() {
            $("#frmInforme").validate({
                submitHandler: function(form) {
                    if (validarDatos()) {
                        Swal.fire({
                            title: "¿Está seguro que desea enviar su informe?",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonText: "Si, enviar",
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

            $(".select2").select2();
        });
    </script>
    <script>
        function validarDatos() {
            let result = true;

            const prAux = document.getElementById("PrAux")
            const horas = document.getElementById("Horas")
            const tipoPub = document.getElementById("IdTipoPub")

            if (((prAux.value == 1) || (tipoPub.value == 2) || (tipoPub.value == 4)) && ((Number(horas.value) === 0) || (horas.value === ""))) {
                // console.log(`Entro con curso en ${curso.value} y horas en ${horas.value}`)
                //Alertar que debe tener horas
                result = false
                Swal.fire({
                    title: "¡Usted es precursor!",
                    text: "Por favor ingrese las horas que correspondan.",
                    icon: "warning",
                });
            }

            return result;
        }
    </script>
</body>

<!-- InstanceEnd -->

</html>