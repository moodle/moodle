<?PHP // $Id$ 
      // chat.php - created with Moodle 1.4.1+ (2004083101)


$string['chatintro'] = 'Въвеждащ текст';
$string['chatname'] = 'Име на стаята';
$string['chatreport'] = 'Сесии';
$string['chattime'] = 'Следваща сесия';
$string['configmethod'] = 'Обикновенния метод за чат изисква клиентите периодично да се свързват със сървъра, за обновления. Той не изисква допълнителни настройки и работи навсякъде, но може да създаде голямо натоварване за сървъри, обслужващи много чатъри. Използването на сървърен демон изисква достъп до UNIX конзола, но осигурява бърза и скалируема среда.';
$string['configoldping'] = 'Максималния период (в секунди), след който трябва да е регистрирано прекъсването на връзката с потребителя. Това е просто горна граница. Обикновенно прекъсванията се отчитат доста бързо. Имайте в предвид, че по-ниските стойности водят до по-голямо натоварване на сървъра. Ако използвате нормалния метод <strong>никога</strong> не задавайте стойност по-малка от 2 * chat_refresh_room.';
$string['configrefreshroom'] = 'Колко често трябва да се обновява стаята? (Стойността трябва да е в секунди.) По-малките стойности правят сървъра да изглежда \'бърз\', но водят до по-голямо натоварване (особено при много чатъри).';
$string['configrefreshuserlist'] = 'Колко често трябва да се обновява списъка с потребителите? (В секунди)';
$string['configserverhost'] = 'Име на компютъра, на който работи сървърния демон';
$string['configserverip'] = 'IP адреса, който съответства на горното име';
$string['configservermax'] = 'Максимален брой на клиентите';
$string['configserverport'] = 'Порт';
$string['currentchats'] = 'Активни чат-сесии';
$string['currentusers'] = 'Текущи потребители';
$string['deletesession'] = 'Изтриване на тази сесия';
$string['deletesessionsure'] = 'Сигурни ли сте, че искате да изтриете тази сесия?';
$string['donotusechattime'] = 'Без публикуване на време';
$string['enterchat'] = 'Щракнете, за да влезете в чата';
$string['errornousers'] = 'Не са открити потребители!';
$string['explaingeneralconfig'] = 'Тези настройки са <strong>винаги</strong> в сила';
$string['explainmethoddaemon'] = 'Тези настройки са в сила <strong>само</strong> ако използвате чат-демона';
$string['explainmethodnormal'] = 'Тези настройки са в сила <strong>само</strong> ако използвате нормалния метод';
$string['generalconfig'] = 'Общи настройки';
$string['helpchatting'] = 'Помощ при чатенето';
$string['messageenter'] = '$a влезе в чата';
$string['messageexit'] = '$a излезе от чата';
$string['messages'] = 'Съобщения';
$string['methoddaemon'] = 'Чат-демон';
$string['methodnormal'] = 'Обикновен метод';
$string['modulename'] = 'Чат';
$string['modulenameplural'] = 'Чатове';
$string['neverdeletemessages'] = 'Без изтриване на съобщения';
$string['nextsession'] = 'Следваща сесия';
$string['noguests'] = 'Този чат не е достъпен за гости';
$string['nomessages'] = 'Няма съобщения';
$string['repeatdaily'] = 'По едно и също време всеки ден';
$string['repeatnone'] = 'Без повторения - публикуване само на указаното време';
$string['repeattimes'] = 'Повтарящи се сесии';
$string['repeatweekly'] = 'По едно и също време всяка седмица';
$string['savemessages'] = 'Запис на изминалата сесия';
$string['seesession'] = 'Преглед на сесията';
$string['sessions'] = 'Сесии';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'Всеки може да преглежда записаните сесии';
$string['viewreport'] = 'Преглед на минали сесии';

?>
