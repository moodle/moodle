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
$string['pluginname'] = 'Turnitin, intihal eklentisi';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin İntihal Eklenti Görevi';
$string['connecttesterror'] = 'Turnitin&#39;e bağlanırken bir hata oluştu. Hata aşağıda belirtilmiştir:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Turnitin&#39;i Etkinleştir';
$string['excludebiblio'] = 'Bibliyografyayı Çıkar';
$string['excludequoted'] = 'Alıntılanan Materyali Çıkar';
$string['excludevalue'] = 'Küçük Eşleşmeleri Çıkar';
$string['excludewords'] = 'Kelimeler';
$string['excludepercent'] = 'Yüzde';
$string['norubric'] = 'Performans değerlendirme ölçeği yok';
$string['otherrubric'] = 'Diğer öğretmenlere ait bir performans değerlendirme ölçeği kullan';
$string['attachrubric'] = 'Bu ödeve bir performans değerlendirme ölçeği ekleyin';
$string['launchrubricmanager'] = 'Performans Değerlendirme Ölçeği Yöneticisini Piyasaya Sür';
$string['attachrubricnote'] = 'Not: Öğrenciler, gönderimden önce ekli performans değerlendirme ölçeği ve içeriğini göreceklerdir.';
$string['anonblindmarkingnote'] = 'Not: Turnitin anonim işaretleme ayarı kaldırıldı. Turnitin, anonim işaretleme ayarı için Moodle&#39;ın kapalı işaretleme ayarını kullanacaktır.';
$string['transmatch'] = 'Çeviri Eşleştirme';
$string["reportgen_immediate_add_immediate"] = "Raporları anında oluşturun. Gönderiler, depoya anında eklenecektir (depo ayarlanmışsa).";
$string["reportgen_immediate_add_duedate"] = "Raporları anında oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).";
$string["reportgen_duedate_add_duedate"] = "Raporları teslim tarihinde oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).";
$string['launchquickmarkmanager'] = 'Quickmark Yöneticisini Başlat';
$string['launchpeermarkmanager'] = 'Peermark Yöneticisini Piyasaya Sür';
$string['studentreports'] = 'Orijinallik Raporunu Öğrencilere Göster';
$string['studentreports_help'] = 'Öğrencilere Turnitin orijinallik raporlarını göstermenizi sağlar. Evet olarak ayarlanırsa Turnitin tarafından oluşturulan orijinallik raporu, öğrenciler tarafından görüntülenebilecektir.';
$string['submitondraft'] = 'Dosyayı yükleme işlemi yapıldığında gönder';
$string['submitonfinal'] = 'Dosyayı öğrenci işaretleme için gönderdikten sonra yolla';
$string['draftsubmit'] = 'Dosyanın Turnitin&#39;e ne zaman gönderilmesi gerekir?';
$string['allownonor'] = 'Tüm dosya türlerinin gönderimine izin verilsin mi?';
$string['allownonor_help'] = 'Bu ayar, tüm dosya türlerinin gönderilmesine izin verir. Bu seçenek &#34;Evet&#34; olarak ayarlandığında, gönderiler mümkünse orijinallik raporu için kontrol edilecek, gönderiler indirilebilecek ve mümkün olduğunda, GradeMark geri bildirim araçlarına erişilebilecektir.';
$string['norepository'] = 'Havuz Yok';
$string['standardrepository'] = 'Standart Havuz';
$string['submitpapersto'] = 'Öğrenci Ödevlerini Depola';
$string['institutionalrepository'] = 'Kurum Havuzu (Uygulanabilir Yer)';
$string['checkagainstnote'] = 'Not: Aşağıdaki seçeneklerden en az birinde "Evet" seçeneğini seçmezseniz Orijinallik raporu oluşturulmayacaktır.';
$string['spapercheck'] = 'Depolanan öğrenci ödevlerinde kontrol et';
$string['internetcheck'] = 'İnternette ara';
$string['journalcheck'] = 'Dergi,<br />süreli yayın ve yayınlarda ara';
$string['compareinstitution'] = 'Bu kurum içinde gönderilen ödevler ile gönderilen dosyaları karşılaştır';
$string['reportgenspeed'] = 'Rapor Oluşturma Hızı';
$string['locked_message'] = 'Kilitli mesaj';
$string['locked_message_help'] = 'Herhangi bir ayar kilitliyse, bu mesaj nedenini söylemek için görüntülenir.';
$string['locked_message_default'] = 'Bu ayar bu site seviyesinde kilitlidir';
$string['sharedrubric'] = 'Paylaşılan Performans Değerlendirme Ölçeği';
$string['turnitinrefreshsubmissions'] = 'Gönderileri Yenile';
$string['turnitinrefreshingsubmissions'] = 'Gönderiler Yenileniyor';
$string['turnitinppulapre'] = 'Turnitin\'e bir dosya göndermek için öncelikle Son Kullanıcı Lisans Sözleşmemizi (EULA) kabul etmeniz gerekmektedir. EULA\'yı kabul etmediğiniz takdirde dosyanız sadece Moodle\'a gönderilecektir. Sözleşmeyi okuyup kabul etmek için buraya tıklayın.';
$string['noscriptula'] = '(Javascript etkinleştirilmediğinden, Turnitin Kullanıcı Sözleşmesi&#39;ni kabul ettikten sonra bir ödev gönderimi yapmadan önce, sayfayı yenilemelisiniz)';
$string['filedoesnotexist'] = 'Dosya silindi';
$string['reportgenspeed_resubmission'] = 'Bu ödev için zaten bir yazılı ödev gönderdiniz ve gönderiniz için bir Benzerlik Raporu oluşturuldu. Yazılı ödevinizi yeniden göndermek isterseniz önceki gönderiniz değiştirilecek ve yeni bir rapor oluşturulacaktır. {$a->num_resubmissions} yeniden gönderimden sonra yeniden gönderimin ardından yeni bir Benzerlik Raporu görebilmek için {$a->num_hours} saat beklemeniz gerekecektir.';

