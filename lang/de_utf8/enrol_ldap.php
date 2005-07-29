<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5.2 + (2005060221)


$string['description'] = '<p>Sie können LDAP Server nutzen, um automatisch Eintragungen zu kontrollieren. Es wird angenommen, dass der LDAP tree Gruppen enthält, die zu Kursen gehören und dass jede der Gruppen/Kurse Einträge von Teilnehmern hat.</p>
<p>Es wird angenommen, dass Kurse als Gruppen in LDAP definiert sind und jede Gruppe über mehrere Mitgliedsfelder verfügt
(<em>member</em> oder <em>memberUid</em>) mit einem eindeutigen Identifikationsfeld für die Nutzer.</p>
<p>Um LDAP Eintragungen zu verwenden, <strong>muß</strong>
jeder Nutzer eine gültige idnumber besitzen. Die LDAP Grupppen müssen diese idnumber im member Feld aufweisen, um den Teilnehmer in den Kurs einzutragen.
Dies funktioniert einwandfrei wenn ausschließlich die LDAP Authentifizierung genutzt wird.</p>
Eintragungen werden aktualisiert wenn der Nutzer  sich einloggt. Sie können auch ein Script nutzen, um Eintragungsdaten zu synchronisieren. Siehe
<em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Dieses plugin kann auch genutzt werde, um automatisch Kurse anzulegen wenn neue Gruppe im LDAP eingerichtet werden.</p>';
$string['enrol_ldap_autocreate'] = 'Kurse können automatisch angelegt werden wenn es Eintragungen zu einem Kurs gibt, der in moodle noch nicht existiert.';
$string['enrol_ldap_autocreation_settings'] = 'Einstellungen für automatische Kurseinrichtung';
$string['enrol_ldap_bind_dn'] = 'Wenn Sie  bind-user für die Kurssuche nutzen wollen, legen Sie es hier fest. Z.B. \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Passwort für bind-user';
$string['enrol_ldap_category'] = 'Kategorie für automatisch erzeugte Kurse';
$string['enrol_ldap_course_fullname'] = 'Option: LDAP Feld für Kurstitel';
$string['enrol_ldap_course_idnumber'] = 'Abbild der eindeutigen Identifizierung in LDAP, meist
<em>cn</em> oder <em>uid</em>. Es wird erwartet, dass der Wert geschlossen wird, wenn die automatische Kurserstellung verwendet wird.';
$string['enrol_ldap_course_settings'] = 'Kurseintragung-Einstellung';
$string['enrol_ldap_course_shortname'] = 'Option: LDAP Feld für den Kurznamen';
$string['enrol_ldap_course_summary'] = 'Opton: LDAP Feld für die Zusammenfassung';
$string['enrol_ldap_editlock'] = 'Schlüsselwert';
$string['enrol_ldap_general_options'] = 'Allgemeine Optionen';
$string['enrol_ldap_host_url'] = 'LDAP host in URL-form definieren, z.B.
\'ldap://ldap.myorg.com/\'
or \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass für Kurssuche (normalerweise \'posixGroup\')';
$string['enrol_ldap_search_sub'] = 'Suche Gruppenmitgliedschaften aus Subcontexts';
$string['enrol_ldap_server_settings'] = 'LDAP Server Einstellungen';
$string['enrol_ldap_student_contexts'] = 'Fundstelle für Liste der Verbindung von Gruppen und Teilnehmerereintragung. Verschiedene Einträge werden durch ein Semikolon getrennt: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Mitgliedseigenschaften, wenn Nutzer zu einer Gruppe gehört/eingetragen wird. Normalerweise \'member\' oder memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Teilnehmereintragung-Einstellungen';
$string['enrol_ldap_teacher_contexts'] = 'Fundstelle für Liste der Verbindung von Gruppen und Trainereintragung. Verschiedene Einträge werden durch ein Semikolon getrennt: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Mitgliedseigenschaften, wenn Nutzer zu einer Gruppe gehört/eingetragen wird. Normalerweise \'member\' oder memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Trainereintragung-Einstellungen';
$string['enrol_ldap_template'] = 'Option: automatisch erstellte Kurse (auto created courses) können ihre Einstellungen aus einem Templatekurs kopieren.';
$string['enrol_ldap_updatelocal'] = 'Update lokaler Daten';
$string['enrol_ldap_version'] = 'Version des LDAP Protokolls auf Ihrem Server';
$string['enrolname'] = 'LDAP';

?>
