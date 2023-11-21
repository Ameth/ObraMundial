<?php 
//include_once('funciones.php');
function LSiqml($cad){
	$search=array("'",";","..","=","*","?","¿","&","_","\\","\<","\>","<script>","</script>","<",">","\"\"","\"");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	return(trim($cad_clear));
}
function LSiqmlLogin($cad){
	$search=array("'","\\","\<","\>","<script>","</script>","<",">","\"\"","\"");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	return(trim($cad_clear));
}
function LSiqmlObs($cad){
	$search=array("'","<script>","</script>","´","¨");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cad_clear = $cad_clear;
    $cad_clear = strtr($cad_clear, $originales, $modificadas);
	//$cad_clear=str_replace("Ñ",'N',$cad_clear);
	//$cad_clear=str_replace("ñ",'n',$cad_clear);
	//$cad_clear = preg_replace("/[\r\n|\n|\r]+/", " ", $cad_clear);
	return($cad_clear);
}
function LSiqmlValor($cad){
	$search=array("$",",",".");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	return(trim($cad_clear));
}
function LSiqmlValorDecimal($cad){
	$search=array("$",",");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	return(trim($cad_clear));
}
function LSiqmlName($cad){
	$search=array("'",";","..","=","*","?","¿","&","\<","\>","<script>","</script>","<",">","\"\"","\"");
	$replace="";
	$cad_clear=str_ireplace($search,$replace,$cad);
	return(trim($cad_clear));
}
?>