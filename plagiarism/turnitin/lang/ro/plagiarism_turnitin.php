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
$string['pluginname'] = 'Plugin de detectare a plagiatului Turnitin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Procesul plugin de detectare a plagiatului Turnitin';
$string['connecttesterror'] = 'Eroare la conectarea la Turnitin. S-a returnat mesajul de eroare de mai jos:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Activare Turnitin';
$string['excludebiblio'] = 'Excluderea bibliografiei';
$string['excludequoted'] = 'Excluderea citatelor';
$string['excludevalue'] = 'Excludere similitudini mici';
$string['excludewords'] = 'Cuvinte';
$string['excludepercent'] = 'Procent';
$string['norubric'] = 'Niciun barem';
$string['otherrubric'] = 'Utilizarea baremului unui alt profesor';
$string['attachrubric'] = 'Atașarea unui barem la această temă';
$string['launchrubricmanager'] = 'Lansarea Managerului de bareme';
$string['attachrubricnote'] = 'Observație: studenții vor putea să vadă baremele atașate și conținutul lor înainte de a-și depune lucrările.';
$string['anonblindmarkingnote'] = 'Observație: setarea separată de însemnări anonime în Turnitin a fost eliminată. Turnitin va utiliza setarea de însemnări anonime din Moodle pentru a determina ce setare trebuie să folosească.';
$string['transmatch'] = 'Similitudini cu traduceri';
$string["reportgen_immediate_add_immediate"] = "Generați rapoarte imediat. Depunerile vor fi adăugate în depozit imediat (dacă depozitul este setat).";
$string["reportgen_immediate_add_duedate"] = "Generați rapoarte imediat. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).";
$string["reportgen_duedate_add_duedate"] = "Generați rapoarte la termenul de depunere. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).";
$string['launchquickmarkmanager'] = 'Lansare Manager Quickmark';
$string['launchpeermarkmanager'] = 'Lansare Manager Peermark';
$string['studentreports'] = 'Afișarea Rapoartelor privind originalitatea pentru studenți';
$string['studentreports_help'] = 'Vă permite să afișați utilizatorilor studenți rapoartele Turnitin privind originalitatea. Dacă este setat la da, rapoartele privind originalitatea generate de Turnitin pot fi văzute de către studenți.';
$string['submitondraft'] = 'Depunere fișier la prima încărcare';
$string['submitonfinal'] = 'Depunerea fișierului atunci când studentul trimite lucrarea pentru însemnări';
$string['draftsubmit'] = 'Când se depune fișierul în Turnitin?';
$string['allownonor'] = 'Se permite depunerea oricărui tip de fișier?';
$string['allownonor_help'] = 'Această setare va permite depunerea oricărui tip de fișier. Dacă opțiunea este setată la „Da”, originalitatea depunerilor va fi verificată în limita posibilităților, depunerile vor fi disponibile pentru descărcare și instrumentele de feedback GradeMark vor fi disponibile în limita posibilităților.';
$string['norepository'] = 'Niciun depozit';
$string['standardrepository'] = 'Depozit standard';
$string['submitpapersto'] = 'Stocarea lucrărilor studenților';
$string['institutionalrepository'] = 'Depozitul instituțional (dacă este cazul)';
$string['checkagainstnote'] = 'Observație: dacă nu selectați „Da” la cel puțin una dintre opțiunile „Verificare prin comparație cu...” de mai jos, Raportul privind originalitatea NU se va genera.';
$string['spapercheck'] = 'Verificare prin comparație cu lucrările depozitate ale studenților';
$string['internetcheck'] = 'Verificare prin comparație cu materialele de pe internet';
$string['journalcheck'] = 'Verificare prin comparație cu jurnale,<br />periodice și publicații';
$string['compareinstitution'] = 'Fișierele depuse se compară cu lucrările depuse în această instituție';
$string['reportgenspeed'] = 'Viteza generării raportului';
$string['locked_message'] = 'Mesaj blocat';
$string['locked_message_help'] = 'Dacă se blochează oricare dintre setări, se afișează acest mesaj pentru a explica motivul.';
$string['locked_message_default'] = 'Această setare este blocată la nivel de site';
$string['sharedrubric'] = 'Barem partajat';
$string['turnitinrefreshsubmissions'] = 'Reîncărcarea depunerilor';
$string['turnitinrefreshingsubmissions'] = 'Se reîmprospătează depunerile';
$string['turnitinppulapre'] = 'Pentru a depune un fișier la Turnitin, mai întâi trebuie să acceptați acordul nostru EULA. Dacă alegeți să nu acceptați acordul nostru EULA, fișierul dvs. va fi depus doar la Moodle. Faceți clic aici pentru a citi și a accepta Acordul.';
$string['noscriptula'] = '(Întrucât nu aveți Javascript activat, va trebui să reîncărcați manual această pagină pentru a putea depune o lucrare după acceptarea Acordului de utilizator Turnitin)';
$string['filedoesnotexist'] = 'Fișierul a fost șters';
$string['reportgenspeed_resubmission'] = 'Ați depus deja o lucrare la această temă și un Raport de similitudine a fost generat pentru depunere. Dacă decideți să vă redepuneți lucrarea, depunerea anterioară va fi înlocuită și va fi generat un raport nou. Pentru a vizualiza un nou Raport de similitudine, după {$a->num_resubmissions} redepuneri, va trebui să așteptați {$a->num_hours} ore după o redepunere.';

