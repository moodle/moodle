<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 + (2005060201)


$string['admindirerror'] = 'O diretório  admin indicado não é correto';
$string['admindirname'] = 'Diretório Admin';
$string['admindirsetting'] = 'Alguns provedores usam /admin como uma URL especial para o acesso ao painel de administração do site. Infelizmente isto entra em conflito com o percurso de acesso predefinido para as páginas de administração de Moodle. Você pode superar este problema mudando o nome do diretório de administração da sua instalação e inserindo este nome aqui. Por exemplo:
<br/> <br /><b>moodleadmin</b><br /> <br />
Isto resolve os problemas dos links da página de administração de Moodle ';
$string['caution'] = 'Atenção';
$string['chooselanguage'] = 'Escolha um idioma';
$string['compatibilitysettings'] = 'Controlando configurações do PHP ...';
$string['configfilenotwritten'] = 'O script do instalador não conseguiu criar o arquivo config.php com as configurações que você definiu, provavelmente o diretório não está protegido e não aceita modificações. Você pode copiar o seguinte código manualmente em um arquivo de texto com o nome config.php e carregar este arquivo no diretório principal de Moodle.';
$string['configfilewritten'] = 'config.php foi criado com sucesso';
$string['configurationcomplete'] = 'Configuração terminada';
$string['database'] = 'Base de dados';
$string['databasecreationsettings'] = 'Agora é necessário configurar a base de dados que vai arquivar os dados de Moodle. Esta base de dados vai ser criada automaticamente pelo instalador Moodle4Windows com as opções definidas abaixo.<br />
<br /> <br />
<b>Tipo:</b> definido como \"mysql\" pelo instalador<br />
<b>Host:</b> definido como \"localhost\" pelo instalador<br />
<b>Nome:</b> nome da base de dados, ex. moodle<br />
<b>Usuário:</b> definido como \"root\" pelo instalador<br />
<b>Senha:</b> a senha da sua base de dados<br />
<b>Prefixo das tabelas:</b> prefixo opcional a ser usado no nome de todas as tabelas';
$string['databasesettings'] = 'Agora você precisa configurar a base de dados em que os dados de Moodle serão conservados. Esta base de dados deve ter sido criada anterirmente bem como o nome de usuário e a senha necessários ao acesso..<br />
<br /> <br />
<b>Tipo:</b> mysql ou postgres7<br />
<b>Host:</b> ex. localhost ou db.isp.com<br />
<b>Nome:</b> nome da base de dados, ex. moodle<br />
<b>Usuário:</b> nome do usuário da sua base de dados<br />
<b>Senha:</b> a senha da sua base de dados<br />
<b>Prefixo das tabelas:</b> prefixo opcional a ser utilizado no nome das tabelas';
$string['dataroot'] = 'Diretório Data';
$string['datarooterror'] = 'O \'Diretório Data\' indicado não foi encontrado e não foi possível criar um novo diretório. Corrija a indicação do percurso ou crie o diretório manualmente.';
$string['dbconnectionerror'] = 'Não foi possível fazer a conexão à base de dados indicada. Controle as configurações da base de dados.';
$string['dbcreationerror'] = 'Erro de ciação de base de dados. Não foi possível criar o nome da base de dados indicado com os parâmetros fornecidos.';
$string['dbhost'] = 'Servidor hospedeiro';
$string['dbpass'] = 'Senha';
$string['dbprefix'] = 'Prefixo das tabelas';
$string['dbtype'] = 'Tipo';
$string['directorysettings'] = '<p>Por favor confirme os endereços desta instalação.</p>

<p><b>Endereço Web:</b>
Indique o endereço web completo para o acesso a Moodle. Se o seu site pode ser acessado por URLs múltiplas, escolha o endereço que pode ser mais intuitivo para os seus estudantes. Não adicione uma barra (slash) ao final do endereço.</p>

<p><b>Diretório de Moodle:</b>
Indique o endereço completo do diretório de instalação, prestando muita atenção quanto ao uso de maiúsculas e minúsculas.</p>