// Plugin settings.
$string['config'] = 'Yapılandırma';
$string['defaults'] = 'Varsayılan Ayarlar';
$string['showusage'] = 'Veri Atıklarını Göster';
$string['saveusage'] = 'Veri Atıklarını Kaydet';
$string['errors'] = 'Hatalar';
$string['turnitinconfig'] = 'Turnitin İntihal Eklenti Yapılandırması';
$string['tiiexplain'] = 'Turnitin ticari bir üründür ve bu servisi kullanmak için ücretli üyeliğe ihtiyacınız vardır, daha fazla bilgi için bkz. <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Turnitin&#39;i Etkinleştir';
$string['useturnitin_mod'] = 'Şunun için Turnitin&#39;i Etkinleştir {$a}';
$string['turnitindefaults'] = 'Turnitin, intihal eklentisi varsayılan ayarları';
$string['defaultsdesc'] = 'Etkinlik Modülünde Turnitin etkinleştirilirken aşağıdaki ayarlar, varsayılan ayarlar olarak belirlendi';
$string['turnitinpluginsettings'] = 'Turnitin, intihal eklentisi ayarları';
$string['pperrorsdesc'] = 'Aşağıdaki dosyaları Turnitin&#39;e yüklerken hata oluştu. Yeniden göndermek için yeniden göndermek istediğiniz dosyaları seçin ve yeniden gönder düğmesine tıklayın. Cron bir daha çalıştırıldığında bu dosyalar işlenecektir.';
$string['pperrorssuccess'] = 'Seçtiğiniz dosyalar yeniden gönderildi ve cron tarafından işlenecektir.';
$string['pperrorsfail'] = 'Seçtiğiniz dosyaların bazılarında hata oluştu. Bu dosyalar için yeni cron etkinliği oluşturulamadı.';
$string['resubmitselected'] = 'Seçilen Dosyaları Yeniden Yolla';
$string['deleteconfirm'] = 'Bu gönderiyi silmek istediğinize emin misiniz?\n\nBu eylem geri alınamaz.';
$string['deletesubmission'] = 'Gönderiyi Sil';
$string['semptytable'] = 'Hiçbir sonuç bulunamadı.';
$string['configupdated'] = 'Yapılandırma güncellendi';
$string['defaultupdated'] = 'Turnitin varsayılan ayarları güncellendi';
$string['notavailableyet'] = 'Erişilebilir değil';
$string['resubmittoturnitin'] = 'Turnitin&#39;e yeniden gönder';
$string['resubmitting'] = 'Yeniden gönderme';
$string['id'] = 'Kimlik';
$string['student'] = 'Öğrenci';
$string['course'] = 'Kurs';
$string['module'] = 'Modül';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Orijinallik Raporunu Görüntüle';
$string['launchrubricview'] = 'İşaretleme için kullanılmış performans değerlendirme ölçeklerini görüntüle';
$string['turnitinppulapost'] = 'Dosyanız Turnitin&#39;e gönderilmedi. Kullanıcı Lisans Sözleşmesi&#39;ni kabul etmek için lütfen buraya tıklayın.';
$string['ppsubmissionerrorseelogs'] = 'Bu dosya Turnitin&#39;e gönderilmedi, lütfen sistem yöneticinize danışın';
$string['ppsubmissionerrorstudent'] = 'Bu dosya Turnitin&#39;e gönderilmedi, lütfen daha ayrıntılı bilgi için öğretmeninize danışın';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin İntihal Eklentisi Dijital Makbuz bildirimleri';
$string['digitalreceipt'] = 'Dijital Makbuz';
$string['digital_receipt_subject'] = 'Bu, sizin Turnitin Dijital Makbuzunuzdur';
$string['pp_digital_receipt_message'] = 'Sayın {$a->firstname} {$a->lastname},<br /><br /><strong>{$a->submission_title}</strong> dosyasını <strong>{$a->course_fullname}</strong> sınıfındaki <strong>{$a->assignment_name}{$a->assignment_part}</strong> ödevine <strong>{$a->submission_date}</strong> tarihinde başarıyla gönderdiniz. Gönderi numaranız <strong>{$a->submission_id}</strong>. Dijital makbuzunuz Doküman Görüntüleyicideki yazdır/indir düğmesinden görüntülenebilir ve yazdırılabilir.<br /><br />Turnitin&#39;i kullandığınız için teşekkür ederiz,<br /><br />Turnitin Ekibi';