// Plugin settings.
$string['config'] = 'Configurație';
$string['defaults'] = 'Setări implicite';
$string['showusage'] = 'Afișarea conținutului bazei de date';
$string['saveusage'] = 'Salvarea conținutului bazei de date';
$string['errors'] = 'Erori';
$string['turnitinconfig'] = 'Configurarea pluginului de detectare a plagiatului Turnitin';
$string['tiiexplain'] = 'Turnitin este un produs comercial și trebuie să aveți un abonament plătit pentru a folosi acest serviciu. Pentru mai multe informații citiți <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Activare Turnitin';
$string['useturnitin_mod'] = 'Activare Turnitin pentru {$a}';
$string['turnitindefaults'] = 'Setările implicite ale pluginului de detectare a plagiatului Turnitin';
$string['defaultsdesc'] = 'Următoarele setări sunt valorile implicite setate la activarea Turnitin într-un modul de activitate';
$string['turnitinpluginsettings'] = 'Setări plugin de detectare a plagiatului Turnitin';
$string['pperrorsdesc'] = 'Problemă la încercarea de a încărca fișierele de mai jos în Turnitin. Pentru a redepune materialul, selectați fișierele pe care doriți să le redepuneți, apoi faceți clic pe butonul de redepunere. Acestea se vor procesa la următoarea executare a lucrării cron.';
$string['pperrorssuccess'] = 'Fișierele pe care le-ați selectat au fost redepuse și vor fi procesate de lucrarea cron.';
$string['pperrorsfail'] = 'Problemă cu unele dintre fișierele pe care le-ați selectat, nu s-a putut crea un nou eveniment cron pentru acestea.';
$string['resubmitselected'] = 'Redepunerea fișierelor selectate';
$string['deleteconfirm'] = 'Sigur doriți să ștergeți această depunere?\n\nAcțiunea nu poate fi anulată.';
$string['deletesubmission'] = 'Ștergere depunere';
$string['semptytable'] = 'Nu s-a găsit niciun rezultat.';
$string['configupdated'] = 'Configurație actualizată';
$string['defaultupdated'] = 'Setările implicite Turnitin au fost actualizate';
$string['notavailableyet'] = 'Indisponibil';
$string['resubmittoturnitin'] = 'Redepunere în Turnitin';
$string['resubmitting'] = 'Redepunere în curs';
$string['id'] = 'ID';
$string['student'] = 'Student';
$string['course'] = 'Curs';
$string['module'] = 'Modul';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Vizualizarea Raportului privind originalitatea';
$string['launchrubricview'] = 'Vizualizarea baremului folosit pentru însemnări';
$string['turnitinppulapost'] = 'Fișierul nu a fost depus în Turnitin. Faceți clic aici pentru a accepta EULA.';
$string['ppsubmissionerrorseelogs'] = 'Acest fișier nu a fost depus în Turnitin, consultați administratorul de sistem';
$string['ppsubmissionerrorstudent'] = 'Acest fișier nu a fost depus în Turnitin, consultați-vă îndrumătorul pentru mai multe detalii';

