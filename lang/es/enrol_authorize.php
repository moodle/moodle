<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.5.3+ (2005060230)


$string['adminauthorizeccapture'] = 'Ajustes Revisar Orden y Auto-Capturar';
$string['adminauthorizeemail'] = 'Ajustes Enviar Email';
$string['adminauthorizesettings'] = 'Ajustes Authorize.net';
$string['adminauthorizewide'] = 'Ajustes Todo el Sitio';
$string['adminreview'] = 'Revisar el orden antes de capturar la tarjeta de crédito.';
$string['anlogin'] = 'Authorize.net: Usuario';
$string['anpassword'] = 'Authorize.net: Contraseña (no requerida)';
$string['anreferer'] = 'Escriba aquí la referencia URL en el caso de que usted la ajuste en su cuenta authorize.net, que enviará una cabecera \"Referer: URL\" en la petición web.';
$string['antestmode'] = 'Authorize.net:';
$string['antrankey'] = 'Authorize.net:';
$string['ccexpire'] = 'Fecha de expiración';
$string['ccexpired'] = 'La tarjeta de crédito ha expirado';
$string['ccinvalid'] = 'Número de tarjeta no válido';
$string['ccno'] = 'Número de la tarjeta de crédito';
$string['cctype'] = 'Tipo de la tarjeta de crédito';
$string['ccvv'] = 'CV2';
$string['ccvvhelp'] = 'Mire el reverso de la tarjeta (3 últimos dígitos)';
$string['choosemethod'] = 'Si conoce la clave de matriculación en el curso, escríbala; en caso contrario, necesitará pagar para acceder al curso.';
$string['chooseone'] = 'Rellene uno o ambos de los siguientes dos campos';
$string['description'] = 'El módulo Authorize.net le permite ajustar cursos de pago vía proveedores CC. Si el costo de cualquier curso es cero, no se pedirá a los estudiantes que paguen. Existe un costo del sitio que usted ajusta aquí por defecto para todo el sitio y además un ajuste por curso que puede efectuar para cada curso individualmente. El costo del curso pasa por alto el costo del sitio.';
$string['enrolname'] = 'Puerta de tarjeta de crédito Authorize.net:';
$string['httpsrequired'] = 'Lamentamos comunicarle que su solicitud no puede procesarse en este momento. La configuración de este sitio no se ha podido realizar correctamente.
<br /><br />
Por favor, no escriba su número de tarjeta de crédito a menos que vea un candado amarillo en la parte inferior del navegador. Ello significa transferidos entre cliente y servidor son encriptados, con el fin de proteger la información durante la transacción entre dos ordenadores y que el número de su tarjeta no puede ser capturado en internet.';
$string['logindesc'] = 'Puede seleccionar la opción <a href=\"$a->url\">loginhttps</a> en la sección Variables/Seguridad.
<br /><br />
Si la selecciona, Moodle usará una conexión https segura únicamente en la página de acceso y pago.';
$string['nameoncard'] = 'Nombre que figura en la tarjeta';
$string['paymentpending'] = 'El pago de este curso está pendiente con este número de orden $a->orderid.';
$string['reviewday'] = 'Capturar la tarjeta de crédito automáticamente a menos que un profesor o administrador revise la orden antes de <b>$a</b>días. EL CRON DEBE ESTAR ACTIVADO.<br />(0 días significa que se desactivará la auto-captura, y que el profesor o administrador revisarán la orden manualmente. La transacción será cancelada si usted desactiva la auto-captura, o si no la revisa antes de 30 días).';
$string['reviewnotify'] = 'Su pago será revisado. En unos días recibirá un email de su profesor.';
$string['sendpaymentbutton'] = 'Enviar pago';
$string['zipcode'] = 'Código postal';

?>
