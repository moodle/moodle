<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.4.4 + (2004083140)


$string['description'] = 'Bu yöntem aşağıda özel olarak biçimlendirilmiş dosyayı belirli aralıklarla kontrol edecek ve işleme alacaktır. Bu dosya şu şekilde olabilir:
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Düzyazı Dosyası';
$string['filelockedmail'] = 'Ders kaydı için kullandığınız dosya ($a) cron uygulaması tarafından silinemedi. Bu, dosyada yanlış izinlerin kullanılması anlamına gelmektedir. Moodle\'nin bu dosyayı silebilmesi için izinleri değiştirin. Aksi takdirde bu işlem sürekli tekrar edecektir.';
$string['filelockedmailsubject'] = 'Önemli hata: Kayıt dosyası';
$string['location'] = 'Dosya yeri';
$string['mailadmin'] = 'Yöneticileri emaille bilgilendir';
$string['mailusers'] = 'Kullanıcıları emaille bilgilendir';

?>
