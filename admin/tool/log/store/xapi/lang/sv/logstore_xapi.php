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
 * Swedish log store lang strings.
 *
 * @package   logstore_xapi
 * @copyright Martin Sandberg <martin.sandberg@xtractor.se>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['endpoint'] = 'Din LRS-endpoint för xAPI';
$string['settings'] = 'Allmänna inställningar';
$string['xapifieldset'] = 'Exempel på anpassad uppsättning fält';
$string['xapi'] = 'xAPI';
$string['password'] = 'Din "LRS basic auth secret/password" för xAPI';
$string['pluginadministration'] = 'Administration av Logstore xAPI';
$string['pluginname'] = 'Logstore xAPI';
$string['submit'] = 'Skicka in';
$string['username'] = 'Din "LRS basic auth key/username" för xAPI';
$string['xapisettingstitle'] = 'Inställningar för Logstore xAPI';
$string['backgroundmode'] = 'Skicka uttryck (statements) via schemalagd uppgift?';
$string['backgroundmode_desc'] = 'Detta kommer att tvinga LMS:et att skicka uttryck (statements) till LRS:et i bakgrunden, via en schemalagd cron-uppgift. Detta för att undvika att sidor tar för lång tid att ladda. Det innebär mindre av realtids-processning, men kommer hjälpa till att förhindra oförutsägbar prestanda i LMS:et kopplat till LRS:ets prestanda.';
$string['maxbatchsize'] = 'Maxstorlek per batch';
$string['maxbatchsize_desc'] = 'Uttryck (statements) skickas till LRS batchvis. Denna inställning kontrollerar max antal uttryck som skickas i en enskild operation. Om du ställer in noll, så kommer alla tillgängliga uttryck att skickas direkt, detta är dock inte rekommenderat.';
$string['taskemit'] = 'Sänd poster till LRS';
$string['routes'] = 'Inkludera handlingar som innehåller följande';
$string['filters'] = 'Filtrera loggar';
$string['logguests'] = 'Logga gästers handlingar';
$string['filters_help'] = 'Aktivera filter som ska INKLUDERA de handlingar som ska loggas.';
$string['mbox'] = 'Identifiera användare via e-postadress';
$string['mbox_desc'] = 'Uttryck (statements) kommer att identifiera användare via deras e-postadress (mbox), när detta är valt.';
$string['send_username'] = 'Identifiera användare via ID';
$string['send_username_desc'] = 'Uttryck kommer att identifiera användare via deras användarnamn, när detta är valt, men endast om identifiering via e-postadress är bortvalt.';
$string['send_jisc_data'] = 'Lägger till JISC-data till uttryck (statements)';
$string['send_jisc_data_desc'] = 'Uttryck kommer att innehålla data som krävs av JISC.';
$string['shortcourseid'] = 'Skicka kortnamn på kurs';
$string['shortcourseid_desc'] = 'Uttryck kommer att innehålla kortnamnet för en kurs, som ett tillägg till kurs-ID.';
$string['sendidnumber'] = 'Skicka kursens och aktivitetens ID-nummer';
$string['sendidnumber_desc'] = 'Uttryck kommer att inkludera ID-nummer (definierat av administratör) för kurser och aktiviteter i objektets tillägg (object extensions)';
$string['send_response_choices'] = 'Skicka svarsalternativ';
$string['send_response_choices_desc'] = 'Uttryck (statements) för flervalsfrågor och sekventiella frågor kommer att skickas till LRS, med korrekta svar och tillgängliga svar';
$string['resendfailedbatches'] = 'Skicka om misslyckade batchar';
$string['resendfailedbatches_desc'] = 'Vid processing av händelser i batchar, pröva att skicka om händelser i mindre batchar om en batch misslyckas. Om detta inte är valt, så kommer inte hela batchen att skickas om en misslyckad händelse inträffar.';
$string['privacy:metadata:logstore_xapi_log'] = 'Tabell som håller xAPI-data för cron-processning';
$string['privacy:metadata:logstore_xapi_log:userid'] = 'Användar-ID för xAPI-tabell för cron-processning';
$string['privacy:metadata:logstore_xapi_failed_log'] = 'Tabell som håller xAPI-data för misslyckade händelser';
$string['privacy:metadata:logstore_xapi_failed_log:userid'] = 'Användar-ID för xAPI-tabell för misslyckade händelser';
