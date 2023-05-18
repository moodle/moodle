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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Formato Botões';
$string['currentsection'] = 'Este tópico';
$string['editsection'] = 'Editar tópico';
$string['deletesection'] = 'Remover tópico';
$string['sectionname'] = 'Tópico';
$string['section0name'] = 'Geral';
$string['hidefromothers'] = 'Ocultar tópico';
$string['showfromothers'] = 'Mostrar tópico';
$string['showdefaultsectionname'] = 'Mostrar o nome padrão das seções';
$string['showdefaultsectionname_help'] = 'Se nenhum nome for definido para a seção, nada será mostrado.<br>
Por definição um tópico sem nome é mostrado como <strong>Tópico [N]</strong>.';
$string['yes'] = 'Sim';
$string['no'] = 'Não';
$string['sectionposition'] = 'Posição da seção zero';
$string['sectionposition_help'] = 'A seção 0 aparecerá junto com a seção visível.<br><br>
<strong>Botões acima da lista</strong><br>Use esta opção se você deseja adicionar algum texto ou
recurso antes da lista de botões.<i>Exemplo: Usar uma imagem para ilustrar o curso.</i><br><br><strong>
Abaixo da seção visível</strong><br>Use esta opção se você deseja adicionar algum texto ou recurso depois da seção visível.
<i>Exemplo: Recursos ou links a serem exibidos independentes da seção visível.</i><br>';
$string['above'] = 'Acima da lista de botões';
$string['below'] = 'Abaixo da seção visível';
$string['divisor'] = 'Número de seções a agrupar - {$a}';
$string['divisortext'] = 'Título do agrupamento - {$a}';
$string['divisortext_help'] = 'As seções agrupadas são usadas para separar seção por tipo ou módulos.
<i>Exemplo: O curso possuiu 10 seções divididas em 2 módulos: Teórico (com 5 seções) e Prático (com 5 seções).<br>
Defina o título com "Teórico" e configure o número de seções para 5.</i><br><br>
Dica: se você desejar usar a tag <strong>&lt;br&gt;</strong> digite <strong>[br]</strong>.';
$string['colorcurrent'] = 'Cor do botão de seção atual';
$string['colorcurrent_help'] = 'A seção atual é a seção marcada com destaque. <br> Defina uma cor em hexadecimal.
<i>Exemplo: #fab747</i><br>Se quiser usar a cor padrão deixe em branco.';
$string['colorvisible'] = 'Cor do botão da seção visível';
$string['colorvisible_help'] = 'A seção visível é a seção selecionada. <br> Defina uma cor em hexadecimal.
<i>Exemplo: #747fab</i><br>.Se quiser usar a cor padrão deixe em branco.';
$string['editing'] = 'Os botões ficam desabilitados enquanto o modo de edição estiver ativado.';
$string['sequential'] = 'Sequencial';
$string['notsequentialdesc'] = 'Cada novo grupo o contador de seções volta para um.';
$string['sequentialdesc'] = 'Contar as seções ignorando os agrupamentos.';
$string['sectiontype'] = 'Estilo da listagem';
$string['numeric'] = 'Numérico';
$string['roman'] = 'Algarismos romanos';
$string['alphabet'] = 'Alfabético';
$string['buttonstyle'] = 'Estilo do botão';
$string['buttonstyle_help'] = 'Define qual a forma geométrica dos botões.';
$string['circle'] = 'Círculo';
$string['square'] = 'Quadrado';
$string['inlinesections'] = 'Seções separadas em linhas';
$string['inlinesections_help'] = 'Exibe cada seção em uma linha.';
