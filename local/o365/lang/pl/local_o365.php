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
 * Polish language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment

$string['pluginname'] = 'Integracja pakietu Microsoft 365';
$string['acp_title'] = 'Panel sterowania administratora pakietu Microsoft 365';
$string['acp_healthcheck'] = 'Sprawdzanie kondycji';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Strona udostępnianych danych kursu Moodle.';
$string['calendar_user'] = 'Kalendarz osobisty (użytkownika)';
$string['calendar_site'] = 'Kalendarz witryny';
$string['erroracpauthoidcnotconfig'] = 'Najpierw ustaw poświadczenia aplikacji w parametrze auth_oidc.';
$string['erroracplocalo365notconfig'] = 'Najpierw skonfiguruj ustawienia parametru local_o365.';
$string['errorhttpclientbadtempfileloc'] = 'Nie można otworzyć lokalizacji tymczasowej, aby zapisać plik.';
$string['errorhttpclientnofileinput'] = 'Brak parametru pliku w httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Nie udało się odświeżyć tokenu';
$string['erroro365apibadcall'] = 'Błąd w wywołania interfejsu API.';
$string['erroro365apibadcall_message'] = 'Błąd w wywołaniu interfejsu API: {$a}';
$string['erroro365apibadpermission'] = 'Nie znaleziono uprawnienia';
$string['erroro365apicouldnotcreatesite'] = 'Wystąpił problem podczas tworzenia strony.';
$string['erroro365apicoursenotfound'] = 'Nie znaleziono kursu.';
$string['erroro365apiinvalidtoken'] = 'Nieprawidłowy lub wygasły token.';
$string['erroro365apiinvalidmethod'] = 'Nieprawidłowy element httpmethod przekazany do wywołania interfejsu API';
$string['erroro365apinoparentinfo'] = 'Nie znaleziono informacji o folderze nadrzędnym';
$string['erroro365apinotimplemented'] = 'Wymagaj zastąpienia.';
$string['erroro365apinotoken'] = 'Brak tokenu dla danego zasobu i użytkownika. Nie udało się pobrać tokenu. Czy odświeżony token użytkownika wygasł?';
$string['erroro365apisiteexistsnolocal'] = 'Strona już istnienie, ale nie udało się znaleźć lokalnego rekordu.';
$string['eventapifail'] = 'Błąd interfejsu API';
$string['eventcalendarsubscribed'] = 'Użytkownik włączył subskrypcję kalendarza';
$string['eventcalendarunsubscribed'] = 'Użytkownik wyłączył subskrypcję kalendarza';
$string['healthcheck_fixlink'] = 'Kliknij tutaj, aby rozwiązać problem.';
$string['settings_usersync'] = 'Synchronizuj użytkowników z usługą Microsoft Entra ID';
$string['settings_usersync_details'] = 'Gdy ta opcja jest włączona, dane użytkowników platformy Moodle i usługi Microsoft Entra ID są synchronizowane zgodnie z powyższymi opcjami.<br /><br /><b>Uwaga: </b>Proces synchronizacji przebiega w skrypcie cron platformy Moodle i synchronizuje 1000 użytkowników na raz. Domyślnie proces jest uruchamiany raz dziennie o godz. 1:00 w strefie czasowej serwera. Aby szybciej zsynchronizować większe zestawy użytkowników, można zwiększyć częstotliwość wykonywania zadania <b>Synchronizuj użytkowników z usługą Microsoft Entra ID</b> za pomocą strony <a href="{$a}">Zarządzanie zaplanowanymi zadaniami.</a><br /><br />Szczegółowe informacje zawiera <a href="https://docs.moodle.org/30/en/Office365#User_sync">dokumentacja funkcji synchronizacji użytkowników</a><br /><br />';
$string['settings_usersync_create'] = 'Utwórz konta na platformie Moodle dla użytkowników w usłudze Microsoft Entra ID';
$string['settings_usersync_delete'] = 'Usuwaj poprzednio zsynchronizowane konta na platformie Moodle, gdy zostaną usunięte z usługi Microsoft Entra ID';
$string['settings_usersync_match'] = 'Dopasuj wcześniej istniejących użytkowników platformy Moodle do kont o tej samej nazwie w usłudze Microsoft Entra ID<br /><small>Porównywane będą nazwy użytkownika w pakiecie Microsoft 365 z nazwami użytkownika na platformie Moodle w celu odnalezienia zgodnych. W dopasowaniach wielkość liter nie jest rozróżniana i ignorowany jest element nazwy odpowiadający klientowi pakietu Microsoft 365. Na przykład nazwa BoB.SmiTh na platformie Moodle byłaby zgodna z nazwą bob.smith@example.onmicrosoft.com. Konta Moodle i Microsoft 365 użytkowników, dla których znaleziono zgodność, zostaną połączone i będą oni mogli korzystać z funkcji integracji Microsoft 365/Moodle. Metoda uwierzytelniania użytkownika nie zmieni się, o ile nie zostanie włączone poniższe ustawienie.</small>';
$string['settings_usersync_matchswitchauth'] = 'Przełącz dopasowanych użytkowników na uwierzytelnianie Microsoft 365 (OpenID Connect)<br /><small>Ta opcja wymaga włączenia powyższego ustawienia „Dopasuj”. Gdy użytkownik jest dopasowany, włączenie tego ustawienia spowoduje przełączenie jego metody uwierzytelniania na OpenID Connect. Będzie się on logować do platformy Moodle danymi logowania do pakietu Microsoft 365. <b>Uwaga:</b> Aby móc korzystać z tego ustawienia, należy pamiętać o włączeniu wtyczki uwierzytelniania OpenID Connect.</small>';
$string['settings_entratenant'] = 'Dierżawca usługi Microsoft Entra ID';
$string['settings_entratenant_details'] = 'Opcja używana do identyfikacji organizacji w usłudze Microsoft Entra ID. Na przykład: „contoso.onmicrosoft.com”';
$string['settings_verifysetup'] = 'Sprawdź konfigurację';
$string['settings_verifysetup_details'] = 'To narzędzie sprawdza, czy wszystkie ustawienia usługi Azure zostały prawidłowo skonfigurowane. Może również naprawić niektóre często występujące błędy.';
$string['settings_verifysetup_update'] = 'Aktualizuj';
$string['settings_verifysetup_checking'] = 'Sprawdzanie...';
$string['settings_verifysetup_missingperms'] = 'Brak uprawnień:';
$string['settings_verifysetup_permscorrect'] = 'Uprawnienia są prawidłowe.';
$string['settings_verifysetup_errorcheck'] = 'Wystąpił błąd podczas próby sprawdzenia konfiguracji usługi Azure.';
$string['settings_verifysetup_unifiedheader'] = 'Ujednolicony interfejs API';
$string['settings_verifysetup_unifieddesc'] = 'Ujednolicony interfejs API zastępuje istniejące interfejsy API poszczególnych aplikacji. Jeżeli jest dostępny, należy go dodać do aplikacji Azure do wykorzystania w przyszłości. Opcjonalnie zastąpi on poprzedni interfejs API.';
$string['settings_verifysetup_unifiederror'] = 'Wystąpił błąd podczas wyszukiwania pomocy do ujednoliconego interfejsu API.';
$string['settings_verifysetup_unifiedactive'] = 'Ujednolicony interfejs API aktywny.';
$string['settings_verifysetup_unifiedmissing'] = 'Nie znaleziono ujednoliconego interfejsu API w tej aplikacji.';
$string['settings_creategroups'] = 'Utwórz grupy użytkowników';
$string['settings_creategroups_details'] = 'Jeśli ta opcja jest włączona, zostanie utworzona i będzie utrzymywana grupa nauczycieli i studentów w pakiecie Microsoft 365 dla każdego kursu na stronie. Wymagane grupy będą tworzone po każdym uruchomieniu skryptu cron (a wszyscy bieżący użytkownicy będą dodawani). Członkostwo w grupie będzie utrzymywane, gdy użytkownicy będą się rejestrować na kursy na platformie Moodle lub gdy będą się z nich wyrejestrowywać.<br /><b>Uwaga: </b>Ta funkcja wymaga dodania ujednoliconego interfejsu API pakietu Microsoft 365 do aplikacji dodanej w usłudze Azure. <a href="https://docs.moodle.org/30/en/Office365#User_groups">Instrukcje i dokumentacja konfiguracji.</a>';
$string['settings_o365china'] = 'Pakiet Microsoft 365 dla Chin';
$string['settings_o365china_details'] = 'Zaznacz to pole, jeżeli korzystasz z pakietu Microsoft 365 dla Chin.';
$string['settings_debugmode'] = 'Rejestruj komunikaty debugowania';
$string['settings_debugmode_details'] = 'Jeżeli ta opcja jest włączona, informacje będą rejestrowane w pliku dziennika platformy Moodle, aby pomóc w identyfikacji problemów.';
$string['settings_detectoidc'] = 'Dane logowania do aplikacji';
$string['settings_detectoidc_details'] = 'Aby móc się komunikować z pakietem Microsoft 365, platforma Moodle musi posiadać dane logowania umożliwiające jej identyfikację. Można je ustawić we wtyczce uwierzytelniania „OpenID Connect”.';
$string['settings_detectoidc_credsvalid'] = 'Dane logowania zostały ustawione.';
$string['settings_detectoidc_credsvalid_link'] = 'Zmień';
$string['settings_detectoidc_credsinvalid'] = 'Dane logowania nie zostały ustawione lub są niepełne.';
$string['settings_detectoidc_credsinvalid_link'] = 'Ustaw dane logowania';
$string['settings_detectperms'] = 'Uprawnienia do aplikacji';
$string['settings_detectperms_details'] = 'Aby korzystać z opcji wtyczki, należy ustawić prawidłowe uprawnienia dla aplikacji w usłudze Microsoft Entra ID.';
$string['settings_detectperms_nocreds'] = 'Najpierw należy ustawić dane logowania do aplikacji. Patrz ustawienie powyżej.';
$string['settings_detectperms_missing'] = 'Brakuje:';
$string['settings_detectperms_errorfix'] = 'Wystąpił błąd podczas próby naprawy uprawnień. Należy je ustawić ręcznie w usłudze Azure.';
$string['settings_detectperms_fixperms'] = 'Napraw uprawnienia';
$string['settings_detectperms_nounified'] = 'Ujednolicony interfejs API nie jest obecny, niektóre nowe funkcje mogą nie działać.';
$string['settings_detectperms_unifiednomissing'] = 'Wszystkie ujednolicone uprawnienia są obecne.';
$string['settings_detectperms_update'] = 'Aktualizuj';
$string['settings_detectperms_valid'] = 'Ustawienia zostały skonfigurowane.';
$string['settings_detectperms_invalid'] = 'Sprawdź uprawnienia w usłudze Microsoft Entra ID';
$string['settings_header_setup'] = 'Ustawienia konfiguracji';
$string['settings_header_options'] = 'Opcje';
$string['settings_healthcheck'] = 'Sprawdzanie kondycji';
$string['settings_healthcheck_details'] = 'Jeżeli jakaś opcja nie działa prawidłowo, można włączyć funkcję sprawdzania kondycji, która zidentyfikuje problem i zaproponuje rozwiązanie';
$string['settings_healthcheck_linktext'] = 'Sprawdź kondycję';
$string['settings_odburl'] = 'Adres URL usługi OneDrive dla firm';
$string['settings_odburl_details'] = 'Adres URL używany w celu uzyskania dostępu do usługi OneDrive dla firm. Zazwyczaj określa go dzierżawca usługi Microsoft Entra ID. Na przykład jeżeli dzierżawca usługi Microsoft Entra ID to „contoso.onmicrosoft.com”, adres ten prawdopodobnie ma postać „contoso-my.sharepoint.com”. Należy wprowadzić tylko nazwę domeny bez ciągu http:// lub https://';
$string['settings_serviceresourceabstract_valid'] = 'Można użyć {$a}.';
$string['settings_serviceresourceabstract_invalid'] = 'Tej wartości nie można użyć.';
$string['settings_serviceresourceabstract_nocreds'] = 'Najpierw ustaw ustawienia logowania do aplikacji';
$string['settings_serviceresourceabstract_empty'] = 'Wprowadź wartość lub kliknij opcję „Wykrywaj”, aby podjąć próbę wykrycia prawidłowej wartości.';
$string['spsite_group_contributors_name'] = 'Współautorzy {$a}';
$string['spsite_group_contributors_desc'] = 'Wszyscy użytkownicy, którzy mają dostęp do opcji zarządzania plikami w kursie {$a}';
$string['task_calendarsyncin'] = 'Synchronizuj zdarzenia o365 w platformie Moodle';
$string['task_coursesync'] = 'Utwórz grupy użytkowników w pakiecie Microsoft 365';
$string['task_syncusers'] = 'Synchronizuj użytkowników z usługą Microsoft Entra ID.';
$string['ucp_connectionstatus'] = 'Status połączenia';
$string['ucp_calsync_availcal'] = 'Dostępne kalendarze platformy Moodle';
$string['ucp_calsync_title'] = 'Synchronizacja kalendarza programu Outlook';
$string['ucp_calsync_desc'] = 'Zaznaczone kalendarze platformy Moodle zostaną zsynchronizowane z kalendarzem programu Outlook.';
$string['ucp_connection_status'] = 'Połączenie z pakietem Microsoft 365 jest:';
$string['ucp_connection_start'] = 'Połącz z pakietem Microsoft 365';
$string['ucp_connection_stop'] = 'Zamknij połączenie z pakietem Microsoft 365';
$string['ucp_features'] = 'Funkcje pakietu Microsoft 365';
$string['ucp_features_intro'] = 'Poniżej podano listę funkcji pakietu Microsoft 365, których można używać, aby usprawnić pracę w platformie Moodle.';
$string['ucp_features_intro_notconnected'] = 'Niektóre funkcje mogą być dostępne dopiero po połączeniu z pakietem Microsoft 365.';
$string['ucp_general_intro'] = 'Ta opcja umożliwia zarządzanie połączeniem z pakietem Microsoft 365.';
$string['ucp_index_entraidlogin_title'] = 'Logowanie do pakietu Microsoft 365';
$string['ucp_index_entraidlogin_desc'] = 'Możesz użyć danych logowania do pakietu Microsoft 365, aby zalogować się do platformy Moodle. ';
$string['ucp_index_calendar_title'] = 'Synchronizacja kalendarza programu Outlook';
$string['ucp_index_calendar_desc'] = 'Ta opcja umożliwia skonfigurowanie ustawień synchronizacji kalendarzy platformy Moodle z kalendarzem programu Outlook. Można wyeksportować zdarzenia z kalendarza platformy Moodle do programu Outlook, a także przenieść zdarzenia z kalendarza programu Outlook do platformy Moodle.';
$string['ucp_index_connectionstatus_connected'] = 'Użytkownik jest obecnie połączony z pakietem Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'Konto użytkownika zostało dopasowane do konta użytkownika <small>"{$a}"</small> pakietu Microsoft 365. Aby zakończyć proces nawiązywania połączenia, kliknij łącze poniżej i zaloguj się do pakietu Microsoft 365.';
$string['ucp_index_connectionstatus_notconnected'] = 'Użytkownik nie jest obecnie połączony z pakietem Microsoft 365';
$string['ucp_index_onenote_title'] = 'Program OneNote';
$string['ucp_index_onenote_desc'] = 'Integracja programu OneNote umożliwia korzystanie z programu OneNote z pakietu Microsoft 365 z platformą Moodle. W programie OneNote można pisać prace i robić notatki do kursów.';
$string['ucp_notconnected'] = 'Połącz się z pakietem Microsoft 365 przed odwiedzeniem tej strony.';
$string['settings_onenote'] = 'Wyłącz program OneNote w pakiecie Microsoft 365';
$string['ucp_status_enabled'] = 'Aktywny';
$string['ucp_status_disabled'] = 'Nie połączono';
$string['ucp_syncwith_title'] = 'Synchronizuj z:';
$string['ucp_syncdir_title'] = 'Zachowanie funkcji synchronizacji:';
$string['ucp_syncdir_out'] = 'Z platformy Moodle do programu Outlook';
$string['ucp_syncdir_in'] = 'Z programu Outlook do platformy Moodle';
$string['ucp_syncdir_both'] = 'Zaktualizuj dane w programie Outlook i na platformie Moodle';
$string['ucp_title'] = 'Panel sterowania pakietu Microsoft 365 / platformy Moodle';
$string['ucp_options'] = 'Opcje';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:enable moodle.Files.LangFilesOrdering.UnexpectedComment
