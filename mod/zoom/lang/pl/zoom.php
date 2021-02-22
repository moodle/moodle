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
 * Polish strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Dziłania';
$string['addtocalendar'] = 'Dodać do kalendarza';
$string['alternative_hosts'] = 'Alternatywne Hosty';
$string['alternative_hosts_help'] = 'Opcja alternatywnego hosta pozwala planować spotkania i desygnować drugiego Pro usera na tym że koncie żeby zacząć spotkanie albo webinar, jeżeli nie możecie. Użytkowniki otrzymają email powiadamiając ich że oni byli dodani jako alternatywni hosty, z linkiem dla początku spotkania. Rozdzielać wielu emeilów przecinkiem (bez spacji).';
$string['allmeetings'] = 'Wszystkie spotkania';
$string['apikey'] = 'Kluć Zoom API';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Secret Zoom API';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Link do Zoom API';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'VoIP i Telephonia';
$string['audio_telephony'] = 'Tylko telephonia';
$string['audio_voip'] = 'Tylko VoIP';
$string['cachedef_zoomid'] = 'ID usera Zoom';
$string['cachedef_sessions'] = 'Informacja-raport z zoom o userach';
$string['calendardescriptionURL'] = 'Link dla połączenia: {$a}.';
$string['calendardescriptionintro'] = "\nOpis:\n{\$a}";
$string['calendariconalt'] = 'Ikona kalendarza';
$string['changehost'] = 'Zmienić host';
$string['clickjoin'] = 'Kliknięta przycisk dla połączenia';
$string['connectionok'] = 'Połączenie działa.';
$string['connectionfailed'] = 'Bład połąchenia: ';
$string['connectionstatus'] = 'Status połąchenia';
$string['defaultsettings'] = 'Standardowe ustawienia Zoom';
$string['defaultsettings_help'] = 'Ustawienia definiuje wartości domyślne dla nowych Zoom spotkań i webinarów.';
$string['downloadical'] = 'Ściągnąć iCal';
$string['duration'] = 'Trwanie (minuty)';
$string['endtime'] = 'Koniec';
$string['err_duration_nonpositive'] = 'Trwanie musi być posytywne.';
$string['err_duration_too_long'] = 'Trwanie nie może być więcej niż 150 godzin.';
$string['err_long_timeframe'] = 'Potrzebowana długość terminu jest za duża, pokazywany jest termin "za ostatni miesiąc".';
$string['err_invalid_password'] = 'Hasło mieści nieprawidłowe znaki.';
$string['err_password'] = 'Hasło może mieć tylko następujące znaki: [a-z A-Z 0-9 @ - _ *]. Maksymalnie 10 znaków.';
$string['err_password_required'] = 'Potrzebuje hasło.';
$string['err_start_time_past'] = 'Początkowa data nie może być w przeszłości.';
$string['errorwebservice_badrequest'] = 'Zoom otrzymał zły request: {$a}';
$string['errorwebservice_notfound'] = 'Resurs nie istnieje: {$a}';
$string['errorwebservice'] = 'Bład pracy webserwisa Zoom: {$a}.';
$string['export'] = 'Export';
$string['firstjoin'] = 'Pierwszy w stanie się dołączyć';
$string['firstjoin_desc'] = 'Czas kiedy użytkownik może dołączyć się do spotkania (minuty przed startem).';
$string['getmeetingreports'] = 'Otrzymać raport spotkania od Zooma';
$string['host'] = 'Host';
$string['invalidscheduleuser'] = 'Nie możesz zaplanować to dla tego użytkownika.';
$string['invalid_status'] = 'Nieprawidłowy status, proszę sprawdzić bazę danych.';
$string['join'] = 'Dołączyć się';
$string['joinbeforehost'] = 'Dołączyć się do spotkania przed hostem';
$string['join_link'] = 'Link dla połączenia';
$string['join_meeting'] = 'Dołączyć się do spotkania';
$string['jointime'] = 'Czas połączenia';
$string['leavetime'] = 'Czas odłączenia';
$string['licensesnumber'] = 'Liczba licencji';
$string['redefinelicenses'] = 'Przedefiniować licencji';
$string['lowlicenses'] = 'Jeżeli ilość licencji przekracza wymagane, wtedy kiedy utworzysz każde nowe "Działanie" użytkownikiem, ono będzie przyznaczono dla PRO licencji obniżając status innego użytkownika. Opcja effectywna kiedy ilość aktywnych PRO-licencji więcej niż 5.';
$string['maskparticipantdata'] = 'Ukryć dane uczęstników';
$string['maskparticipantdata_help'] = 'Zapobiega pojawy danych o uczęstnikach w raportach (przydatny dla stron krórę ukrywa dane uczęstników, e.g., for HIPAA).';
$string['meeting_nonexistent_on_zoom'] = 'Nieistnieję na Zoom';
$string['meeting_finished'] = 'Skończone';
$string['meeting_not_started'] = 'Nie rozpoczęto';
$string['meetingoptions'] = 'Opcje spotkania';
$string['meetingoptions_help'] = "*Dołączyć się do spotkania przed hostem* pozwala uczęstnikam dołączyć się do konferencji prhed tym jak dołączy host albo kiedy sam host nie może przebywać na spotkaniu.\n\n*Poczekalnia* pozwala hostu controlować kiedy uczęstniki dołączająć sie do spotkania.\n\nTe dwie opcje wzajemnie się wykluczające, więc wybranie jednego spowoduje odznaczenie drugiego. Możliwe jest również odznaczenie obu z nich.\n\n*Uwierzytelnieni użytkownicy* wymagają, aby wszyscy uczestnicy zalogowali się na swoje autoryzowane konto Zoom, aby móc dołączyć.";
$string['meeting_started'] = 'W trakcie';
$string['meeting_time'] = 'Czas początku';
$string['modulename'] = 'Zoom spotkania';
$string['modulenameplural'] = 'Zoom Spotkania';
$string['modulename_help'] = 'Zoom to platforma do wideokonferencji i konferencji internetowych, która daje upoważnionym użytkownikom możliwość organizowania spotkań online.';
$string['newmeetings'] = 'Nowe spotkanie';
$string['nomeetinginstances'] = 'Nie znaleziono sesji dla tego spotkania.';
$string['noparticipants'] = 'W tej chwili nie znaleziono uczestników tej sesji.';
$string['nosessions'] = 'Nie znaleziono sesji dla określonego zakresu.';
$string['nozooms'] = 'Niema spotkań';
$string['off'] = 'Wyłączono';
$string['oldmeetings'] = 'Zakończone spotkania';
$string['on'] = 'Włączono';
$string['option_audio'] = 'Opcji audio';
$string['option_authenticated_users'] = 'Tylko uwierzytelnieni użytkownicy';
$string['option_host_video'] = 'Hostować wideo';
$string['option_jbh'] = 'Włącz dołączanie przed hostem';
$string['option_mute_upon_entry'] = 'Wycisz przy wejściu';
$string['option_mute_upon_entry_help'] = 'Automatycznie wyciszaj wszystkich uczestników, gdy dołączają do spotkania. Gospodarz kontroluje, czy uczestnicy mogą wyłączyć wyciszenie.';
$string['option_participants_video'] = 'Wideo uczestników';
$string['option_proxyhost'] = 'Używać proxy';
$string['option_proxyhost_desc'] = 'Serwer proxy ustawiono tutaj jako \'<code>&lt;hostname&gt;:&lt;port&gt;</code>\' służy tylko do komunikacji z Zoom. Pozostaw puste, aby użyć domyślnych ustawień proxy Moodle. Musisz to ustawić tylko wtedy, gdy nie chcesz ustawiać globalnego proxy w Moodle.';
$string['option_waiting_room'] = 'Włącz poczekalniu';
$string['participantdatanotavailable'] = 'Szczegóły niedostępne';
$string['participantdatanotavailable_help'] = 'Dane uczestnika nie są dostępne dla tej sesji Zoom (np. Ze względu na zgodność z HIPAA).';
$string['participants'] = 'Uczestniki';
$string['password'] = 'Hasło';
$string['passwordprotected'] = 'Ochrona hasłem';
$string['pluginadministration'] = 'Zarządzać spotkaniem Zoom';
$string['pluginname'] = 'Zoom meeting';
$string['privacy:metadata:zoom_meeting_details'] = 'Tabela bazy danych, w której są przechowywane informacje o każdej instancji spotkania.';
$string['privacy:metadata:zoom_meeting_details:topic'] = 'Nazwa spotkania, w którym uczestniczył użytkownik.';
$string['privacy:metadata:zoom_meeting_participants'] = 'Tabela bazy danych, w której są przechowywane informacje o uczestnikach spotkania.';
$string['privacy:metadata:zoom_meeting_participants:duration'] = 'Jak długo uczestnik był na spotkaniu';
$string['privacy:metadata:zoom_meeting_participants:join_time'] = 'Czas, kiedy uczestnik dołączył do spotkania';
$string['privacy:metadata:zoom_meeting_participants:leave_time'] = 'Czas, kiedy uczestnik opuścił spotkanie';
$string['privacy:metadata:zoom_meeting_participants:name'] = 'Imię i nazwisko uczestnika';
$string['privacy:metadata:zoom_meeting_participants:user_email'] = 'E-mail uczestnika';
$string['recurringmeeting'] = 'Spotkanie cykliczne';
$string['recurringmeeting_help'] = 'Nie ma daty zakończenia';
$string['recurringmeetinglong'] = 'Spotkanie cykliczne (spotkanie bez daty i godziny zakończenia)';
$string['recycleonjoin'] = 'Odzyskaj licencję po dołączeniu';
$string['licenseonjoin'] = 'Wybierz tę opcję, jeśli chcesz, aby gospodarz otrzymał licencję podczas rozpoczynania spotkania, <i> oraz </i> podczas tworzenia.';
$string['report'] = 'Raporty';
$string['reportapicalls'] = 'Zgłoś wyczerpane wywołania interfejsu API';
$string['resetapicalls'] = 'Zresetuj liczbę dostępnych wywołań interfejsu API';
$string['schedulefor'] = 'Zaplanować spotkanie dla';
$string['scheduleforself'] = 'Zaplanować dla siebie';
$string['search:activity'] = 'Zoom - informacje o działalności';
$string['sessions'] = 'Sesje';
$string['start'] = 'Początek';
$string['starthostjoins'] = 'Rozpocznij wideo, gdy dołączy gospodarz';
$string['start_meeting'] = 'Rozpocznij spotkanie';
$string['startpartjoins'] = 'Włącz wideo, gdy uczestnik dołączy';
$string['start_time'] = 'Kiedy';
$string['starttime'] = 'Czas rozpoczęcia';
$string['status'] = 'Status';
$string['title'] = 'Tytuł';
$string['topic'] = 'Temat';
$string['unavailable'] = 'W tej chwili nie można dołączyć';
$string['updatemeetings'] = 'Zaktualizować ustawienia spotkania z Zoom';
$string['usepersonalmeeting'] = 'Użyć osobistego identyfikatora spotkania {$a}';
$string['waitingroom'] = 'Poczekalnia włączona';
$string['webinar'] = 'Webinar';
$string['webinar_help'] = 'Ta opcja jest dostępna tylko dla wstępnie autoryzowanych kont Zoom.';
$string['webinar_already_true'] = '<p> <b> Ten moduł został już ustawiony jako webinar, a nie spotkanie. Nie możesz zmienić tego ustawienia po utworzeniu webinaru. </b> </p>';
$string['webinar_already_false'] = '<p> <b> Ten moduł został już ustawiony jako spotkanie, a nie webinar. Nie możesz zmienić tego ustawienia po utworzeniu spotkania. </b> </p>';
$string['zoom:addinstance'] = 'Dodać nowe spotkanie Zoom';
$string['zoomerr'] = 'Wystąpił błąd podczas korzystania z funkcji Zoom.'; // Generic error.
$string['zoomerr_apikey_missing'] = 'Nie znaleziono klucz Zoom API';
$string['zoomerr_apisecret_missing'] = 'Nie znaleziono sekret Zoom API';
$string['zoomerr_id_missing'] = 'Musisz określić identyfikator modułu kursu lub identyfikator instancji';
$string['zoomerr_licensesnumber_missing'] = 'Znaleziono największe ustawienie powiększenia, ale nie znaleziono ustawienia numeru licencji';
$string['zoomerr_maxretries'] = 'Ponowna próba wykonania połączenia {$a->maxretries} razy, ale zawiodło: {$a->response}';
$string['zoomerr_meetingnotfound'] = 'Tego spotkania nie można znaleźć w Zoom. Możesz <a href="{$a->recreate}">ponownie utworzyć tutaj</a> lub <a href="{$a->delete}">całkowicie usunąć</a>.';
$string['zoomerr_meetingnotfound_info'] = 'Tego spotkania nie można znaleźć w Zoom. Jeśli masz pytania, skontaktuj się z gospodarzem spotkania.';
$string['zoomerr_usernotfound'] = 'Nie można znaleźć Twojego konta w Zoom. Jeśli używasz Zoom po raz pierwszy, musisz aktywować swoje konto Zoom, logując się do <a href="{$a}" target="_blank">{$a}</a>. Po aktywowaniu konta Zoom załaduj ponownie tę stronę i kontynuuj konfigurowanie spotkania. W przeciwnym razie upewnij się, że Twój adres e-mail w Zoom jest zgodny z adresem e-mail w tym systemie.';
$string['zoomurl'] = 'Adres strony domowej Zoom';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'Pokazać spotkania Zoom';
