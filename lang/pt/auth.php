<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004042600)


$string['auth_dbdescription'] = 'Este método usa uma tabela numa base de dados externa para verificar se um nome de utilizador e palavra chave são válidos. Se for uma conta nova, a informação de outros campos pode ser também transferida para Moodle.';
$string['auth_dbextrafields'] = '<p>Estes campos são optativos. Pode optar por preencher previamente alguns dos campos do utilizador em Moodle com informação dos campos da <b>base de dados externa</b> que especificar aqui.</p><p>Se deixar estes campos em branco, nada será transferido de LDAP e os valores por omissão do Moodle serão usados.</p><p>De qualquer forma o utilizador poderá editar todos estes campos mais tarde depois de entrar no servidor.';
$string['auth_dbfieldpass'] = 'Nome do campo que contem as palavras chave';
$string['auth_dbfielduser'] = 'Nome do campo que contem os nomes de utilizadores';
$string['auth_dbhost'] = 'Endereço IP do computador que hospeda a base de dados de utilizadores.';
$string['auth_dbname'] = 'Nome da própria base de dados';
$string['auth_dbpass'] = 'Palavra chave para o utilizador acima';
$string['auth_dbpasstype'] = 'Indique o modo que se está a usar no campo de palavra chave. A criptografia MD5 é útil para trabalhar com outras aplicações como PostNuke';
$string['auth_dbtable'] = 'Nome da tabela na base de dados';
$string['auth_dbtitle'] = 'Use uma base de dados externa';
$string['auth_dbtype'] = 'O tipo de base de dados (veja <a href=\'../lib/adodb/readme.htm#drivers\'>Documentação do ADOdb</a> para mais pormenores)';
$string['auth_dbuser'] = 'Nome de utilizador para aceder à base de dados';
$string['auth_emaildescription'] = 'Confirmação via correio electrónico é o método de autenticação padrão. Quando o utilizador se inscrever, após ter escolhido o nome de utilizador e palavra chave, será-lhe enviada uma mensagem de confirmação para o seu endereço de correio electrónico. Essa mensagem contem um apontador seguro para uma página onde o utilizador pode confirmar a sua conta. Quando o utilizador entrar no futuro o seu nome de utilizador e palavra chave serão conferidos na base de dados do Moodle.';
$string['auth_emailtitle'] = 'Autenticação baseada no correio electrónico';
$string['auth_imapdescription'] = 'Este método usa um servidor de IMAP para verificar se um nome de utilizador e palavra chave são válidos.';
$string['auth_imaphost'] = 'Endereço do servidor de IMAP. Use o NÚMERO IP e não o nome no DNS.';
$string['auth_imapport'] = 'Número da porta do servidor IMAP. Geralmente esta é 143 ou 993.';
$string['auth_imaptitle'] = 'Use um servidor IMAP';
$string['auth_imaptype'] = 'Tipo de servidor IMAP. Os servidores IMAP podem usar diferentes métodos de autenticação e negociação.';
$string['auth_ldap_bind_dn'] = 'Se quiser usar o bind-user para procurar utilizadores, especifique-o aqui. Algo como \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Palavra chave para o bind-user.';
$string['auth_ldap_contexts'] = 'Lista dos contextos onde os utilizadores são encontrados. Contextos diferentes separados com \';\'. Por exemplo: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Se permitir a criação de utilizadores com confirmação por email, especifique o contexto em que os utilizadores são criados. Este contexto deverá ser diferente do de outros utilizadores por medidas de segurança. Não é necessário adicionar este contexto à variável ldap_context, pois o Moodle irá, automáticamente, procurar utilizadores associados a este contexto.';
$string['auth_ldap_creators'] = 'Lista de grupos cujos membros tém permissões para criar novos cursos. Separe vários grupos com \';\'. Geralmente como \'cn=teacher,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Especifique o servidor de LDAP na forma de uma
URL completa, como \'ldap://ldap.myorg.com/\' ou \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_memberattribute'] = 'Especifica o atributo de utilizador membro, quando os utilizadores pertencem a um grupo. Geralmente \'member\'';
$string['auth_ldap_search_sub'] = 'Escreva &lt;&gt; 0; se quiser procurar utilizadores nos sub-contextos.';
$string['auth_ldap_update_userinfo'] = 'Actualizar informação de utilizador (nome, apelido, endereço...) de LDAP para Moodle. Para informação sobre a correspondência, consulte /auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = 'O atributo usado para nomear/procurar utilizadores. Geralmente \'cn\'.';
$string['auth_ldap_version'] = 'A versão do protocolo LDAP que o seu servidor estiver a usar.';
$string['auth_ldapdescription'] = 'Este método fornece autenticação usando um servidor de LDAP. Se o nome de utilizador e palavra chave forem válidos, Moodle cria um novo registo de utilizador na sua base de dados. Este módulo pode ler atributos do utilizador em LDAP e preencher os valores pedidos em Moodle. As seguintes vezes que o utilizador entrar, só serão verificados o nome de utilizador e palavra chave.';
$string['auth_ldapextrafields'] = '<p>Estes campos são optativos. Pode optar por obter a informação para alguns campos em Moodle a partir de informação dos <b>campos LDAP</b> que especificar aqui.</p><p>Se deixar estes campos em branco, nada será transferido de LDAP e os valores por omissão do Moodle serão usados.</p><p>De qualquer forma o utilizador poderá editar todos estes campos mais tarde depois de entrar no servidor.';
$string['auth_ldaptitle'] = 'Use um servidor LDAP';
$string['auth_manualdescription'] = 'Este método elimina qualquer hipótese de os utilizadores poderem criar as suas próprias contas. Todas as contas terão que ser manualmente criadas pelo administrador.';
$string['auth_manualtitle'] = 'Apenas contas manuais';
$string['auth_multiplehosts'] = 'Podem ser especificados vários servidores (p.e. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Este método usa um servidor NNTP para verificar se um nome de utilizador e palavra chave são válidos.';
$string['auth_nntphost'] = 'Endereço do servidor NNTP. Use o NÚMERO IP e não o nome no DNS.';
$string['auth_nntpport'] = 'Porta do servidor NNTP (normalmente 119)';
$string['auth_nntptitle'] = 'Use um servidor NNTP';
$string['auth_nonedescription'] = 'Os utilizadores podem registrar-se e criar contas válidas imediatamente, sem autenticação em nenhum servidor externo e sem nenhuma confirmação por correio. Tenha cuidado se usar esta opção - pense nos problemas de segurança e administração que isto poderia causar.';
$string['auth_nonetitle'] = 'Nenhuma autenticação';
$string['auth_pop3description'] = 'Este método usa um servidor POP3 para verificar se um nome de utilizador e palavra chave são válidos.';
$string['auth_pop3host'] = 'Endereço do servidor POP3. Use o NÚMERO IP e não o nome no DNS.';
$string['auth_pop3port'] = 'Porta do servidor POP3 (normalmente 110)';
$string['auth_pop3title'] = 'Use um servidor POP3';
$string['auth_pop3type'] = 'Tipo de servidor. Se o seu servidor usar certificados de segurança, escolha pop3cert.';
$string['auth_user_create'] = 'Permitir a criação de utilizadores';
$string['auth_user_creation'] = 'Novos (anonimos) utilizadores podem criar contas de autenticação externa confirmadas por email. Se activar esta opção, lembre-se de configurar as opções no módulo específico para criação de utilizadores.';
$string['auth_usernameexists'] = 'O nome escolhido já existe. Escolha outro.';
$string['authenticationoptions'] = 'Opções de autenticação';
$string['authinstructions'] = 'Aqui pode incluir instruções para os seus utilizadores, para que saibam que tipo de nome de utilizador e palavra chave deverão usar. O texto que escreva aqui aparecerá na página de entrada. Se deixar este campo em branco, não será dadas nenhumas instruções.';
$string['changepassword'] = 'Mude o endereço da palavra chave';
$string['changepasswordhelp'] = 'Aqui pode especificar um local onde os utilizadores podem recuperar ou alterar a sua palavra chave e nome de usuário caso se esqueçam dela. Isto será fornecido aos utilizadores como um botão na página de entrada a servidor e na sua página de utilizador. Se deixar este espaço em branco o botão não aparecerá.';
$string['chooseauthmethod'] = 'Escolha um método de autenticação: ';
$string['guestloginbutton'] = 'Botão de entrada como visitante';
$string['instructions'] = 'Instruções';
$string['md5'] = 'Criptografia MD5';
$string['plaintext'] = 'Texto simples';
$string['showguestlogin'] = 'Pode optar por esconder ou mostrar o botão de entrada para visitantes na página de entrada.';

?>
