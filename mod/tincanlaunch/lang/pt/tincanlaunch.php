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
 * Strings em Português para plugin tincanlaunch
 *
 * Traduzido por: Kélvin Santiago - kelvinsleonardo@gmail.com
 *
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Tin Can Launch Link';
$string['modulenameplural'] = 'Tin Can Launch Links';
$string['modulename_help'] = 'Um plugin para Moodle que permite lançar o Tin Can (xAPI) e o conteúdo então é rastreado pelo LRS.';

// Iniciar configuração padrão LRS.
$string['tincanlaunchlrsfieldset'] = 'Configurações padrões para atividades Launch Link.';
$string['tincanlaunchlrsfieldset_help'] = 'Em todo site quando se cria uma nova atividade, valores padrões são utilizados. Cada atividade pode prover valores alternativos.';

$string['tincanlaunchlrsendpoint'] = 'Endpoint';
$string['tincanlaunchlrsendpoint_help'] = 'O LRS endpoint (e.g. http://lrs.example.com/endpoint/). deve incluir a barra no final.';
$string['tincanlaunchlrsendpoint_default'] = '';

$string['tincanlaunchlrslogin'] = 'Login Básico';
$string['tincanlaunchlrslogin_help'] = 'Sua chave de login LRS.';
$string['tincanlaunchlrslogin_default'] = '';

$string['tincanlaunchlrspass'] = 'Senha Básica';
$string['tincanlaunchlrspass_help'] = 'Sua senha LRS (secret).';
$string['tincanlaunchlrspass_default'] = '';

$string['tincanlaunchlrsduration'] = 'Duração';
$string['tincanlaunchlrsduration_help'] = 'Usado com integração basica de autenticação LRS. Solicita o LRS para manter as credenciais válidas nessa quantidade de minutos.';
$string['tincanlaunchlrsduration_default'] = '9000';

$string['tincanlaunchlrsauthentication'] = 'Integração LRS';
$string['tincanlaunchlrsauthentication_help'] = 'Use os recursos de integração adicionais para criar novas credenciais de autenticação para cada lançamento que suporta os LRSS.';
$string['tincanlaunchlrsauthentication_watershedhelp'] = 'Para integrar com o Watershed, insira as credenciais de login e senha do Watershed nos campos abaixo. Esteja ciente que esses dados serão armazenados no banco de dados do moodle. Use uma conta criada que não utiliza uma senha de qualquer outra conta. Para outras configurações de integração, insira a autenticação básica Login/Password abaixo.';
$string['tincanlaunchlrsauthentication_watershedhelp_label'] = 'Integração com Watershed';
$string['tincanlaunchlrsauthentication_option_0'] = 'Nenhum';
$string['tincanlaunchlrsauthentication_option_1'] = 'Watershed';
$string['tincanlaunchlrsauthentication_option_2'] = 'Learning Locker 1';

$string['tincanlaunchuseactoremail'] = 'Identificar por e-mail';
$string['tincanlaunchuseactoremail_help'] = 'Se marcar como selecionado, os alunos serão identificados pelo endereço de e-mail, se tiver registrado no moodle.';

$string['tincanlaunchcustomacchp'] = 'Customizar conta homepage';
$string['tincanlaunchcustomacchp_help'] = 'Se for inserido o moodle irá utilizar essa home page com um número ID para identificar o aluno. Se o ID não for inserido pelo aluno, será identificado pelo e-mail ou pelo número do ID do moodle. Nota: Se o ID do aluno mudar eles irão perder o acesso aos registros associados aquele ID. Os relatórios no LRS poderão ser afetados também.';
$string['tincanlaunchcustomacchp_default'] = '';

// Configurações de Atividade.
$string['tincanlaunchname'] = 'Lançar nome do link';
$string['tincanlaunchname_help'] = 'O nome do link como irá aparecer para o usuário.';

$string['tincanlaunchurl'] = 'Lançar URL';
$string['tincanlaunchurl_help'] = 'A URL base da atividade do Tin Can que você deseja lançar/iniciar (e.g. http://example.com/content/index.html).';

$string['tincanactivityid'] = 'ID Atividade';
$string['tincanactivityid_help'] = 'O IRI de de identificação para a atividade primária que está sendo lançada.';

