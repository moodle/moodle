<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Russian strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Действия';
$string['addtocalendar'] = 'Добавить в календарь';
$string['alternative_hosts'] = 'Альтернативные хосты';
$string['alternative_hosts_help'] = 'Опция "Альтернативный хост" позволяет планировать встречу и назначить другого Pro пользователя на этом же аккаунте для начала встречи или вебинара, если Вы не имеете возможности. Эти пользователи получат письмо сообщая их что они были добавлены как альтернативный хост, вместе с ссилкой чтобы начать встречу. Разделять несколько е-мейлов запятой (без пробелов).';
$string['allmeetings'] = 'Все встречи';
$string['apikey'] = 'Ключ Zoom API';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Секрет Zoom API';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Ссилка на Zoom API';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'VoIP и Телефония';
$string['audio_telephony'] = 'Только Телефония';
$string['audio_voip'] = 'Только VoIP';
$string['cachedef_zoomid'] = 'Идентификатор пользователя zoom';
$string['cachedef_sessions'] = 'Информация-отчет про пользователя из zoom';
$string['calendardescriptionURL'] = 'Ссилка на подключение к конференции: {$a}.';
$string['calendardescriptionintro'] = "\nОписание:\n{\$a}";
$string['calendariconalt'] = 'Иконка календаря';
$string['changehost'] = 'Сменить хост';
$string['clickjoin'] = 'Нажата кнопка присоеденения к конференции';
$string['connectionok'] = 'Соеденение работает.';
$string['connectionfailed'] = 'Ошибка соеденения: ';
$string['connectionstatus'] = 'Статус соеденения';
$string['defaultsettings'] = 'Стандартные настройки Zoom';
$string['defaultsettings_help'] = 'Эти настройки определяють данные по умолчанию для всех новых Zoom встреч и конференций.';
$string['downloadical'] = 'Скачать iCal';
$string['duration'] = 'Длительность (минут)';
$string['endtime'] = 'Время окончания';
$string['err_duration_nonpositive'] = 'Длительность должна быть положительная.';
$string['err_duration_too_long'] = 'Длительность не может быть больше 150 часов.';
$string['err_long_timeframe'] = 'Требуемый отрезок времени слишком большой, показаны результаты за последний месяц.';
$string['err_invalid_password'] = 'Пароль местит не допустимые символы.';
$string['err_password'] = 'Пароль должен состоятся только со следующих символов: [a-z A-Z 0-9 @ - _ *]. Максимум 10 символов.';
$string['err_password_required'] = 'Требуется пароль.';
$string['err_start_time_past'] = 'Дата начала не может быть в прошлом.';
$string['errorwebservice_badrequest'] = 'Zoom получил плохой запрос: {$a}';
$string['errorwebservice_notfound'] = 'Ресурс не существует: {$a}';
$string['errorwebservice'] = 'Ошибка вебсервиса Zoom: {$a}.';
$string['export'] = 'Експорт';
$string['firstjoin'] = 'Могут присоеденится до';
$string['firstjoin_desc'] = 'Время когда пользователь может присоеденится еще до планированой встречи (минут до начала).';
$string['getmeetingreports'] = 'Получить отчет о конференции Zoom';
$string['host'] = 'Хост';
$string['invalidscheduleuser'] = 'Вы не можете запланировать для этих пользователей.';
$string['invalid_status'] = 'Неверный статус, проверте базу.';
$string['join'] = 'Присоеденится';
$string['joinbeforehost'] = 'Присоеденится перед хостом';
$string['join_link'] = 'Ссилка для подключения';
$string['join_meeting'] = 'Присоеденится';
$string['jointime'] = 'Время подключения';
$string['leavetime'] = 'Время конца';
$string['licensesnumber'] = 'Количество лицензий';
$string['redefinelicenses'] = 'Обновить лицензии';
$string['lowlicenses'] = 'Если количество ваших лицензий превышит указаную, тогда когда пользователь создает новую активность, она будет предназначана к PRO лицензии уменьшая статус другого пользователя. Эта опция еффективна когда количество активных PRO-лицензий больше 5.';
$string['maskparticipantdata'] = 'Спрятать данные участников';
$string['maskparticipantdata_help'] = 'Предовращает появлению данных о участников в отчетах (полезное для сайтов которые скривают данные о пользователю, например, для HIPAA).';
$string['meeting_nonexistent_on_zoom'] = 'Не существует на Zoom';
$string['meeting_finished'] = 'Завершено';
$string['meeting_not_started'] = 'Не начался';
$string['meetingoptions'] = 'Настройки конференции';
$string['meetingoptions_help'] = "*Присоеденится перед хостом* позволяет учасникам рисоеденятся к конференции перед тем как зайдет хост или когда хост не может присоеденится к встречи.\n\n*Зал ожидания* позволяет хосту контролировать когда учасники присоеденяются к конференции.\n\nЭти две опции зависимы друг от друга, поэтому выбор одного снимет выбор другого. Также возможно снять выбор с двух.\n\n*Зарегистрированые пользователи* требует всем пользователям ввойти в свои авторизированые аккаунты zoom чтобы иметь возможность присоеденится.";
$string['meeting_started'] = 'В процессе';
$string['meeting_time'] = 'Начало';
$string['modulename'] = 'Zoom конференции';
$string['modulenameplural'] = 'Zoom Конференции';
$string['modulename_help'] = 'Zoom это видео и веб платформа конференций которая позволяет авторизированым пользователям иметь возможность проводить онлайн встречи.';
$string['newmeetings'] = 'Новая конференция';
$string['nomeetinginstances'] = 'Не найдено сессий для этой встречи';
$string['noparticipants'] = 'Сейчас нет данних по учасниках этой конференции.';
$string['nosessions'] = 'Не найдено сессий для указаного района.';
$string['nozooms'] = 'Нет встреч';
$string['off'] = 'Отключено';
$string['oldmeetings'] = 'Оконченые встречи';
$string['on'] = 'Включено';
$string['option_audio'] = 'Аудио опции';
$string['option_authenticated_users'] = 'Только авторизированые пользоветели';
$string['option_host_video'] = 'Видео хост';
$string['option_jbh'] = 'Включить возможность подключения перед хостом';
$string['option_mute_upon_entry'] = 'Выключать микрофон при соеденении';
$string['option_mute_upon_entry_help'] = 'автоматически глушить всех участников когда они присоеденяются к конференции. Хост контролирует когда участники могут включить у себя микрофон.';
$string['option_participants_video'] = 'Видео участников';
$string['option_proxyhost'] = 'Использовать прокси';
$string['option_proxyhost_desc'] = 'Вписаное прокси как \'<code>&lt;hostname&gt;:&lt;port&gt;</code>\' используется только для комуникаций с Zoom. Оставте пустым чтобы использовать стандартные настройки прокси в Moodle. Вам нужно это вписовать если не хотите прописовать глобальное прокси на Moodle.';
$string['option_waiting_room'] = 'Включить зал ожидания';
$string['participantdatanotavailable'] = 'Детали не доступны';
$string['participantdatanotavailable_help'] = 'Данние по участникам для етой Zoom сессии недоступны (Например, из-за HIPAA-соответствия).';
$string['participants'] = 'Участники';
$string['password'] = 'Пароль';
$string['passwordprotected'] = 'Защишено парлем';
$string['pluginadministration'] = 'Редактировать Zoom конференцию';
$string['pluginname'] = 'Zoom meeting';
$string['privacy:metadata:zoom_meeting_details'] = 'Таблица базы сохраняет информацию о каждым инстансе конференции.';
$string['privacy:metadata:zoom_meeting_details:topic'] = 'Назнание конференции к которой присоеденяется пользователь.';
$string['privacy:metadata:zoom_meeting_participants'] = 'Таблица базы которая сохраняет информацию о участников конференции.';
$string['privacy:metadata:zoom_meeting_participants:duration'] = 'Как долго участник бил на встрече';
$string['privacy:metadata:zoom_meeting_participants:join_time'] = 'Время когда участник присоеденился к встрече';
$string['privacy:metadata:zoom_meeting_participants:leave_time'] = 'Время когда участник вышел с конференции';
$string['privacy:metadata:zoom_meeting_participants:name'] = 'Имя участника';
$string['privacy:metadata:zoom_meeting_participants:user_email'] = 'Е-мейл участника';
$string['recurringmeeting'] = 'Повторяема';
$string['recurringmeeting_help'] = 'Нет конечной даты';
$string['recurringmeetinglong'] = 'Повторяема конференция (встреча без даты и время конца)';
$string['recycleonjoin'] = 'Обновить лицензии когда присоеденяешся';
$string['licenseonjoin'] = 'Выбирайте эту опцию если Вы котите хосту надать лицензию перед началом конференции, <i>также как и</i> перед созданием.';
$string['report'] = 'Отчёти';
$string['reportapicalls'] = 'Вызов API отчётов истощен';
$string['resetapicalls'] = 'Обновить количество доступных вызовов API';
$string['schedulefor'] = 'Запланировать для';
$string['scheduleforself'] = 'Запланировать для себя';
$string['search:activity'] = 'Zoom - информация о деятельности';
$string['sessions'] = 'Сессии';
$string['start'] = 'Начать';
$string['starthostjoins'] = 'Запуск видео при подключении';
$string['start_meeting'] = 'Начать конференцию';
$string['startpartjoins'] = 'Запуск видео при подключении участника';
$string['start_time'] = 'Когда';
$string['starttime'] = 'Время начала';
$string['status'] = 'Статус';
$string['title'] = 'Заголовок';
$string['topic'] = 'Тема';
$string['unavailable'] = 'Сейчас нет возможности подключится';
$string['updatemeetings'] = 'Обновить настройки конференции для Zoom';
$string['usepersonalmeeting'] = 'использовать персональный ID конференции {$a}';
$string['waitingroom'] = 'Зал ожидания включён';
$string['webinar'] = 'Вебинар';
$string['webinar_help'] = 'Эта опция доступна только для пре-авторизованых пользователей Zoom.';
$string['webinar_already_true'] = '<p><b>Этот модуль уже виставлен как вебинар, не конференция. Вы не можете изменить эту настройку после создания вебинара.</b></p>';
$string['webinar_already_false'] = '<p><b>Этот модуль уже виставлен как конференция, не вебинар. Вы не можете изменить эту настройку после создания конференции.</b></p>';
$string['zoom:addinstance'] = 'Создать новую Zoom конференцию';
$string['zoomerr'] = 'Возникла ошибка с Zoom.'; // Generic error.
$string['zoomerr_apikey_missing'] = 'Ключ Zoom API не найден';
$string['zoomerr_apisecret_missing'] = 'Секрет Zoom API не найден';
$string['zoomerr_id_missing'] = 'Вам нужно указать ID курса или ID инстанса';
$string['zoomerr_licensesnumber_missing'] = 'Максимальные настройки Zoom найдены, но параметр "licensesnumber" не найден';
$string['zoomerr_maxretries'] = 'Сделано {$a->maxretries} попиток дозвонится, но неудачно: {$a->response}';
$string['zoomerr_meetingnotfound'] = 'Эта конференция не может быть найдена на Zoom. Вы можете <a href="{$a->recreate}">пересоздать её тут</a> или <a href="{$a->delete}">полностью её удалить</a>.';
$string['zoomerr_meetingnotfound_info'] = 'Эта конференция не может быть найдена на Zoom. Пожалуйста обратитесь к хосту конференции если имеете вопросы.';
$string['zoomerr_usernotfound'] = 'Невозможно найти Ваш аккаунт на Zoom. Если Вы используете Zoom впервые, Вы должны активировать Ваш Zoom аккаунт зайдя на <a href="{$a}" target="_blank">{$a}</a>. Как только Вы активировали свой Zoom аккаунт, перезагрузите эту страницу и продолжайте настраивать конференцию. В другом случаи убедитесь что ваш е-мейл на Zoom совпадает с е-мейлом в системе.';
$string['zoomurl'] = 'Ссилка домашней страници Zoom';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'Показать Zoom конференции';
