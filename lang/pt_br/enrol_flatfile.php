<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.4.1 (2004083101)


$string['description'] = 'Este método controla e processa regularmente um arquivo de texto com formato especial no endereço indicado por você
Veja o seguinte exemplo: 
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Arquivo Flat';
$string['filelockedmail'] = 'O arquivo de texto que você está utilizando para fazer as inscrições ($a) não pôde ser cancelado pelo processo cron. Isto normalmente significa que a configuração das permissões do arquivo não é compatível. Por favor corrija as permissões para que o sistema possa cancelar o arquivo e impedir que o mesmo seja processado diversas vêzes';
$string['filelockedmailsubject'] = 'Erro importante: Arquivo de inscrição';
$string['location'] = 'Localização do arquivo';
$string['mailadmin'] = 'Avisar administrador via email';
$string['mailusers'] = 'Avisar usuários via email';

?>
