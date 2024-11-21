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
 * Strings for component 'format_remuiformat'
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Nome do plugin.
$string['pluginname'] = 'Formatos de curso Edwiser';
$string['plugin_description'] = 'Os cursos são apresentados como listas expansíveis ou como cards de seções com design responsivo para melhor navegação.';
// Configurações.
$string['defaultcoursedisplay'] = 'Visualização padrão do curso';
$string['defaultcoursedisplay_desc'] = 'Mostrar todas as seções em uma única página ou a seção zero e a seção escolhida em cada página.';

$string['defaultbuttoncolour'] = 'Cor padrão do botão Ver tópico';
$string['defaultbuttoncolour_desc'] = 'A cor do botão Ver tópico.';

$string['defaultoverlaycolour'] = 'Cor padrão do overlay ao passar o mouse sobre atividades';
$string['defaultoverlaycolour_desc'] = 'A cor do overlay ao passar o mouse sobre atividades.';

$string['enablepagination'] = 'Habilitar paginação';
$string['enablepagination_desc'] = 'Isso permite a visualização em várias páginas quando o número de seções/atividades é muito grande.';

$string['defaultnumberoftopics'] = 'Número padrão de tópicos por página';
$string['defaultnumberoftopics_desc'] = 'O número de tópicos a ser exibido em uma página.';

$string['defaultnumberofactivities'] = 'Número padrão de atividades por página';
$string['defaultnumberofactivities_desc'] = 'O número de atividades a ser exibido em uma página.';

$string['off'] = 'Desligado';
$string['on'] = 'Ligado';

$string['defaultshowsectiontitlesummary'] = 'Mostrar resumo do título da seção ao passar o mouse';
$string['defaultshowsectiontitlesummary_desc'] = 'Mostrar o resumo do título da seção ao passar o mouse sobre o box de grade.';

$string['sectiontitlesummarymaxlength'] = 'Definir comprimento máximo do resumo da seção/atividades.';
$string['sectiontitlesummarymaxlength_help'] = 'Defina o comprimento máximo do resumo do título da seção/atividades exibido no card.';

$string['defaultsectionsummarymaxlength'] = 'Definir comprimento máximo do resumo da seção/atividades.';
$string['defaultsectionsummarymaxlength_desc'] = 'Defina o comprimento máximo do resumo da seção/atividades exibido no card.';

$string['hidegeneralsectionwhenempty'] = 'Ocultar seção geral quando vazia';
$string['hidegeneralsectionwhenempty_help'] = 'Quando a seção geral não tiver atividade e resumo, você pode ocultá-la.';

// Seção.
$string['sectionname'] = 'Seção';
$string['sectionnamecaps'] = 'SEÇÃO';
$string['section0name'] = 'Introdução';
$string['hidefromothers'] = 'Ocultar seção';
$string['showfromothers'] = 'Mostrar seção';
$string['viewtopic'] = 'Ver';
$string['editsection'] = 'Editar seção';
$string['editsectionname'] = 'Editar nome da seção';
$string['newsectionname'] = 'Novo nome para a seção {$a}';
$string['currentsection'] = 'Esta seção';
$string['addnewsection'] = 'Adicionar Seção';
$string['moveresource'] = 'Mover recurso';

// Atividade.
$string['viewactivity'] = 'Ver Atividade';
$string['markcomplete'] = 'Marcar como Completo';
$string['grade'] = 'Nota';
$string['notattempted'] = 'Não Tentado';
$string['subscribed'] = "Inscrito";
$string['notsubscribed'] = "Não Inscrito";
$string['completed'] = "Completo";
$string['notcompleted'] = 'Não Completo';
$string['progress'] = 'Progresso';
$string['showinrow'] = 'Mostrar em linha';
$string['showincard'] = 'Mostrar em card';
$string['moveto'] = 'Mover para';
$string['changelayoutnotify'] = 'Atualize a página para ver as mudanças.';
$string['generalactivities'] = 'Atividades';
$string['coursecompletionprogress'] = 'Progresso do Curso';
$string['resumetoactivity'] = 'Retomar';

// Para formato de lista.
$string['remuicourseformat'] = 'Escolher layout';
$string['remuicourseformat_card'] = 'Layout de Card';
$string['remuicourseformat_list'] = 'Layout de Lista';
$string['remuicourseformat_help'] = 'Escolha um layout de curso';
$string['remuicourseimage_filemanager'] = 'Imagem do Cabeçalho do Curso';
$string['remuicourseimage_filemanager_help'] = 'Esta imagem será exibida no card da seção geral no layout de card e como plano de fundo da seção geral no layout de lista. <strong>Tamanho de imagem recomendado 1272x288.</strong>';
$string['addsections'] = 'Adicionar seções';
$string['teacher'] = 'Professor';
$string['teachers'] = 'Professores';
$string['remuiteacherdisplay'] = 'Mostrar imagem do professor';
$string['remuiteacherdisplay_help'] = 'Mostrar imagem do professor no cabeçalho do curso.';
$string['defaultremuiteacherdisplay'] = 'Mostrar imagem do professor';
$string['defaultremuiteacherdisplay_desc'] = 'Mostrar imagem do professor no cabeçalho do curso.';

