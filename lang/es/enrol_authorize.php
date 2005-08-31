<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.5.2 (2005060220)


$string['adminreview'] = 'Revisar el orden antes de capturar la tarjeta de crédito.';
$string['anlogin'] = 'Authorize.net: Usuario';
$string['anpassword'] = 'Authorize.net: Contraseña (no requerida)';
$string['anreferer'] = 'Defina el referente URL si ha seleccionado ese ajuste en su cuenta authorize.net. Esto enviará una línea \"Referer: URL\" incrustada en la solicitud.';
$string['antestmode'] = 'Ejecutar las transacciones sólo en modo de prueba (no se envía dinero)';
$string['antrankey'] = 'Authorize.net: Clave de transacción';
$string['ccexpire'] = 'Fecha de expiración';
$string['ccexpired'] = 'La tarjeta de crédito ha expirado';
$string['ccinvalid'] = 'Número de tarjeta no válido';
$string['ccno'] = 'Número de la tarjeta de crédito';
$string['cctype'] = 'Tipo de la tarjeta de crédito';
$string['ccvv'] = 'Verificación de la tarjeta';
$string['ccvvhelp'] = 'Mire el reverso de la tarjeta (3 últimos dígitos)';
$string['choosemethod'] = 'Si conoce la clave de matriculación del curso, escríbala; en caso contrario, necesitará pagar por este curso.';
$string['chooseone'] = 'Rellene uno o ambos de los siguientes dos campos';
$string['description'] = 'El módulo Authorize.net permite planificar cursos de pago. Si el costo de un curso es cero, no se pedirá a los estudiantes que paguen. Existe un costo para todo el sitio que usted puede fijar aquí como valor por defecto para el sitio completo y además una opción que permite fijar el costo de cada curso. El costo del curso pasa por alto el costo del sitio.';
$string['enrolname'] = 'Puerta de Tarjeta de Crédito de Authorize.net';
$string['httpsrequired'] = 'Sentimos informarle que su petición no puede cursarse en este momento. La configuración del sitio podría no ser correcta.
<br /><br />
Por favor, no escriba el número de su tarjeta de crédito a menos que vea un candado amarillo en la parte inferior del navegador. Esto significa que se encriptarán todos los datos entre el cliente y el servidor, de modo que la información durante la transacción entre dos ordenadores estará protegida y nadie podrá capturar el número de su tarjeta de crédito.';
$string['logindesc'] = 'Esta opción deberá estar ACTIVADA.
<br /><br />
Puede seleccionar la opción <a href=\"$a->url\">loginhttps</a> en la sección Variables/Seguridad.
<br /><br />
Al activarla, Moodle utilizará una conexión https segura únicamente para las páginas de acceso y pago.';
$string['nameoncard'] = 'Nombre que figura en la tarjeta';
$string['reviewday'] = 'Capturar la tarjeta de crédito automáticamente a menos que un profesor o administrador revise la orden en el plazo de <b>$a</b> días. EL CRON DEBE ESTAR ACTIVADO.<br />(0=disable=teacher,admin review it. La transacción será cancelada a menos que la revise en 30 días)';
$string['reviewnotify'] = 'Su pago será sometido a revisión. Espere un correo electrónico de su profesor en los próximos días.';
$string['sendpaymentbutton'] = 'Enviar pago';
$string['zipcode'] = 'Código postal';

?>