// Receipts.
$string['messageprovider:submission'] = 'Notificări privind confirmările digitale din pluginul de detectare a plagiatului Turnitin';
$string['digitalreceipt'] = 'Confirmare digitală';
$string['digital_receipt_subject'] = 'Aceasta este Confirmarea digitală Turnitin';
$string['pp_digital_receipt_message'] = 'Stimate/Stimată {$a->firstname} {$a->lastname},<br /><br />Ați depus cu succes fișierul <strong>{$a->submission_title}</strong> cu tema <strong>{$a->assignment_name}{$a->assignment_part}</strong> din cursul <strong>{$a->course_fullname}</strong>, pe data de <strong>{$a->submission_date}</strong>. ID-ul depunerii este <strong>{$a->submission_id}</strong>. Confirmarea digitală completă se poate vizualiza și imprima prin intermediul butonului de imprimare/descărcare din vizualizatorul de documente.<br /><br />Vă mulțumim pentru utilizarea Turnitin,<br /><br />Echipa Turnitin';

// Paper statuses.
$string['turnitinid'] = 'ID Turnitin';
$string['turnitinstatus'] = 'Stare Turnitin';
$string['pending'] = 'În așteptare';
$string['similarity'] = 'Similitudine';
$string['notorcapable'] = 'Pentru acest fișier nu se poate genera un Raport privind originalitatea.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Studentul a văzut lucrarea la:';
$string['student_notread'] = 'Studentul nu a văzut această lucrare.';
$string['launchpeermarkreviews'] = 'Lansare evaluări Peermark';

// Cron.
$string['ppqueuesize'] = 'Numărul evenimentelor din coada de evenimente a pluginului de detectare a plagiatului';
$string['ppcronsubmissionlimitreached'] = 'La această executare a lucrării cron nu se mai trimit în Turnitin alte depuneri, deoarece la o executare se procesează cel mult {$a}';
$string['cronsubmittedsuccessfully'] = 'Lucrarea: {$a->title} (ID TII: {$a->submissionid}) pentru tema {$a->assignmentname} din cursul {$a->coursename} a fost depusă cu succes în Turnitin.';
$string['pp_submission_error'] = 'Turnitin a returnat o eroare legată de depunere:';
$string['turnitindeletionerror'] = 'Ștergerea depunerii Turnitin nu a reușit. Copia locală Moodle a fost înlăturată, dar depunerea din Turnitin nu a putut fi ștearsă.';
$string['ppeventsfailedconnection'] = 'La această executare a lucrării cron nu se vor procesa alte evenimente cu pluginul de detectare a plagiatului Turnitin, deoarece nu se poate stabili conexiunea cu Turnitin.';