$string['remuidefaultsectionview'] = 'Escolher visualização padrão das seções';
$string['remuidefaultsectionview_help'] = 'Escolha uma visualização padrão para as seções do curso.';
$string['expanded'] = 'Expandir Todos';
$string['collapsed'] = 'Recolher Todos';

$string['remuienablecardbackgroundimg'] = 'Imagem de fundo da seção';
$string['remuienablecardbackgroundimg_help'] = 'Habilitar imagem de fundo da seção. Por padrão, está desativado. Ele busca a imagem do resumo da seção.';
$string['enablecardbackgroundimg'] = 'Mostrar imagem de fundo da seção no card.';
$string['disablecardbackgroundimg'] = 'Ocultar imagem de fundo da seção no card.';
$string['next'] = 'Próximo';
$string['previous'] = 'Anterior';

$string['remuidefaultsectiontheme'] = 'Escolher tema padrão das seções';
$string['remuidefaultsectiontheme_help'] = 'Escolha um tema padrão para as seções do curso.';

$string['dark'] = 'Escuro';
$string['light'] = 'Claro';

$string['defaultcardbackgroundcolor'] = 'Definir cor de fundo da seção no formato de card.';
$string['cardbackgroundcolor_help'] = 'Ajuda de cor de fundo de Card.';
$string['cardbackgroundcolor'] = 'Definir cor de fundo da seção no formato de card.';
$string['defaultcardbackgroundcolordesc'] = 'Descrição da cor de fundo de Card.';

// GDPR.
$string['privacy:metadata'] = 'O plugin Formatos de Curso Edwiser não armazena dados pessoais.';

// Validação.
$string['coursedisplay_error'] = 'Por favor, escolha uma combinação correta de layout.';

// Textos de atividades concluídas.
$string['activitystart'] = "Vamos Começar";
$string['outof'] = 'de';
$string['activitiescompleted'] = 'atividades concluídas';
$string['activitycompleted'] = 'atividade concluída';
$string['activitiesremaining'] = 'atividades restantes';
$string['activityremaining'] = 'atividade restante';
$string['allactivitiescompleted'] = "Todas as atividades concluídas";

// Usado em format.js ao alterar o layout do curso.
$string['showallsectionperpage'] = 'Mostrar todas as seções por página';

// Seção geral do formato de card.
$string['showfullsummary'] = '+ Mostrar resumo completo';
$string['showless'] = 'Mostrar Menos';
$string['showmore'] = 'Mostrar Mais';
$string['Complete'] = 'completo';

// Rastreamento de uso.
$string['enableusagetracking'] = "Habilitar Rastreamento de Uso";
$string['enableusagetrackingdesc'] = "<strong>AVISO DE RASTREAMENTO DE USO</strong>

<hr class='text-muted' />

<p>O Edwiser agora irá coletar dados anônimos para gerar estatísticas de uso do produto.</p>

<p>Essas informações nos ajudarão a orientar o desenvolvimento na direção certa e fazer a comunidade Edwiser prosperar.</p>

<p>Dito isso, não coletamos seus dados pessoais ou dos seus alunos durante este processo. Você pode desativar isso do plugin sempre que desejar optar por não participar deste serviço.</p>

<p>Uma visão geral dos dados coletados está disponível <strong><a href='https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products' target='_blank'>aqui</a></strong>.</p>";

$string['edw_format_hd_bgpos'] = "Posição da imagem de fundo do cabeçalho do curso";
$string['bottom'] = "inferior";
$string['center'] = "centro";
$string['top'] = "superior";
$string['left'] = "esquerda";
$string['right'] = "direita";
$string["edw_format_hd_bgpos_help"] = "Escolha a posição da imagem de fundo";

$string['edw_format_hd_bgsize'] = "Tamanho da imagem de fundo do cabeçalho do curso";
$string['cover'] = "cobrir";
$string['contain'] = "conter";
$string['auto'] = "automático";
$string['edw_format_hd_bgsize_help'] = "Selecione o tamanho da imagem de fundo do cabeçalho do curso";
$string['courseinformation'] = "Informações do Curso";
$string["defaultheader"] = 'Padrão ';
$string["remuiheader"] = 'Cabeçalho';
$string["headereditingbutton"] = "Selecionar posição do botão de edição";
$string['headereditingbutton_help'] = "Selecionar posição do botão de edição. Esta configuração não funcionará no remui, verifique a configuração do curso";

$string['headeroverlayopacity'] = "Alterar a opacidade do overlay do cabeçalho";
$string['headeroverlayopacity_help'] = "O valor padrão já está definido como '100'. Para ajustar a opacidade, por favor, insira um valor entre 0 e 100";
$string['viewalltext'] = 'Ver tudo';