<p><b>Diretório Data:</b>
Indique um diretório para o arquivamento de documentos carregados no servidor. Este diretório deve ter as autorizações de acesso configuradas para que o Usuário do Servidor (ex. \'nobody\' ou \'apache\')possa acessar e criar novos arquivos. Atenção, este diretório não deve ter o acesso via web autorizado.</p>';
$string['dirroot'] = 'Diretório Moodle';
$string['dirrooterror'] = 'A configuração do percurso de acesso ao Diretório Moodle parece errada - não foi possível encontrar uma instalação de Moodle neste endereço. O valor abaixo foi reconfigurado.';
$string['download'] = 'Download';
$string['fail'] = 'Erro';
$string['fileuploads'] = 'Carregamento de arquivos';
$string['fileuploadserror'] = 'Isto deve estar ativado';
$string['fileuploadshelp'] = '<p>Parece que o envio de documentos a este servidor não está habilitado.</p>
<p>Moodle pode ser instalado, mas não será possível carregar arquivos ou imagens nos cursos.</p>
<p>para habilitar o envio de arquivos é necessário editar edit o arquivo php.ini do sistema and mudar a configuração de  
<b>file_uploads</b> para \'1\'.</p>';
$string['gdversion'] = 'Versão do gd';
$string['gdversionerror'] = 'A library GD';
$string['gdversionhelp'] = '<p>parece que o seu servidor não tem o GD instalado.</p>
<p>GD é uma library de PHP necessária à elaboração de imagens como os fotos do perfil do usuário e os gráficos de estatísticas. Moodle funciona sem o GD mas a elaboração de imagens não será possível.</p>
<p>para adicionar o GD ao PHP emUnix, compile o PHP usando o parâmetro --with-gd .</p>
<p>Em Windows edite php.ini and cancele o comentário à linha que se refere a libgd.dll.</p>';
$string['installation'] = 'Instalação';
$string['magicquotesruntime'] = 'Run Time Magic Quotes ';
$string['magicquotesruntimeerror'] = 'Isto deve estar desativado';
$string['magicquotesruntimehelp'] = '<p> A runtime Magic Quotes  deve ser desativada para que Moodle to funcione corretamente.</p>

<p>Normalmente esta runtime já é desativada ... controle o parâmetro <b>magic_quotes_runtime</b> no seu arquivo php.ini .</p>

<p>Se você não tem acesso ao arquivo php.ini , adicione a seguinte linha no código de um arquivo chamado .htaccess no diretório Moodle:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'Limite de Memória';
$string['memorylimiterror'] = 'A configuração do limite da memória do PHP está muito baixa ... isto pode causar problemas no futuro.';
$string['memorylimithelp'] = '<p>O limite de memória do PHP configurado atualmente no seu servidor é de $a.</p>

<p>Este limite pode causar problemas no futuro, especialmente quando muitos módulos estiverem ativados ou em caso de um número elevado de usuários.</p>

<p>É aconselhável a configuração do limite de memória com o valor mais alto possível, como 16M. Você pode tentar um dos seguintes caminhos:</p>
<ol>
<li>Se você puder, recompile o PHP com <i>--enable-memory-limit</i>.
Com esta operação Moodle será capaz de configurar o limite de memória sózinho.</li>
<li>Se você tiver acesso ao arquivo php.ini, você pode mudar o parâmetro <b>memory_limit</b> para um valor próximo a 16M. Se você não tiver acesso direto, peça ao administrador do sistema para fazer esta operação.</li>
<li>Em alguns servidores é possível fazer esta mudança criando um arquivo .htaccess no diretório Moodle. O arquivo deve conter a seguinte expressão:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Alguns servidores não aceitam este procedimento e <b>todas</b> as páginas PHP do servidor ficam bloqueadas ou imprimem mensagens de erro. Neste caso será necessário cancelar o arquivo .htaccess .</p>
</li></ol> ';
$string['mysqlextensionisnotpresentinphp'] = 'O pHP não foi configurado corretamente com a extensão MySQL e não pode comunicar com a base de dados. Controle o seu php.ini ou faça a recompilação do PHP.';
$string['pass'] = 'Senha';
$string['phpversion'] = 'Versão do PHP';
$string['phpversionerror'] = 'A versão do PHP não deve ser inferior a 4.1.0';
$string['phpversionhelp'] = '<p>Moodle requer a versão 4.1.0 de PHP ou posterior.</p>
<p>A sua versão é $a</p>
<p>Atualize a versão do PHP!</p>';
$string['safemode'] = 'Modalidade segura';
$string['safemodeerror'] = 'Moodle pode ter problemas se a modalidade segura estiver ativa';
$string['safemodehelp'] = '<p>Moodle pode ter alguns problemas quando o safe mode está ativado. Provavelmente não será possível criar novos arquivos.</p>
<p>O Safe mode normalmente é ativado apenas por serviços de web hosting públicos enabled by paranóicos, é possível que você tenha que escolher um outro serviço de webhosting para o seu site.</p>
<p>Você pode continuar a instalação mas provavelmente outros problemas surgirão.</p>';
$string['sessionautostart'] = 'Início da sessão automático';
$string['sessionautostarterror'] = 'Isto deve estar ativado';
$string['sessionautostarthelp'] = '<p>Moodle requer o suporte a sessões e não funciona sem isto.</p>

<p>As sessões podem se habilitadas no arquivo php.ini ... controle o parâmetro session.auto_start .</p>';
$string['wwwroot'] = 'Endereço web';
$string['wwwrooterror'] = 'Este endereço web não está correto - a instalação do Moodle não foi lencontrada.';

?>