// Error codes.
$string['tii_submission_failure'] = 'Consultați-vă îndrumătorul sau administratorul de sistem pentru mai multe detalii';
$string['faultcode'] = 'Cod de eroare';
$string['line'] = 'Linie';
$string['message'] = 'Mesaj';
$string['code'] = 'Cod';
$string['tiisubmissionsgeterror'] = 'Eroare la încercarea de a obține depunerile pentru această temă de la Turnitin';
$string['errorcode0'] = 'Acest fișier nu a fost depus în Turnitin, consultați administratorul de sistem';
$string['errorcode1'] = 'Acest fișier nu a fost trimis în Turnitin, deoarece conținutul este insuficient pentru generarea unui Raport privind originalitatea.';
$string['errorcode2'] = 'Acest fișier nu se va depune în Turnitin pentru că depășește dimensiunea maximă admisă de {$a->maxfilesize}';
$string['errorcode3'] = 'Acest fișier nu a fost depus în Turnitin, deoarece utilizatorul nu a acceptat Acordul de licență cu utilizatorul final al Turnitin.';
$string['errorcode4'] = 'Pentru această temă trebuie să încărcați un tip de fișier acceptat. Tipurile acceptate sunt: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps și .rtf';
$string['errorcode5'] = 'Acest fișier nu a fost depus în Turnitin din cauza unei probleme la crearea modulului în Turnitin, care împiedică depunerile, pentru informații suplimentare, consultați jurnalele API';
$string['errorcode6'] = 'Acest fișier nu a fost depus în Turnitin din cauza unei probleme la editarea modulului în Turnitin, care împiedică depunerile, pentru informații suplimentare, consultați jurnalele API';
$string['errorcode7'] = 'Acest fișier nu a fost depus în Turnitin din cauza unei probleme la crearea utilizatorului în Turnitin, care împiedică depunerile, pentru informații suplimentare, consultați jurnalele API';
$string['errorcode8'] = 'Acest fișier nu a fost depus în Turnitin din cauza unei probleme la crearea fișierului temporar. Cauza probabilă este un nume de fișier incorect. Redenumiți fișierul și reîncărcați-l prin Editare depunere.';
$string['errorcode9'] = 'Imposibil de depus fișierul: în lista de fișiere nu există conținut accesibil care să poată fi depus.';
$string['coursegeterror'] = 'Nu au putut fi obținute datele cursului';
$string['configureerror'] = 'Pentru a putea utiliza modul într-un curs, trebuie să-l configurați complet ca Administrator. Contactați administratorul Moodle.';
$string['turnitintoolofflineerror'] = 'Avem o problemă temporară. Încercați din nou în scurt timp.';
$string['defaultinserterror'] = 'Eroare la încercarea de a introduce o valoare de setare implicită în baza de date';
$string['defaultupdateerror'] = 'Eroare la încercarea de a actualiza o valoare de setare implicită în baza de date';
$string['tiiassignmentgeterror'] = 'Eroare la încercarea de a obține o temă de la Turnitin';
$string['assigngeterror'] = 'Imposibil de obținut datele Turnitin';
$string['classupdateerror'] = 'Nu au putut fi actualizate datele cursului Turnitin';
$string['pp_createsubmissionerror'] = 'Eroare la încercarea de a crea depunerea în Turnitin';
$string['pp_updatesubmissionerror'] = 'Eroare la încercarea de a redepune lucrarea în Turnitin';
$string['tiisubmissiongeterror'] = 'Eroare la încercarea de a obține o depunere de la Turnitin';

