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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'URL de início';
$string['adminurldesc'] = 'O URL de início de LTI usada para acessar o relatório de acessibilidade.';
$string['allyclientconfig'] = 'Configuração do Ally';
$string['ally:clientconfig'] = 'Acessar e atualizar a configuração do cliente';
$string['ally:viewlogs'] = 'Visualizador de registros do Ally';
$string['clientid'] = 'Código do cliente';
$string['clientiddesc'] = 'O código do cliente do Ally';
$string['code'] = 'Código';
$string['contentauthors'] = 'Autores do conteúdo';
$string['contentauthorsdesc'] = 'Os administradores e os usuários atribuídos a essas funções selecionadas terão seus arquivos de curso carregados avaliados quanto à acessibilidade. Os arquivos recebem uma classificação de acessibilidade. Classificações baixas significam que o arquivo precisa de alterações para ser mais acessível.';
$string['contentupdatestask'] = 'Tarefa de atualizações de conteúdos';
$string['curlerror'] = 'Erro de cURL: {$a}';
$string['curlinvalidhttpcode'] = 'Código de status HTTP inválido: {$a}';
$string['curlnohttpcode'] = 'Não é possível verificar o código de status HTTP';
$string['error:invalidcomponentident'] = 'Identificador de componente inválido {$a}';
$string['error:pluginfilequestiononly'] = 'Somente componentes de questões são suportados para este URL';
$string['error:componentcontentnotfound'] = 'Conteúdo não encontrado para {$a}';
$string['error:wstokenmissing'] = 'Está faltando a chave de serviço da web. Talvez um usuário administrador precise executar a configuração automática?';
$string['excludeunused'] = 'Excluir arquivos não utilizados';
$string['excludeunuseddesc'] = 'Omitir arquivos anexados ao conteúdo HTML, mas vinculados ou que referenciam no HTML.';
$string['filecoursenotfound'] = 'O arquivo transmitido não pertence a nenhum curso';
$string['fileupdatestask'] = 'Enviar atualizações de arquivos para o Ally';
$string['id'] = 'Código';
$string['key'] = 'Chave';
$string['keydesc'] = 'A chave do consumidor LTI.';
$string['level'] = 'Nível';
$string['message'] = 'Mensagem';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL de atualizações de arquivos';
$string['pushurldesc'] = 'Notificações push sobre atualizações de arquivos para este URL.';
$string['queuesendmessagesfailure'] = 'Ocorreu um erro ao enviar mensagens para AWS SQS. Dados do erro: $a';
$string['secret'] = 'Segredo';
$string['secretdesc'] = 'O segredo da LTI.';
$string['showdata'] = 'Mostrar dados';
$string['hidedata'] = 'Ocultar dados';
$string['showexplanation'] = 'Mostrar explicação';
$string['hideexplanation'] = 'Ocultar explicação';
$string['showexception'] = 'Mostrar exceção';
$string['hideexception'] = 'Ocultar exceção';
$string['usercapabilitymissing'] = 'O usuário fornecido não pode excluir este arquivo.';
$string['autoconfigure'] = 'Serviço da web do Ally de configuração automática';
$string['autoconfiguredesc'] = 'Criar automaticamente o usuário e a função de serviço da web para o Ally.';
$string['autoconfigureconfirmation'] = 'Crie automaticamente a função de serviço da Web e o usuário para o Ally e ative o serviço da Web. As seguintes ações serão realizadas:<ul><li>criar uma função chamada \'ally_webservice\' e um usuário com o nome de usuário \'ally_webuser\'</li><li>adicionar o usuário \'ally_webuser\' à função \'ally_webservice\'</li><li>ativar os serviços da web</li><li>ativar o protocolo de serviço da web rest</li><li>ativar o serviço web do Ally</li><li>criar um token para a conta \'ally_webuser\'</li></ul>';
$string['autoconfigsuccess'] = 'Sucesso - o serviço da web do Ally foi configurado automaticamente.';
$string['autoconfigtoken'] = 'A chave de serviço da web é o seguinte:';
$string['autoconfigapicall'] = 'Você pode testar se o serviço da web está funcionando por meio da seguinte URL:';
$string['privacy:metadata:files:action'] = 'A ação realizada no arquivo (por exemplo: criado, atualizado ou excluído).';
$string['privacy:metadata:files:contenthash'] = 'O hash de conteúdo do arquivo para determinar exclusividade.';
$string['privacy:metadata:files:courseid'] = 'O código do curso ao qual pertence o arquivo.';
$string['privacy:metadata:files:externalpurpose'] = 'Para integração com o Ally, os arquivos precisam ser trocados com o Ally.';
$string['privacy:metadata:files:filecontents'] = 'O conteúdo do arquivo real é enviado ao Ally para avaliá-lo quanto à acessibilidade.';
$string['privacy:metadata:files:mimetype'] = 'O tipo MIME de arquivo (por exemplo: texto/simples, imagem/jpeg, entre outros).';
$string['privacy:metadata:files:pathnamehash'] = 'O hash do nome de caminho do arquivo para identificá-lo com exclusividade.';
$string['privacy:metadata:files:timemodified'] = 'A hora em que o campo foi modificado pela última vez.';
$string['cachedef_annotationmaps'] = 'Armazenar dados de anotação para cursos';
$string['cachedef_fileinusecache'] = 'Cache em uso de arquivos do Ally';
$string['cachedef_pluginfilesinhtml'] = 'Cache em HTML de arquivos do Ally';
$string['cachedef_request'] = 'Cache de solicitação de filtro do Ally';
$string['pushfilessummary'] = 'Resumo das atualizações de arquivo do Ally.';
$string['pushfilessummary:explanation'] = 'Resumo das atualizações de arquivo enviadas para o Ally.';
$string['section'] = 'Seção {$a}';
$string['lessonanswertitle'] = 'Resposta para a lição &quot;{$a}&quot;';
$string['lessonresponsetitle'] = 'Resposta para a lição &quot;{$a}&quot;';
$string['logs'] = 'Registros do Ally';
$string['logrange'] = 'Faixa de registros';
$string['loglevel:none'] = 'Nenhuma';
$string['loglevel:light'] = 'Leve';
$string['loglevel:medium'] = 'Médio';
$string['loglevel:all'] = 'Todas';
$string['logcleanuptask'] = 'Tarefa de limpeza de registros do Ally';
$string['loglifetimedays'] = 'Manter registros por essa quantidade de dias';
$string['loglifetimedaysdesc'] = 'Manter registros do Ally por essa quantidade de dias. Defina como 0 para não excluir os registros. Uma tarefa agendada é (por padrão) definida para ser executada diariamente e removerá entradas de registro que tiverem mais do que essa quantidade de dias.';
$string['logger:filtersetupdebugger'] = 'Registro de configuração do filtro do Ally';
$string['logger:pushtoallysuccess'] = 'Push bem-sucedido para o terminal do Ally';
$string['logger:pushtoallyfail'] = 'Push malsucedido para o terminal do Ally';
$string['logger:pushfilesuccess'] = 'Push bem-sucedido de arquivo(s) para o terminal do Ally';
$string['logger:pushfileliveskip'] = 'Falha de push de arquivo ativo';
$string['logger:pushfileliveskip_exp'] = 'Ignorando push de arquivo(s) ativo(s) devido a problemas de comunicação. O push de arquivo ativo será restaurado quando a tarefa de atualização de arquivo for bem-sucedida. Reveja sua configuração.';
$string['logger:pushfileserror'] = 'Push malsucedido para o terminal do Ally';
$string['logger:pushfileserror_exp'] = 'Erros associados ao push de atualizações de conteúdo para serviços do Ally.';
$string['logger:pushcontentsuccess'] = 'Push bem-sucedido de conteúdo para o terminal do Ally';
$string['logger:pushcontentliveskip'] = 'Falha do push de conteúdo ativo';
$string['logger:pushcontentliveskip_exp'] = 'Ignorando o push de conteúdo ativo devido a problemas de comunicação. O push de conteúdo ativo será restaurado quando a tarefa de atualização de conteúdo for bem-sucedida. Reveja sua configuração.';
$string['logger:pushcontentserror'] = 'Push malsucedido para o terminal do Ally';
$string['logger:pushcontentserror_exp'] = 'Erros associados ao push de atualizações de conteúdo para serviços do Ally.';
$string['logger:addingconenttoqueue'] = 'Adicionando conteúdo à fila de push';
$string['logger:annotationmoderror'] = 'Falha na anotação de conteúdo do módulo do Ally.';
$string['logger:annotationmoderror_exp'] = 'O módulo não foi identificado corretamente.';
$string['logger:failedtogetcoursesectionname'] = 'Falha ao obter o nome da seção do curso';
$string['logger:moduleidresolutionfailure'] = 'Falha ao resolver o código do módulo';
$string['logger:cmidresolutionfailure'] = 'Falha ao resolver o código do módulo do curso';
$string['logger:cmvisibilityresolutionfailure'] = 'Falha ao resolver a visibilidade do módulo do curso';
$string['courseupdatestask'] = 'Enviar eventos do curso para o Ally';
$string['logger:pushcoursesuccess'] = 'Push bem-sucedido de evento(s) do curso para o terminal do Ally';
$string['logger:pushcourseliveskip'] = 'Falha no push de evento de curso ativo';
$string['logger:pushcourseerror'] = 'Falha no push de evento de curso ativo';
$string['logger:pushcourseliveskip_exp'] = 'Ignorando o push de evento(s) de curso ativo devido a problemas de comunicação. O push de evento de curso ativo será restaurado quando a tarefa de atualização de eventos do curso for bem-sucedida. Reveja sua configuração.';
$string['logger:pushcourseserror'] = 'Push malsucedido para o terminal do Ally';
$string['logger:pushcourseserror_exp'] = 'Erros associados ao push de atualizações do curso para serviços do Ally.';
$string['logger:addingcourseevttoqueue'] = 'Adicionando evento do curso à fila de push';
$string['logger:cmiderraticpremoddelete'] = 'O código do módulo do curso tem problemas pré-exclusão.';
$string['logger:cmiderraticpremoddelete_exp'] = 'O módulo não foi identificado corretamente. Ele não existe por causa da exclusão da seção ou há outro fator que acionou o botão de exclusão e ele não está sendo encontrado.';
$string['logger:servicefailure'] = 'Falha ao consumir serviço.';
$string['logger:servicefailure_exp'] = '<br>Classe: {$a->class}<br>Parâmetros: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Falha ao atribuir uma competência de arquétipo de professor à função ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Capacidade: {$a->cap}<br>Permissão: {$a->permission}';
$string['deferredcourseevents'] = 'Enviar eventos adiados do curso';
$string['deferredcourseeventsdesc'] = 'Permitir o envio de eventos armazenados do curso que se acumularam durante a falha de comunicação com o Ally';
