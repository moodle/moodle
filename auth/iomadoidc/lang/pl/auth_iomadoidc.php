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
 * @package auth_iomadoidc
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'IOMAD OpenID Connect';
$string['auth_iomadoidcdescription'] = 'Wtyczka IOMAD OpenID Connect udostępnia funkcję rejestracji jednokrotnej przy użyciu dostawców tożsamości, których można skonfigurować.';
$string['cfg_authendpoint_key'] = 'Punkt końcowy autoryzacji';
$string['cfg_authendpoint_desc'] = 'Identyfikator URI punktu końcowego autoryzacji od dostawcy tożsamości do wykorzystania.';
$string['cfg_autoappend_key'] = 'Dołączaj automatycznie';
$string['cfg_autoappend_desc'] = 'Automatycznie dołączaj ten ciąg przy logowaniu użytkowników za pomocą nazwy użytkownika/hasła. Jest to przydatne, gdy dostawca tożsamości wymaga stosowania wspólnej domeny, ale nie chce wymagać jej wpisywania przez użytkowników podczas logowania. Na przykład jeżeli pełna nazwa użytkownika wtyczki IOMAD OpenID Connect to „jan@przyklad.com”, a w tym polu zostanie wprowadzony ciąg „@przyklad.com”, użytkownik będzie musiał jedynie wpisać słowo „jan” jako swoją nazwę użytkownika. <br /><b>Uwaga:</b> Jeżeli istnieją nazwy użytkowników powodujące konflikt, np. jeżeli istnieje użytkownik platformy Moodle o tej samej nazwie, do określenia, który użytkownik ma pierwszeństwo stosowane są ustawienia priorytetów wtyczki uwierzytelniania.';
$string['cfg_clientid_key'] = 'Identyfikator klienta';
$string['cfg_clientid_desc'] = 'Zarejestrowany identyfikator klienta w dostawcy tożsamości.';
$string['cfg_clientsecret_key'] = 'Tajny klucz klienta';
$string['cfg_clientsecret_desc'] = 'Zarejestrowany tajny klucz klienta w dostawcy tożsamości. W przypadku niektórych dostawców tożsamości jest on również określany jako klucz.';
$string['cfg_err_invalidauthendpoint'] = 'Nieprawidłowy punkt końcowy autoryzacji';
$string['cfg_err_invalidtokenendpoint'] = 'Nieprawidłowy punkt końcowy tokenu';
$string['cfg_err_invalidclientid'] = 'Nieprawidłowy identyfikator klienta';
$string['cfg_err_invalidclientsecret'] = 'Nieprawidłowy tajny klucz klienta';
$string['cfg_icon_key'] = 'Ikona';
$string['cfg_icon_desc'] = 'Ikona do wyświetlania obok nazwy dostawcy na stronie logowania.';
$string['cfg_iconalt_o365'] = 'Ikona pakietu Office 365';
$string['cfg_iconalt_locked'] = 'Ikona zablokowana';
$string['cfg_iconalt_lock'] = 'Ikona blokady';
$string['cfg_iconalt_go'] = 'Zielony okrąg';
$string['cfg_iconalt_stop'] = 'Czerwony okrąg';
$string['cfg_iconalt_user'] = 'Ikona użytkownika';
$string['cfg_iconalt_user2'] = 'Alternatywna ikona użytkownika';
$string['cfg_iconalt_key'] = 'Ikona klucza';
$string['cfg_iconalt_group'] = 'Ikona grupy';
$string['cfg_iconalt_group2'] = 'Alternatywna ikona grupy';
$string['cfg_iconalt_mnet'] = 'Ikona MNET';
$string['cfg_iconalt_userlock'] = 'Użytkownik z ikoną blokady';
$string['cfg_iconalt_plus'] = 'Ikona znaku plus';
$string['cfg_iconalt_check'] = 'Ikona znaku wyboru';
$string['cfg_iconalt_rightarrow'] = 'Ikona strzałki w prawo';
$string['cfg_customicon_key'] = 'Niestandardowa ikona';
$string['cfg_customicon_desc'] = 'Jeżeli użytkownik chce użyć własnej ikony, może ją przesłać za pomocą tej opcji. Nową ikoną można zastąpić dowolną ikonę wybraną powyżej. <br /><br /><b>Uwagi dotyczące używania niestandardowych ikon:</b><ul><li>Rozmiar tego obrazu <b>nie</b> zostanie zmieniony na stronie logowania, zatem zalecamy załadowanie obrazu o maksymalnym rozmiarze 35x35 pikseli.</li><li>Jeżeli użytkownik przesłał niestandardową ikonę i chce przywrócić jedną ze standardowych ikon programu, należy kliknąć ikonę niestandardową w polu powyżej i kliknąć przycisk „Usuń”. Następnie należy kliknąć przycisk „OK” oraz przycisk „Zapisz zmiany” w dolnej części formularza. Wybrana ikona standardowa będzie wyświetlana na stronie logowania do platformy Moodle.</li></ul>';
$string['cfg_debugmode_key'] = 'Rejestruj komunikaty debugowania';
$string['cfg_debugmode_desc'] = 'Jeśli ta opcja jest włączona, informacje będą rejestrowane w pliku dziennika platformy Moodle, aby pomóc w identyfikacji problemów.';
$string['cfg_loginflow_key'] = 'Przepływ logowania';
$string['cfg_loginflow_authcode'] = 'Żądanie autoryzacji';
$string['cfg_loginflow_authcode_desc'] = 'W przypadku tego przepływu użytkownik klika nazwę dostawcy tożsamości (patrz „Nazwa dostawcy” powyżej) na stronie logowania do platformy Moodle i zostaje przekierowany do dostawcy, aby się zalogować. Po pomyślnym zalogowaniu użytkownik jest ponownie przekierowywany do strony platformy Moodle, na której odbywa się logowanie do platformy Moodle w sposób niewidoczny. Jest to najbardziej ustandaryzowany i bezpieczny sposób logowania się użytkownika.';
$string['cfg_loginflow_rocreds'] = 'Uwierzytelnienie nazwy użytkownika/hasła';
$string['cfg_loginflow_rocreds_desc'] = 'W przypadku tego przepływu użytkownik wprowadza nazwę użytkownika i hasło do formularza logowania się do platformy Moodle w taki sam sposób jak w przypadku logowania ręcznego. Dane logowania użytkownika są następnie przesyłane do dostawcy tożsamości w tle w celu uwierzytelnienia. Ten przepływ jest najbardziej niewidoczny dla użytkownika, ponieważ użytkownik nie wchodzi w bezpośrednią interakcję z dostawcą tożsamości. Nie wszyscy dostawcy tożsamości obsługują ten przepływ.';
$string['cfg_iomadoidcresource_key'] = 'Zasób';
$string['cfg_iomadoidcresource_desc'] = 'Zasób wtyczki IOMAD OpenID Connect, do którego ma zostać wysłane żądanie.';
$string['cfg_iomadoidcscope_key'] = 'Scope';
$string['cfg_iomadoidcscope_desc'] = 'Zakres OIDC do użycia.';
$string['cfg_opname_key'] = 'Nazwa dostawcy';
$string['cfg_opname_desc'] = 'Jest to etykieta dla użytkownika końcowego określająca rodzaj danych logowania, których użytkownik musi użyć do logowania. Ta etykieta jest używana w obszarach wtyczki widocznych dla użytkownika w celu zidentyfikowania dostawcy.';
$string['cfg_redirecturi_key'] = 'Adres URI przekierowania';
$string['cfg_redirecturi_desc'] = 'Jest to identyfikator URI, który należy zarejestrować jako „Adres URI przekierowania”. Dostawca tożsamości wtyczki IOMAD OpenID Connect powinien zapytać o ten identyfikator podczas rejestracji platformy Moodle jako klienta. <br /><b>UWAGA:</b> Należy go wprowadzić do dostawcy IOMAD OpenID Connect *dokładnie* w takiej postaci, w jakiej występuje w tym miejscu. Dowolna różnica uniemożliwi logowanie za pomocą IOMAD OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Punkt końcowy tokenu';
$string['cfg_tokenendpoint_desc'] = 'Adres URI punktu końcowego tokenu od dostawcy tożsamości do wykorzystania.';
$string['event_debug'] = 'Komunikaty debugowania';
$string['errorauthdisconnectemptypassword'] = 'Hasło nie może być puste';
$string['errorauthdisconnectemptyusername'] = 'Pole nazwy użytkownika nie może być puste';
$string['errorauthdisconnectusernameexists'] = 'Ta nazwa użytkownika jest już zajęta. Wybierz inną nazwę.';
$string['errorauthdisconnectnewmethod'] = 'Użyj sposobu logowania';
$string['errorauthdisconnectinvalidmethod'] = 'Otrzymano nieprawidłowy sposób logowania.';
$string['errorauthdisconnectifmanual'] = 'W przypadku korzystania z ręcznego sposobu logowania wprowadź dane logowania poniżej.';
$string['errorauthinvalididtoken'] = 'Otrzymano nieprawidłowy token identyfikatora.';
$string['errorauthloginfailednouser'] = 'Nieprawidłowe dane logowania: nie znaleziono użytkownika w platformie Moodle.';
$string['errorauthnoauthcode'] = 'Nie otrzymano kodu uwierzytelniania.';
$string['errorauthnocreds'] = 'Skonfiguruj dane logowania klienta wtyczki IOMAD OpenID Connect.';
$string['errorauthnoendpoints'] = 'Skonfiguruj punkty końcowe serwera wtyczki IOMAD OpenID Connect.';
$string['errorauthnohttpclient'] = 'Ustaw klienta HTTP.';
$string['errorauthnoidtoken'] = 'Nie otrzymano tokenu identyfikatora IOMAD OpenID Connect.';
$string['errorauthunknownstate'] = 'Stan nieznany.';
$string['errorauthuseralreadyconnected'] = 'Połączono już z innym użytkownikiem wtyczki IOMAD OpenID Connect.';
$string['errorauthuserconnectedtodifferent'] = 'Uwierzytelniony użytkownik wtyczki IOMAD OpenID Connect jest już połączony z użytkownikiem platformy Moodle.';
$string['errorbadloginflow'] = 'Określono nieprawidłowy przepływ logowania. Uwaga: jeżeli ten komunikat jest wyświetlany po niedawnej instalacji lub aktualizacji, należy wyczyścić pamięć podręczną platformy Moodle.';
$string['errorjwtbadpayload'] = 'Nie udało się odczytać ładunku tokenu JWT.';
$string['errorjwtcouldnotreadheader'] = 'Nie udało się odczytać nagłówka tokenu JWT';
$string['errorjwtempty'] = 'Otrzymano token JWT, który jest pusty lub nie jest ciągiem.';
$string['errorjwtinvalidheader'] = 'Nieprawidłowy nagłówek tokenu JWT';
$string['errorjwtmalformed'] = 'Otrzymano nieprawidłowo utworzony token JWT.';
$string['errorjwtunsupportedalg'] = 'Tokeny JWS Alg lub JWE nie są obsługiwane';
$string['erroriomadoidcnotenabled'] = 'Wtyczka uwierzytelniania IOMAD OpenID Connect nie jest włączona.';
$string['errornodisconnectionauthmethod'] = 'Nie można odłączyć, ponieważ żadna wtyczka uwierzytelniania nie jest włączona (poprzedni sposób logowania użytkownika lub sposób logowania ręcznego).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Otrzymano nieprawidłowy adres URI punktu końcowego.';
$string['erroriomadoidcclientnocreds'] = 'Ustaw dane logowania klienta przy pomocy ustawionych danych logowania';
$string['erroriomadoidcclientnoauthendpoint'] = 'Nie ustawiono punktu końcowego autoryzacji. Ustaw przy użyciu $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'Nie ustawiono punktu końcowego tokenu. Ustaw przy użyciu $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'Punkt końcowy musi w tym celu korzystać z certyfikatu SSL/TLS.';
$string['errorucpinvalidaction'] = 'Otrzymano nieprawidłowe działanie.';
$string['erroriomadoidccall'] = 'Błąd w IOMAD OpenID Connect. Więcej informacji można znaleźć w dziennikach.';
$string['erroriomadoidccall_message'] = 'Błąd w IOMAD OpenID Connect: {$a}';
$string['eventuserauthed'] = 'Zautoryzowano użytkownika przy użyciu wtyczki IOMAD OpenID Connect';
$string['eventusercreated'] = 'Utworzono użytkownika we wtyczce IOMAD OpenID Connect';
$string['eventuserconnected'] = 'Podłączono użytkownika do wtyczki IOMAD OpenID Connect';
$string['eventuserloggedin'] = 'Zalogowano użytkownika za pomocą wtyczki IOMAD OpenID Connect';
$string['eventuserdisconnected'] = 'Odłączono użytkownika od wtyczki IOMAD OpenID Connect';
$string['iomadoidc:manageconnection'] = 'Zarządzaj połączeniem z wtyczką IOMAD OpenID Connect';
$string['ucp_general_intro'] = 'Ta opcja umożliwia zarządzanie połączeniem z {$a}. Jeżeli jest włączona, użytkownik może korzystać z konta {$a} do logowania się do platformy Moodle zamiast używania oddzielnej nazwy użytkownika i hasła. Po połączeniu nie trzeba będzie pamiętać nazwy użytkownika ani hasła do platformy Moodle — wszystkie operacje logowania będą obsługiwane przez {$a}.';
$string['ucp_login_start'] = 'Używaj {$a} w celu logowania się do platformy Moodle';
$string['ucp_login_start_desc'] = 'Spowoduje to przełączenie konta na logowanie się do platformy Moodle przy użyciu {$a}. Po włączeniu tej opcji użytkownik będzie się logował przy użyciu danych logowania {$a} — bieżąca nazwa użytkownika i hasło do platformy Moodle nie będą działać. Można odłączyć konto w dowolnym momencie i powrócić do zwykłego sposobu logowania się.';
$string['ucp_login_stop'] = 'Nie używaj {$a} w celu logowania się do platformy Moodle';
$string['ucp_login_stop_desc'] = 'Obecnie używasz {$a}, aby logować się do platformy Moodle. Kliknij opcję „Nie używaj logowania {$a}”, aby odłączyć konto na platformie Moodle od {$a}. Logowanie do platformy Moodle przy użyciu konta {$a} nie będzie możliwe. Musisz utworzyć nazwę użytkownika i hasło, aby zalogować się do platformy Moodle bezpośrednio.';
$string['ucp_login_status'] = 'Login {$a} to:';
$string['ucp_status_enabled'] = 'Włączone';
$string['ucp_status_disabled'] = 'Wyłączone';
$string['ucp_disconnect_title'] = 'Rozłączono {$a}';
$string['ucp_disconnect_details'] = 'Spowoduje to odłączenie konta na platformie Moodle od {$a}. Konieczne będzie utworzenie nazwy użytkownika i hasła w celu zalogowania się do platformy Moodle.';
$string['ucp_title'] = 'Zarządzanie {$a}';
