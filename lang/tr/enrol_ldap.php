<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.6 development (2005072200)


$string['description'] = '<p>Ders kayýtlarýný kontrol etmek için bir LDAP sunucu kullanabilirsiniz.
LDAP aðacýnýn kurslarý referans eden gruplarý ve her bir grubun/dersin öðrenci üyeliklerinin ayarlandýðýný var sayýyoruz.</p>

<p>Kurslarýn LDAP içinde grup olarak tanýmlandýðýný ve her bir grubun (yani dersin) çoklu üyelik alanlarýnýn olduðunu - ki bu alanlarýn kullanýcýyý tanýmlamak için tekil olmasý gerekir (<em>member</em> veya <em>memberUid</em> gibi) - varsayýyoruz.</p>

<p>LDAP kayýt yöntemini kullanabilmeniz için kullanýcýlarýnýzýn geçerli bir idnumber alaný <strong>olmalý</strong>. Bir kullanýcý derse kaydolduðunda LDAP gruplarý bu idnumber alanýný içermeli. Zaten LDAP yetkilendirmesini kullanýyorsanýz genellikle bu iyi çalýþýr.</p>

<p>Ders kayýtlarý kullanýcý giriþ yaptýðýnda güncellenir ve ayrýca kayýtlarýn senkronize olmasý için bir betik de çalýþtýrabilirsiniz.
Buraya bakýnýz: <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>

<p>Bu eklenti, LDAP içinde görünen yeni gruplardan otomatik olarak yeni kurslar da oluþturabilir.</p>';
$string['enrol_ldap_autocreate'] = 'Moodle içinde henüz var olmayan bir kursa kayýtlar varsa kurslar otomatik olarak oluþturulabilir.';
$string['enrol_ldap_autocreation_settings'] = 'Otomatik kurs oluþturma ayarlarý';
$string['enrol_ldap_bind_dn'] = 'Kullanýcýlarý aramak için yetkili-kullanýcý kullanmak istiyorsanýz burada belirtin. Örnek: \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Yetkili kullanýcý için þifre';
$string['enrol_ldap_category'] = 'Otomatik oluþturulan kurslar için kategori.';
$string['enrol_ldap_course_fullname'] = 'Ýsteðe baðlý: Tam adýnýn alýnacaðý LDAP alaný';
$string['enrol_ldap_course_idnumber'] = 'LDAP\'taki birincil tanýmlayýcýyý belirtin. Genellikle <em>cn</em> veya <em>uid</em>. Otomatik kurs oluþturmayý kullanýyorsanýz deðeri kilitlemeniz önerilir. ';
$string['enrol_ldap_course_settings'] = 'Kurs kaydý ayarlarý';
$string['enrol_ldap_course_shortname'] = 'Ýsteðe baðlý: Kýsa adýnýn alýnacaðý LDAP alaný';
$string['enrol_ldap_course_summary'] = 'Ýsteðe baðlý: Özetin alýnacaðý LDAP alaný';
$string['enrol_ldap_editlock'] = 'Deðeri kilitle';
$string['enrol_ldap_general_options'] = 'Genel Seçenekler';
$string['enrol_ldap_host_url'] = 'LDAP sunucunun adresini belirtin.
Ör: \'ldap://ldap.sirketim.com/\' veya \'ldaps://ldap.sirketim.com/\' ';
$string['enrol_ldap_objectclass'] = 'Kurslarý aramak için kullanýlacak objectClass. Genellikle \'posixGroup\'';
$string['enrol_ldap_search_sub'] = 'Grup üyeliklerini alt-baðlamlardan ara';
$string['enrol_ldap_server_settings'] = 'LDAP sunucu ayarlarý';
$string['enrol_ldap_student_contexts'] = 'Öðrenci kayýtlarýnýn nerede olduðunu gösteren baðlam listeleri. Farklý baðlamlarý \';\' ile ayýrýn. Örnek: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Üye niteliði. Öðrencilerin ders kaydý yapýlýrken bir gruba mensup olduðunda. Genellikle \'member\' veya \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Öðrenci kaydý ayarlarý';
$string['enrol_ldap_teacher_contexts'] = 'Eðitimci kayýtlarýnýn nerede olduðunu gösteren baðlam listeleri. Farklý baðlamlarý \';\' ile ayýrýn. Örnek: \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Üye niteliði. Eðitimcilerin derse eðitimci olarak atanýrken bir gruba mensup olduðunda. Genellikle \'member\' veya \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Eðitimci kaydý ayarlarý';
$string['enrol_ldap_template'] = 'Ýsteðe baðlý: Otomatik oluþturulan kurslar, bir kurs þemasýndan ayarlarýný kopyalayabilir. Þablon kursun kýsa adýný giriniz.';
$string['enrol_ldap_updatelocal'] = 'Yerel veriyi güncelle';
$string['enrol_ldap_version'] = 'Sunucunun kullandýðý protokol sürümü';
$string['enrolname'] = 'LDAP';

?>
