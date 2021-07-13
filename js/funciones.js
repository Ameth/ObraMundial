function justNumbers(e, cad){//Permitir solo numeros y puntos
	var keynum = window.event ? window.event.keyCode : e.which;
	if(keynum>=1&&keynum<=31){
		return true;		
	}
	if(keynum==46){
		if(ExisteCaracter(cad,".")){
			return false;
		}else{
			return true;
		}
	}
	return /\d/.test(String.fromCharCode(keynum));
}

//Funciones para permitir solo 2 decimales
var textoAnterior = '';
function cumpleReglas(simpleTexto)
	{
		//la pasamos por una poderosa expresión regular
		var expresion = new RegExp("^(|([0-9]{1,6}(\\.([0-9]{1,2})?)?))$");

		//si pasa la prueba, es válida
		if(expresion.test(simpleTexto))
			return true;
		return false;
	}//end function checaReglas

//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
function revisaCadena(textItem)
	{
		//si comienza con un punto, le agregamos un cero
		if(textItem.value.substring(0,1) == '.') 
			textItem.value = '0' + textItem.value;

		//si no cumples las reglas, no te dejo escribir
		if(!cumpleReglas(textItem.value)){
			textItem.value = textoAnterior;
		}else{ //todo en orden
			textoAnterior = textItem.value;
		}
	}//end function revisaCadena

function justNumbersOnly(e){//Permitir solo numeros
	var keynum = window.event ? window.event.keyCode : e.which;
	if((keynum>=1&&keynum<=31)||(keynum==45)){
		return true;
	}else{
		return /\d/.test(String.fromCharCode(keynum));
	}
}

function ExisteCaracter(Cadena, Caracter){
	if(Cadena.indexOf(Caracter)==-1){
		return false;
	}else{
		return true;
	}
}

