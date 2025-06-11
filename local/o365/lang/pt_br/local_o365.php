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
 * Portuguese language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment

$string['pluginname'] = 'Integração ao Microsoft 365';
$string['acp_title'] = 'Painel de controle de administração do Microsoft 365';
$string['acp_healthcheck'] = 'Verificação de integridade';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Site para dados compartilhados de curso do Moodle.';
$string['calendar_user'] = 'Calendário pessoal (do usuário)';
$string['calendar_site'] = 'Calendário do site';
$string['erroracpauthoidcnotconfig'] = 'Defina antes as credenciais do aplicativo em auth_oidc.';
$string['erroracplocalo365notconfig'] = 'Configure antes local_o365.';
$string['errorhttpclientbadtempfileloc'] = 'Não foi possível abrir o local temporário para armazenar o arquivo.';
$string['errorhttpclientnofileinput'] = 'Nenhum parâmetro de arquivo em httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Não foi possível atualizar o token';
$string['erroro365apibadcall'] = 'Erro na chamada da API.';
$string['erroro365apibadcall_message'] = 'Erro na chamada da API: {$a}';
$string['erroro365apibadpermission'] = 'Permissão não encontrada.';
$string['erroro365apicouldnotcreatesite'] = 'Problema ao criar o site.';
$string['erroro365apicoursenotfound'] = 'Curso não encontrado.';
$string['erroro365apiinvalidtoken'] = 'Token inválido ou expirado.';
$string['erroro365apiinvalidmethod'] = 'httpmethod inválido transmitido para apicall';
$string['erroro365apinoparentinfo'] = 'Não foi possível encontrar informações da pasta pai';
$string['erroro365apinotimplemented'] = 'Isso deve ser substituído.';
$string['erroro365apinotoken'] = 'Não possuía um token disponível para o recurso e o usuário especificados e não foi possível obter um. O token de atualização do usuário está expirado?';
$string['erroro365apisiteexistsnolocal'] = 'O site já existe, mas não foi possível encontrar o registro local.';
$string['eventapifail'] = 'Falha na API';
$string['eventcalendarsubscribed'] = 'O usuário se inscreveu em um calendário';
$string['eventcalendarunsubscribed'] = 'O usuário cancelou a inscrição em um calendário';
$string['healthcheck_fixlink'] = 'Clique aqui para corrigir.';
$string['settings_usersync'] = 'Sincronize usuários com o Microsoft Entra ID';
$string['settings_usersync_details'] = 'Quando essa configuração estiver ativada, os usuários do Moodle e do AD do Azure serão sincronizados de acordo com as opções acima.<br /><br /><b>Observação: </b>o trabalho de sincronização é executado no cron do Moodle. Por padrão, ele é executado uma vez ao dia, à 1h no fuso horário local do seu servidor. Para sincronizar grandes conjuntos de usuários com mais agilidade, você pode aumentar a frequência da tarefa <b>Sincronizar usuários com o AD do Azure</b> usando a <a href="{$a}">página de gerenciamento de tarefas agendadas.</a><br /><br />Para obter instruções mais detalhadas, consulte a <a href="https://docs.moodle.org/30/en/Office365#User_sync">documentação de sincronização de usuários</a><br /><br />';
$string['settings_usersync_create'] = 'Criar contas no Moodle para usuários no AD do Azure';
$string['settings_usersync_delete'] = 'Excluir contas sincronizadas anteriormente no Moodle quando elas forem excluídas do AD do Azure';
$string['settings_usersync_match'] = 'Associar usuários pré-existentes do Moodle a contas de mesmo nome no AD do Azure<br /><small>Essa opção verificará o nome de usuário no Microsoft 365 e o nome de usuário no Moodle e tentará encontrar correspondências. As correspondências não diferenciam letras maiúsculas de minúsculas e ignoram o locatário do Microsoft 365. Por exemplo, o nome de usuário CaRlOs.SilVa do Moodle seria considerado correspondente a carlos.silva@exemplo.onmicrosoft.com. Os usuários com correspondências identificadas terão suas contas do Moodle e do Microsoft 365 conectadas e poderão usar todos os recursos da integração entre Microsoft 365 e Moodle. O método de autenticação dos usuários não será alterado, a menos que a configuração abaixo seja ativada.</small>';
$string['settings_usersync_matchswitchauth'] = 'Alterar a autenticação dos usuários associados para o Microsoft 365 (OpenID Connect)<br /><small>Essa opção exige que a configuração de correspondência acima esteja ativada. Quando for realizada a correspondência de um usuário, a ativação dessa configuração alterará o método de autenticação dele para o OpenID Connect. Com isso, o usuário poderá fazer login no Moodle com as credenciais do Microsoft 365. <b>Observação:</b> se você quiser usar essa configuração, certifique-se de que o plugin de autenticação do OpenID Connect esteja ativado.</small>';
$string['settings_entratenant'] = 'Locatário do AD do Azure';
$string['settings_entratenant_details'] = 'Usado para identificar sua organização no AD do Azure. Por exemplo: "contoso.onmicrosoft.com"';
$string['settings_verifysetup'] = 'Configuração do Azure';
$string['settings_verifysetup_details'] = 'Essa ferramenta verifica se tudo está configurado corretamente no Azure. Ela também pode corrigir alguns erros comuns.';
$string['settings_verifysetup_update'] = 'Atualizar';
$string['settings_verifysetup_checking'] = 'Verificando...';
$string['settings_verifysetup_missingperms'] = 'Permissões não encontradas:';
$string['settings_verifysetup_permscorrect'] = 'As permissões estão corretas.';
$string['settings_verifysetup_errorcheck'] = 'Ocorreu um erro ao tentar verificar a configuração do Azure.';
$string['settings_verifysetup_unifiedheader'] = 'API unificada';
$string['settings_verifysetup_unifieddesc'] = 'A API unificada substitui as atuais APIs específicas de aplicativos. Se ela estiver disponível, recomendamos que você a adicione a seu aplicativo Azure para se preparar para o futuro. Futuramente, ela substituirá a API legada.';
$string['settings_verifysetup_unifiederror'] = 'Houve um erro ao verificar se há suporte para a API unificada.';
$string['settings_verifysetup_unifiedactive'] = 'API unificada ativa.';
$string['settings_verifysetup_unifiedmissing'] = 'A API unificada não foi encontrada neste aplicativo.';
$string['settings_creategroups'] = 'Criar grupos de usuários';
$string['settings_creategroups_details'] = 'Se essa opção estiver ativada, um grupo de professores e alunos no Microsoft 365 será criado e mantido para cada curso do site. Serão criados todos os grupos necessários a cada execução do cron (além da adição de todos os membros atuais). Depois disso, a associação a grupos será mantida à medida que os usuários se inscreverem ou cancelarem a inscrição em cursos do Moodle.<br /><b>Observação: </b>esse recurso exige a API unificada do Microsoft 365 incluída no aplicativo adicionado ao Azure. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Instruções de configuração e documentação</a> (em inglês).';
$string['settings_o365china'] = 'Microsoft 365 para a China';
$string['settings_o365china_details'] = 'Marque essa opção se você estiver usando a versão do Microsoft 365 para a China.';
$string['settings_debugmode'] = 'Registrar mensagens de depuração';
$string['settings_debugmode_details'] = 'Se essa configuração estiver ativada, informações que podem ajudar a identificar problemas serão registradas no log do Moodle.';
$string['settings_detectoidc'] = 'Credenciais do aplicativo';
$string['settings_detectoidc_details'] = 'Para se comunicar com o Microsoft 365, o Moodle precisa de credenciais de identificação, que são definidas no plugin de autenticação "OpenID Connect".';
$string['settings_detectoidc_credsvalid'] = 'As credenciais foram definidas.';
$string['settings_detectoidc_credsvalid_link'] = 'Alterar';
$string['settings_detectoidc_credsinvalid'] = 'As credenciais não foram definidas ou estão incompletas.';
$string['settings_detectoidc_credsinvalid_link'] = 'Definir credenciais';
$string['settings_detectperms'] = 'Permissões do aplicativo';
$string['settings_detectperms_details'] = 'Para usar os recursos do plugin, é necessário configurar as permissões corretas para o aplicativo no AD do Azure.';
$string['settings_detectperms_nocreds'] = 'Antes é necessário definir as credenciais do aplicativo. Veja a configuração acima.';
$string['settings_detectperms_missing'] = 'Não encontrado:';
$string['settings_detectperms_errorfix'] = 'Ocorreu um erro ao tentar corrigir as permissões. Configure-as manualmente no Azure.';
$string['settings_detectperms_fixperms'] = 'Corrigir permissões';
$string['settings_detectperms_nounified'] = 'A API unificada não está presente. É possível que alguns recursos novos não funcionem.';
$string['settings_detectperms_unifiednomissing'] = 'Todas as permissões unificadas estão presentes.';
$string['settings_detectperms_update'] = 'Atualizar';
$string['settings_detectperms_valid'] = 'As permissões foram configuradas.';
$string['settings_detectperms_invalid'] = 'Verificar permissões no AD do Azure';
$string['settings_header_setup'] = 'Configuração';
$string['settings_header_options'] = 'Opções';
$string['settings_healthcheck'] = 'Verificação de integridade';
$string['settings_healthcheck_details'] = 'Geralmente, quando algo não está funcionando corretamente, a execução da verificação de integridade pode identificar o problema e sugerir soluções';
$string['settings_healthcheck_linktext'] = 'Executar verificação de integridade';
$string['settings_odburl'] = 'URL do OneDrive for Business';
$string['settings_odburl_details'] = 'O URL usado para acessar o OneDrive for Business. Geralmente, ele pode ser determinado pelo seu locatário do AD do Azure. Por exemplo, se seu locatário do AD do Azure for "contoso.onmicrosoft.com", o URL provavelmente será "contoso-my.sharepoint.com". Insira apenas o nome do domínio e não inclua http:// ou https://';
$string['settings_serviceresourceabstract_valid'] = '{$a} pode ser utilizado.';
$string['settings_serviceresourceabstract_invalid'] = 'Parece que não é possível utilizar esse valor.';
$string['settings_serviceresourceabstract_nocreds'] = 'Antes defina as credenciais do aplicativo.';
$string['settings_serviceresourceabstract_empty'] = 'Insira um valor ou clique em "Detectar" para tentar detectar o valor correto.';
$string['spsite_group_contributors_name'] = '{$a} colaboradores';
$string['spsite_group_contributors_desc'] = 'Todos os usuários que têm acesso para gerenciar arquivos do curso {$a}';
$string['task_calendarsyncin'] = 'Sincronizar eventos do o365 com o Moodle';
$string['task_coursesync'] = 'Criar grupos de usuários no Microsoft 365';
$string['task_syncusers'] = 'Sincronizar usuários com o AD do Azure';
$string['ucp_connectionstatus'] = 'Status da conexão';
$string['ucp_calsync_availcal'] = 'Calendários do Moodle disponíveis';
$string['ucp_calsync_title'] = 'Sincronização com calendário do Outlook';
$string['ucp_calsync_desc'] = 'Os calendários marcados serão sincronizados a partir do Moodle com seu calendário do Outlook.';
$string['ucp_connection_status'] = 'A conexão ao Microsoft 365 está:';
$string['ucp_connection_start'] = 'Conectar-se ao Microsoft 365';
$string['ucp_connection_stop'] = 'Desconectar-se do Microsoft 365';
$string['ucp_features'] = 'Recursos do Microsoft 365';
$string['ucp_features_intro'] = 'Veja abaixo uma lista com os recursos que podem ser usados para aprimorar o Moodle com o Microsoft 365.';
$string['ucp_features_intro_notconnected'] = 'Alguns desses recursos podem ficar indisponíveis até que você se conecte ao Microsoft 365.';
$string['ucp_general_intro'] = 'Aqui você pode gerenciar sua conexão ao Microsoft 365.';
$string['ucp_index_entraidlogin_title'] = 'Login no Microsoft 365';
$string['ucp_index_entraidlogin_desc'] = 'Você pode usar suas credenciais do Microsoft 365 para fazer login no Moodle. ';
$string['ucp_index_calendar_title'] = 'Sincronização com calendário do Outlook';
$string['ucp_index_calendar_desc'] = 'Aqui você pode configurar a sincronização entre seus calendários do Moodle e do Outlook. Você pode exportar eventos de calendários do Moodle para o Outlook ou adicionar eventos do Outlook ao Moodle.';
$string['ucp_index_connectionstatus_connected'] = 'Você está conectado ao Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'Sua conta foi associada ao usuário <small>"{$a}"</small> do Microsoft 365. Para concluir essa conexão, clique no link abaixo e faça login no Microsoft 365.';
$string['ucp_index_connectionstatus_notconnected'] = 'Você não está conectado ao Microsoft 365';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'A integração ao OneNote permite que você use o Microsoft 365 OneNote com o Moodle. Você pode concluir tarefas usando o OneNote e, com facilidade, fazer anotações sobre seus cursos.';
$string['ucp_notconnected'] = 'Conecte-se ao Microsoft 365 antes de acessar esta página.';
$string['settings_onenote'] = 'Desativar o Microsoft 365 OneNote';
$string['ucp_status_enabled'] = 'Ativo';
$string['ucp_status_disabled'] = 'Não conectado';
$string['ucp_syncwith_title'] = 'Sincronizar com:';
$string['ucp_syncdir_title'] = 'Comportamento de sincronização:';
$string['ucp_syncdir_out'] = 'Do Moodle para o Outlook';
$string['ucp_syncdir_in'] = 'Do Outlook para o Moodle';
$string['ucp_syncdir_both'] = 'Atualizar tanto o Outlook quanto o Moodle';
$string['ucp_title'] = 'Painel de controle do Microsoft 365/Moodle';
$string['ucp_options'] = 'Opções';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:enable moodle.Files.LangFilesOrdering.UnexpectedComment
