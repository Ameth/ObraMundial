<div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-success " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                   <li>
                        <a href="#">
                            <i class="fa fa-user-circle"></i> <?php echo $_SESSION['User'];?>
                        </a>
                    </li>
					<li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-question-circle"></i>  Ayuda
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="enviar_mail_soporte.php" class="dropdown-item" target="_blank">
                                <div>
                                   <i class="fa fa-envelope fa-fw"></i> Enviar una solicitud de soporte
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                    <li>
                        <a href="logout.php">
                            <i class="fa fa-sign-out"></i> Cerrar sesi&oacute;n
                        </a>
                    </li>
                </ul>
            </nav>
        </div>