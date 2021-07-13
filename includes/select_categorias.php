<?php 
$Cons_Menu="Select * From uvw_tbl_Categorias Where ID_Padre=0 and EstadoCategoria=1";
$SQL_Menu=sqlsrv_query($conexion,$Cons_Menu,array(),array( "Scrollable" => 'static' ));
$Num_Menu=sqlsrv_num_rows($SQL_Menu);
?>
<select name="Categoria" class="form-control m-b" id="Categoria">
   <option value="" selected="selected">(Todas)</option>
   <?php 
	while($row_Menu=sqlsrv_fetch_array($SQL_Menu)){
		echo "<optgroup label='".$row_Menu['NombreCategoria']."'>";

		$Cons_MenuLvl2="Select * From uvw_tbl_Categorias Where ID_Padre=".$row_Menu['ID_Categoria']." and EstadoCategoria=1";
		$SQL_MenuLvl2=sqlsrv_query($conexion,$Cons_MenuLvl2,array(),array( "Scrollable" => 'static' ));
		$Num_MenuLvl2=sqlsrv_num_rows($SQL_MenuLvl2);

		if($Num_MenuLvl2>=1){			
			while($row_MenuLvl2=sqlsrv_fetch_array($SQL_MenuLvl2)){
				$Cons_MenuLvl3="Select * From uvw_tbl_Categorias Where ID_Padre=".$row_MenuLvl2['ID_Categoria']." and EstadoCategoria=1";
				$SQL_MenuLvl3=sqlsrv_query($conexion,$Cons_MenuLvl3,array(),array( "Scrollable" => 'static' ));
				$Num_MenuLvl3=sqlsrv_num_rows($SQL_MenuLvl3);

				if($Num_MenuLvl3>=1){
					echo "<optgroup label='".$row_MenuLvl2['NombreCategoria']."'>";
					while($row_MenuLvl3=sqlsrv_fetch_array($SQL_MenuLvl3)){
						$Selected="";
						if((isset($_GET['Categoria']))&&(strcmp($row_MenuLvl3['ID_Categoria'],$_GET['Categoria'])==0)){ $Selected="selected=\"selected\"";}
						echo "<option value='".$row_MenuLvl3['ID_Categoria']."' ".$Selected.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row_MenuLvl3['NombreCategoria']."</option>";
					}
					echo "</optgroup>";
				}else{
					$Selected="";
					if((isset($_GET['Categoria']))&&(strcmp($row_MenuLvl2['ID_Categoria'],$_GET['Categoria'])==0)){ $Selected="selected=\"selected\"";}
					echo "<option value='".$row_MenuLvl2['ID_Categoria']."' ".$Selected.">&nbsp;&nbsp;&nbsp;".$row_MenuLvl2['NombreCategoria']."</option>";
				}
			}
		}
		echo "</optgroup>";
	 }?>
</select>