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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'Adres URL uruchamiania';
$string['adminurldesc'] = 'Adres URL uruchamiania usług LTI używany w celu uzyskania dostępu do raportu ułatwień dostępu.';
$string['allyclientconfig'] = 'Konfiguracja usługi Ally';
$string['ally:clientconfig'] = 'Otwórz i zaktualizuj konfigurację klienta';
$string['ally:viewlogs'] = 'Przeglądarka dzienników usługi Ally';
$string['clientid'] = 'Identyfikator klienta';
$string['clientiddesc'] = 'Identyfikator klienta usługi Ally';
$string['code'] = 'Kod';
$string['contentauthors'] = 'Autorzy treści';
$string['contentauthorsdesc'] = 'Przesłane pliki kursu administratorów i użytkowników przypisanych do wybranych ról zostaną ocenione pod kątem ułatwień dostępu. Do plików przypisuje się wskaźnik dostępności. Niski wskaźnik oznacza konieczność wprowadzenia zmian do pliku.';
$string['contentupdatestask'] = 'Zadanie aktualizacji zawartości';
$string['curlerror'] = 'Błąd cURL: {$a}';
$string['curlinvalidhttpcode'] = 'Nieprawidłowy kod statusu HTTP: {$a}';
$string['curlnohttpcode'] = 'Nie można zweryfikować kodu statusu HTTP';
$string['error:invalidcomponentident'] = 'Nieprawidłowy identyfikator komponentu {$a}';
$string['error:pluginfilequestiononly'] = 'W przypadku tego adresu URL obsługiwane są tylko komponenty pytań';
$string['error:componentcontentnotfound'] = 'Nie znaleziono zawartości elementu {$a}';
$string['error:wstokenmissing'] = 'Brak tokenu usługi sieciowej. Być może administrator musi uruchomić automatyczną konfigurację?';
$string['excludeunused'] = 'Wyklucz nieużywane pliki';
$string['excludeunuseddesc'] = 'Pomiń pliki, które są dołączone do treści HTML, ale do których łącza/odniesienia znajdują się w kodzie HTML.';
$string['filecoursenotfound'] = 'Przekazany plik nie należy do żadnego kursu';
$string['fileupdatestask'] = 'Prześlij aktualizacje plików do usługi Ally';
$string['id'] = 'Identyfikator';
$string['key'] = 'Klucz';
$string['keydesc'] = 'Klucz konsumenta LTI.';
$string['level'] = 'Poziom';
$string['message'] = 'Wiadomość';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'Adres URL aktualizacji plików';
$string['pushurldesc'] = 'Powiadomienia Push dotyczące aktualizacji plików pod tym adresem URL.';
$string['queuesendmessagesfailure'] = 'Wystąpił błąd podczas wysyłania wiadomości do AWS SQS. Dane błędu: $a';
$string['secret'] = 'Tajny klucz*';
$string['secretdesc'] = 'Tajny ciąg LTI.';
$string['showdata'] = 'Pokaż dane';
$string['hidedata'] = 'Ukryj dane';
$string['showexplanation'] = 'Pokaż wyjaśnienie';
$string['hideexplanation'] = 'Ukryj wyjaśnienie';
$string['showexception'] = 'Pokaż wyjątek';
$string['hideexception'] = 'Ukryj wyjątek';
$string['usercapabilitymissing'] = 'Podany użytkownik nie ma możliwości usunięcia tego pliku.';
$string['autoconfigure'] = 'Automatycznie skonfiguruj usługę sieciową Ally';
$string['autoconfiguredesc'] = 'Automatycznie utwórz rolę i użytkownika usługi sieciowej dla usługi Ally.';
$string['autoconfigureconfirmation'] = 'Automatycznie utwórz rolę i użytkownika usługi sieciowej dla usługi Ally i włącz usługę sieciową. Zostaną wykonane poniższe działania:<ul><li>utworzenie roli o nazwie „ally_webservice” i użytkownika o nazwie „ally_webuser”,</li><li>dodanie użytkownika „ally_webuser” do roli „ally_webservice”,</li><li>włączenie usług sieciowych,</li><li>włączenie protokołu REST usługi sieciowej,</li><li>włączenie usługi sieciowej Ally,</li><li>utworzeniu tokenu dla konta „ally_webuser”.</li></ul>';
$string['autoconfigsuccess'] = 'Powodzenie — usługa sieciowa Ally została automatycznie skonfigurowana.';
$string['autoconfigtoken'] = 'Token usługi sieciowej jest następujący:';
$string['autoconfigapicall'] = 'Możesz przetestować działanie usługi sieciowej za pośrednictwem następującego adresu URL:';
$string['privacy:metadata:files:action'] = 'Działanie wykonane na pliku, np. utworzony, zaktualizowany lub usunięty.';
$string['privacy:metadata:files:contenthash'] = 'Skrót zawartości pliku w celu określenia unikalności.';
$string['privacy:metadata:files:courseid'] = 'Identyfikator kursu, do którego należy plik.';
$string['privacy:metadata:files:externalpurpose'] = 'W celu integracji z usługą Ally pliki muszą być wymieniane z usługą Ally.';
$string['privacy:metadata:files:filecontents'] = 'Rzeczywista zawartość pliku jest wysyłana do usługi Ally w celu jej oceny pod kątem ułatwień dostępu.';
$string['privacy:metadata:files:mimetype'] = 'Typ MIME pliku, np. text/plain, image/jpeg itp.';
$string['privacy:metadata:files:pathnamehash'] = 'Skrót ścieżki do pliku w celu jego jednoznacznego zidentyfikowania.';
$string['privacy:metadata:files:timemodified'] = 'Czas ostatniej modyfikacji pola.';
$string['cachedef_annotationmaps'] = 'Zapisz dane adnotacji do kursów';
$string['cachedef_fileinusecache'] = 'Pliki Ally w użytkowej pamięci podręcznej';
$string['cachedef_pluginfilesinhtml'] = 'Pliki Ally w pamięci podręcznej HTML';
$string['cachedef_request'] = 'Pamięć podręczna żądań filtru usługi Ally';
$string['pushfilessummary'] = 'Podsumowanie aktualizacji plików usługi Ally.';
$string['pushfilessummary:explanation'] = 'Podsumowanie aktualizacji plików wysyłanych do usługi Ally.';
$string['section'] = 'Sekcja {$a}';
$string['lessonanswertitle'] = 'Odpowiedź dla lekcji „{$a}”';
$string['lessonresponsetitle'] = 'Udzielona odpowiedź dla lekcji „{$a}”';
$string['logs'] = 'Dzienniki usługi Ally';
$string['logrange'] = 'Zakres dziennika';
$string['loglevel:none'] = 'żaden';
$string['loglevel:light'] = 'Lekki';
$string['loglevel:medium'] = 'Średnie';
$string['loglevel:all'] = 'Wszystkie';
$string['logcleanuptask'] = 'Zadanie usuwania wpisów z dziennika Ally';
$string['loglifetimedays'] = 'Zachowaj wpisy w dziennikach przez tę liczbę dni';
$string['loglifetimedaysdesc'] = 'Zachowaj wpisy w dziennikach Ally przez tę liczbę dni. Ustaw na 0, aby nigdy nie usuwać wpisów. Zaplanowane zadanie jest (domyślnie) ustawione na codzienne uruchamianie i będzie usuwać wpisy w dzienniku, które mają więcej niż podaną liczbę dni.';
$string['logger:filtersetupdebugger'] = 'Dziennik konfiguracji filtra usługi Ally';
$string['logger:pushtoallysuccess'] = 'Pomyślne przesłano do punktu końcowego usługi Ally';
$string['logger:pushtoallyfail'] = 'Nie udało się przesłać do punktu końcowego usługi Ally';
$string['logger:pushfilesuccess'] = 'Pomyślne przesłano pliki do punktu końcowego usługi Ally';
$string['logger:pushfileliveskip'] = 'Niepowodzenie przesłania aktywnego pliku';
$string['logger:pushfileliveskip_exp'] = 'Pomijanie przesyłania aktywnych plików z powodu problemów z komunikacją. Przesyłanie aktywnych plików zostanie przywrócone, jeśli zadanie aktualizacji plików zakończy się pomyślnie. Przejrzyj konfigurację.';
$string['logger:pushfileserror'] = 'Nie udało się przesłać do punktu końcowego usługi Ally';
$string['logger:pushfileserror_exp'] = 'Błędy związane z przesyłaniem aktualizacji zawartości do usług Ally.';
$string['logger:pushcontentsuccess'] = 'Pomyślnie przesłano zawartość do punktu końcowego usługi Ally';
$string['logger:pushcontentliveskip'] = 'Niepowodzenie przesyłania aktywnej zawartości';
$string['logger:pushcontentliveskip_exp'] = 'Pomijanie przesyłania aktywnej zawartości z powodu problemów z komunikacją. Przesyłanie aktywnej zawartości zostanie przywrócone, jeśli zadanie aktualizacji zawartości zakończy się pomyślnie. Przejrzyj konfigurację.';
$string['logger:pushcontentserror'] = 'Nie udało się przesłać do punktu końcowego usługi Ally';
$string['logger:pushcontentserror_exp'] = 'Błędy związane z przesyłaniem aktualizacji zawartości do usług Ally.';
$string['logger:addingconenttoqueue'] = 'Dodawanie zawartości do kolejki przesyłania';
$string['logger:annotationmoderror'] = 'Niepowodzenie utworzenia adnotacji zawartości modułu Ally.';
$string['logger:annotationmoderror_exp'] = 'Moduł nie został poprawnie zidentyfikowany.';
$string['logger:failedtogetcoursesectionname'] = 'Nie udało się pobrać nazwy sekcji kursu';
$string['logger:moduleidresolutionfailure'] = 'Nie udało się rozpoznać identyfikatora modułu';
$string['logger:cmidresolutionfailure'] = 'Nie udało się rozpoznać identyfikatora modułu kursu';
$string['logger:cmvisibilityresolutionfailure'] = 'Nie udało się rozpoznać problemu z widocznością modułu kursu';
$string['courseupdatestask'] = 'Prześlij zdarzenia kursu do usługi Ally';
$string['logger:pushcoursesuccess'] = 'Pomyślne przesłano zdarzenia kursu do punktu końcowego usługi Ally';
$string['logger:pushcourseliveskip'] = 'Niepowodzenie przesłania aktywnego zdarzenia kursu';
$string['logger:pushcourseerror'] = 'Niepowodzenie przesłania aktywnego zdarzenia kursu';
$string['logger:pushcourseliveskip_exp'] = 'Pomijanie przesyłania aktywnych zdarzeń kursu z powodu problemów z komunikacją. Przesyłanie aktywnych zdarzeń kursu zostanie przywrócone, jeśli zadanie aktualizacji zdarzeń kursu zakończy się pomyślnie. Przejrzyj konfigurację.';
$string['logger:pushcourseserror'] = 'Nie udało się przesłać do punktu końcowego usługi Ally';
$string['logger:pushcourseserror_exp'] = 'Błędy związane z przesyłaniem aktualizacji kursu do usług Ally.';
$string['logger:addingcourseevttoqueue'] = 'Dodawanie zdarzenia kursu do kolejki przesyłania';
$string['logger:cmiderraticpremoddelete'] = 'Przed usunięciem modułu kursu wystąpiły problemy.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Moduł nie został poprawnie zidentyfikowany; albo nie istnieje z powodu usunięcia sekcji, albo wystąpił inny czynnik, który wyzwolił hak usunięcia, ale odnalezienie modułu było niemożliwe.';
$string['logger:servicefailure'] = 'Niepowodzenie podczas korzystania z usługi.';
$string['logger:servicefailure_exp'] = '<br>Klasa: {$a->class}<br>Parametry: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Niepowodzenie podczas przypisywania możliwości roli bazowej nauczyciela do roli ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Możliwość: {$a->cap}<br>Uprawnienie: {$a->permission}';
$string['deferredcourseevents'] = 'Wyślij odroczone zdarzenia kursowe';
$string['deferredcourseeventsdesc'] = 'Zezwól na wysyłanie zapisanych zdarzeń kursowych, które zostały zgromadzone podczas awarii komunikacji z Ally';
