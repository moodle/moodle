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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

/*
 * To change this template, choose Tools | Templates.
 * and open the template in the editor.
 */

// General.
$string['pluginname'] = 'Wtyczka plagiatu Turnitin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Zadanie wtyczki plagiatu Turnitin';
$string['connecttesterror'] = 'Wystąpił błąd podczas łączenia z Turnitin. Poniżej znajduje się informacja o błędzie:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Włącz Turnitin';
$string['excludebiblio'] = 'Pomiń bibliografię';
$string['excludequoted'] = 'Wyklucz cytaty';
$string['excludevalue'] = 'Wyklucz małe dopasowania';
$string['excludewords'] = 'Słowa';
$string['excludepercent'] = 'Procent';
$string['norubric'] = 'Brak arkusza';
$string['otherrubric'] = 'Użyj arkusza należącego do innego instruktora';
$string['attachrubric'] = 'Dołącz arkusz do tego zadania';
$string['launchrubricmanager'] = 'Uruchom Menedżera Arkuszy';
$string['attachrubricnote'] = 'Uwaga: Przed dokonaniem wysyłki studenci będą mogli zobaczyć dołączone arkusze wraz z ich zawartością.';
$string['anonblindmarkingnote'] = 'Uwaga: Oddzielne ustawienie anonimowych poprawek Turnitin zostało usunięte. Do ustalenia statusu ustawienia anonimowych poprawek zostanie użyte ustawienie ślepych poprawek usługi Moodle.';
$string['transmatch'] = 'Przetłumaczone zbieżności';
$string["reportgen_immediate_add_immediate"] = "Generuj raporty natychmiast. Wysyłki zostaną natychmiast dodane do magazynu (jeśli magazyn został ustawiony).";
$string["reportgen_immediate_add_duedate"] = "Generuj raporty natychmiast. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).";
$string["reportgen_duedate_add_duedate"] = "Generuj raporty w danym terminie. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).";
$string['launchquickmarkmanager'] = 'Uruchom Menedżera Quickmark';
$string['launchpeermarkmanager'] = 'Uruchom Menedżera Peermark';
$string['studentreports'] = 'Wyświetl raporty oryginalności studentom';
$string['studentreports_help'] = 'Umożliwia wyświetlenie raportów oryginalności Turnitin studentom-użytkownikom. Wybór „tak” udostępnia raport oryginalności wygenerowany przez Turnitin do wglądu studenta.';
$string['submitondraft'] = 'Przekaż plik w momencie wysłania';
$string['submitonfinal'] = 'Przekaż plik kiedy student wyśle do oceny';
$string['draftsubmit'] = 'Kiedy plik powinien być przekazany do Turnitin?';
$string['allownonor'] = 'Zezwolić na wysyłkę plików dowolnego typu?';
$string['allownonor_help'] = 'To ustawienie umożliwi wysłanie każdego rodzaju pliku. Wybór &#34;Tak&#34; spowoduje, że wysyłki zostaną sprawdzone pod względem oryginalności tam, gdzie to możliwe, wysyłki będą dostępne do pobrania, a narzędzia do wydawania opinii GradeMark będą dostępne w miarę możliwości.';
$string['norepository'] = 'Brak magazynu';
$string['standardrepository'] = 'Magazyn standardowy';
$string['submitpapersto'] = 'Przechowuj prace studentów';
$string['institutionalrepository'] = 'Magazyn instytucji (jeśli dotyczy)';
$string['checkagainstnote'] = 'Uwaga: W przypadku niewybrania ustawienia „Tak” w co najmniej jednej opcji „Sprawdzić w...” widocznej poniżej raport oryginalności NIE zostanie utworzony.';
$string['spapercheck'] = 'Porównaj z przechowywanymi pracami studentów';
$string['internetcheck'] = 'Sprawdź w Internecie';
$string['journalcheck'] = 'Sprawdź w czasopismach,<br />periodykach i publikacjach';
$string['compareinstitution'] = 'Porównaj wysłane pliki z pracami wysłanymi wewnątrz tej instytucji';
$string['reportgenspeed'] = 'Szybkość generowania raportu';
$string['locked_message'] = 'Komunikat o blokadzie';
$string['locked_message_help'] = 'Jeżeli jakiekolwiek ustawienia są zablokowane, ten wyświetlany komunikat będzie zawierał wyjaśnienie powodu blokady.';
$string['locked_message_default'] = 'To ustawienie jest zablokowane na poziomie strony internetowej';
$string['sharedrubric'] = 'Udostępniany arkusz';
$string['turnitinrefreshsubmissions'] = 'Odśwież wysyłki';
$string['turnitinrefreshingsubmissions'] = 'Odświeżanie wysyłek';
$string['turnitinppulapre'] = 'Aby przesłać plik do Turnitin, należy najpierw zaakceptować umowę EULA. Brak akceptacji umowy EULA spowoduje przesłanie pliku wyłącznie do Moodle. Kliknij tutaj, aby przeczytać i zaakceptować umowę.';
$string['noscriptula'] = '(Ponieważ javascript nie jest włączony, strona wymaga manualnego odświeżenia przed dokonaniem wysyłki i po zaakceptowaniu umowy użytkownika Turnitin)';
$string['filedoesnotexist'] = 'Plik został usunięty';
$string['reportgenspeed_resubmission'] = 'Już wysłałeś pracę do tego zadania i został wygenerowany raport podobieństwa dla Twojej wysyłki. Jeśli zdecydujesz się wysłać ponownie swoją pracę, Twoja wcześniejsza wysyłka zostanie zastąpiona i zostanie wygenerowany nowy raport. W przypadku {$a->num_resubmissions} ponownych wysyłek trzeba poczekać {$a->num_hours} godzin od ponownego wysłania, aby zobaczyć nowy raport podobieństwa.';

