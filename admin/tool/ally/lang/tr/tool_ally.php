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

$string['adminurl'] = 'Başlatma URL\'si';
$string['adminurldesc'] = 'Erişilebilirlik raporuna erişmek için kullanılan LTI başlatma URL\'si.';
$string['allyclientconfig'] = 'Ally yapılandırması';
$string['ally:clientconfig'] = 'İstemci yapılandırmasına eriş ve güncelleştir';
$string['ally:viewlogs'] = 'Ally günlükleri görüntüleyicisi';
$string['clientid'] = 'İstemci kimliği';
$string['clientiddesc'] = 'Ally istemci kimliği';
$string['code'] = 'Kod';
$string['contentauthors'] = 'İçerik yazarları';
$string['contentauthorsdesc'] = 'Bu seçili rollere atanmış yöneticilerin ve kullanıcıların karşıya yüklenmiş kurs dosyalarının erişilebilir olup olmadığı değerlendirilir. Dosyalara bir erişilebilirlik puanı verilir. Düşük puanlar, daha erişilebilir olması için dosyada değişiklik yapılması gerektiği anlamına gelir.';
$string['contentupdatestask'] = 'İçerik güncelleştirme görevi';
$string['curlerror'] = 'cURL hatası: {$a}';
$string['curlinvalidhttpcode'] = 'Geçersiz HTTP durum kodu: {$a}';
$string['curlnohttpcode'] = 'HTTP durum kodu doğrulanamıyor';
$string['error:invalidcomponentident'] = 'Geçersiz bileşen tanımlayıcısı {$a}';
$string['error:pluginfilequestiononly'] = 'Bu url için yalnızca soru bileşenleri desteklenir';
$string['error:componentcontentnotfound'] = '{$a} için içerik bulunamadı';
$string['error:wstokenmissing'] = 'Web hizmet belirteci yok. Bir yönetici kullanıcının otomatik yapılandırmayı çalıştırması gerekebilir.';
$string['excludeunused'] = 'Kullanılmayan dosyaları hariç tut';
$string['excludeunuseddesc'] = 'HTML\'deki bağlantılı dosyalar/referans dosyaları haricinde HTML içeriğine eklenmiş olan dosyaları yok sayın.';
$string['filecoursenotfound'] = 'Aktarılan dosya herhangi bir kursa ait değil';
$string['fileupdatestask'] = 'Dosya güncelleştirmelerini Ally\'a aktar';
$string['id'] = 'Kimlik';
$string['key'] = 'Anahtar';
$string['keydesc'] = 'LTI tüketici anahtarı.';
$string['level'] = 'Seviye';
$string['message'] = 'İleti';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'Dosya güncelleştirmeleri URL\'si';
$string['pushurldesc'] = 'Bu URL ile ilgili dosya güncelleştirmelerine yönelik anında bildirimler.';
$string['queuesendmessagesfailure'] = 'AWS SQS\'e iletiler gönderilirken bir hata oluştu. Hata verisi: $a';
$string['secret'] = 'Parola';
$string['secretdesc'] = 'LTI parolası.';
$string['showdata'] = 'Verileri göster';
$string['hidedata'] = 'Verileri gizle';
$string['showexplanation'] = 'Açıklamayı göster';
$string['hideexplanation'] = 'Açıklamayı gizle';
$string['showexception'] = 'İstisnayı göster';
$string['hideexception'] = 'İstisnayı gizle';
$string['usercapabilitymissing'] = 'Sağlanan kullanıcının bu dosyayı silme izni yok.';
$string['autoconfigure'] = 'Ally web hizmetini otomatik yapılandır';
$string['autoconfiguredesc'] = 'Ally için web hizmeti rolünü ve kullanıcıyı otomatik olarak oluşturun.';
$string['autoconfigureconfirmation'] = 'Ally için web hizmeti rolünü ve kullanıcıyı otomatik olarak oluşturup web hizmetlerini etkinleştirin. Aşağıdaki işlemler gerçekleştirilecektir:<ul><li>"ally_webservice" isimli bir rol ve "ally_webuser" kullanıcı adına sahip bir kullanıcı oluşturma</li><li>"ally_webuser" adlı kullanıcıyı "ally_webservice" rolüne ekleme</li><li>web hizmetlerini etkinleştirme</li><li>rest web hizmeti protokollerini etkinleştirme</li><li>ally web hizmetini etkinleştirme</li><li>\'ally_webuser\' hesabı için bir belirteç oluşturma</li></ul>';
$string['autoconfigsuccess'] = 'Başarılı - Ally web hizmeti otomatik olarak yapılandırıldı.';
$string['autoconfigtoken'] = 'Web hizmeti belirteci aşağıdaki gibidir:';
$string['autoconfigapicall'] = 'Web hizmetinin çalışıp çalışmadığını anlamak üzere test etmek için şu url\'yi kullanabilirsiniz:';
$string['privacy:metadata:files:action'] = 'Dosyada yapılan işlem, örneğin: Oluşturuldu, güncelleştirildi veya silindi.';
$string['privacy:metadata:files:contenthash'] = 'Benzersiz olup olmadığını belirlemek için dosyanın içerik karması.';
$string['privacy:metadata:files:courseid'] = 'Dosyanın ait olduğu kurs kimliği.';
$string['privacy:metadata:files:externalpurpose'] = 'Ally ile entegrasyon için Ally ile dosya alışverişi yapılmalıdır.';
$string['privacy:metadata:files:filecontents'] = 'Asıl dosyanın içeriği erişilebilir olup olmadığının değerlendirilmesi için Ally\'a gönderildi.';
$string['privacy:metadata:files:mimetype'] = 'Dosya MIME türü, örneğin: Metin/düz, resim/jpeg, vb.';
$string['privacy:metadata:files:pathnamehash'] = 'Dosyayı benzersiz şekilde tanımlayan yol adı karması.';
$string['privacy:metadata:files:timemodified'] = 'Alanın son değiştirildiği zaman.';
$string['cachedef_annotationmaps'] = 'Kurslar için ek açıklama verilerini sakla';
$string['cachedef_fileinusecache'] = 'Dosyaları kullanılan önbellekte birleştir';
$string['cachedef_pluginfilesinhtml'] = 'Dosyaları HTML önbelleğinde birleştir';
$string['cachedef_request'] = 'Ally filtre isteği önbelleği';
$string['pushfilessummary'] = 'Ally dosya güncelleştirme özeti.';
$string['pushfilessummary:explanation'] = 'Ally\'a gönderilen dosya güncelleştirmelerinin özeti.';
$string['section'] = 'Bölüm {$a}';
$string['lessonanswertitle'] = '"{$a}" dersi için yanıt';
$string['lessonresponsetitle'] = '"{$a}" dersi için yanıt';
$string['logs'] = 'Ally günlükleri';
$string['logrange'] = 'Günlük aralığı';
$string['loglevel:none'] = 'Yok';
$string['loglevel:light'] = 'Hafif';
$string['loglevel:medium'] = 'Orta';
$string['loglevel:all'] = 'Tümü';
$string['logcleanuptask'] = 'Ally günlüğü temizleme görevi';
$string['loglifetimedays'] = 'Günlükleri şu kadar gün boyunca sakla';
$string['loglifetimedaysdesc'] = 'Ally günlüklerini şu kadar gün boyunca sakla. 0 ila günlükleri asla silme arasında bir değere ayarlayın. Zamanlanmış bir görev (varsayılan olarak) günlük olarak çalışacak şekilde ayarlanır ve belirtilen günden daha eski günlük girdilerini kaldırır.';
$string['logger:filtersetupdebugger'] = 'Ally filtre kurulumu günlüğü';
$string['logger:pushtoallysuccess'] = 'Ally uç noktasına başarılı aktarma';
$string['logger:pushtoallyfail'] = 'Ally uç noktasına başarısız aktarma';
$string['logger:pushfilesuccess'] = 'Ally uç noktasına başarılı dosya aktarma';
$string['logger:pushfileliveskip'] = 'Canlı dosya aktarma hatası';
$string['logger:pushfileliveskip_exp'] = 'İletişim sorunlarından dolayı canlı dosya aktarma işlemi atlandı. Dosya güncelleştirmeleri görevi başarılı olduğunda canlı dosya aktarımı geri yüklenecek. Lütfen yapılandırmanızı inceleyin.';
$string['logger:pushfileserror'] = 'Ally uç noktasına başarısız aktarma';
$string['logger:pushfileserror_exp'] = 'Ally hizmetlerine içerik güncelleştirmelerinin aktarılması ile ilgili hatalar.';
$string['logger:pushcontentsuccess'] = 'Ally uç noktasına başarılı içerik aktarma';
$string['logger:pushcontentliveskip'] = 'Canlı içerik aktarma hatası';
$string['logger:pushcontentliveskip_exp'] = 'İletişim sorunlarından dolayı canlı içerik aktarma işlemi atlandı. İçerik güncelleştirmeleri görevi başarılı olduğunda canlı içerik aktarımı geri yüklenecek. Lütfen yapılandırmanızı inceleyin.';
$string['logger:pushcontentserror'] = 'Ally uç noktasına başarısız aktarma';
$string['logger:pushcontentserror_exp'] = 'Ally hizmetlerine içerik güncelleştirmelerinin aktarılması ile ilgili hatalar.';
$string['logger:addingconenttoqueue'] = 'Aktarma kuyruğuna içerik ekleme';
$string['logger:annotationmoderror'] = 'Ally modülü içerik ek açıklaması başarısız oldu.';
$string['logger:annotationmoderror_exp'] = 'Modül doğru şekilde tanımlanmamış.';
$string['logger:failedtogetcoursesectionname'] = 'Kurs bölümünün adı alınamadı';
$string['logger:moduleidresolutionfailure'] = 'Modül kimliği çözümlenemedi';
$string['logger:cmidresolutionfailure'] = 'Kurs modülü kimliği çözümlenemedi';
$string['logger:cmvisibilityresolutionfailure'] = 'Kurs modülü görünürlüğü çözümlenemedi';
$string['courseupdatestask'] = 'Kurs olaylarını Ally\'a aktar';
$string['logger:pushcoursesuccess'] = 'Kurs olayları Ally uç noktasına başarıyla aktarıldı';
$string['logger:pushcourseliveskip'] = 'Canlı kurs olayı aktarma hatası';
$string['logger:pushcourseerror'] = 'Canlı kurs olayı aktarma hatası';
$string['logger:pushcourseliveskip_exp'] = 'İletişim sorunlarından dolayı canlı kurs olayı aktarma işlemi atlanıyor. Kurs olayı güncelleştirmeleri görevi başarılı olduğunda canlı kurs olayı aktarımı geri yüklenecek. Lütfen yapılandırmanızı inceleyin.';
$string['logger:pushcourseserror'] = 'Ally uç noktasına başarısız aktarma';
$string['logger:pushcourseserror_exp'] = 'Ally hizmetlerine kurs güncelleştirmeleri aktarımı ile ilgili hatalar.';
$string['logger:addingcourseevttoqueue'] = 'Aktarma kuyruğuna kurs olayı ekleme';
$string['logger:cmiderraticpremoddelete'] = 'Kurs modülü kimliğinde silme öncesi sorunlar var.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Modül doğru şekilde tanımlanmamış. Bölümün silinmesinden dolayı modül yok veya silme bağlantısını tetikleyen ve bulunamayan başka bir neden var.';
$string['logger:servicefailure'] = 'Hizmet kullanılırken bir hata oldu.';
$string['logger:servicefailure_exp'] = '<br>Sınıf: {$a->class}<br>Parametreler: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Bir öğretmen kök örnek yeteneği ally_webservice rolüne atanamadı.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Yetenek: {$a->cap}<br>İzin: {$a->permission}';
$string['deferredcourseevents'] = 'Ertelenen kurs etkinliklerini gönder';
$string['deferredcourseeventsdesc'] = 'Ally ile iletişim sorunları yaşandığında biriken ve depolanan kurs etkinliklerinin gönderilmesine izin verin';