// Javascript.
$string['closebutton'] = 'Închidere';
$string['loadingdv'] = 'Se încarcă vizualizatorul de documente Turnitin...';
$string['changerubricwarning'] = 'Modificarea sau detașarea unui barem va înlătura din lucrările cu această temă toate punctajele acordate pe baza baremului, inclusiv grilele de notare punctate anterior. Notele generale ale lucrărilor notate anterior se vor păstra.';
$string['messageprovider:submission'] = 'Notificări privind confirmările digitale din pluginul de detectare a plagiatului Turnitin';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Stare Turnitin';
$string['deleted'] = 'Șters';
$string['pending'] = 'În așteptare';
$string['because'] = 'Cauza este faptul că un administrator a șters o temă în așteptare din coada de procesare și a întrerupt depunerea în Turnitin.<br /><strong>Fișierul există în continuare în Moodle, contactați profesorul.</strong><br />Căutați mai jos codul de eroare:';
$string['submitpapersto_help'] = '<strong>Niciun depozit: </strong><br />Turnitin este configurat să nu stocheze documentele depuse în niciun depozit. Vom procesa lucrarea numai pentru o verificare inițială de similaritate.<br /><br /><strong>Depozit standard: </strong><br />Turnitin va trimite un exemplar al documentului depus numai în Depozitul standard. Alegând această opțiune, Turnitin va fi configurat să folosească numai documentele depuse pentru a efectua verificări de similaritate pentru documente depuse în viitor.<br /><br /><strong>Depozitul instituțional (dacă este cazul): </strong><br />Prin alegerea acestei opțiuni, Turnitin este configurat să adauge documentele depuse într-un depozit privat al instituției. Verificările de similaritate pentru documentele depuse se vor efectua de către alți profesori din instituția dvs.';
$string['errorcode12'] = 'Fișierul nu a fost depus la Turnitin deoarece aparține unei teme la care cursul a fost șters. ID rând: ({$a->id}) | ID modul de curs: ({$a->cm}) |  ID de utilizator: ({$a->userid})';
$string['errorcode15'] = 'Acest fișier nu a fost depus la Turnitin deoarece modulul de activitate căruia îi aparține nu a putut fi găsit';
$string['tiiaccountconfig'] = 'Configurarea contului Turnitin';
$string['turnitinaccountid'] = 'ID de cont Turnitin';
$string['turnitinsecretkey'] = 'Cheia partajată Turnitin';
$string['turnitinapiurl'] = 'URL-ul API Turnitin';
$string['tiidebugginglogs'] = 'Depanare și înregistrare în jurnal';
$string['turnitindiagnostic'] = 'Activați Modul diagnostic';
$string['turnitindiagnostic_desc'] = '<b>[Atenție]</b><br />Activați Modul diagnostic numai pentru a identifica problemele cu API Turnitin.';
$string['tiiaccountsettings_desc'] = 'Asigurați-vă că aceste setări sunt identice cu cele pe care le-ați configurat în contul Turnitin, în caz contrar puteți întâmpina probleme în crearea temelor și/dau în lucrul cu materialele depuse de studenți.';
$string['tiiaccountsettings'] = 'Setările contului Turnitin';
$string['turnitinusegrademark'] = 'Utilizare GradeMark';
$string['turnitinusegrademark_desc'] = 'Stabiliți dacă depunerile se vor nota prin GradeMark.<br /><i>(Opțiunea este disponibilă doar utilizatorilor pentru conturile cărora s-a configurat GradeMark)</i>';
$string['turnitinenablepeermark'] = 'Activarea temelor Peermark';
$string['turnitinenablepeermark_desc'] = 'Stabiliți dacă se vor putea crea teme Peermark.<br/><i>(Opțiunea este disponibilă doar utilizatorilor pentru conturile cărora s-a configurat Peermark)</i>';
$string['transmatch_desc'] = 'Determină dacă setarea Similitudini cu traduceri va fi disponibilă în ecranul de configurare a temei.<br /><i>(Activați această opțiune doar dacă Similitudini cu traduceri este activată la contul Turnitin)</i>';
$string['repositoryoptions_0'] = 'Activarea opțiunilor de depozit standard pentru profesor';
$string['repositoryoptions_1'] = 'Activarea opțiunilor de depozit extinse pentru profesor';
$string['repositoryoptions_2'] = 'Toate lucrările se trimit în depozitul standard';
$string['repositoryoptions_3'] = 'Nu se trimite nicio lucrare în niciun depozit';
$string['turnitinrepositoryoptions'] = 'Teme – depozit de lucrări';
$string['turnitinrepositoryoptions_desc'] = 'Alegeți opțiunile de depozit pentru temele Turnitin.<br /><i>(Depozitul instituțional este disponibil doar utilizatorilor pentru ale căror conturi s-a activat acest lucru)</i>';
$string['tiimiscsettings'] = 'Diverse setări de plugin';
$string['pp_agreement_default'] = 'Confirm că această depunere este munca mea și îmi asum toată răspunderea pentru orice violare a drepturilor de autor care se poate acea loc ca rezultat al acestei depuneri.';
$string['pp_agreement_desc'] = '<b>[Opțional]</b><br />Introduceți o declarație de confirmare a acordului pentru depuneri.<br />(<b>Observație:</b> Dacă acordul este lăsat complet gol, studenților nu li se va cere nicio confirmare a acordului în momentul depunerii)';
$string['pp_agreement'] = 'Exonerare de răspundere/Acord';
$string['studentdataprivacy'] = 'Setările de confidențialitate a datelor studenților';
$string['studentdataprivacy_desc'] = 'Următoarele setări pot fi configurate pentru a împiedica transmiterea datelor personale ale studentului către Turnitin, prin API.';
$string['enablepseudo'] = 'Activarea confidențialității studentului';
$string['enablepseudo_desc'] = 'Dacă această opțiune este selectată, adresele de e-mail ale studenților vor fi transformate în pseudoadrese echivalente în apelurile API Turnitin.<br /><i>(<b>Observație:</b> Această opțiune nu se poate modifica dacă s-au sincronizat deja date ale utilizatorului din Moodle în Turnitin)</i>';
$string['pseudofirstname'] = 'Pseudoprenumele studentului';
$string['pseudofirstname_desc'] = '<b>[Opțional]</b><br />Prenumele studentului care se va afișa în vizualizatorul de documente Turnitin';
$string['pseudolastname'] = 'Pseudonumele de familie al studentului';
$string['pseudolastname_desc'] = 'Numele de familie al studentului care se va afișa în vizualizatorul de documente Turnitin';
$string['pseudolastnamegen'] = 'Generarea automată a numelui de familie';
$string['pseudolastnamegen_desc'] = 'Dacă opțiunea este setată la valoarea da și presudonumele de familie este setat la un câmp din profilul de utilizator, câmpul se va completa automat cu un identificator unic.';
$string['pseudoemailsalt'] = 'Completare cu numere pseudoaleatoare la criptare';
$string['pseudoemailsalt_desc'] = '<b>[Opțional]</b><br />O completare opțională pentru a mări complexitatea pseudoadresei de e-mail generate a studentului.<br />(<b>Observație:</b> Completarea trebuie să rămână neschimbată pentru a avea pseudoadrese de e-mail uniforme)';
$string['pseudoemaildomain'] = 'Pseudodomeniu de e-mail';
$string['pseudoemaildomain_desc'] = '<b>[Opțional]</b><br />Un domeniu opțional pentru pseudoadresele de e-mail. (Dacă rămâne necompletat, primește valoarea implicită @tiimoodle.com)';
$string['pseudoemailaddress'] = 'Pseudoadresă de e-mail';
$string['connecttest'] = 'Testarea conexiunii Turnitin';
$string['connecttestsuccess'] = 'Moodle s-a conectat cu succes la Turnitin.';
$string['diagnosticoptions_0'] = 'Dezactivat';
$string['diagnosticoptions_1'] = 'Standard';
$string['diagnosticoptions_2'] = 'Depanare';
$string['repositoryoptions_4'] = 'Depuneți toate lucrările la depozitul instituției';
$string['turnitinrepositoryoptions_help'] = '<strong>Activarea opțiunilor de depozit standard pentru profesor: </strong><br />Profesorii pot configura Turnitin să adauge documente în depozitul standard, în depozitul privat al instituţiei sau în niciun depozit.<br /><br /><strong>Activarea opțiunilor de depozit extinse pentru profesor: </strong><br />Această opțiune le permite profesorilor să vizualizeze o setare a temei care îi lasă pe studenți să specifice sistemului Turnitin unde vor fi stocate documentele lor. Studenții pot alege să adauge documentele în depozitul standard de documente sau în depozitul privat al instituției dumneavoastră.<br /><br /><strong>Toate lucrările se trimit în depozitul standard: </strong><br />În mod implicit, toate documentele vor fi adăugate în depozitul standard al studentului.<br /><br /><strong>Nu se trimite nicio lucrare în niciun depozit: </strong><br />Documentele vor fi folosite numai pentru a efectua verificarea inițială de sistemul Turnitin și pentru a le afișa profesorului pentru evaluare.<br /><br /><strong>Depuneți toate lucrările la depozitul instituției: </strong><br />Turnitin este configurat să stocheze toate lucrările în depozitul de lucrări al instituției. Verificările de similaritate pentru documentele depuse se vor efectua de către alți profesori din instituția dumneavoastră.';
$string['turnitinuseanon'] = 'Utilizarea însemnărilor anonime';
$string['createassignmenterror'] = 'Eroare la încercarea de a crea tema în Turnitin';
$string['editassignmenterror'] = 'Eroare la încercarea de a modifica tema în Turnitin';
$string['ppassignmentediterror'] = 'Modulul {$a->title} (ID TII: {$a->assignmentid}) nu s-a putut edita în Turnitin, consultați jurnalele API pentru mai multe informații';
$string['pp_classcreationerror'] = 'Acest curs nu a putut fi creat în Turnitin, consultați jurnalele API pentru mai multe informații';
$string['unlinkusers'] = 'Dezasocierea utilizatorilor';
$string['relinkusers'] = 'Reasocierea utilizatorilor';
$string['unlinkrelinkusers'] = 'Dezasocierea/reasocierea utilizatorilor Turnitin';
$string['nointegration'] = 'Fără integrare';
$string['sprevious'] = 'Anterior';
$string['snext'] = 'Următorul';
$string['slengthmenu'] = 'Afișarea intrărilor din _MENU_';
$string['ssearch'] = 'Căutare:';
$string['sprocessing'] = 'Se încarcă datele din Turnitin...';
$string['szerorecords'] = 'Nu există date de afișat.';
$string['sinfo'] = 'Se afișează intrările _START_ – _END_ din _TOTAL_.';
$string['userupdateerror'] = 'Imposibil de actualizat datele utilizatorilor';
$string['connecttestcommerror'] = 'Imposibil de conectat la Turnitin. Verificați setarea URL-ului API.';
$string['userfinderror'] = 'Eroare la încercarea de a găsi utilizatorul în Turnitin';
$string['tiiusergeterror'] = 'Eroare la încercarea de obține detaliile utilizatorului de la Turnitin';
$string['usercreationerror'] = 'Crearea utilizatorului Turnitin nu a reușit';
$string['ppassignmentcreateerror'] = 'Acest modul nu a putut fi creat în Turnitin, consultați jurnalele API pentru mai multe informații';
$string['excludebiblio_help'] = 'Această setare îi permite profesorului să excludă de la verificarea de similitudini textele care apar în secțiunile de bibliografie, de opere citate, sau de referințe ale lucrărilor, atunci când se generează Rapoartele privind originalitatea. Setarea poate fi modificată în Rapoartele privind originalitatea individuale.';
$string['excludequoted_help'] = 'Această setare îi permite profesorului să excludă de la verificarea de similitudini textele care apar între semnele citării, atunci când se generează Rapoartele privind originalitatea. Această setare poate fi modificată în Rapoartele privind originalitatea individuale.';
$string['excludevalue_help'] = 'Această setare permite îi profesorului să excludă din Rapoartele privind originalitatea similitudinile care nu au lungimea suficientă (determinată de către profesor). Această setare poate fi modificată în Rapoartele privind originalitatea individuale.';
$string['spapercheck_help'] = 'Verificare prin comparație cu depozitul de lucrări ale studenților din Turnitin, în timpul procesării Rapoartelor privind originalitatea lucrărilor. Dacă deselectați această opțiune, procentajul indicelui de similitudine poate scădea.';
$string['internetcheck_help'] = 'Verificarea se face prin comparație cu depozitul internet în timpul procesării Rapoartelor privind originalitatea lucrărilor. Este posibil ca procentajul indicelui de similitudine să scadă dacă această opțiune este deselectată.';
$string['journalcheck_help'] = 'Verificarea se face prin comparație cu depozitul Turnitin de jurnale, periodice și publicații, în timpul procesării Rapoartelor privind originalitatea lucrărilor. Este posibil ca procentajul indicelui de similitudine să scadă dacă această opțiune este deselectată.';
$string['reportgenspeed_help'] = 'Există trei opțiuni pentru această setare a temei: „Generați rapoarte imediat. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).”, „Generați rapoarte imediat. Depunerile vor fi adăugate în depozit imediat (dacă depozitul este setat).” și „Generați rapoarte la termenul de depunere. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).”<br /><br />Opțiunea „Generați rapoarte imediat. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).” generează Raportul privind originalitatea imediat ce studentul depune lucrarea. Dacă selectați această opțiune, studenții nu vor putea să redepună lucrarea.<br /><br />Pentru a permite redepunerile, selectați opțiunea „Generați rapoarte imediat. Depunerile vor fi adăugate în depozit imediat (dacă depozitul este setat).”. Aceasta permite studenților să redepună în continuare lucrări cu tema respectivă, până la termen. Procesarea Rapoartelor privind originalitatea pentru redepuneri poate dura până la 24 de ore.<br /><br />Opțiunea „Generați rapoarte la termenul de depunere. Depunerile vor fi adăugate în depozit la termenul de depunere (dacă depozitul este setat).” va genera un Raport privind originalitatea doar la termenul de depunere definit în temă. Această setare va face ca toate lucrările trimise în contul temei să fie comparate între ele la momentul creării Rapoartelor privind originalitatea.';
$string['turnitinuseanon_desc'] = 'Stabiliți dacă însemnările anonime vor fi permise la notarea depunerilor.<br /><i>(Opțiunea este disponibilă doar utilizatorilor pentru conturile cărora s-au configurat însemnările anonima)</i>';