// Plugin settings.
$string['config'] = 'Konfiguracja';
$string['defaults'] = 'Ustawienia domyślne';
$string['showusage'] = 'Pokaż zrzut danych';
$string['saveusage'] = 'Zachowaj zrzut danych';
$string['errors'] = 'Błędy';
$string['turnitinconfig'] = 'Konfiguracja wtyczki plagiatu Turnitin';
$string['tiiexplain'] = 'Turnitin jest produktem komercyjnym — do korzystania z niego wymagana jest płatna subskrypcja. Więcej informacji znajduje się w <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Włącz Turnitin';
$string['useturnitin_mod'] = 'Włącz Turnitin dla {$a}';
$string['turnitindefaults'] = 'Ustawienia domyślne wtyczki plagiatu Turnitin';
$string['defaultsdesc'] = 'Następujące ustawienia są ustawieniami domyślnymi gdy Turnitin jest włączony wewnątrz modułu aktywności';
$string['turnitinpluginsettings'] = 'Ustawienia wtyczki plagiatu Turnitin';
$string['pperrorsdesc'] = 'Podczas próby wysłania poniższych plików do Turnitin wystąpił problem. Aby wysłać je ponownie, wybierz pliki, które mają zostać ponownie wysłane, i naciśnij przycisk Wyślij ponownie. Pliki zostaną przetworzone przy następnym uruchomieniu Cron.';
$string['pperrorssuccess'] = 'Wybrane pliki zostały wysłane ponownie i zostaną przetworzone przez Cron.';
$string['pperrorsfail'] = 'W przypadku niektórych wybranych plików wystąpił problem. Utworzenie dla nich nowego zdarzenia Cron nie powiodło się.';
$string['resubmitselected'] = 'Wyślij wybrane pliki ponownie';
$string['deleteconfirm'] = 'Czy na pewno chcesz usunąć tę wysyłkę?\n\nTej operacji nie można cofnąć.';
$string['deletesubmission'] = 'Usunąć wysyłkę';
$string['semptytable'] = 'Nie znaleziono rezultatów.';
$string['configupdated'] = 'Konfiguracja zaktualizowana';
$string['defaultupdated'] = 'Ustawienia domyślne Turnitin zaktualizowane';
$string['notavailableyet'] = 'Niedostępny';
$string['resubmittoturnitin'] = 'Wyślij ponownie do Turnitin';
$string['resubmitting'] = 'Ponowne wysyłanie';
$string['id'] = 'Identyfikator';
$string['student'] = 'Student';
$string['course'] = 'Kurs';
$string['module'] = 'Moduł';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Zobacz raport oryginalności';
$string['launchrubricview'] = 'Zobacz arkusz użyty do poprawek';
$string['turnitinppulapost'] = 'Plik nie został przesłany do Turnitin. Kliknij tutaj, aby zaakceptować naszą umowę EULA.';
$string['ppsubmissionerrorseelogs'] = 'Plik nie został przesłany do Turnitin. Skontaktuj się z administratorem systemu.';
$string['ppsubmissionerrorstudent'] = 'Plik nie został przesłany do Turnitin. Skontaktuj się z tutorem, aby uzyskać więcej informacji.';

