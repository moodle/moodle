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
 * Plugin strings are defined here.
 *
 * @package     mod_googlemeet
 * @category    string
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'Chave de API';
$string['checkweekdays'] = 'Selecione os dias da semana que se enquadram no intervalo de datas selecionado.';
$string['clientid'] = 'ID do cliente OAuth';
$string['clientid_desc'] = '<a href="https://github.com/ronefel/moodle-mod_googlemeet/wiki/Como-criar-a-chave-de-API-e-o-ID-do-cliente-OAuth" target="_blank">Como criar a chave de API e o ID do cliente OAuth</a>';
$string['date'] = 'Data';
$string['duration'] = 'Duração';
$string['earlierto'] = 'A data do evento não pode ser anterior à data de início do curso ({$a}).';
$string['emailcontent'] = 'Conteúdo do e-mail';
$string['emailcontent_default'] = '<p>Olá %userfirstname%,</p>
<p>Este lembrete é para lembrar você de que haverá um evento do Google Meet em %coursename%</p>
<p><b>%googlemeetname%</b></p>
<p>Quando: %eventdate% %duration% %timezone%</p>
<p>Link de acesso: %url%</p>';
$string['emailcontent_help'] = 'Quando uma notificação é enviada a um aluno, ele obtém o conteúdo do email desse campo. Os seguintes curingas podem ser usados:
<ul>
<li>%userfirstname%</li>
<li>%userlastname%</li>
<li>%coursename%</li>
<li>%googlemeetname%</li>
<li>%eventdate%</li>
<li>%duration%</li>
<li>%timezone%</li>
<li>%url%</li>
<li>%cmid%</li>
</ul>';
$string['entertheroom'] = 'Entrar na sala';
$string['eventdate'] = 'Data do evento';
$string['from'] = 'das';
$string['generateurlroom'] = 'Gerar URL da sala';
$string['googlemeet:addinstance'] = 'Adicionar novo Google Meet™ para Moodle';
$string['googlemeet:editrecording'] = 'Editar as gravações';
$string['googlemeet:removerecording'] = 'Remover as gravações';
$string['googlemeet:syncgoogledrive'] = 'Sincronizar com o Google Drive';
$string['googlemeet:view'] = 'Ver Google Meet™ para Moodle';
$string['hide'] = 'Ocultar';
$string['invalideventenddate'] = 'Esta data não pode ser anterior à "Data do evento"';
$string['invalideventendtime'] = 'O horário de término deve ser maior que o horário de início';
$string['invalidstoredurl'] = 'Não é possível exibir este recurso, a URL do Google Meet é inválida.';
$string['jstableinfo'] = 'Mostrando {start} a {end} de {rows} gravações';
$string['jstableinfofiltered'] = 'Mostrando {start} a {end} de {rows} gravações (filtrado de {rowsTotal} gravações)';
$string['jstableloading'] = 'Carregando...';
$string['jstablenorows'] = 'Nenhuma gravação encontrada';
$string['jstableperpage'] = '{select} gravações por página';
$string['jstablesearch'] = 'Procurar...';
$string['lastsync'] = 'Última sincronização:';
$string['loading'] = 'Carregando';
$string['messageprovider:notification'] = 'Lembrete de início do evento do Google Meet';
$string['minutesbefore'] = 'Minutos antes';
$string['minutesbefore_help'] = 'Número de minutos antes do início do evento quando a notificação deve ser enviada.';
$string['modulename'] = 'Google Meet™ para Moodle';
$string['modulename_help'] = 'O módulo Google Meet™ para Moodle permite ao professor criar uma sala do Google Meet como recurso do curso e, após as reuniões, disponibilizar as gravações aos alunos, salvas no Google Drive.
<p>©2018 Google LLC All rights reserved.<br/>
Google Meet and the Google Meet logo are registered trademarks of Google LLC.</p>
';
$string['modulenameplural'] = 'Instâncias do Google Meet™ para Moodle';
$string['multieventdateexpanded'] = 'Recorrência da data do evento expandido';
$string['multieventdateexpanded_desc'] = 'Mostrar as configurações de "Recorrência da data do evento" expandidas por padrão ao criar uma nova Sala.';
$string['name'] = 'Nome';
$string['never'] = 'Nunca';
$string['notfoundrecordingname'] = 'Nenhuma gravação encontrada com o nome';
$string['notfoundrecordingsfolder'] = 'A pasta "Meet Recordings" não foi encontrada no Google Drive.';
$string['notification'] = 'Notificação';
$string['notificationexpanded'] = 'Notificação expandida';
$string['notify'] = 'Enviar notificação para o estudante';
$string['notify_help'] = 'Se marcada, uma notificação será enviada ao aluno sobre a data de início do evento.';
$string['notifycationexpanded_desc'] = 'Mostrar as configurações de "Notificação" expandidas por padrão ao criar uma nova sala.';
$string['notifytask'] = 'Tarefa de notificação do Google Meet™ para Moodle';
$string['notpossiblesync'] = 'Não é possível sincronizar com uma conta diferente daquela que criou a sala.';
$string['or'] = 'ou';
$string['play'] = 'Reproduzir';
$string['pluginadministration'] = 'Administração do Google Meet™ para Moodle';
$string['pluginname'] = 'Google Meet™ para Moodle';
$string['privacy:metadata:googlemeet_notify_done'] = 'Registra notificações enviadas aos usuários sobre o início dos eventos. Esses dados são temporários e são excluídos após a data de início do evento.';
$string['privacy:metadata:googlemeet_notify_done:eventid'] = 'O ID do evento';
$string['privacy:metadata:googlemeet_notify_done:userid'] = 'O ID do usuário';
$string['privacy:metadata:googlemeet_notify_done:timesent'] = 'O timestamp indicando quando o usuário recebeu uma notificação';
$string['earlierto'] = 'A data do evento não pode ser anterior à data de início do curso ({$a}).';
$string['recording'] = 'Gravação';
$string['recordings'] = 'Gravações';
$string['recordingswiththename'] = 'Gravações com o nome:';
$string['recurrenceeventdate'] = 'Recorrência da data do evento';
$string['recurrenceeventdate_help'] = 'Esta função possibilita a criação de várias recorrências da data do evento.
<br>* <strong>Repetir</strong>: Selecione os dias da semana em que sua classe se reunirá (por exemplo, segunda-feira / quarta-feira / sexta-feira).
<br>* <strong>Repetir a cada</strong>: Isso permite uma configuração de frequência. Se sua classe se reunirá todas as semanas, selecione 1; se reunirá a cada duas semanas, selecione 2; a cada 3 semanas, selecione 3, e assim por diante.
<br>* <strong>Repetir até</strong>: Selecione o último dia de reunião (o último dia que você deseja levar a recorrência da data do evento).
';
$string['repeatasfollows'] = 'Repita a data do evento acima da seguinte forma';
$string['repeatevery'] = 'Repetir a cada';
$string['repeaton'] = 'Repetir';
$string['repeatuntil'] = 'Repetir até';
$string['requirednamefield'] = 'Digite o nome da sala para criar automaticamente.';
$string['roomcreator'] = 'Criador da sala:';
$string['roomname'] = 'Nome da sala';
$string['roomurl'] = 'URL da sala';
$string['roomurlexpanded'] = 'URL da sala expandido';
$string['roomurlexpanded_desc'] = 'Mostrar as configurações de "URL da sala" expandidas por padrão ao criar uma nova sala.';
$string['show'] = 'Mostrar';
$string['strftimedm'] = '%a. %d %b.';
$string['strftimedmy'] = '%a. %d %b. %Y';
$string['strftimedmyhm'] = '%a. %d %b. %Y %H:%M';
$string['strftimehm'] = '%H:%M';
$string['syncwithgoogledrive'] = 'Sincronizar com o Google Drive';
$string['thereisnorecordingtoshow'] = 'Não há gravação para mostrar.';
$string['timeahead'] = 'Não é possível criar várias recorrências da data do evento que excedam um ano, ajuste as datas de início e término.';
$string['timedate'] = '%d/%m/%Y %H:%M';
$string['to'] = 'até';
$string['today'] = 'Hoje';
$string['upcomingevents'] = 'Próximos eventos';
$string['url_failed'] = 'É obrigatório uma URL válida do Google Meet';
$string['visible'] = 'Visível';
$string['week'] = 'Semana(s)';
