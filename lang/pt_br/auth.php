<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 beta (2002111900)


$string['auth_dbdescription'] = "Este método usa uma tabela externa da base de dados para verificar se um nome de usuário e uma senha sejam válidos.";
$string['auth_dbfieldpass'] = "Nome do campo que contem as senhas";
$string['auth_dbfielduser'] = "Nome do campo que contem os nomes de usuários";
$string['auth_dbhost'] = "Endereço IP do computador que hospeda o usuário da base de dados.";
$string['auth_dbname'] = "Nome da própria base de dados";
$string['auth_dbpass'] = "Senha que combina com o nome de usuário acima";
$string['auth_dbtable'] = "Nome da tabela na base de dados";
$string['auth_dbtitle'] = "Use uma base de dados externa";
$string['auth_dbtype'] = "O tipo da base de dados (veja <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A > para maiores detalhes)";
$string['auth_dbuser'] = "Nome de usuário com acesso à base de dados";
$string['auth_emaildescription'] = "Confirmação de e-mail é o método de autenticação padão.  Quando o usuário se inscrever, enquanto escolhendo seu próprio nome de usuário e senha, um e-mail de confirmação é enviado ao endereço de e-mail do usuário.  Este e-mail contém uma ligação segura a uma página onde o usuário pode confirmar sua conta.";
$string['auth_emailtitle'] = "Atutenticação baseada em e-mail";
$string['auth_imapdescription'] = "Este método usa um usuário do IMAP para verificar se um nome de usuário e uma senha sejam válidos.";
$string['auth_imaphost'] = "O endereço do usuário do IMAP.  Use o NÚMERO IP e não o nome do DNS.";
$string['auth_imapport'] = "Número da porta de usuário do IMAP.  Geralmente este é 143 ou 993.";
$string['auth_imaptitle'] = "Use um servidor IMAP";
$string['auth_imaptype'] = "O tipo do usuário do IMAP.  Veja a página da ajuda (acima) para mais detalhes.";
$string['auth_ldap_bind_dn'] = "Se você quiser usar o bind-user para procurar usuários, especifique-o aqui.  De preferência 'do cn=ldapuser, ou=public, o=org'";
$string['auth_ldap_bind_pw'] = "Senha para o bind-user.";
$string['auth_ldap_contexts'] = "Lista dos contextos onde os usuários são encontrados.  Contextos diferentes separados com ';'.  Para o exemplo:  'ou=users, o=org;  ou=others, o=org'";
$string['auth_ldap_host_url'] = "Especifique o servidor de hospedagem do LDAP no formuçário de endereço como 'ldap://ldap.myorg.com/' ou 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Ponha o &lt;&gt; do valor;  0 se você preferir para procurar usuários dos sub-contextos.";
$string['auth_ldap_update_userinfo'] = "Informação de atualização do usuário(primeiro nome, sobrenome, endereço.) de LDAP a Moodle.  Verifique /auth/ldap/attr_mappings.php traçando a informação";
$string['auth_ldap_user_attribute'] = "O atributo usado para nomear e procurar usuários.  Geralmente 'cn'.";
$string['auth_ldapdescription'] = "Este método fornece a autenticção de encontro a um usuário externo de LDAP.  Se o nome de usuário e a senha forem válidos, Moodle cría uma entrada de usuário nova em sua base de dados.  Este módulo pode ler atributos do usuário de LDAP e prefil nos campos criados no Moodle.  Para ver se há inícios de uma sessão somente o nome de usuário e a senha são verificados.";
$string['auth_ldaptitle'] = "Use um servidor LDAP";
$string['auth_nntpdescription'] = "Este método usa um usuário do NNTP para verificar se um nome de usuário e uma senha sejam válidos.";
$string['auth_nntphost'] = "O endereço do usuário do NNTP.  Use o NÚMERO IP e não o nome do DNS.";
$string['auth_nntpport'] = "Porta de usuário (119 é o mais comum)";
$string['auth_nntptitle'] = "Use um servidor NNTP";
$string['auth_nonedescription'] = "Os usuários podem se registrar e podem criar contas válidas imediatamente, sem autenticação de um servidor externo e nenhuma confirmação por e-mail.  Tenha cuidado que usa esta opção - pense na segurança e problemas de administração que isto poderia causar.";
$string['auth_nonetitle'] = "Nenhuma autenticação";
$string['auth_pop3description'] = "Este método usa um usuário POP3 verificar se um nome de usuário e uma senha sejam válidos.";
$string['auth_pop3host'] = "O endereço do servidor POP3.  Use o NÚMERO IP e não o nome do DNS.";
$string['auth_pop3port'] = "Porta de usuário (110 é o mais comum)";
$string['auth_pop3title'] = "Use um servidor POP3";
$string['auth_pop3type'] = "Tipo do usuário.  Se seu usuário usar a segurança do certificado, escolha pop3cert.";
$string['authenticationoptions'] = "Opções de autenticação";
$string['authinstructions'] = "Aqui você pode prover instruções para seus usuários, assim eles sabem qual username e contra-senha que eles deveriam estar usando.  O texto no que você entra aqui se aparecerá na página de login.  Se você deixa este espaço em branco então que nenhuma instrução será imprimida.";
$string['chooseauthmethod'] = "Escolha um método de autenticação: ";
$string['showguestlogin'] = "Você pode esconder ou pode mostrar o botao login de convidado na página de login.";
$string['auth_dbextrafields'] = "Estes campos são opcionais.  Você pode escolher preencher algum dos campos com informação de usuário nos camposda base de dados </B> especifique aqui.<P> se você deixar estes em branco, manterão o formato padrão. <P> em um ou outro caso, o usuário poderá editar todos estes campos depois de confirmada a entrada.";
$string['instructions'] = "Intruções";
$string['auth_ldapextrafields'] = "Estes campos são opcionais.  Você pode escolher preenchar algum com informação do usuário Moodle <B> nos campos de LDAP </B> esse você especifica aqui.  <P> se você deixar o espaço em branco, nada será transferido então de LDAP e os padrões do Moodle serão usados. <P> em um ou outro caso, o usuário poderá  editar todos estes campos depois de confirmada sua entrada.";
$string['guestloginbutton'] = "Botão de entrada como visitante";
$string['changepassword'] = "Mude o endereço da senha";
$string['changepasswordhelp'] = "Aqui você pode especificar um local em que seus usuários podem recuperar ou mudar sua senha do nome de usuário se esquecerem.  Isto será fornecido aos usuários como um endereço alternativo na página do início de uma sessão.  Se você deixar este espaço em branco o endereço não aparecerá.";
$string['auth_dbpasstype'] = "Especifique o formato que o campo da senha está usando.  O encriptação MD5 é útil para conectar a outras aplicações comuns da aplicações como PostNuke";
$string['md5'] = "Encriptação MD5";
$string['plaintext'] = "Simples texto";
?>