// Receipts.
$string['messageprovider:submission'] = 'Powiadomienia o potwierdzeniu elektronicznym wtyczki plagiatu Turnitin';
$string['digitalreceipt'] = 'Potwierdzenie elektroniczne';
$string['digital_receipt_subject'] = 'To jest Twoje potwierdzenie elektroniczne Turnitin';
$string['pp_digital_receipt_message'] = 'Witaj {$a->firstname} {$a->lastname},<br /><br />Udało Ci się pomyślnie przesłać plik <strong>{$a->submission_title}</strong> do zadania <strong>{$a->assignment_name}{$a->assignment_part}</strong> w ramach klasy <strong>{$a->course_fullname}</strong> dnia <strong>{$a->submission_date}</strong>. Twój identyfikator wysyłki to <strong>{$a->submission_id}</strong>. Pełne potwierdzenie elektroniczne można wyświetlić i wydrukować, używając przycisku drukowania/pobierania w przeglądarce dokumentów.<br /><br />Dziękujemy za korzystanie z systemu Turnitin.<br /><br />Zespół Turnitin';

// Paper statuses.
$string['turnitinid'] = 'Identyfikator Turnitin';
$string['turnitinstatus'] = 'Status systemu Turnitin';
$string['pending'] = 'Oczekujące';
$string['similarity'] = 'Podobieństwo';
$string['notorcapable'] = 'Wygenerowanie raportu oryginalności dla tego pliku jest niemożliwe.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Student zobaczył pracę';
$string['student_notread'] = 'Student nie zobaczył tej pracy.';
$string['launchpeermarkreviews'] = 'Uruchom recenzje Peermark';

// Cron.
$string['ppqueuesize'] = 'Liczba zdarzeń w kolejce zdarzeń wtyczki plagiatu';
$string['ppcronsubmissionlimitreached'] = 'Uruchomiona instancja Cron umożliwia przetworzenie jednorazowo {$a} wysyłek, dlatego kolejne wysyłki nie będą przez nią przesyłane do Turnitin';
$string['cronsubmittedsuccessfully'] = 'Wysyłka: {$a->title} (Identyfikator Turnitin: {$a->submissionid}) do zadania {$a->assignmentname} w ramach kursu {$a->coursename} została pomyślnie przesłana do Turnitin.';
$string['pp_submission_error'] = 'System Turnitin zwrócił błąd dotyczący Twojej wysyłki:';
$string['turnitindeletionerror'] = 'Usunięcie wysyłki do Turnitin nie powiodło się. Lokalna kopia Moodle została usunięta, ale wysyłka do Turnitin nie może być usunięta.';
$string['ppeventsfailedconnection'] = 'Wtyczka plagiatu Turnitin uruchomiona w ramach tej instancji Cron nie będzie przetwarzać żadnych zdarzeń, ponieważ ustanowienie połączenia z Turnitin nie jest możliwe.';

