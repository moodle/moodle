<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2.1 (2004032500)


$string['auth_dbdescription'] = 'Este método usa uma tabela de uma base de dados externa para verificar se a senha e o nome do usuário são válidos. Se a conta for nova, a informação de outros campos também deve ser copiada em Moodle.';
$string['auth_dbextrafields'] = 'Estes campos são opcionais. Pode-se optar por preencher alguns dos campos do usuário em Moodle com informação de <b>campos da base de dados externa</b> especificados aqui.<p>Deixando estes campos em branco, serão usados valores predefinidos.<p>Nos dois casos, o usuário poderá editar todos estes campos quando tiver entrado no sistema.';
$string['auth_dbfieldpass'] = 'Nome do campo que contém as senhas';
$string['auth_dbfielduser'] = 'Nome do campo que contém os nomes de usuários';
$string['auth_dbhost'] = 'Computador que hospeda o server da base de dados.';
$string['auth_dbname'] = 'Nome da base de dados';
$string['auth_dbpass'] = 'Senha correspondente ao usuário acima';
$string['auth_dbpasstype'] = 'Indique o formato usado no campo de senhas. A codificação MD5 é útil na conexão com outras aplicações web comuns como PostNuke';
$string['auth_dbtable'] = 'Nome da tabela na base de dados';
$string['auth_dbtitle'] = 'Use uma base de dados externa';
$string['auth_dbtype'] = 'O tipo de base de dados (veja <a href=\"../lib/adodb/readme.htm#drivers\"> Documentação do ADOdb</a> para mais detalhes)';
$string['auth_dbuser'] = 'Nome de usuário com permissão de leitura da base de dados';
$string['auth_emaildescription'] = 'Confirmação via correio eletrônico é o método de autenticação predefinido. Depois que o usuário se inscrever, escolhendo o nome de usuário e a senha, receberá uma mensagem de confirmação via Email. Essa mensagem contém um link seguro a uma página onde o usuário deve confirmar a sua inscrição. Quando o usuário preencher os campos relativos ao nome de usuário e à senha na página de ingresso, estes dados serão confrontados com os valores arquivados na base de dados.';
$string['auth_emailtitle'] = 'Autenticação via correio eletrônico';
$string['auth_imapdescription'] = 'Este método usa um servidor IMAP para verificar se o nome de usuário e  a senha são válidos.';
$string['auth_imaphost'] = 'Endereço do servidor IMAP. Use o NÚMERO IP e não o nome DNS.';
$string['auth_imapport'] = 'Número da porta do servidor IMAP. Geralmente é 143 ou 993.';
$string['auth_imaptitle'] = 'Use um servidor IMAP';
$string['auth_imaptype'] = 'Tipo de servidor IMAP. Os servidores IMAP podem usar diferentes métodos de autenticação e negociação.';
$string['auth_ldap_bind_dn'] = 'Para usar o bind-user para procurar usuários, especifique o parâmetro aqui. Algo como \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password para o bind-user.';
$string['auth_ldap_contexts'] = 'Lista dos contextos onde os usuários estão localizados. Separe contextos diferentes com \';\'. Por exemplo: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Se você ativar a confirmação via correio eletrônico para a criação de usuário, indique o contexto em que os usuários são criados. Este contexto deve ser diverso de outros usuários para evitar problemas de segurança. Você não precisa adicionar este contexto ao ldap_context-variable, isto vai ser feito automaticamente.';
$string['auth_ldap_creators'] = 'Lista dos grupos em que os membros tem permissão para criar novos cursos. Separe os grupos com \';\'. Por exemplo \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Especifique o servidor LDAP usando o URL como \'ldap://ldap.myorg.com/\' ou \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_memberattribute'] = 'Especifique o atributo do usuário quando pertence a um grupo. Normalmente o atributo é \'membro\'';
$string['auth_ldap_search_sub'] = 'Inserir valor &lt;&gt; 0 se quiser procurar usuários nos sub-contextos.';
$string['auth_ldap_update_userinfo'] = 'Atualizar os dados dos usuários (nome, sobrenome, endereço..) a partir do LDAP. Para informação sobre o mapeamento consulte /auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = 'O atributo usado para nomear/procurar usuários. Geralmente \'cn\'.';
$string['auth_ldap_version'] = 'A versão do protocolo LDAP que o seu servidor usa';
$string['auth_ldapdescription'] = 'Este método faz a autenticação em relação a um servidor LDAP externo. Se o nome do usuário e a senha forem válidos, Moodle cria um novo registo de usuário na sua base de dados. Este módulo pode ler atributos do usuário a partir do LDAP e preencher os valores desejados em Moodle. Em ingressos sucessivos serão verificados apenas o nome de usuário e a senha.';
$string['auth_ldapextrafields'] = 'Estes campos são opcionais. É possivel optar por preencher campos de usuários com informação de <b>campos LDAP</b> especificados aqui.<p>Deixando estes campos em branco, serão usados valores predefinidos.<p>Nos dois casos, o usuário poderá editar todos estes campos quando tiver entrado no sistema.';
$string['auth_ldaptitle'] = 'Use um servidor LDAP';
$string['auth_manualdescription'] = 'Este método impede que os usuários criem as suas próprias contas. Todas as contas deverão ser criadas manualmente pelo Administrador.';
$string['auth_manualtitle'] = 'Apenas contas manuais';
$string['auth_multiplehosts'] = 'Hosts multiplos podem ser definidos, po exemplo: host1.com;host2.com;host3.com';
$string['auth_nntpdescription'] = 'Este método usa um servidor NNTP para verificar se o nome do usuário e a senha são válidos.';
$string['auth_nntphost'] = 'Endereço do servidor NNTP. Use o NÚMERO IP e não o nome DNS.';
$string['auth_nntpport'] = 'Porta do servidor  (normalmente 119)';
$string['auth_nntptitle'] = 'Use um servidor NNTP';
$string['auth_nonedescription'] = 'Os usuários podem criar contas válidas imediatamente, sem autenticação por servidor externo e sem nenhuma confirmação por correio. Tenha cuidado usando esta opção - pense nos problemas de segurança e administração que pode causar.';
$string['auth_nonetitle'] = 'Nenhuma autenticação';
$string['auth_pop3description'] = 'Este método usa um servidor POP3 para verificar se o nome de usuário e a senha são válidos.';
$string['auth_pop3host'] = 'Endereço do servidor POP3. Use o NÚMERO IP e não o nome DNS.';
$string['auth_pop3port'] = 'Porta do servidor  (normalmente 110)';
$string['auth_pop3title'] = 'Use um servidor POP3';
$string['auth_pop3type'] = 'Tipo de servidor. Se o seu servidor usar certificados de segurança, escolha pop3cert.';
$string['auth_user_create'] = 'Consentir criação de utente';
$string['auth_user_creation'] = 'Novos usuários (anônimos) podem criar contas de usuários no código de origem externa confirmados via Email. Se você ativar esta permissão lembre-se de configurar também as opções específicas dos módulos para a criação de usuários.';
$string['auth_usernameexists'] = 'O nome de usuário escolhido já existe. Por favor escolha um outro nome.';
$string['authenticationoptions'] = 'Opções de autenticação';
$string['authinstructions'] = 'Aqui você pode incluir instruções para os seus usuários, para que saibam qual nome de usuário e senha devem usar. Este texto vai aparecer na página de ingresso. Se deixar este campo em branco, não será dada nenhuma instrução.';
$string['changepassword'] = 'URL para modificar a senha';
$string['changepasswordhelp'] = 'Aqui você pode especificar um endereço onde os usuários podem recuperar ou modificar a senha e o nome de usuário esquecidos. Este será publicado como um botão na página de entrada e na página do usuário. Se deixar este espaço em branco o botão não aparecerá.';
$string['chooseauthmethod'] = 'Escolha um método de autenticação: ';
$string['guestloginbutton'] = 'Botão de entrada como visitante';
$string['instructions'] = 'Instruções';
$string['md5'] = 'codificação MD5';
$string['plaintext'] = 'Texto simples';
$string['showguestlogin'] = 'Você pode esconder ou mostrar o botão de entrada para visitantes na página de ingresso.';

?>
