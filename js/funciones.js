function blockUI(run=true) {
	if(run){
		$.blockUI({
			message: '<div class="sk-grid sk-primary mx-auto mb-4"><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div><div class="sk-grid-cube"></div></div><h5 class="text-body">CARGANDO...</h5>',
			css: {
				backgroundColor: 'transparent',
				border: '0',
				zIndex: 9999999
			},
			overlayCSS:  {
				backgroundColor: '#fff',
				opacity: 0.8,
				zIndex: 9999990
			}
		});
	}else{
		$.unblockUI();
	}

}

function mostrarNotify(title, pMsg='',pType=''){
	
	let type = (pType=='') ? 'success' : pType;
	let msg = (pMsg=='') ? '' : pMsg;
	
	toastr[type](msg, title, {
      positionClass:     "toast-top-right",
      closeButton:       true,
      progressBar:       true,
      preventDuplicates: false,
      newestOnTop:       true
    });
}

function maxLength(id){
	$('#'+id).each(function() {
		$(this).maxlength({
		  warningClass: 'label label-success',
		  limitReachedClass: 'label label-danger',
		  separator: ' de ',
		  preText: 'Has escrito ',
		  postText: ' caracteres disponibles.',
		  validate: true,
		  threshold: +this.getAttribute('maxlength')
		});
	  });
}

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
function cumpleReglas(simpleTexto){
	//la pasamos por una poderosa expresión regular
	var expresion = new RegExp("^(|([0-9]{1,8}(\\.([0-9]{1,2})?)?))$");

	//si pasa la prueba, es válida
	if(expresion.test(simpleTexto))
		return true;
	return false;
}//end function checaReglas

//ESTA FUNCIÓN REVISA QUE TODO LO QUE SE ESCRIBA ESTÉ EN ORDEN
function revisaCadena(textItem){
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

function esFecha(cadena){//Validar que el campo sea una fecha valida
	//El formato debe ser yyyy-mm-dd
	
	if( (trim(cadena) == "") || (trim(cadena).length != 10) )
		return false;
	
	//Cuando el formato es dd/mm/yyyy
//	var dia  = parseInt(cadena.substr(0,2), 10);
//	var mes  = parseInt(cadena.substr(3,2), 10);
//	var anio = parseInt(cadena.substr(6,4), 10);
	
	//Cuando el formato es yyyy-mm-dd
	var anio = parseInt(cadena.substr(0,4), 10);
	var mes  = parseInt(cadena.substr(5,2), 10);
	var dia  = parseInt(cadena.substr(8,2), 10);	
	
	// Año
	if( isNaN(anio) || (anio < 1900) )
		return false;
	// Mes
	if( isNaN(mes) || (mes < 1) || (mes > 12) )
		return false;
	// Día
	if( isNaN(dia) || (dia < 1) || (dia > 31) )
		return false;
	else
	{
		if( (dia == 31) && ((mes == 4 ) || (mes == 6) || (mes == 9) || (mes == 11)) )
			return false;
		var diaMax = 31;
		if( (anio % 4 == 0) && (anio % 100 != 0) || (anio % 400 == 0) )
			diaMax = 29;
		else
			diaMax = 28;
		if( dia > diaMax )
			return false;
	}
	return true;
}

function esHora(cadena){//Validar que el campo sea una hora correcta
	//El formato debe ser HH:MM (hora en formato de 24 horas)
	
	if( (trim(cadena) == "") || (trim(cadena).length != 5) )
		return false;
	
	var hora = parseInt(cadena.substr(0,2), 10);
	var min = parseInt(cadena.substr(3,2), 10);
	
	// Hora
	if( isNaN(hora) || (hora > 23) )
		return false;
	
	// min
	if( isNaN(min) || (min > 59) )
		return false;

	return true;
}

function validarRangoHoras(HInicio, HFin){
	let result = true;
//	var HInicio = document.getElementById("HoraInicio").value;
//	var HFin = document.getElementById("HoraFin").value;
	
	HInicioMinutos = parseInt(HInicio.substr(3,2));
	HInicioHoras = parseInt(HInicio.substr(0,2));

	HFinMinutos = parseInt(HFin.substr(3,2));
	HFinHoras = parseInt(HFin.substr(0,2));

	TranscurridoMinutos = HFinMinutos - HInicioMinutos;
	TranscurridoHoras = HFinHoras - HInicioHoras;

	if (TranscurridoMinutos < 0) {
		TranscurridoHoras--;
		TranscurridoMinutos = 60 + TranscurridoMinutos;
	}
	
	if(TranscurridoHoras < 0){
		result = false
	}
	
	return result
}

// Elimina espacios al principio y fin de la cadena
function trim( cadena ){
	cadena += "";
	cadena = cadena.replace(/^\s+/, '');
	return cadena.replace(/\s+$/, '');
}

function generarColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function generar_clave(longitud){
  var caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ2346789";
  var pass = "";
  for (i=0; i<longitud; i++) pass += caracteres.charAt(Math.floor(Math.random()*caracteres.length));
  return pass;
}

function MostrarFechaHora(){
	var FechaActual = new Date();
	var Anio = FechaActual.getFullYear();
	var Mes = FechaActual.getMonth() + 1;
	var Dia = FechaActual.getDate();
	var Hora = FechaActual.getHours();
	var Minuto = FechaActual.getMinutes();
	
	if(Mes<10){
		Mes = "0" + Mes;
	}
	
	if(Dia<10){
		Dia = "0" + Dia;
	}
	
	if(Hora<10){
		Hora = "0" + Hora;
	}
	
	if(Minuto<10){
		Minuto = "0" + Minuto;
	}
	   
	var FechaHora = Anio + "-" + Mes + "-" + Dia + " " + Hora + ":" + Minuto;
	
	return FechaHora;
}

function DescargarSAPDownload(url, parametros='', loading=false){
	if(loading){
		$('.ibox-content').toggleClass('sk-loading',true);
	}
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url, true);
	xhr.responseType = 'arraybuffer';
	xhr.onload = async function () {
		if (this.status === 200) {
			var filename = "";
			var disposition = xhr.getResponseHeader('Content-Disposition');
			if (disposition && disposition.indexOf('attachment') !== -1) {
				var filenameRegexp = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
				var matches = filenameRegexp.exec(disposition);
				if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
			}
			var type = xhr.getResponseHeader('Content-Type');
			
			var blob = typeof File === 'function'
				? new File([this.response], filename, { type: type })
				: new Blob([this.response], { type: type });
//			console.log(blob);
			if (typeof window.navigator.msSaveBlob !== 'undefined') {
				// IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var URL = window.URL || window.webkitURL;
				var downloadUrl = URL.createObjectURL(blob);
//				console.log(downloadUrl)
				if (filename) {
					// use HTML5 a[download] attribute to specify filename
					var a = document.createElement("a");
					// safari doesn't support this yet
					if (typeof a.download === 'undefined') {
						window.location = downloadUrl;
					} else {
						a.href = downloadUrl;
						a.download = filename;
						document.body.appendChild(a);
						a.click();
					}
				} else {
					window.location = downloadUrl;
				}
	            URL.revokeObjectURL(downloadUrl);
			}
		}
		if(loading){
			$('.ibox-content').toggleClass('sk-loading',false);
		}		
	};
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	if(parametros!=''){
		xhr.send(parametros);
	}else{
		xhr.send();
	}
	
}