// Error codes.
$string['tii_submission_failure'] = 'Aby uzyskać więcej informacji, skonsultuj się ze swoim tutorem lub administratorem systemu';
$string['faultcode'] = 'Kod błędu';
$string['line'] = 'Wiersz';
$string['message'] = 'Wiadomość';
$string['code'] = 'Kod';
$string['tiisubmissionsgeterror'] = 'Wystąpił błąd podczas próby uzyskania przesyłek do tego zadania z Turnitin';
$string['errorcode0'] = 'Plik nie został przesłany do Turnitin. Skontaktuj się z administratorem systemu.';
$string['errorcode1'] = 'Ten plik nie został przesłany do Turnitin, ponieważ nie zawiera treści w ilości wystarczającej do wygenerowania raportu oryginalności.';
$string['errorcode2'] = 'Ten plik nie zostanie przesłany do Turnitin, ponieważ jego wielkość przekracza dozwolony rozmiar {$a->maxfilesize}';
$string['errorcode3'] = 'Plik nie został przesłany do Turnitin, ponieważ użytkownik nie zaakceptował umowy licencyjnej użytkownika końcowego Turnitin.';
$string['errorcode4'] = 'Musisz wysłać typ pliku obsługiwany przez to zadanie. Akceptowane typy pliku to: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps oraz .rtf';
$string['errorcode5'] = 'Ten plik nie został wysłany do Turnitin, ponieważ wystąpił problem z utworzeniem modułu w Turnitin, co uniemożliwia dokonywanie przesyłek. Szczegółowe informacje znajdują się w dziennikach API';
$string['errorcode6'] = 'Ten plik nie został wysłany do Turnitin, ponieważ wystąpił problem z edycją ustawień modułu w Turnitin, co uniemożliwia dokonywanie przesyłek. Szczegółowe informacje znajdują się w dziennikach API';
$string['errorcode7'] = 'Ten plik nie został wysłany do Turnitin, ponieważ wystąpił problem z utworzeniem użytkownika w Turnitin, co uniemożliwia dokonywanie przesyłek. Szczegółowe informacje znajdują się w dziennikach API';
$string['errorcode8'] = 'Ten plik nie został wysłany do Turnitin, ponieważ wystąpił problem z utworzeniem pliku tymczasowego. Najbardziej prawdopodobną przyczyną jest nieprawidłowa nazwa pliku. Zmień nazwę pliku i wyślij go ponownie za pomocą opcji Edytuj wysyłkę.';
$string['errorcode9'] = 'Plik nie może zostać wysłany, ponieważ w puli pliku nie znajduje się żadna dostępna treść, którą można wysłać.';
$string['coursegeterror'] = 'Nie udało się pobrać danych kursu';
$string['configureerror'] = 'Musisz w pełni skonfigurować ten moduł jako administrator, aby użyć go w kursie. Skontaktuj się z administratorem Moodle.';
$string['turnitintoolofflineerror'] = 'Wystąpiły tymczasowe trudności. Spróbuj ponownie później.';
$string['defaultinserterror'] = 'Wystąpił błąd podczas próby wprowadzenia ustawienia domyślnego do bazy danych';
$string['defaultupdateerror'] = 'Wystąpił błąd podczas próby aktualizacji ustawienia domyślnego w bazie danych';
$string['tiiassignmentgeterror'] = 'Wystąpił błąd podczas próby uzyskania zadania z Turnitin';
$string['assigngeterror'] = 'Nie udało się uzyskać danych narzędzia Turnitin';
$string['classupdateerror'] = 'Aktualizacja danych klasy Turnitin nie powiodła się';
$string['pp_createsubmissionerror'] = 'Wystąpił błąd podczas próby dokonania wysyłki w Turnitin';
$string['pp_updatesubmissionerror'] = 'Wystąpił błąd podczas próby dokonania ponownej wysyłki do Turnitin';
$string['tiisubmissiongeterror'] = 'Wystąpił błąd podczas próby uzyskania przesyłki z Turnitin';

