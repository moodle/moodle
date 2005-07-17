<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 ALPHA (2005051500)


$string['description'] = '<p>Maaari kang gumamit ng LDAP server para makontrol ang pagpapaenrol mo.
                          Inaasahan na ang LDAP tree mo ay naglalaman ng mga grupo na nakamapa sa 
                          mga kurso, at ang bawat grupo/kurso na iyon ay may mga entry ng 
                          miyembro na nakamapa sa mga mag-aaral.</p>
                          <p>Inaasahan na ang mga kurso ay itinakda bilang grupo sa
                          LDAP, kung saan ang bawat grupo ay maraming pangmiyembrong field na 
                          (<em>member</em> o <em>memberUid</em>) na naglalaman ng natatanging
                          pagkakakilanlan ng user.</p>
                          <p>Para magamit ang LDAP na pageenrol, <strong>kailangan</strong> ng mga user mo 
                          na magkaroon ng tanggap na idnumber field.  Kailangang nasa pangmiyembrong field ng mga grupo 
                          ng LDAP  ang idnumber na iyon para maenrol ang isang user
                          sa kurso.
                          Kadalasan ay gagana ito ng maayos kung gumagamit ka na ng LDAP na 
                          Pagaauthenticate.</p>
                          <p>Mababago ang mga pageenrol kapag naglog-in ang user.  Maaari
                           ka ring magpatakbo ng script para mapanitiling naka-synch ang mga pageenrol.  Tingnan ang
                          <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
                          <p>Ang plugin na ito ay maaari ring isaayos na awtomatikong lumikha ng mga 
                          bagong kurso kapag may bagong grupo na lumitaw sa LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Maaaring likhain ng awtomatiko ang mga kurso kung may
                                    pag-eenrol sa isang kurso na wala pa 
                                    sa Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Kaayusan ng awtomatikong paglikha ng kurso';
$string['enrol_ldap_bind_dn'] = 'Kung gusto mong gumamit ng bind-user upang maghanap ng user,
                                 itakda ito rito.  Tulad ng 
                                 \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Password para sa bind-user.';
$string['enrol_ldap_category'] = 'Ang kategoriya para sa mga kursong nilikha nang awtomatiko.';
$string['enrol_ldap_course_fullname'] = 'Opsiyonal: LDAP field na pagkukunan ng buong pangalan.';
$string['enrol_ldap_course_idnumber'] = 'Imapa sa natatanging indentifier sa LDAP, karaniwan ay 
                                         <em>cn</em> o <em>uid</em>. Iminumungkahi na 
                                         ikandado ang halaga kung gumagamit ka 
                                         ng awtomatikong paglikha ng kurso.';
$string['enrol_ldap_course_settings'] = 'Kaayusang ng pag-eenrol sa kurso';
$string['enrol_ldap_course_shortname'] = 'Opsiyonal: LDAP field na pagkukunan ng maikling pangalan.';
$string['enrol_ldap_course_summary'] = 'Opsiyonal: LDAP field na pagkukunan ng buod.';
$string['enrol_ldap_editlock'] = 'Ikandado ang halaga';
$string['enrol_ldap_general_options'] = 'Mga Pangkalahatang Opsiyon';
$string['enrol_ldap_host_url'] = 'Itakda ang  LDAP host sa anyong URL tulad ng
                                  \'ldap://ldap.myorg.com/\' 
                                  o \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass na ginagamit sa paghahanap ng mga kurso.  Karaniwan ay
                                     \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Hanapin ang kinasasapiang pangkat mula sa subkonteksto.';
$string['enrol_ldap_server_settings'] = 'Mga Kaayusan ng LDAP Server';
$string['enrol_ldap_student_contexts'] = 'Listahan ng konteksto kung saan naroroon ang mga grupo na 
                                          may pag-eenrol ng mga mag-aaral.  Paghiwalayin ang magkakaibang 
                                          konteksto sa pamamagitan ng \';\'. Halimbawa: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Attribute ng miyembro, kung ang user ay kabilang
                                          (nakaenrol) sa isang grupo.  Karaniwan ay \'member\'
                                          o \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Kaayusan ng pageenrol ng mag-aaral';
$string['enrol_ldap_teacher_contexts'] = 'Listahan ng mga konteksto kung saan naroroon ang mga grupo 
                                          na may pag-eenrol ng guro.  Paghiwalayin ang magkakaibang
                                          konteksto sa pamamagitan ng \';\'. Halimbawa: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Attribute ng miyembro, kung ang user ay kabilang 
                                          (nakaenrol) sa isang grupo. Karaniwan ay \'member\'
                                          o \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Kaayusan ng pageenrol ng guro';
$string['enrol_ldap_template'] = 'Opsiyonal: ang mga awto-nilikhang kurso ay makokopya 
                                  ang kaayusan nila sa isang template na kurso.';
$string['enrol_ldap_updatelocal'] = 'Baguhin ang lokal na datos';
$string['enrol_ldap_version'] = 'Ang bersiyon ng LDAP protocol na ginagamit ng server mo.';
$string['enrolname'] = 'LDAP';

?>
