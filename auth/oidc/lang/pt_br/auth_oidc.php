<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Portuguese - Brazil language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'O plugin OpenID Connect oferece o recurso de logon único usando provedores de identidade que podem ser configurados.';
$string['cfg_authendpoint_key'] = 'Ponto de extremidade de autorização';
$string['cfg_authendpoint_desc'] = 'O URI do ponto de extremidade de autorização do seu provedor de identidade a ser usado.';
$string['cfg_autoappend_key'] = 'Acrescentar automaticamente';
$string['cfg_autoappend_desc'] = 'Acrescente essa cadeia de caracteres automaticamente ao efetuar o login de usuários utilizando o fluxo de login com nome de usuário e senha. Isso é útil quando seu provedor de identidade exige um domínio comum, mas não quer exigir que os usuários o digitem ao fazer login. Por exemplo, se o usuário completo do OpenID Connect for "joao@exemplo.com" e você inserir "@exemplo.com" aqui, o usuário só precisará inserir "joao" como nome de usuário. <br /><b>Observação:</b> caso exista conflito entre nomes de usuários, ou seja, exista um usuário do Moodle com o mesmo nome, a prioridade do plugin de autenticação é usada para determinar qual usuário prevalecerá.';
$string['cfg_clientid_key'] = 'ID do cliente';
$string['cfg_clientid_desc'] = 'Seu ID do cliente registrado no provedor de identidade.';
$string['cfg_clientsecret_key'] = 'Segredo do cliente';
$string['cfg_clientsecret_desc'] = 'Seu segredo do cliente registrado no provedor de identidade. Em alguns provedores, ele também é chamado de chave.';
$string['cfg_err_invalidauthendpoint'] = 'Ponto de extremidade de autorização inválido';
$string['cfg_err_invalidtokenendpoint'] = 'Ponto de extremidade de token inválido';
$string['cfg_err_invalidclientid'] = 'ID do cliente inválido';
$string['cfg_err_invalidclientsecret'] = 'Segredo do cliente inválido';
$string['cfg_icon_key'] = 'Ícone';
$string['cfg_icon_desc'] = 'Um ícone a ser exibido ao lado do nome do provedor na página de login.';
$string['cfg_iconalt_o365'] = 'Ícone do Microsoft 365';
$string['cfg_iconalt_locked'] = 'Ícone de bloqueado';
$string['cfg_iconalt_lock'] = 'Ícone de bloqueio';
$string['cfg_iconalt_go'] = 'Círculo verde';
$string['cfg_iconalt_stop'] = 'Círculo vermelho';
$string['cfg_iconalt_user'] = 'Ícone do usuário';
$string['cfg_iconalt_user2'] = 'Ícone alternativo do usuário';
$string['cfg_iconalt_key'] = 'Ícone de chave';
$string['cfg_iconalt_group'] = 'Ícone do grupo';
$string['cfg_iconalt_group2'] = 'Ícone alternativo do grupo';
$string['cfg_iconalt_mnet'] = 'Ícone da MNET';
$string['cfg_iconalt_userlock'] = 'Ícone de usuário com bloqueio';
$string['cfg_iconalt_plus'] = 'Ícone de sinal de adição';
$string['cfg_iconalt_check'] = 'Ícone de marca de seleção';
$string['cfg_iconalt_rightarrow'] = 'Ícone de seta para a direita';
$string['cfg_customicon_key'] = 'Ícone personalizado';
$string['cfg_customicon_desc'] = 'Se você quiser usar seu próprio ícone, faça o upload dele aqui. Isso substituirá qualquer ícone escolhido acima. <br /><br /><b>Observações sobre o uso de ícones personalizados:</b><ul><li>Essa imagem <b>não</b> será redimensionada na página de login; portanto, recomendamos o upload de uma imagem de, no máximo, 35x35 pixels.</li><li>Caso você tenha feito o upload de um ícone personalizado e queira voltar a usar um dos ícones padrão, clique no ícone personalizado na caixa acima, em "Excluir", em "OK" e depois clique em "Salvar alterações" na parte inferior deste formulário. Agora o ícone padrão selecionado será exibido na página de login do Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Registrar mensagens de depuração';
$string['cfg_debugmode_desc'] = 'Se essa configuração estiver ativada, informações que podem ajudar a identificar problemas serão registradas no log do Moodle.';
$string['cfg_loginflow_key'] = 'Fluxo de login';
$string['cfg_loginflow_authcode'] = 'Solicitação de autorização';
$string['cfg_loginflow_authcode_desc'] = 'Ao usar esse fluxo, o usuário clicará no nome do provedor de identidade (consulte "Nome do provedor" acima) na página de login do Moodle e será redirecionado para o provedor para fazer login. Depois de efetuar com sucesso o login, o usuário será redirecionado de volta para o Moodle, onde o login ocorrerá de modo transparente. Essa é a maneira mais padronizada e segura de realizar o login do usuário.';
$string['cfg_loginflow_rocreds'] = 'Autenticação de nome de usuário e senha';
$string['cfg_loginflow_rocreds_desc'] = 'Ao usar esse fluxo, o usuário informará seu nome de usuário e sua senha no formulário de login do Moodle da mesma forma que faria em um login manual. As credenciais serão, então, transmitidas em segundo plano para o provedor de identidade no intuito de obter a autenticação. Esse fluxo é o mais simples para o usuário, pois ele não interage diretamente com o provedor de identidade. Tenha em mente que nem todos os provedores de identidade aceitam a utilização desse fluxo.';
$string['cfg_oidcresource_key'] = 'Recurso';
$string['cfg_oidcresource_desc'] = 'O recurso do OpenID Connect para o qual a solicitação deverá ser enviada.';
$string['cfg_oidcscope_key'] = 'Escopo';
$string['cfg_oidcscope_desc'] = 'O escopo do OIDC a ser usado.';
$string['cfg_opname_key'] = 'Nome do provedor';
$string['cfg_opname_desc'] = 'Esse é um rótulo visível para o usuário que identifica o tipo de credenciais que devem ser utilizadas pelo usuário no login. Esse rótulo é usado em todas as partes visíveis para o usuário deste plugin para a identificação do seu provedor.';
$string['cfg_redirecturi_key'] = 'URI de redirecionamento';
$string['cfg_redirecturi_desc'] = 'Esse é o URI a ser registrado como o "URI de redirecionamento". Seu provedor de identidade do OpenID Connect deve solicitá-lo ao registrar o Moodle como cliente. <br /><b>OBSERVAÇÃO:</b> é necessário inserir essa informação no seu provedor do OpenID Connect EXATAMENTE como ela é exibida aqui. Qualquer diferença impedirá que logins sejam efetuados usando o OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Ponto de extremidade de token';
$string['cfg_tokenendpoint_desc'] = 'O URI do ponto de extremidade de token do seu provedor de identidade a ser usado.';
$string['event_debug'] = 'Mensagem de depuração';
$string['errorauthdisconnectemptypassword'] = 'A senha não pode ficar em branco';
$string['errorauthdisconnectemptyusername'] = 'O nome de usuário não pode ficar em branco';
$string['errorauthdisconnectusernameexists'] = 'Esse nome de usuário já está em uso. Escolha outro nome.';
$string['errorauthdisconnectnewmethod'] = 'Usar método de login';
$string['errorauthdisconnectinvalidmethod'] = 'Método de login inválido recebido.';
$string['errorauthdisconnectifmanual'] = 'Se estiver usando o método de login manual, insira as credenciais abaixo.';
$string['errorauthinvalididtoken'] = 'id_token inválido recebido.';
$string['errorauthloginfailednouser'] = 'Login inválido: usuário não encontrado no Moodle.';
$string['errorauthnoauthcode'] = 'Código de autorização não recebido.';
$string['errorauthnocreds'] = 'Configure as credenciais de cliente do OpenID Connect.';
$string['errorauthnoendpoints'] = 'Configure os pontos de extremidade de servidor do OpenID Connect.';
$string['errorauthnohttpclient'] = 'Defina um cliente de HTTP.';
$string['errorauthnoidtoken'] = 'O id_token do OpenID Connect não foi recebido.';
$string['errorauthunknownstate'] = 'Estado desconhecido.';
$string['errorauthuseralreadyconnected'] = 'Você já está conectado a um usuário diferente do OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'O usuário do OpenID Connect que realizou a autenticação já está conectado a um usuário do Moodle.';
$string['errorbadloginflow'] = 'Fluxo de login inválido especificado. Observação: se você recebeu esta mensagem após uma instalação ou atualização recente, limpe seu cache do Moodle.';
$string['errorjwtbadpayload'] = 'Não foi possível ler o conteúdo de JWT.';
$string['errorjwtcouldnotreadheader'] = 'Não foi possível ler o cabeçalho de JWT.';
$string['errorjwtempty'] = 'Cadeia de caracteres vazia ou inválida de JWT recebida.';
$string['errorjwtinvalidheader'] = 'Cabeçalho de JWT inválido';
$string['errorjwtmalformed'] = 'JWT malformado recebido.';
$string['errorjwtunsupportedalg'] = 'JWS Alg ou JWE não compatível';
$string['erroroidcnotenabled'] = 'O plugin de autenticação do OpenID Connect não está ativado.';
$string['errornodisconnectionauthmethod'] = 'Não é possível se desconectar, pois não há plugin de autenticação ativado ao qual retornar (o método de login anterior do usuário ou o método de login manual).';
$string['erroroidcclientinvalidendpoint'] = 'URI de ponto de extremidade inválido recebido.';
$string['erroroidcclientnocreds'] = 'Defina as credenciais de cliente com setcreds';
$string['erroroidcclientnoauthendpoint'] = 'Nenhum ponto de extremidade de autorização definido. Defina-o com $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'Nenhum ponto de extremidade de token definido. Defina-o com $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'Para isso, é necessário que o ponto de extremidade de token esteja usando SSL/TLS.';
$string['errorucpinvalidaction'] = 'Ação inválida recebida.';
$string['erroroidccall'] = 'Erro no OpenID Connect. Verifique os logs para obter mais informações.';
$string['erroroidccall_message'] = 'Erro no OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Usuário autorizado com o OpenID Connect';
$string['eventusercreated'] = 'Usuário criado com o OpenID Connect';
$string['eventuserconnected'] = 'Usuário conectado ao OpenID Connect';
$string['eventuserloggedin'] = 'Usuário com login efetuado no OpenID Connect';
$string['eventuserdisconnected'] = 'Usuário desconectado do OpenID Connect';
$string['oidc:manageconnection'] = 'Gerenciar conexão ao OpenID Connect';
$string['ucp_general_intro'] = 'Aqui você pode gerenciar sua conexão ao {$a}. Se essa configuração estiver ativada, você poderá usar sua conta do {$a} para fazer login no Moodle em vez de precisar de nome de usuário e senha separados. Depois que estiver conectado, você não precisará mais se lembrar de um nome de usuário e uma senha para o Moodle, pois todos os logins serão administrados pelo {$a}.';
$string['ucp_login_start'] = 'Começar a usar o {$a} para fazer login no Moodle';
$string['ucp_login_start_desc'] = 'Essa configuração fará uma alteração na sua conta, que passará a usar o {$a} para fazer login no Moodle. Depois de ativada, você fará login usando suas credenciais do {$a}; seu nome de usuário e sua senha do Moodle não serão aceitos. Você pode desconectar sua conta quando quiser e voltar a fazer login como antes.';
$string['ucp_login_stop'] = 'Parar de usar o {$a} para fazer login no Moodle';
$string['ucp_login_stop_desc'] = 'No momento, você está usando o {$a} para fazer login no Moodle. Ao clicar em "Para de usar o login do {$a}", você desconectará sua conta do Moodle do {$a}. Você não poderá mais fazer login no Moodle com sua conta do {$a} e precisará criar um nome de usuário e uma senha para poder fazer login diretamente no Moodle.';
$string['ucp_login_status'] = 'O login via {$a} está:';
$string['ucp_status_enabled'] = 'Ativado';
$string['ucp_status_disabled'] = 'Desativado';
$string['ucp_disconnect_title'] = 'Desconexão do {$a}';
$string['ucp_disconnect_details'] = 'Essa ação desconectará sua conta do Moodle do {$a}. Você precisará criar um nome de usuário e uma senha para fazer login no Moodle.';
$string['ucp_title'] = 'Gerenciamento do {$a}';