// Javascript.
$string['closebutton'] = 'Zamknij';
$string['loadingdv'] = 'Wczytywanie przeglądarki dokumentów Turnitin...';
$string['changerubricwarning'] = 'Zmiana lub odłączenie arkusza spowoduje usunięcie wszystkich wyników arkusza za prace z tego zadania, łącznie z naliczonymi wcześniej kartami wyników. Ogólne oceny za wcześniej ocenione prace pozostaną bez zmian.';
$string['messageprovider:submission'] = 'Powiadomienia o potwierdzeniu elektronicznym wtyczki plagiatu Turnitin';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Status systemu Turnitin';
$string['deleted'] = 'Usunięto';
$string['pending'] = 'Oczekujące';
$string['because'] = 'Powodem tego jest usunięcie przez administratora oczekującego zadania z kolejki przetwarzania i przerwanie wysyłki do Turnitin.<br /><strong>Plik w dalszym ciągu znajduje się w systemie Moodle. Skontaktuj się z instruktorem.</strong><br />Poniżej znajdują się kody błędu (jeżeli są dostępne):';
$string['submitpapersto_help'] = '<strong>Brak magazynu: </strong><br />Turnitin nie będzie przechowywać wysłanych dokumentów w żadnym magazynie. Przetworzymy pracę tylko w celu przeprowadzenia wstępnej weryfikacji podobieństwa.<br /><br /><strong>Magazyn standardowy: </strong><br />Turnitin będzie przechowywać kopię wysłanego dokumentu tylko w magazynie standardowym. Wybór tej opcji powoduje, że Turnitin będzie wykorzystywać przechowywane dokumenty tylko w celu przeprowadzenia weryfikacji podobieństwa do dokumentów wysłanych w przyszłości.<br /><br /><strong>Magazyn instytucji (jeśli dotyczy): </strong><br />Wybranie tej opcji spowoduje, że Turnitin będzie dodawać wysłane dokumenty tylko do prywatnego magazynu instytucji. Weryfikację podobieństwa wysłanych dokumentów będą przeprowadzać inni instruktorzy z instytucji. ';
$string['errorcode12'] = 'Ten plik nie został wysłany do Turnitin, ponieważ należy on do zadania powiązanego z usuniętym kursem. Identyfikator wiersza: ({$a->id}) | Identyfikator modułu kursu: ({$a->cm}) | Identyfikator użytkownika: ({$a->userid})';
$string['errorcode15'] = 'Ten plik nie został przesłany do Turnitin, ponieważ nie można znaleźć modułu aktywności, do którego należy';
$string['tiiaccountconfig'] = 'Konfiguracja konta Turnitin';
$string['turnitinaccountid'] = 'Identyfikator konta Turnitin';
$string['turnitinsecretkey'] = 'Klucz dzielony Turnitin';
$string['turnitinapiurl'] = 'URL API Turnitin';
$string['tiidebugginglogs'] = 'Wykrywanie błędów i dzienniki';
$string['turnitindiagnostic'] = 'Włączenie trybu diagnostycznego';
$string['turnitindiagnostic_desc'] = '<b>[Uwaga]</b><br />Tryb diagnostyczny należy uruchamiać tylko na potrzeby wykrywania problemów z API Turnitin.';
$string['tiiaccountsettings_desc'] = 'Należy upewnić się, że te ustawienia są takie same, jak te skonfigurowane na koncie Turnitin. W innym przypadku mogą wystąpić problemy z tworzeniem zadań lub wysłanymi pracami studentów.';
$string['tiiaccountsettings'] = 'Ustawienia konta Turnitin';
$string['turnitinusegrademark'] = 'Użyj GradeMark';
$string['turnitinusegrademark_desc'] = 'Wybierz, czy do oceny przesłanych prac użyć GradeMark.<br /><i>(opcja dostępna dla użytkowników, którzy mają na swoim koncie skonfigurowane narzędzie GradeMark)</i>';
$string['turnitinenablepeermark'] = 'Włącz zadania Peermark';
$string['turnitinenablepeermark_desc'] = 'Wybierz, czy zezwolić na tworzenie zadań Peermark<br/><i>(opcja dostępna tylko dla użytkowników konta ze skonfigurowanym narzędziem Peermark)</i>';
$string['transmatch_desc'] = 'Opcja pozwala wybrać, czy ustawienie Przetłumaczone zbieżności będzie dostępne na ekranie konfiguracji zadania.<br /><i>(włącz tę opcję tylko wtedy, gdy na Twoim koncie Turnitin jest włączone ustawienie Przetłumaczone zbieżności)</i>';
$string['repositoryoptions_0'] = 'Włącz standardowe opcje magazynu dla instruktora';
$string['repositoryoptions_1'] = 'Włącz rozszerzone opcje archwizowania dla instruktora.';
$string['repositoryoptions_2'] = 'Wyślij wszystkie prace do archiwum standardowego.';
$string['repositoryoptions_3'] = 'Nie wysyłaj żadnych prac do archiwum.';
$string['turnitinrepositoryoptions'] = 'Archiwum zadanych prac';
$string['turnitinrepositoryoptions_desc'] = 'Skonfiguruj opcje magazynu zadań Turnitin.<br /><i>(opcja dostępna tylko dla użytkowników, którzy mają na swoim koncie włączony magazyn instytucji)</i>';
$string['tiimiscsettings'] = 'Różne ustawienia wtyczek';
$string['pp_agreement_default'] = 'Potwierdzam autorstwo tej przesyłki i przyjmuję pełną odpowiedzialność, jaka może wyniknąć z naruszenia praw autorskich w związku z tą przesyłką.';
$string['pp_agreement_desc'] = '<b>[Opcjonalnie]</b><br />Wprowadź formułę potwierdzenia umowy dla wysyłek.<br />(<b>Uwaga:</b> Pozostawienie pustego pola oznacza, że nie wymaga się potwierdzenia umowy od studentów)';
$string['pp_agreement'] = 'Zrzeczenie się / Umowa';
$string['studentdataprivacy'] = 'Ustawienia ochrony danych osobowych studenta';
$string['studentdataprivacy_desc'] = 'Poniższe ustawienia można skonfigurować tak, aby dane osobowe studentów&#39; nie były przesyłane do Turnitin za pośrednictwem API.';
$string['enablepseudo'] = 'Aktywuj ustawienia ochrony danych osobowych studenta';
$string['enablepseudo_desc'] = 'Wybór tej opcji spowoduje, że adresy e-mail studentów otrzymają odpowiedniki dla połączeń z API Turnitin.<br /><i>(<b>Uwaga:</b>Ta opcja nie może być zmieniona, jeśli jakiekolwiek dane użytkownika Moodle zostały już zsynchronizowane z Turnitin)</i>';
$string['pseudofirstname'] = 'Pseudonazwa studenta';
$string['pseudofirstname_desc'] = '<b>[Opcjonalnie]</b><br />Imię studenta do wyświetlenia w przeglądarce dokumentów Turnitin';
$string['pseudolastname'] = 'Pseudonazwa studenta';
$string['pseudolastname_desc'] = 'Nazwisko studenta do wyświetlenia w przeglądarce dokumentów Turnitin';
$string['pseudolastnamegen'] = 'Automatycznie generować nazwisko';
$string['pseudolastnamegen_desc'] = 'Wybór „tak” oraz umieszczenie pseudonazwy (aliasu) w polu profilu użytkownika spowoduje automatyczne wprowadzenie unikatowego identyfikatora.';
$string['pseudoemailsalt'] = 'Szyfrowanie pseudolosowe (SALT - ang.- dane losowe stosowane w procesie szyfrowania)';
$string['pseudoemailsalt_desc'] = '<b>[Opcjonalne]</b><br />Opcjonalne dane losowe stosowane w procesie szyfrowania w celu zwiększenia złożoności generowanego adresu e-mail pseudostudenta.<br />(<b>Uwaga:</b> Dane losowe powinny pozostać niezmienione, aby zachować spójność pseudoadresów e-mail)';
$string['pseudoemaildomain'] = 'Pseudodomena mailowa';
$string['pseudoemaildomain_desc'] = '<b>[Opcjonalne]</b><br />Opcjonalna domena pseudoadresów e-mail. (po pozostawieniu pustego pola przywracana jest domyślna domena @tiimoodle.com)';
$string['pseudoemailaddress'] = 'Pseudoadres e-mail';
$string['connecttest'] = 'Test połączenia z Turnitin';
$string['connecttestsuccess'] = 'Moodle z powodzeniem połączył się z Turnitin.';
$string['diagnosticoptions_0'] = 'Wył.';
$string['diagnosticoptions_1'] = 'Standardowa';
$string['diagnosticoptions_2'] = 'Wykrywanie błędów';
$string['repositoryoptions_4'] = 'Wyślij wszystkie prace do magazynu instytucji';
$string['turnitinrepositoryoptions_help'] = '<strong>Włącz standardowe opcje magazynu dla instruktora: </strong><br />Instruktorzy mogą zdecydować, czy Turnitin ma dodawać dokumenty do magazynu standardowego albo prywatnego magazynu instytucji czy też w ogóle nie umieszczać ich w magazynie.<br /><br /><strong>Włącz rozszerzone opcje archwizowania dla instruktora.: </strong><br />Ta opcja pozwala instruktorom wyświetlić ustawienie zadania, które umożliwia studentom wskazanie Turnitin, gdzie mają być przechowywane ich dokumenty. Studenci mogą dodawać swoje dokumenty do standardowego magazynu studenta lub do prywatnego magazynu instytucji.<br /><br /><strong>Wyślij wszystkie prace do archiwum standardowego.: </strong><br />Wszystkie dokumenty są domyślnie dodawane do standardowego magazynu studenta.<br /><br /><strong>Nie wysyłaj żadnych prac do archiwum.: </strong><br />Dokumenty będą używane tylko w celu przeprowadzenia wstępnej weryfikacji w Turnitin. Będą też wyświetlane instruktorom w celu dokonania oceny.<br /><br /><strong>Wyślij wszystkie prace do magazynu instytucji: </strong><br />Turnitin ma przechowywać wszystkie prace w magazynie prac instytucji. Weryfikację podobieństwa do wysłanych dokumentów będą przeprowadzać inni instruktorzy z instytucji.';
$string['turnitinuseanon'] = 'Użyj anonimowych poprawek';
$string['createassignmenterror'] = 'Wystąpił błąd podczas próby utworzenia zadania w Turnitin';
$string['editassignmenterror'] = 'Wystąpił błąd podczas próby edytownia zadania w Turnitin';
$string['ppassignmentediterror'] = 'Edytowanie modułu {$a->title} (Identyfikator Turnitin: {$a->assignmentid}) w Turnitin nie powiodło się — szczegółowe informacje znajdują się w dziennikach API';
$string['pp_classcreationerror'] = 'Utworzenie klasy w Turnitin nie powiodło się. Szczegółowe informacje znajdują się w dziennikach API.';
$string['unlinkusers'] = 'Odłącz użytkowników';
$string['relinkusers'] = 'Ponownie połącz użytkowników';
$string['unlinkrelinkusers'] = 'Odłącz / Połącz ponownie użytkowników Turnitin';
$string['nointegration'] = 'Brak integracji';
$string['sprevious'] = 'Poprzedni(a)(e)';
$string['snext'] = 'Następny(a)(e)';
$string['slengthmenu'] = 'Pokaż wpisy_MENU_';
$string['ssearch'] = 'Szukać:';
$string['sprocessing'] = 'Wprowadzanie danych od Turnitin...';
$string['szerorecords'] = 'Brak wyników do pokazania.';
$string['sinfo'] = 'Pokazuje od _START_do_END__TOTAL_wpisy.';
$string['userupdateerror'] = 'Aktualizacja danych użytkownika nie powiodła się';
$string['connecttestcommerror'] = 'Nie udało się połączyć z Turnitin. Sprawdź swoje ustawienia URL API.';
$string['userfinderror'] = 'Wystąpił błąd przy próbie znalezienia użytkownika w Turnitin';
$string['tiiusergeterror'] = 'Wystąpił błąd podczas próby uzyskania danych użytkownika od Turnitin';
$string['usercreationerror'] = 'Utworzenie użytkownika Turnitin nie powiodło się';
$string['ppassignmentcreateerror'] = 'Utworzenie modułu w Turnitin nie powiodło się. Szczegółowe informacje znajdują się w dziennikach API.';
$string['excludebiblio_help'] = 'To ustawienie pozwala instruktorowi na wyłączenie z prac studentów bibliografii, cytowanych prac lub przypisów, aby nie szukano w nich dopasowań przy sporządzaniu raportów oryginalności. Ustawienie to można nadpisać w indywidualnych raportach oryginalności.';
$string['excludequoted_help'] = 'To ustawienie umożliwia instruktorowi wyłączenie tekstu cytatów z prac studentów, tak aby cytaty nie podlegały sprawdzaniu przy sporządzaniu raportów oryginalności. Ustawienie można wyłączyć dla indywidualnych raportów oryginalności.';
$string['excludevalue_help'] = 'To ustawienie pozwala instruktorowi na wyłączenie dopasowań o niewystarczającej długości (według uznania instruktora) ze sporządzania raportów oryginalności. Można nadpisać to ustawienie dla indywidualnych raportów oryginalności.';
$string['spapercheck_help'] = 'Przy sporządzaniu raportów oryginalności dla prac skorzystaj z magazynu prac studentów Turnitin. W przypadku rezygnacji z tej opcji procent wskaźnika podobieństwa może ulec obniżeniu.';
$string['internetcheck_help'] = 'Przy sporządzaniu raportów oryginalności dla prac sprawdzić w internetowym magazynie Turnitin. W przypadku zrezygnowania z tej opcji procent wskaźnika podobieństwa może ulec obniżeniu.';
$string['journalcheck_help'] = 'Przy sporządzaniu raportów oryginalności dla prac sprawdzić w czasopismach, periodykach i publikacjach bazy danych Turnitin. W przypadku zrezygnowania z tej opcji procent wskaźnika podobieństwa może ulec obniżeniu.';
$string['reportgenspeed_help'] = 'Są trzy opcje konfiguracji tego zadania: &#39;Generuj raporty natychmiast. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).&#39;, &#39;Generuj raporty natychmiast. Wysyłki zostaną natychmiast dodane do magazynu (jeśli magazyn został ustawiony).&#39; oraz &#39;Generuj raporty w danym terminie. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).&#39;<br /><br />Wybór opcji &#39;Generuj raporty natychmiast. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).&#39; powoduje sporządzanie raportu oryginalności natychmiast po dokonaniu wysyłki przez studenta. Jeśli ta opcja jest wybrana, studenci nie mogą ponownie wysłać pracy do zadania.<br /><br />Aby umożliwić ponowne wysyłki, wybierz opcję &#39;Generuj raporty natychmiast. Wysyłki zostaną natychmiast dodane do magazynu (jeśli magazyn został ustawiony).&#39; W ten sposób studenci będą mogli wysyłać prace, aż do czasu terminu oddania zadania. Sporządzenie raportu oryginalności dla ponownych wysyłek może zająć do 24 godzin.<br /><br />Wybór opcji &#39;Generuj raporty w danym terminie. Wysyłki zostaną dodane do magazynu w danym terminie (jeśli magazyn został ustawiony).&#39; spowoduje sporządzenie raportu oryginalności tylko w terminie oddania zadania&#39;. To ustawienie sprawi, że wszystkie prace oddane do zadania zostaną porównane ze sobą nawzajem podczas sporządzania raportów oryginalności.';
$string['turnitinuseanon_desc'] = 'Wybierz, czy zezwolić na anonimowe poprawki przy ocenianiu wysyłek.<br /><i>(opcja dostępna tylko dla użytkowników konta ze skonfigurowaną funkcją anonimowych poprawek)</i>';