// Paper statuses.
$string['turnitinid'] = 'Turnitin Numarası';
$string['turnitinstatus'] = 'Turnitin durumu';
$string['pending'] = 'Bekliyor';
$string['similarity'] = 'Benzerlik';
$string['notorcapable'] = 'Bu dosya için bir Özgünlük Raporunun oluşturulması mümkün değil.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Öğrencinin ödevi görüntülediği tarih:';
$string['student_notread'] = 'Öğrenci henüz bu ödevi görüntülemedi.';
$string['launchpeermarkreviews'] = 'Peermark Değerlendirmelerini Yayınla';

// Cron.
$string['ppqueuesize'] = 'İntihal Eklentisi etkinlik sırasındaki etkinlik sayısı';
$string['ppcronsubmissionlimitreached'] = 'Bir çalıştırmada yalnızca {$a} işlendiği için Turnitin&#39;e daha fazla gönderim yapılmayacak';
$string['cronsubmittedsuccessfully'] = 'Gönderi: {$a->coursename} kursundaki {$a->assignmentname} ödevi için {$a->title} (TII Numarası: {$a->submissionid}) başarıyla Turnitin&#39;e gönderilmiştir.';
$string['pp_submission_error'] = 'Turnitin gönderiminizle ilgili bir hata oluştu:';
$string['turnitindeletionerror'] = 'Turnitin gönderisi silinirken bir hata oluştu. Yerel Moodle kopyası kaldırıldı ancak Turnitin&#39; deki gönderi silinemedi.';
$string['ppeventsfailedconnection'] = 'Turnitin ile bağlantı kurulamadığından Turnitin intihal eklentisi tarafından bir etkinlik işleme alınmayacak.';

