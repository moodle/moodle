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
 * Local language pack from http://www.moodle.vsu.ru
 *
 * @package    qtype
 * @subpackage ddmarker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addmoreitems'] = 'Заготовки для ещё {no} маркеров';
$string['alttext'] = 'Альтернативный текст';
$string['answer'] = 'Ответ';
$string['bgimage'] = 'Фоновое изображение';
$string['coords'] = 'Координаты';
$string['correctansweris'] = 'Верный ответ: {$a}';
$string['draggableimage'] = 'Перетаскиваемое изображение';
$string['draggableitem'] = 'Перетаскиваемый элемент';
$string['draggableitemheader'] = 'Перетаскиваемый элемент {$a}';
$string['draggableitemtype'] = 'Тип';
$string['draggableword'] = 'Перетаскиваемый текст';
$string['dropzone'] = 'Зона размещения {$a}';
$string['dropzoneheader'] = 'Зоны размещения';
$string['followingarewrong'] = 'Следующие маркеры были помещены в ошибочные области: {$a}.';
$string['followingarewrongandhighlighted'] = 'Следующие маркеры были неверно расположены: {$a}. Подсвеченные маркеры теперь показаны с верными расположениями.<br /> Нажмите на маркер, чтобы подсветить разрешенную область.';
$string['formerror_nobgimage'] = 'Вам нужно выбрать изображение для использования как фон для области перетаскивания';
$string['formerror_noitemselected'] = 'Вы определили зону размещения, но не выбрали маркер, который будет размещаться в этой зоне';
$string['formerror_nosemicolons'] = 'Отсутствуют запятые в вашей строке координат. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['formerror_onlysometagsallowed'] = 'Только теги "{$a}" разрешены в качестве метки для маркера';
$string['formerror_onlyusewholepositivenumbers'] = 'Пожалуйста, используйте только целые положительные числа для определения x,y координат и/или ширины и высоты фигуры. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['formerror_polygonmusthaveatleastthreepoints'] = 'Для многоугольника вам нужно определить как минимум 3 точки. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['formerror_shapeoutsideboundsofbgimage'] = 'Фигура, которую вы определяете, вышла за пределы фонового изображения';
$string['formerror_toomanysemicolons'] = 'Очень много ";" в качестве разделяющих элементов координат, которые вы указали. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['formerror_unrecognisedwidthheightpart'] = 'Не удалось определить ширину и высоту, которые вы указали. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['formerror_unrecognisedxypart'] = 'Не удалось определить x и y, которые вы указали. Ваши координаты для {$a->shape} должны быть представлены как - {$a->coordsstring}';
$string['infinite'] = 'несколько';
$string['marker'] = 'Маркер';
$string['markers'] = 'Маркеры';
$string['marker_n'] = 'Маркер {no}';
$string['nolabel'] = 'Нет текстовой метки';
$string['pleasedragatleastonemarker'] = 'Ваш ответ не заполнен, вы обязаны разместить как минимум 1 маркер на изображение.';
$string['pluginname'] = 'Перетаскивание маркеров';
$string['pluginname_help'] = 'Выберите файл с фоновым изображением, введите текстовые метки для маркеров и определите зоны размещения на фоновом изображении, на которые нужно будет перетаскивать ответы.';
$string['pluginnameadding'] = 'Добавить перетаскивание маркеров';
$string['pluginnameediting'] = 'Редактировать перетаскивания маркеров';
$string['pluginnamesummary'] = 'Маркеры перетаскиваются на фоновое изображение.';
$string['previewarea'] = 'Область предпросмотра -';
$string['previewareaheader'] = 'Предпросмотр';
$string['previewareamessage'] = 'Выберите файл с фоновым изображением, введите текстовые метки для маркеров и определите зоны на фоновом изображении, куда они должны перетаскиваться.<br/> для многоугольника: x1,y1;x2,y2;x3,y3;x4,y4....(где x1, y1 - x,y координаты первой вершины, x2, y2 - x,y координаты второй и т.д. Вам не нужно повторять координаты первой вершины, чтобы сомкнуть многоугольник)<br/> для окружности: x,y;r (где x, y - xy координаты центра окружности и r - радиус)<br/> для прямоугольника: x,y;w,h (где x,y - xy координаты верхнего левого угла прямоугольника и w и h ширина и высота прямоугольника)';
$string['refresh'] = 'Обновить предпросмотр';
$string['shape'] = 'Фигура';
$string['shape_circle'] = 'Окружность';
$string['shape_circle_coords'] = 'x,y;r (где x, y - xy координаты центра окружности и r - радиус)';
$string['shape_circle_lowercase'] = 'окружность';
$string['shape_polygon'] = 'Многоугольник';
$string['shape_polygon_coords'] = 'x1,y1;x2,y2;x3,y3;x4,y4....(где x1, y1 - x,y координаты первой вершины, x2, y2 - x,y координаты второй и т.д. Вам не нужно повторять координаты первой вершины, чтобы сомкнуть многоугольник)';
$string['shape_polygon_lowercase'] = 'многоугольник';
$string['shape_rectangle'] = 'Прямоугольник';
$string['shape_rectangle_coords'] = 'x,y;w,h (где x,y - xy координаты верхнего левого угла прямоугольника и w и h ширина и высота прямоугольника)';
$string['shape_rectangle_lowercase'] = 'прямоугольник';
$string['showmisplaced'] = 'Выделить зоны размещения, в которых нет расположенных верно маркеров';
$string['shuffleimages'] = 'Перемешать перетаскиваемые элементы каждый раз при доступе к вопросу';
$string['stateincorrectlyplaced'] = 'Сохранять неверно размещенные маркеры';
$string['summariseplaceno'] = 'Зона размещения {$a}';
$string['ytop'] = 'Верх';
