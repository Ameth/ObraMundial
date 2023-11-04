<?php 
require_once("includes/conexion.php");
/*
$Cons_Menu="Select * From uvw_tbl_Categorias Where ID_Padre=0 and EstadoCategoria=1 and ID_Permiso IN (Select ID_Permiso From uvw_tbl_PermisosPerfiles Where ID_PerfilUsuario='".$_SESSION['Perfil']."')";
$SQL_Menu=sqlsrv_query($conexion,$Cons_Menu,array(),array( "Scrollable" => 'Buffered' ));
$Num_Menu=sqlsrv_num_rows($SQL_Menu);
*/
?>
      <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
	                    <img src="img/logo_150X150.png" alt=""/>
	                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
							<span class="clear">
								<br>
								<span class="block m-t-xs"><strong class="font-bold"><?php echo $_SESSION['NomUser'];?></strong></span> 
								<span class="text-muted text-xs block"><?php echo $_SESSION['NomPerfil'];?></span>
							</span>
						</a>
	                </div>
                    <div class="logo-element">
                    	<img src="img/logo_30X30.png" class="img-circle" alt="" width="30" height="30"/> 
                    </div>
                </li>
                <li class="active">
                    <a class="alnk" href="index1.php"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
                </li>
		   		<?php if(PermitirFuncion(201)||PermitirFuncion(202)||PermitirFuncion(203)||PermitirFuncion(204)){?>
            	<li>
                    <a href="#"><i class="fa fa-bank"></i> <span class="nav-label">Congregaci&oacute;n</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<?php if(PermitirFuncion(202)){?><li><a class="alnk" href="gestionar_publicadores.php"><i class="fa fa-user"></i> Publicadores</a></li><?php }?>
                  		<?php if(PermitirFuncion(203)){?><li><a class="alnk" href="gestionar_grupos.php"><i class="fa fa-group"></i> Grupos de predicación</a></li><?php }?>
						<?php if(PermitirFuncion(206)){?><li><a class="alnk" href="gestionar_periodos.php"><i class="fa fa-book"></i> Periodos</a></li><?php }?>
                    </ul>
                </li>
                <?php }?>
				<?php if(PermitirFuncion([301,302])){?>
            	<li>
                    <a href="#"><i class="fa fa-pencil"></i> <span class="nav-label">Ingresar informes</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<?php if(PermitirFuncion(301)){?><li><a class="alnk" href="gestionar_informes.php"><i class="fa fa-clock-o"></i> Informes de predicación</a></li><?php }?>
						<?php if(PermitirFuncion(302)){?><li><a class="alnk" href="asistencia.php"><i class="fa fa-users"></i> Asistencia a las reuniones</a></li><?php }?>
                    </ul>
                </li>
                <?php }?>
				<?php if(PermitirFuncion(401)||PermitirFuncion(402)||PermitirFuncion(403)||PermitirFuncion(404)){?>
            	<li>
                    <a href="#"><i class="fa fa-line-chart"></i> <span class="nav-label">Reportes</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<?php if(PermitirFuncion(405)){?><li><a class="alnk" href="informe_datos_publicador.php"><i class="fa fa-user"></i> Datos de los publicadores</a></li><?php }?>
						<?php if(PermitirFuncion(401)){?><li><a class="alnk" href="informe_servicio_mensual.php"><i class="fa fa-bar-chart-o"></i> Informe de servicio mensual</a></li><?php }?>
						<?php if(PermitirFuncion(402)){?><li><a class="alnk" href="informe_servicio_cong.php"><i class="fa fa-list-alt"></i> Informe de servicio de la congregación</a></li><?php }?>
						<?php if(PermitirFuncion(403)){?><li><a class="alnk" href="informe_precursores_regulares.php"><i class="fa fa-suitcase"></i> Precursores regulares</a></li><?php }?>
						<?php if(PermitirFuncion(404)){?><li><a class="alnk" href="informe_registro_publicador.php"><i class="fa fa-address-card"></i> Descargar Registros de publicador (S-21)</a></li><?php }?>
                    </ul>
                </li>
                <?php }?>
	            <li>
                    <a href="#"><i class="fa fa-gears"></i> <span class="nav-label">Administraci&oacute;n</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
						<li><a href="cambiar_clave.php"><i class="fa fa-lock"></i> Cambiar contrase&ntilde;a</a></li>
						<?php if(PermitirFuncion(101)){?><li><a class="alnk" href="gestionar_congregacion.php"><i class="fa fa-bank"></i> Gestionar congregación</a></li><?php }?>
						<?php if(PermitirFuncion(502)){?><li><a class="alnk" href="gestionar_usuarios.php"><i class="fa fa-user"></i> Gestionar usuarios</a></li><?php }?>
                  		<?php if(PermitirFuncion(501)){?><li><a class="alnk" href="gestionar_perfiles.php"><i class="fa fa-users"></i> Gestionar perfiles</a></li><?php }?>
                  		<li><a class="alnk" href="informe_link.php"><i class="fa fa-link"></i> Link para ingresar informes</a></li>
                        <li><a class="alnk" href="contrato_confidencialidad.php"><i class="fa fa-handshake-o"></i> Acuerdo de confidencialidad</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>