// Error codes.
$string['tii_submission_failure'] = 'Daha ayrıntılı bilgi için lütfen öğretmeninize veya sistem yöneticinize danışın';
$string['faultcode'] = 'Hata Kodu';
$string['line'] = 'Satır';
$string['message'] = 'Mesaj';
$string['code'] = 'Kod';
$string['tiisubmissionsgeterror'] = 'Turnitin&#39;de bu ödev için yapılan gönderiler alınırken bir sorun oluştu';
$string['errorcode0'] = 'Bu dosya Turnitin&#39;e gönderilmedi, lütfen sistem yöneticinize danışın';
$string['errorcode1'] = 'Orijinallik Raporu oluşturmak için yeterli içerik olmadığından bu dosya Turnitin&#39;e gönderilmedi.';
$string['errorcode2'] = 'İzin verilen {$a->maxfilesize} maksimum boyutu aştığı için bu dosya Turnitin&#39;e gönderilmeyecek';
$string['errorcode3'] = 'Kullanıcı, Turnitin Son Kullanıcı Lisans Sözleşmesini kabul etmediğinden bu dosya Turnitin&#39;e gönderilmemiştir.';
$string['errorcode4'] = 'Bu ödev için desteklenen bir dosya türü yüklemeniz gerekmektedir. Kabul edilen dosya türleri: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps ve .rtf';
$string['errorcode5'] = 'Bu dosya Turnitin&#39;e gönderilmedi çünkü Turnitin&#39;de modül oluştururken gönderileri etkileyen bir hata meydana geldi. Daha fazla bilgi için lütfen API kayıtlarınıza bakın';
$string['errorcode6'] = 'Bu dosya Turnitin&#39;e gönderilmedi çünkü Turnitin&#39;de modül ayarlarını düzenlerken gönderileri etkileyen bir hata meydana geldi. Daha fazla bilgi için lütfen API kayıtlarınıza bakın';
$string['errorcode7'] = 'Bu dosya Turnitin&#39;e gönderilmedi çünkü Turnitin&#39;de kullanıcı oluştururken gönderileri etkileyen bir hata meydana geldi. Daha fazla bilgi için lütfen API kayıtlarınıza bakın';
$string['errorcode8'] = 'Bu dosya Turnitin&#39;e gönderilemedi çünkü temp dosyasını oluştururken bir hata meydana geldi. Muhtemel nedeni geçersiz dosya adıdır. Lütfen dosyayı yeniden adlandırın ve Gönderiyi Düzenle seçeneğini kullanarak yeniden yükleyin.';
$string['errorcode9'] = 'Dosya havuzunda gönderilecek erişilebilir bir içerik olmadığından dosya gönderilemiyor.';
$string['coursegeterror'] = 'Kurs verileri alınamadı';
$string['configureerror'] = 'Bu modülü, herhangi bir kursta kullanmaya başlamadan önce yönetici olarak yapılandırmalısınız. Lütfen Moodle yöneticinizle iletişime geçin.';
$string['turnitintoolofflineerror'] = 'Geçici bir sorun yaşıyoruz. Lütfen kısa bir süre sonra tekrar deneyin.';
$string['defaultinserterror'] = 'Veritabanına varsayılan ayar değeri girilirken bir hata oluştu';
$string['defaultupdateerror'] = 'Veritabanında varsayılan ayar değerleri güncellenirken bir hata oluştu';
$string['tiiassignmentgeterror'] = 'Turnitin&#39;den ödev alınırken bir sorun oluştu';
$string['assigngeterror'] = 'Turnitin verileri alınamadı';
$string['classupdateerror'] = 'Turnitin Sınıf verileri güncellenemedi';
$string['pp_createsubmissionerror'] = 'Turnitin&#39;de bir gönderi oluşturulurken sorun oluştu';
$string['pp_updatesubmissionerror'] = 'Turnitin&#39;e ödevinizi yeniden gönderirken bir sorun oluştu';
$string['tiisubmissiongeterror'] = 'Turnitin&#39;den bir gönderi alınırken sorun oluştu';

