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
 * Ukrainian strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Дії';
$string['addtocalendar'] = 'Додати до календаря';
$string['alternative_hosts'] = 'Альтернативні хости';
$string['alternative_hosts_help'] = 'Опція "Альтернативний хост" дозволяє планувати зустріч і призначити іншого Про користувача на цьому ж аккаунті щоб розпочати зустріч або вебінар, якщо Ви не можете. Ці користувачі отримають е-мейл повідомляючи їх що вони були додані як альтернативний хост, з посиланням щоб розпочати зустріч. Розділяти декілька е-мейлів комами (без пробілів).';
$string['allmeetings'] = 'Всі зустрічі';
$string['apikey'] = 'Zoom API ключ';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Zoom API секрет';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Посилання на Zoom API';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'VoIP і Телефонія';
$string['audio_telephony'] = 'Тільки Телефонія';
$string['audio_voip'] = 'Тільки VoIP';
$string['cachedef_zoomid'] = 'ІД користувача Zoom';
$string['cachedef_sessions'] = 'Інформація-звіт про користувача із zoom';
$string['calendardescriptionURL'] = 'URL для зустрічі: {$a}.';
$string['calendardescriptionintro'] = "\nОпис:\n{\$a}";
$string['calendariconalt'] = 'Іконка календаря';
$string['changehost'] = 'Змінити хост';
$string['clickjoin'] = 'Натиснута кнопка приєднання до зустрічі';
$string['connectionok'] = 'Є з\'єднання.';
$string['connectionfailed'] = 'Помилка з\'єднання: ';
$string['connectionstatus'] = 'Статус з\'єднання';
$string['defaultsettings'] = 'Налаштування Zoom по замовчуванню';
$string['defaultsettings_help'] = 'Ці налаштування визначають замовчування для всіх нових Zoom зустрічей та вебінарів.';
$string['downloadical'] = 'Завантажити iCal';
$string['duration'] = 'Тривалість (хвилин)';
$string['endtime'] = 'Час завершення';
$string['err_duration_nonpositive'] = 'Тривалість має бути додатня.';
$string['err_duration_too_long'] = 'Тривалість не може перевищувати 150 годин.';
$string['err_long_timeframe'] = 'Запитуваний відрізок часу дуже довгий, показ результатів за останній місяць.';
$string['err_invalid_password'] = 'Пароль містить недопустимі символи.';
$string['err_password'] = 'Пароль може містити лише наступні символи: [a-z A-Z 0-9 @ - _ *]. Максимум 10 символів.';
$string['err_password_required'] = 'Необхідний пароль.';
$string['err_start_time_past'] = 'Дата початку не може бути в минулому.';
$string['errorwebservice_badrequest'] = 'Zoom отримав погану відповідь: {$a}';
$string['errorwebservice_notfound'] = 'Ресурс не існує: {$a}';
$string['errorwebservice'] = 'Помилка вебсервісу Zoom: {$a}.';
$string['export'] = 'Експорт';
$string['firstjoin'] = 'Можуть приєднатися за';
$string['firstjoin_desc'] = 'Час коли користувач може приєднатися перед запланованою зустрічю (хвилин перед стартом).';
$string['getmeetingreports'] = 'Отримувати звіт зустрічі від Zoom';
$string['host'] = 'Хост';
$string['invalidscheduleuser'] = 'Ви не можете запланувати для вибраних користувачів.';
$string['invalid_status'] = 'Неправильний статус, перевірте базу даних.';
$string['join'] = 'Приєднатися';
$string['joinbeforehost'] = 'Приєднуватися перед хостом';
$string['join_link'] = 'Посилання на приєднання';
$string['join_meeting'] = 'Приєднатися';
$string['jointime'] = 'Час приєднання';
$string['leavetime'] = 'Час завершення';
$string['licensesnumber'] = 'Кількість ліцензій';
$string['redefinelicenses'] = 'Переоголосити ліцензії';
$string['lowlicenses'] = 'Якщо кількість Ваших ліцензій перевисить необхідні, тоді коли Ви створите нову активність користувачем, вона буде призначена до PRO ліцензії зменьшуючи статус іншого користувача. Дана опція ефективна коли кількість активних PRO-ліцензій більше ніж 5.';
$string['maskparticipantdata'] = 'Сховати дані учасників';
$string['maskparticipantdata_help'] = 'Запобігає появленню даних пр учасників в звітах (корисне для сайтів що приховують дані користувача, наприклад, для HIPAA).';
$string['meeting_nonexistent_on_zoom'] = 'Не існує на Zoom';
$string['meeting_finished'] = 'Завершене';
$string['meeting_not_started'] = 'Не розпочалося';
$string['meetingoptions'] = 'Налаштування зустрічі';
$string['meetingoptions_help'] = "*Приєднатися перед хостом* дозволяє учасникам приєднуватись до конференції перед тим як зайде хост або коли хост не може відвідати зустріч.\n\n*Зал очікування* дозволяє хосту контролювати коли учасник приєднується до конференції.\n\nЦих дві опції залежні один від одного, тому вибір одного зніме вибір іншого. Також можливо зняти виділення з обох.\n\n*Зареєстровані користувачі* необхідність всіх учасників увійти у їхні активні zoom акаунти щоб приєднатися.";
$string['meeting_started'] = 'В процесі';
$string['meeting_time'] = 'Початок';
$string['modulename'] = 'Zoom конференції';
$string['modulenameplural'] = 'Zoom Конференції';
$string['modulename_help'] = 'Zoom це відео і веб конференційна платформа що дає зареєстрованим користувачам можливість надавати онлайн зустрічі.';
$string['newmeetings'] = 'Нова зустріч';
$string['nomeetinginstances'] = 'Не знайдено жодної сесії для цієї зустрічі.';
$string['noparticipants'] = 'Не знайдено учасників для цієї сесії на даний момент часу.';
$string['nosessions'] = 'Не знайдено жодної сесії для вказаного вибору.';
$string['nozooms'] = 'Нема зустрічей';
$string['off'] = 'Вимкнено';
$string['oldmeetings'] = 'Завершені зустрічі';
$string['on'] = 'Увімкнено';
$string['option_audio'] = 'Аудіо опції';
$string['option_authenticated_users'] = 'Тільки зареєстровані користувачі';
$string['option_host_video'] = 'Відео хост';
$string['option_jbh'] = 'Увімкнути приєднання перед хостом';
$string['option_mute_upon_entry'] = 'Вимкнути мікрофон при вході';
$string['option_mute_upon_entry_help'] = 'Автоматично вимикати мікрофон Всіх учасників коли Вони приєднуються до зустрічі. Хост контролює коли учасники можуть Увімкнути свої мікрофони.';
$string['option_participants_video'] = 'Відео учасників';
$string['option_proxyhost'] = 'Використовувати проксі';
$string['option_proxyhost_desc'] = 'Впишіть проксі тут як \'<code>&lt;hostname&gt;:&lt;port&gt;</code>\' це використовується тільки для зв\'язку з Zoom. Оставте пустим щоб використовувати стандартні налаштування проксі системи Moodle. Вам необхідно прописувати цей пункт тільки якщо ви не хочете виставляти глобальне проксі на Moodle.';
$string['option_waiting_room'] = 'Увімкнути зал очікування';
$string['participantdatanotavailable'] = 'Деталі не відомі';
$string['participantdatanotavailable_help'] = 'Дані по учасникам не доступні для цієї Zoom сесії (Наприклад, через HIPAA-відповідність).';
$string['participants'] = 'Учасники';
$string['password'] = 'Пароль';
$string['passwordprotected'] = 'Захищено паролем';
$string['pluginadministration'] = 'Керувати Zoom зустрічю';
$string['pluginname'] = 'Zoom meeting';
$string['privacy:metadata:zoom_meeting_details'] = 'Таблиця бази яка зберігає інформацію про кожну інстанцію зустрічей.';
$string['privacy:metadata:zoom_meeting_details:topic'] = 'Ім\'я зустрічі до якої приєднується користувач.';
$string['privacy:metadata:zoom_meeting_participants'] = 'Таблиця бази яка зберігає інформацію про учасників зустрічі.';
$string['privacy:metadata:zoom_meeting_participants:duration'] = 'Як довго учасники перебувала на зустрічі';
$string['privacy:metadata:zoom_meeting_participants:join_time'] = 'Час коли учасник приєднався до зустрічі';
$string['privacy:metadata:zoom_meeting_participants:leave_time'] = 'Час коли учасник покинув зустріч';
$string['privacy:metadata:zoom_meeting_participants:name'] = 'Ім\'я учасника';
$string['privacy:metadata:zoom_meeting_participants:user_email'] = 'Е-мейл учасника';
$string['recurringmeeting'] = 'Повторювана';
$string['recurringmeeting_help'] = 'Немає кінцевої дати';
$string['recurringmeetinglong'] = 'Повторювана зустріч (зустріч без кінцевої дати та часу)';
$string['recycleonjoin'] = 'Переробити ліцензію після приєднання';
$string['licenseonjoin'] = 'Вибирайте цю опцію якщо Ви хочете хосту надати ліцензію перед початком зустрічі, <i>так само як</i> підчас створення.';
$string['report'] = 'Звіти';
$string['reportapicalls'] = 'Виклик API звітів виснажений';
$string['resetapicalls'] = 'Поновити кількість доступних викликів API';
$string['schedulefor'] = 'Запланувати зустріч для';
$string['scheduleforself'] = 'Запланувати для себе';
$string['search:activity'] = 'Zoom - інформація про діяльність';
$string['sessions'] = 'Сесії';
$string['start'] = 'Почати';
$string['starthostjoins'] = 'Увімкнути відео Коли приєднається хост';
$string['start_meeting'] = 'Почати зустріч';
$string['startpartjoins'] = 'Увімкнути відео коли зайдуть учасники';
$string['start_time'] = 'Коли';
$string['starttime'] = 'Час початку';
$string['status'] = 'Статус';
$string['title'] = 'Заголовок';
$string['topic'] = 'Тема';
$string['unavailable'] = 'Зараз неможливо приєднатися';
$string['updatemeetings'] = 'Оновити налаштування конференції для Zoom';
$string['usepersonalmeeting'] = 'Використовуйте персональний ID конференції {$a}';
$string['waitingroom'] = 'Зал очікування увімкнений';
$string['webinar'] = 'Вебінар';
$string['webinar_help'] = 'Ця опція доступна лише для пре-авторизованих Zoom акаунтів.';
$string['webinar_already_true'] = '<p><b>Цей модуль вже встановлений як вебінар, не зустріч. Ви не можете змінити це налаштування після створення вебінару.</b></p>';
$string['webinar_already_false'] = '<p><b>Цей модуль вже встановлений як зустріч, не вебінар. Ви не можете змінити це налаштування після створення зустрічі.</b></p>';
$string['zoom:addinstance'] = 'Додати нову зустріч Zoom';
$string['zoomerr'] = 'Виникла помилка з Zoom.'; // Generic error.
$string['zoomerr_apikey_missing'] = 'Zoom API ключ не знайдено';
$string['zoomerr_apisecret_missing'] = 'Секрет Zoom API не знайдено';
$string['zoomerr_id_missing'] = 'Ви маєте вказати ID курса або ID інстансу';
$string['zoomerr_licensesnumber_missing'] = 'Zoom максимальні налаштування знайдені але, налаштування \'licensesnumber\' не знайдено';
$string['zoomerr_maxretries'] = 'Спробувано {$a->maxretries} разів щоб дозвонитися, але невдало: {$a->response}';
$string['zoomerr_meetingnotfound'] = 'Ця зустріч не моде бути знайдена на Zoom. Ви можете <a href="{$a->recreate}">перестворити її тут</a> або <a href="{$a->delete}">видалити її зовсім</a>.';
$string['zoomerr_meetingnotfound_info'] = 'Ця зустріч не моде бути знайдена на Zoom. Будь ласка зверніться до хосту зустрічі якщо Ви маєте питання.';
$string['zoomerr_usernotfound'] = 'Неможливо знайти Ваш аккаунт на Zoom. Якщо Ви використовуєте Zoom вперше, Ви повинні активувати Ваш Zoom аккаунт ввійшовши за <a href="{$a}" target="_blank">{$a}</a>. Коли Ви активували Ваш Zoom аккаунт, перезавантажте цю сторінку і продовжуйте налаштовувати Вашу зустріч. Інакше переконайтесь що Ваш е-мейл на Zoom відповідає вашому е-мейлу в цій системі.';
$string['zoomurl'] = 'Домашня сторінка Zoom';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'Показати зустрічі Zoom';