$string['tincanpackage'] = 'Pacote zip';
$string['tincanpackage_help'] = 'Se você tem um curso Tin Can compactado, você pode fazer o upload aqui. Se você carregar um pacote, a URL de lançamento e o campo ID de atividade será preenchido automaticamente, quando você salvar usando os dados do arquivo tincan.xml contida no arquivo zip. Você pode editar essas configurações a qualquer momento, mas não deve alterar o ID da atividade, a não ser que você saiba o que está fazendo.';

$string['tincanpackagetitle'] = 'Configurações de lançamento';
$string['tincanpackagetext'] = 'Você pode preencher as configurações de lançamento e ID da atividade diretamente, ou enviando um pacote zip onde deve conter o arquivo tincan.xml. A URL de lançamento definida no arquivo tincan.xml pode apontar para outros arquivos no pacote .zip ou para uma URL externa. O ID da atividade deve sempre ter um URL completo ou outro IRI.';

$string['lrsheading'] = 'Configurações de LRS';
$string['lrsdefaults'] = 'Configuradões padrões de LRS';
$string['lrssettingdescription'] = 'Por padrão, esta atividade usa a configuração global de LRS encontrada em Site administration > Plugins > Activity modules > Tin Can Launch Link. Para alterar a configuração para esta especifica atividade, selecione Desbloqueio Padrão.';
$string['overridedefaults'] = 'Desbloqueio padrão';
$string['overridedefaults_help'] = 'Permite a atividade ter diferentes configuradões de LRS.';

$string['behaviorheading'] = 'Comportamento do módulo';

$string['tincanmultipleregs'] = 'Permitir multiplos registros';
$string['tincanmultipleregs_help'] = 'Se for selecionada, permite o aluno iniciar mais de um registro para atividade. Os alunos podem sempre voltar para os registros que já tenham sido iniciados, mesmo se esta configuração estiver desmarcada.';
$string['apCreationFailed'] = 'Falha ao criar Watershed Activity Provider.';

// Zip erros.
$string['badmanifest'] = 'Alguns erros nos manifestos: veja os erros no log';
$string['badimsmanifestlocation'] = 'Um arquivo tincan.xml foi encontrado porém não está na raiz do seu arquivo zip, por favor recompactar o seu curso.';
$string['badarchive'] = 'Você deve fornecer um arquivo zip válido';
$string['nomanifest'] = 'Pacote de arquivo incorreto - está faltando tincan.xml';

$string['tincanlaunch'] = 'Tin Can Launch Link';
$string['pluginadministration'] = 'Tin Can Launch Link administração';
$string['pluginname'] = 'Tin Can Launch Link';

// Configuração de conclusão de verbos.
$string['completionverb'] = 'Verbo';
$string['completionverbgroup'] = 'Rastreamento completo pelo verbo';
$string['completionverbgroup_help'] = 'O moddle irá olhas para as demonstrações onde o ator é o usuário atual, o objeto ID da atividade especificado e o verbo será aquele definido aqui. Se ele encontrar uma de uma correspondência ao statement, a atividade será marcada como concluída.';

// Configuração de Visualização.
$string['tincanlaunchviewfirstlaunched'] = 'Lançado pela primeira vez';
$string['tincanlaunchviewlastlaunched'] = 'Último lançado';
$string['tincanlaunchviewlaunchlinkheader'] = 'Link do Início';
$string['tincanlaunchviewlaunchlink'] = 'Lançamento';

$string['tincanlaunch_completed'] = 'Experiência completa!';
$string['tincanlaunch_progress'] = 'Tentativa em andamento.';
$string['tincanlaunch_attempt'] = 'Nova tentativa';
$string['tincanlaunch_notavailable'] = 'The Learning Record Store não está disponível. Entre em contato com o administrador do sistema.';

$string['idmissing'] = 'Você deve especificar um ID do modulo do curso ou uma instância ID';

// Eventos.
$string['eventactivitylaunched'] = 'Atividade Lançada';
$string['eventactivitycompleted'] = 'Atividade Completa';

$string['tincanlaunch:addinstance'] = 'Adicionar uma nova atividade Tin Can (xAPI) para um curso';

$string['expirecredentials'] = 'Credenciais expiradas';