function SoloNumeros(evt){//Otro metodo para no permitir el ingreso de letras, solo numeros.
	if(window.event){//asignamos el valor de la tecla a keynum
		keynum = evt.keyCode; //IE
	}else{
		keynum = evt.which; //FF
	}
	//comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
	if((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 9 || keynum == 13 || keynum == 6 ){
		return true;
	}else{
		return false;
	}
}

function number_format(amount, decimals) {

	amount += ''; // por si pasan un numero en vez de un string
	amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

	decimals = decimals || 0; // por si la variable no fue fue pasada

	// si no es un numero o es igual a cero retorno el mismo cero
	if (isNaN(amount) || amount === 0) 
		return parseFloat(0).toFixed(decimals);

	// si es mayor o menor que cero retorno el valor formateado como numero
	amount = '' + amount.toFixed(decimals);

	var amount_parts = amount.split('.'),
		regexp = /(\d+)(\d{3})/;

	while (regexp.test(amount_parts[0]))
		amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

	return amount_parts.join('.');
}

function generar_clave(longitud)
{
  var caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
  var pass = "";
  for (i=0; i<longitud; i++) pass += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
  return pass;
}

function ResetCall(){
	var TagLlamada=document.getElementById("dv_TagLlamada");
	var ElastixLlamada=document.getElementById("dv_ElastixLlamada");
	var cllName=document.getElementById("cllName");
	
	cllName.value='';
	TagLlamada.src='frm1.php?type=1';
	ElastixLlamada.src='frm2.php?type=1';	
}

function EsTel(CadenaLL){
	var TipoDest=document.getElementById("TipoDestino").value;
	var Tel=document.getElementById("Destino").value;
	var TagLlamada=document.getElementById("dv_TagLlamada");
	var ElastixLlamada=document.getElementById("dv_ElastixLlamada");
	var cllName=document.getElementById("cllName");
	if(TipoDest==1){
		var FileName=Tel+'_'+Base64.decode(CadenaLL);
		cllName.value=Base64.encode(FileName);
		TagLlamada.src='frm1.php?type=2&etiq='+Base64.encode(Tel);
		ElastixLlamada.src='frm2.php?type=2&dest='+Base64.encode(Tel)+'&etiq='+Base64.encode(FileName);
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"progressBar": true,
			"preventDuplicates": true,
			"positionClass": "toast-top-right",
			"onclick": null,
			"showDuration": "400",
			"hideDuration": "1000",
			"timeOut": "7000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
		toastr.success(Tel,'Llamando...');
	}else{
		ResetCall();
	}
}

function CalculaAcuerdo(){
	var Clt=document.getElementById("CardCode");
	var chkInt=document.getElementById("chkCobInt");
	//var chkFactNoVenc=document.getElementById("chkVerFactNoVenc");
	var Int=0;
	var FactVenc=0;
	
	if(chkInt.checked==true){
		Int=1;
	}else{
		Int=0;
	}
	
	if($("#chkVerFactNoVenc").length != 0){
		var chkFactNoVenc=document.getElementById("chkVerFactNoVenc");
		if(chkFactNoVenc.checked==true){
			FactVenc=1;
		}else{
			FactVenc=0;
		}
	}
	
	$.ajax({
		type: "POST",
		url: "ajx_cuadro_acuerdos.php?type=2&clt="+Clt.value+"&int="+Int+"&factvenc="+FactVenc,
		success: function(response){
			if(response!=""){					
				$('#dv_TblIntMora').html(response).fadeIn();
			}
		}
	});
	$.ajax({
		url:"ajx_buscar_datos_json.php",
		data:{type:11,CardCode:Clt.value,IntMora:Int,FactNoVenc:FactVenc},
		dataType:'json',
		success: function(data){
			document.getElementById('TotalSaldo').value=number_format(data.TotalSaldo,0);
			document.getElementById('InteresesMora').value=number_format(data.TotalIntMora,0);
			document.getElementById('GastosCobranza').value=number_format(data.TotalGastosCob,0);
			document.getElementById('CobroPrejuridico').value=number_format(data.TotalCobroPre,0);
			
			var TotalSaldo=document.getElementById("TotalSaldo");
			var IntMora=document.getElementById("InteresesMora");
			var RetiroAnt=document.getElementById("RetiroAnticipado");
			var GastosCob=document.getElementById("GastosCobranza");
			var CobroPreJ=document.getElementById("CobroPrejuridico");
			var TotalLiq=document.getElementById("TotalLiquidado");
			var Descuento=document.getElementById("Descuento");
			var TotalPagar=document.getElementById("TotalPagar");
			var AbonoInicial=document.getElementById("AbonoInicial");
			var SaldoDiferir=document.getElementById("SaldoDiferir");	

			if(RetiroAnt.value==""){
				RetiroAnt.value="0";
			}
			if(Descuento.value==""){
				Descuento.value="0";
			}
			if(AbonoInicial.value==""){
				AbonoInicial.value="0";
			}			
			
			//CobroPreJ.value=number_format((parseFloat(TotalSaldo.value.replace(/,/g, ''))+parseFloat(IntMora.value.replace(/,/g, ''))+parseFloat(RetiroAnt.value.replace(/,/g, '')))*0.1,0);
	
			TotalLiq.value=number_format(parseFloat(TotalSaldo.value.replace(/,/g, ''))+parseFloat(IntMora.value.replace(/,/g, ''))+parseFloat(RetiroAnt.value.replace(/,/g, ''))+parseFloat(GastosCob.value.replace(/,/g, ''))+parseFloat(CobroPreJ.value.replace(/,/g, '')),0);

			TotalPagar.value=number_format(parseFloat(TotalLiq.value.replace(/,/g, ''))-parseFloat(Descuento.value.replace(/,/g, '')),0);

			RetiroAnt.value=number_format(RetiroAnt.value,0);
			Descuento.value=number_format(Descuento.value,0);
			AbonoInicial.value=number_format(AbonoInicial.value,0);	
			if(AbonoInicial.value!="0"){
				SaldoDiferir.value=number_format(parseFloat(TotalPagar.value.replace(/,/g, ''))-parseFloat(AbonoInicial.value.replace(/,/g, '')),0);
			}else{
				SaldoDiferir.value="0";
			}
			
			
		}
	});
}

function CalculaLiqIntereses(){
	var Clt=document.getElementById("CardCode");
	var chkInt=document.getElementById("chkCobIntLiqInt");
	//var chkFactNoVenc=document.getElementById("chkVerFactNoVencLiqInt");
	var Int=0;
	var FactVenc=0;
	
	if(chkInt.checked==true){
		Int=1;
	}else{
		Int=0;
	}
	
	if($("#chkVerFactNoVencLiqInt").length != 0){
		var chkFactNoVenc=document.getElementById("chkVerFactNoVencLiqInt");
		if(chkFactNoVenc.checked==true){
			FactVenc=1;
		}else{
			FactVenc=0;
		}
	}
	
	$.ajax({
		type: "POST",
		url: "ajx_cuadro_acuerdos.php?type=2&clt="+Clt.value+"&int="+Int+"&factvenc="+FactVenc,
		success: function(response){
			if(response!=""){					
				$('#dv_TblIntMoraLiqInt').html(response).fadeIn();
			}
		}
	});
	$.ajax({
		url:"ajx_buscar_datos_json.php",
		data:{type:11,CardCode:Clt.value,IntMora:Int,FactNoVenc:FactVenc},
		dataType:'json',
		success: function(data){
			document.getElementById('TotalSaldoLiqInt').value=number_format(data.TotalSaldo,0);
			document.getElementById('InteresesMoraLiqInt').value=number_format(data.TotalIntMora,0);
			document.getElementById('GastosCobranzaLiqInt').value=number_format(data.TotalGastosCob,0);
			document.getElementById('CobroPrejuridicoLiqInt').value=number_format(data.TotalCobroPre,0);
			
			var TotalSaldo=document.getElementById("TotalSaldoLiqInt");
			var IntMora=document.getElementById("InteresesMoraLiqInt");
			var RetiroAnt=document.getElementById("RetiroAnticipadoLiqInt");
			var GastosCob=document.getElementById("GastosCobranzaLiqInt");
			var CobroPreJ=document.getElementById("CobroPrejuridicoLiqInt");
			var TotalLiq=document.getElementById("TotalLiquidadoLiqInt");
			var Descuento=document.getElementById("DescuentoLiqInt");
			var TotalPagar=document.getElementById("TotalPagarLiqInt");

			if(RetiroAnt.value==""){
				RetiroAnt.value="0";
			}
			if(Descuento.value==""){
				Descuento.value="0";
			}		
			
			//CobroPreJ.value=number_format((parseFloat(TotalSaldo.value.replace(/,/g, ''))+parseFloat(IntMora.value.replace(/,/g, ''))+parseFloat(RetiroAnt.value.replace(/,/g, '')))*0.1,0);
	
			TotalLiq.value=number_format(parseFloat(TotalSaldo.value.replace(/,/g, ''))+parseFloat(IntMora.value.replace(/,/g, ''))+parseFloat(RetiroAnt.value.replace(/,/g, ''))+parseFloat(GastosCob.value.replace(/,/g, ''))+parseFloat(CobroPreJ.value.replace(/,/g, '')),0);

			TotalPagar.value=number_format(parseFloat(TotalLiq.value.replace(/,/g, ''))-parseFloat(Descuento.value.replace(/,/g, '')),0);

			RetiroAnt.value=number_format(RetiroAnt.value,0);
			Descuento.value=number_format(Descuento.value,0);			
		}
	});
}

function CalcularMesesFechas(){
	var FechaAcuerdo=document.getElementById("FechaAcuerdo").value;
	var FechaMora=document.getElementById("FechaMora").value;
	//f1 = "2018-12-01";
	//f2 = "2019-03-20";

	aF1 = FechaMora.split("-");
	aF2 = FechaAcuerdo.split("-");

	numMeses = (parseInt(aF2[0])*12 + parseInt(aF2[1])) - (parseInt(aF1[0])*12 + parseInt(aF1[1]));
	if (aF2[2]<aF1[2]){
		numMeses = numMeses - 1;
	}
	return numMeses;
}