// Javascript.
$string['closebutton'] = 'Kapat';
$string['loadingdv'] = 'Turnitin Doküman Görüntüleyici Yükleniyor...';
$string['changerubricwarning'] = 'Performans değerlendirme ölçeğini değiştirmeniz veya kaldırmanız bu ödevdeki, daha önce işaretlenen skor kartları dahil, tüm performans değerlendirme ölçeği skorlarını kaldırılacaktır. Puanlanmış önceki ödevlerin toplam puanı bekleyecektir.';
$string['messageprovider:submission'] = 'Turnitin İntihal Eklentisi Dijital Makbuz bildirimleri';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin durumu';
$string['deleted'] = 'Silindi';
$string['pending'] = 'Bekliyor';
$string['because'] = 'Bunun nedeni bir yöneticinin bekleyen ödevi işlem kuyruğundan silmesi ve Turnitin&#39;e gönderiyi iptal etmesidir.<br /><strong>Dosya Moodle&#39;da kalacaktır, lütfen eğitmeninizle iletişime geçin.</strong><br />Lütfen hata kodları için aşağıya bakın:';
$string['submitpapersto_help'] = '<strong>Havuz Yok: </strong><br />Turnitin\'e, gönderilen belgeleri herhangi bir havuzda depolamama talimatı verilir. Yazılı ödevi yalnızca ilk benzerlik kontrolünü gerçekleştirmek üzere işleyeceğiz.<br /><br /><strong>Standart Havuz: </strong><br />Turnitin, gönderilen belgenin bir kopyasını yalnızca Standart Havuzda depolayacaktır. Bu seçeneğin belirlenmesi Turnitin\'e, depolanan belgeleri yalnızca ileride gönderilecek herhangi bir belgeyle karşılaştırarak benzerlik kontrolleri gerçekleştirmek için kullanma talimatı verir.<br /><br /><strong>Kurum Havuzu (Uygulanabilir Yer): </strong><br />Bu seçeneğin belirlenmesi Turnitin\'e yalnızca gönderilen belgeleri kurumunuza özel bir havuza ekleme talimatı verir. Gönderilen belgeler üzerindeki benzerlik kontrolleri, kurumunuzdaki diğer eğitmenler tarafından gerçekleştirilecektir.';
$string['errorcode12'] = 'Bu dosya, dersin silindiği bir ödeve ait olduğundan Turnitin\'e gönderilmedi. Satır Kimliği: ({$a->id}) | Ders Modülü Kimliği: ({$a->cm}) | Kullanıcı Kimliği: ({$a->userid})';
$string['errorcode15'] = 'Bu dosya, ait olduğu etkinlik modülü bulunamadığı için, Turnitin\'e gönderilemedi';
$string['tiiaccountconfig'] = 'Turnitin Hesap Yapılandırması';
$string['turnitinaccountid'] = 'Turnitin Hesap Numarası';
$string['turnitinsecretkey'] = 'Turnitin Ortak Anahtarı';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = 'Hata Ayıklama ve Günlük Oluşturma';
$string['turnitindiagnostic'] = 'Teşhis Modunu Etkinleştir';
$string['turnitindiagnostic_desc'] = '<b>[Dikkat]</b><br />Teşhis modunu sadece Turnitin API ile ilgili problemleri ortaya çıkarmak için etkinleştirin.';
$string['tiiaccountsettings_desc'] = 'Lütfen bu ayarların Turnitin hesabınızda yapılandırdığınız ayarlarla aynı olduğundan emin olun, aksi taktirde ödev oluştururken ve/veya öğrenci gönderimlerinde sorun yaşayabilirsiniz.';
$string['tiiaccountsettings'] = 'Turnitin Hesap Ayarları';
$string['turnitinusegrademark'] = 'GradeMark&#39;ı Kullan';
$string['turnitinusegrademark_desc'] = 'Gönderileri puanlamak için GradeMark&#39;ı kullanıp kullanmayacağınızı seçin.<br /><i>(Bu özelliği yalnızca hesaplarında GradeMark&#39;ı yapılandırmış olanlar kullanabilir)</i>';
$string['turnitinenablepeermark'] = 'Peermark Ödevlerini Etkinleştir';
$string['turnitinenablepeermark_desc'] = 'Peermark Ödevlerinin oluşturulmasına izin verilip verilmeyeceğini seçin<br/><i>(Bu özelliği yalnızca hesaplarına PeerMark&#39;ı yapılandırmış olanlar kullanabilir)</i>';
$string['transmatch_desc'] = 'Ödev kurulum ekranında Çeviri Eşleştirmenin bir ayar olarak gösterilip gösterilmeyeceğini belirler.<br /><i>(Bu seçeneği Turnitin hesabınızda Çeviri Eşleştirme etkin ise etkinleştiriniz)</i>';
$string['repositoryoptions_0'] = 'Standart eğitmen havuz seçeneklerini etkinleştir';
$string['repositoryoptions_1'] = 'Genişletilmiş eğitmen havuz seçeneklerini etkinleştir';
$string['repositoryoptions_2'] = 'Tüm yazılı ödevleri standart havuza gönder';
$string['repositoryoptions_3'] = 'Havuza hiçbir yazılı ödev göndermeyin';
$string['turnitinrepositoryoptions'] = 'Yazılı Ödev Havuzları';
$string['turnitinrepositoryoptions_desc'] = 'Turnitin ödevleri için havuz ayarlarını seçin.<br /><i>(Kurumsal Havuz, yalnızca bu seçeneği hesaplarında etkinleştiren kullanıcılar tarafından kullanılabilir)</i>';
$string['tiimiscsettings'] = 'Diğer Eklenti Ayarları';
$string['pp_agreement_default'] = 'Bu kutuyu işaretleyerek, bu gönderinin kendi çalışmam olduğunu onaylıyor ve bu gönderi sonrasında ortaya çıkabilecek telif hakkı ihlalinin sorumluluğunu kabul ediyorum.';
$string['pp_agreement_desc'] = '<b>[İsteğe Bağlı]</b><br />Bu gönderi için bir sözleşme onayı ifadesi girin.<br />(<b>Not:</b> Sözleşme boş bırakıldıysa, öğrenci ödev gönderimi sırasında sözleşme onayı gerekmeyecektir)';
$string['pp_agreement'] = 'Feragatname / Sözleşme';
$string['studentdataprivacy'] = 'Öğrenci Veri Gizlilik Ayarları';
$string['studentdataprivacy_desc'] = 'Aşağıdaki ayarlar, öğrenci kişisel verilerinin API aracılığıyla Turnitin&#39;e aktarılmadığından emin olmak için yapılandırılabilir.';
$string['enablepseudo'] = 'Öğrenci Gizliliğini Etkinleştir';
$string['enablepseudo_desc'] = 'Bu seçenek seçildiğinde, öğrenci e-posta adresi sahte bir Turnitin API arama e-posta adresine dönüştürülecektir.<br /><i>(<b>Not:</b> Bu seçenek, Turnitin ile eşitlenmiş herhangi bir Moodle kullanıcı verisi mevcutsa değiştirilemez)</i>';
$string['pseudofirstname'] = 'Sahte Öğrenci Adı';
$string['pseudofirstname_desc'] = '<b>[İsteğe Bağlı]</b><br />Turnitin doküman görüntüleyicide görüntülenecek öğrenci adı';
$string['pseudolastname'] = 'Sahte Öğrenci Soyadı';
$string['pseudolastname_desc'] = 'Turnitin doküman görüntüleyicide görüntülenecek öğrenci soyadı';
$string['pseudolastnamegen'] = 'Otomatik Soyadı Oluştur';
$string['pseudolastnamegen_desc'] = 'Evet olarak ayarlanmışsa ve sahte soyadı kullanıcı profil alanına ayarlanmışsa, bu alan otomatik olarak özel bir tanımlayıcıyla doldurulacaktır.';
$string['pseudoemailsalt'] = 'Sahte Kripto Verisi';
$string['pseudoemailsalt_desc'] = '<b>[İsteğe Bağlı]</b><br />Oluşturulan Sahte Öğrenci e-posta adresinin karmaşıklığını arttırmak için bir şifre kodlaması.<br />(<b>Not:</b> Kodlama sahte e-posta adresinin tutarlılığı için değiştirilmeden bırakılmalıdır.)';
$string['pseudoemaildomain'] = 'Sahte E-posta Alanı';
$string['pseudoemaildomain_desc'] = '<b>[İsteğe Bağlı]</b><br />Sahte e-posta adresleri için isteğe bağlı alan adı. (Boş bırakılırsa varsayılan: @tiimoodle.com)';
$string['pseudoemailaddress'] = 'Sahte E-posta Adresi';
$string['connecttest'] = 'Turnitin Bağlantısını Test Et';
$string['connecttestsuccess'] = 'Moodle başarılı bir biçimde Turnitin&#39;e bağlandı.';
$string['diagnosticoptions_0'] = 'Kapalı';
$string['diagnosticoptions_1'] = 'Standart';
$string['diagnosticoptions_2'] = 'Hata Ayıklama';
$string['repositoryoptions_4'] = 'Tüm yazılı ödevleri kurum havuzuna gönder';
$string['turnitinrepositoryoptions_help'] = '<strong>Standart eğitmen havuz seçeneklerini etkinleştir: </strong><br />Eğitmenler Turnitin\'e, belgeleri standart havuza veya kurumun özel havuzuna ekleme ya da hiçbir havuza eklememe talimatı verebilirler.<br /><br /><strong>Genişletilmiş eğitmen havuz seçeneklerini etkinleştir: </strong><br />Bu seçenek, öğrencilerin Turnitin\'e belgelerinin nerede depolanacağı konusunda talimat verebilmelerini sağlamak için eğitmenlerin bir ödev ayarını görüntüleyebilmelerini sağlar. Öğrenciler belgelerini standart öğrenci havuzuna veya kurumunuzun özel havuzuna eklemeyi tercih edebilirler.<br /><br /><strong>Tüm yazılı ödevleri standart havuza gönder: </strong><br />Tüm belgeler varsayılan olarak standart öğrenci havuzuna eklenecektir.<br /><br /><strong>Havuza hiçbir yazılı ödev göndermeyin: </strong><br />Belgeler yalnızca Turnitin\'de ilk kontrolü gerçekleştirmek ve puanlama için eğitmene gösterilmek üzere kullanılacaktır.<br /><br /><strong>Tüm yazılı ödevleri kurum havuzuna gönder: </strong><br />Turnitin\'e, tüm yazılı ödevleri kurum yazılı ödev havuzunda depolama talimatı verilir. Gönderilen belgeler üzerindeki benzerlik kontrolleri, yalnızca kurumunuzdaki diğer eğitmenler tarafından gerçekleştirilecektir.';
$string['turnitinuseanon'] = 'Anonim İşaretleme Kullan';
$string['createassignmenterror'] = 'Turnitin&#39;de bir ödev oluşturulurken sorun oluştu';
$string['editassignmenterror'] = 'Turnitin&#39;de bir ödevle ilgili düzenleme yapılırken bir sorun oluştu';
$string['ppassignmentediterror'] = 'Modül {$a->title} (TII Numarası: {$a->assignmentid}) Turnitin&#39;de düzenlenemedi, daha ayrıntılı bilgi için lütfen API kayıtlarınıza bakın';
$string['pp_classcreationerror'] = 'Bu sınıf Turnitin&#39;de oluşturulamadı, daha ayrıntılı bilgi için lütfen API kayıtlarınıza bakın';
$string['unlinkusers'] = 'Kullanıcıların Bağlantısını Kaldır';
$string['relinkusers'] = 'Kullanıcıları yeniden bağlantılandır';
$string['unlinkrelinkusers'] = 'Kullanıcıların Bağlantısını Kaldır/Yeniden Bağlantılandır';
$string['nointegration'] = 'Bütünleştirme yok';
$string['sprevious'] = 'Önceki';
$string['snext'] = 'Sonraki';
$string['slengthmenu'] = 'MENU_ Girişlerini_Göster';
$string['ssearch'] = 'Arama:';
$string['sprocessing'] = 'Veriler Turnitin&#39;den yükleniyor...';
$string['szerorecords'] = 'Gösterilecek kayıt yok.';
$string['sinfo'] = '_START_ /_END_ of _TOTAL_ giriş gösteriliyor.';
$string['userupdateerror'] = 'Kullanıcı verileri güncellenemedi';
$string['connecttestcommerror'] = 'Turnitin&#39; e bağlanılamadı. API URL ayarlarını tekrar kontrol edin.';
$string['userfinderror'] = 'Kullanıcı Turnitin&#39;de aranırken bir sorun oluştu';
$string['tiiusergeterror'] = 'Turnitin&#39;den kullanıcı detayları alınırken bir sorun oluştu';
$string['usercreationerror'] = 'Turnitin kullanıcı oluşturma işlemi başarısız oldu';
$string['ppassignmentcreateerror'] = 'Bu modül Turnitin&#39;de oluşturulamadı, daha ayrıntılı bilgi için lütfen API kayıtlarınıza bakın';
$string['excludebiblio_help'] = 'Bu ayar eğitmene, öğrenci ödevindeki bibliyografya, alıntı ve referans bölümündeki metni, Orijinallik Raporu hazırlanırken, çıkarabilme imkanı sağlar. Bu ayar, istenen Orijinallik Raporunda iptal edilebilir.';
$string['excludequoted_help'] = 'Bu ayar eğitmene, alıntı bölümündeki metni, Orijinallik Raporu hazırlanırken, çıkarabilme imkanı sağlar. Bu ayar, istenen Orijinallik Raporunda iptal edilebilir.';
$string['excludevalue_help'] = 'Bu ayar, eğitmenin, Orijinallik Raporu oluşturulurken, (eğitmen tarafından belirlenen) uzunluğa uymayan eşleşmeleri dışarıda bırakmasına imkan tanır. Bu ayar, bireysel Orijinallik Raporlarında iptal edilebilir.';
$string['spapercheck_help'] = 'Ödevlerin Orijinallik Raporları oluşturulurken, Turnitin öğrenci ödevleri havuzundaki ödevlerle karşılaştırma yapılır. Bu seçilmediği takdirde, benzerlik endeksi yüzdesi azalabilir.';
$string['internetcheck_help'] = 'Ödevler için Orijinallik Raporu oluşturulurken, Turnitin internet havuzundaki materyaller ile karşılaştırma yapılır. Bu seçilmediğinde, benzerlik endeksi yüzdesi azalabilir.';
$string['journalcheck_help'] = 'Ödevler için Orijinallik Raporu oluşturulurken, Turnitin dergileri, süreli yayınları ve yayın havuzundaki materyaller ile karşılaştırma yapılır. Bu seçilmediğinde, benzerlik endeksi yüzdesi azalabilir.';
$string['reportgenspeed_help'] = 'Bu ödev ayarı için üç seçenek vardır: &#39;Raporları anında oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).&#39;, &#39;Raporları anında oluşturun. Gönderiler, depoya anında eklenecektir (depo ayarlanmışsa).&#39; ve &#39;Raporları teslim tarihinde oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).&#39;<br /><br />&#39;Raporları anında oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).&#39; seçeneği bir öğrenci bir gönderide bulunduğunda derhal bir Orijinallik Raporu oluşturur. Bu seçenek etkinleştirildiğinde öğrencileriniz ödevlerini yeniden gönderemezler.<br /><br />Yeniden gönderilere izin vermek için &#39;Raporları anında oluşturun. Gönderiler, depoya anında eklenecektir (depo ayarlanmışsa).&#39; seçimini yapın. Bu ayar, öğrencilerin teslim tarihine kadar yazılı ödevlerini istedikleri kadar yeniden göndermelerine izin verir. Yeniden gönderiler için Orijinallik Raporlarının işlenme süresi 24 saate kadar çıkabilir.<br /><br />&#39;Raporları teslim tarihinde oluşturun. Gönderiler, depoya teslim tarihinde eklenecektir (depo ayarlanmışsa).&#39; seçeneği yalnızca ödevin teslim tarihinde bir Orijinallik Raporu oluşturur. Bu ayar, ödev için gönderilen tüm yazılı ödevlerin, Orijinallik Raporları oluşturulduğunda birbirleriyle karşılaştırmalarını sağlar.';
$string['turnitinuseanon_desc'] = 'Gönderiler puanlanırken Anonim İşaretlemeye izin verilip verilmeyeceğini seçin.<br /><i>(Bu özelliği yalnızca hesaplarında Anonim İşaretlemeyi yapılandırmış olanlar kullanabilir)</i>';
