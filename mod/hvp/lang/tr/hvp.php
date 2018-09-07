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

$string['modulename'] = 'Etkileşimsel İçerik';
$string['modulename_help'] = 'H5P etkinlik modülü Etkileşimsel Videolar, Soru Setleri, Sürükle ve Bırak Sorular, Çoktan Seçmeli Sorular, Sunumlar ve daha bir çoğu etkileşimsel içerik türünü oluşturmanızı sağlar.

Bir zengin içerik oluşturma gereci olmasının yanında, H5P içeriğin yeniden kullanımı ve paylaşılması için H5P dosyalarının içeri ve dışarı aktarılmasına da olanak sağlar.

Kullanıcının etkileşim ve skorları xAPI kullanılarak saklanır ve Moodle Puan Defterine kaydedilir.

Etkileşimsel H5P içerik bir .h5p dosya yüklenerek eklenebilir. .h5p dosyalarını h5p.org üzerinde oluşturup indirebilirsiniz';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'Etkileşimsel İçerik';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'Giriş';
$string['h5pfile'] = 'H5P Dosyası';
$string['fullscreen'] = 'Tam Ekran';
$string['disablefullscreen'] = 'Tam ekranı kapat';
$string['download'] = 'İndir';
$string['copyright'] = 'Kullanım hakları';
$string['embed'] = 'Kat';
$string['showadvanced'] = 'İleri düzeyi göster';
$string['hideadvanced'] = 'İleri düzeyi gizle';
$string['resizescript'] = 'Katılan içeriğin dinamik boyutlandırılmasını istiyorsanız web sitenize bu dizgeyi dahil edin:';
$string['size'] = 'Boyut';
$string['close'] = 'Kapat';
$string['title'] = 'Başlık';
$string['author'] = 'Yazan';
$string['year'] = 'Yıl';
$string['source'] = 'Kaynak';
$string['license'] = 'Lisans';
$string['thumbnail'] = 'Küçük resim';
$string['nocopyright'] = 'Bu içerik için telif hakkı bilgisi yok.';
$string['downloadtitle'] = 'Bu içeriği bir H5P dosyası olarak indir.';
$string['copyrighttitle'] = 'Bu içeriğin telif hakkı bilgisine bak.';
$string['embedtitle'] = 'Bu içeriğin katma koduna bak.';
$string['h5ptitle'] = 'Daha fazla içerik için H5P.org adresine gidin.';
$string['contentchanged'] = 'Son kullandığınızdan bu yana bu içerik değişti.';
$string['startingover'] = "Yeniden başlayacaksınız.";
$string['confirmdialogheader'] = 'Eylemi onayla';
$string['confirmdialogbody'] = 'Devam etmeyi istediğinizi onaylayın. Bu eylem geri alınamaz.';
$string['cancellabel'] = 'İptal';
$string['confirmlabel'] = 'Onayla';
$string['noh5ps'] = 'Bu kurs içn etkileşimsel içerik yok.';

$string['lookforupdates'] = 'H5P güncellemelerine bak';
$string['removetmpfiles'] = 'Eski H5P geçici dosyalarını kaldır';
$string['removeoldlogentries'] = 'Eski H5P kütük girdilerini kaldır';

// Admin settings.
$string['displayoptionnevershow'] = 'Never show';
$string['displayoptionalwaysshow'] = 'Always show';
$string['displayoptionpermissions'] = 'Show only if user has permissions to export H5P';
$string['displayoptionpermissionsembed'] = 'Show only if user has permissions to embed H5P';
$string['displayoptionauthoron'] = 'Controlled by author, default is on';
$string['displayoptionauthoroff'] = 'Controlled by author, default is off';
$string['displayoptions'] = 'Seçenekleri Göster';
$string['enableframe'] = 'Eylem çubuğunu ve çerçevesini göster';
$string['enabledownload'] = 'İndirme tuşu';
$string['enableembed'] = 'Katma tuşu';
$string['enablecopyright'] = 'Telif hakkı tuşu';
$string['enableabout'] = 'H5P bilgisi tuşu';

$string['sendusagestatistics'] = 'Contribute usage statistics';
$string['sendusagestatistics_help'] = 'Usage statistics numbers will automatically be reported to help the developers better understand how H5P is used and to determine potential areas of improvement. Read more about which <a {$a}>data is collected on h5p.org</a>.';
$string['enablesavecontentstate'] = 'İçerik durumunu kaydet';
$string['enablesavecontentstate_help'] = 'Her bir kullanıcı için mevcut etkileşimsel çerik durumunu kendiliğinden kaydet. Böylece kullanıcı bıraktığı yerden devam edebilir.';
$string['contentstatefrequency'] = 'İçerik durumunu kaydetme sıklığı';
$string['contentstatefrequency_help'] = 'Saniye değeriyle, kullanıcının ilerlemesi ne sıklıkla kendiliğinden kaydedilsin. Ajax istemleriyle sorun yaşıyorsanız bu sayıyı artırın.';

// Admin menu.
$string['settings'] = 'H5P Ayarları';
$string['libraries'] = 'H5P Kitaplıkları';

// Upload libraries section.
$string['uploadlibraries'] = 'Kitaplıkları Yükle';
$string['options'] = 'Seçenekler';
$string['onlyupdate'] = 'Yalnızca mevcut kitaplıkları güncelle';
$string['disablefileextensioncheck'] = 'Dosya uzantısı denetimini devreden çıkar';
$string['disablefileextensioncheckwarning'] = "Dikkat! Dosya uzantısı denetimini devreden çıkarmak, php uzantılı dosyaların da yüklenmesine olanak vereceği için, güvenlik sorunu oluşturabilir. Bu tür dosyalar sitenize zararlı kodların yüklenmesini sağlayabilir. Ne yükleneceğinden emin olmadıkça bu seçeneği kullanmayın.";
$string['upload'] = 'Yükle';

// Installed libraries section.
$string['installedlibraries'] = 'Kurulu Kitaplıklar';
$string['invalidtoken'] = 'Güvenlik bilgisi geçersiz.';
$string['missingparameters'] = 'Parametreler eksik';
$string['nocontenttype'] = 'No content type was specified.';
$string['invalidcontenttype'] = 'The chosen content type is invalid.';
$string['installdenied'] = 'You do not have permission to install content types. Contact the administrator of your site.';
$string['downloadfailed'] = 'Downloading the requested library failed.';
$string['validationfailed'] = 'The requested H5P was not valid';
$string['validatingh5pfailed'] = 'Validating h5p package failed.';

// H5P library list headers on admin page.
$string['librarylisttitle'] = 'Başlık';
$string['librarylistrestricted'] = 'Kısıtlı';
$string['librarylistinstances'] = 'Oluşumlar';
$string['librarylistinstancedependencies'] = 'Oluşum bağımlılıkları';
$string['librarylistlibrarydependencies'] = 'Kitaplık bağımlılıkları';
$string['librarylistactions'] = 'Eylemler';

// H5P library page labels.
$string['addlibraries'] = 'Kitaplık ekle';
$string['installedlibraries'] = 'Kurulu kitaplıklar';
$string['notapplicable'] = 'Yok';
$string['upgradelibrarycontent'] = 'Kitaplık içeriğini yükselt';

// Upgrade H5P content page.
$string['upgrade'] = 'H5P yazılımını yükselt';
$string['upgradeheading'] = '{$a} içeriğini yükselt';
$string['upgradenoavailableupgrades'] = 'Bu kitaplık için mevcut yükseltme yok.';
$string['enablejavascript'] = 'JavaScript devreye sokulmalı.';
$string['upgrademessage'] = '{$a} içerik oluşumu yükseltilecek. Yükseltme sürümünü seçin.';
$string['upgradeinprogress'] = '%ver sürümüne yükseltiliyor ...';
$string['upgradeerror'] = 'Parametreler işlenirken bir sorun oluştu:';
$string['upgradeerrordata'] = '%lib kitaplığı için veriler yüklenemedi.';
$string['upgradeerrorscript'] = '%lib kitaplığı için yükseltme dizgesi yüklenemedi.';
$string['upgradeerrorcontent'] = '%id içeriği yükseltilemedi:';
$string['upgradeerrorparamsbroken'] = 'Parametreler bozuk.';
$string['upgradedone'] = '{$a} içerik oluşumu başarıyla yükseltildi.';
$string['upgradereturn'] = 'Geri dön';
$string['upgradenothingtodo'] = "Yükseltilecek içerik oluşumu yok.";
$string['upgradebuttonlabel'] = 'Yükselt';
$string['upgradeinvalidtoken'] = 'Hata: Güvenlik bilgisi geçersiz!';
$string['upgradelibrarymissing'] = 'Hata: Kitaplığınız yok!';

// Results / report page.
$string['user'] = 'Kullanıcı';
$string['score'] = 'Skor';
$string['maxscore'] = 'En yüksek skor';
$string['finished'] = 'Bitti';
$string['loadingdata'] = 'Veri yükleniyor.';
$string['ajaxfailed'] = 'Veri yüklenemedi.';
$string['nodata'] = "Ölçütünüze uyan veri yok.";
$string['currentpage'] = 'Sayfa $current / $total';
$string['nextpage'] = 'Sonraki sayfa';
$string['previouspage'] = 'Önceki sayfa';
$string['search'] = 'Ara';
$string['empty'] = 'Sonuç yok';
$string['viewreportlabel'] = 'Report';
$string['dataviewreportlabel'] = 'View Answers';
$string['invalidxapiresult'] = 'No xAPI results were found for the given content and user id combination';
$string['reportnotsupported'] = 'Not supported';
$string['reportingscorelabel'] = 'Score:';
$string['reportingscaledscorelabel'] = 'Gradebook score:';
$string['reportingscoredelimiter'] = 'out of';
$string['reportingscaledscoredelimiter'] = ',';
$string['reportingquestionsremaininglabel'] = 'questions remaining to grade';
$string['reportsubmitgradelabel'] = 'Submit grade';
$string['noanswersubmitted'] = 'This user hasn\'t submitted an answer to the H5P yet';

// Editor.
$string['javascriptloading'] = 'JavaScript bekleniyor ...';
$string['action'] = 'Eylem';
$string['upload'] = 'Yükle';
$string['create'] = 'Oluştur';
$string['editor'] = 'Editör';

$string['invalidlibrary'] = 'Kitaplık geçersz';
$string['nosuchlibrary'] = 'Böyle bir kitaplık yok';
$string['noparameters'] = 'Parametre yok';
$string['invalidparameters'] = 'Parametreler geçersiz';
$string['missingcontentuserdata'] = 'Hata: İçerik kullanıcısı verisi bulunamadı';

$string['maximumgrade'] = 'Maximum grade';
$string['maximumgradeerror'] = 'Please enter a valid positive integer as the max points available for this activity';

// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'Yeni bir H5P Etkinliği ekle';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'Kurs içindeki H5P içeriğinden dışa aktarma dosyası al';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'H5P içeriği için sonucu kaydet';
$string['hvp:savecontentuserdata'] = 'H5P içerik kullanıcısı verisini kaydet';
$string['hvp:viewresults'] = 'H5P içeriği için sonucu gör';
$string['hvp:viewallresults'] = 'View result for all users in course';
$string['hvp:restrictlibraries'] = 'Bir H5P kitaplığını kısıtla';
$string['hvp:userestrictedlibraries'] = 'Kısıtlı H5P kitaplıkları kullan';
$string['hvp:updatelibraries'] = 'Bir H5P kitaplığı sürümünü yükle';
$string['hvp:getcachedassets'] = 'Ön belleğe alınmış H5P içerik değerlerini al';
$string['hvp:installrecommendedh5plibraries'] = 'Install recommended H5P libraries';

// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'Kitaplıkları yükseltme yetkiniz yok.';
$string['nopermissiontorestrict'] = 'Kitaplıkları kısıtlama yetkiniz yok.';
$string['nopermissiontosavecontentuserdata'] = 'İçerik kullanıcısı verilerini kaydetme yetkiniz yok.';
$string['nopermissiontosaveresult'] = 'Bu içerik için sonucu kaydetme yetkiniz yok.';
$string['nopermissiontoviewresult'] = 'Bu içerik için sonuçları görme yetkiniz yokY.';

// Editor translations.
$string['noziparchive'] = 'PHP sürümünüz ZipArchive desteklemiyor.';
$string['noextension'] = 'Yüklediğiniz dosya geçerli bir HTML5 Paketi değil (Dosya uzantısı .h5p değil.)';
$string['nounzip'] = 'Yüklediğiniz dosya geçerli bir HTML5 Paketi değil (Dosya açılamadı)';
$string['noparse'] = 'Ana h5p.json dosyası işlenemedi';
$string['nojson'] = 'Ana h5p.json dosyası geçersiz';
$string['invalidcontentfolder'] = 'İçerik klasörü geçersiz';
$string['nocontent'] = 'content.json dosyası bulunamadı ya da işlenemedi';
$string['librarydirectoryerror'] = 'Kitaplık dizin adı machineName ya da machineName-majorVersion.minorVersion (library.json gereksinimi) ile uyuşmalı. (Dizin: {$a->%directoryName} , machineName: {$a->%machineName}, majorVersion: {$a->%majorVersion}, minorVersion: {$a->%minorVersion})';
$string['missingcontentfolder'] = 'Geçerli bir içerik klasörü eksik';
$string['invalidmainjson'] = 'Geçerli bir ana h5p.json dosyası yok';
$string['missinglibrary'] = 'Gereken kitaplık eksik {$a->@library}';
$string['missinguploadpermissions'] = "Yüklediğiniz dosyada kitaplıklar olabilir ama yeni kitaplık yükleme izniniz yok. Bu konuda site yönetimiyle iletişime geçin.";
$string['invalidlibraryname'] = 'Kitaplık adı geçersiz: {$a->%name}';
$string['missinglibraryjson'] = 'Geçerli json formatında library.json dosyası bu kitaplık {$a->%name} için bulunamadı';
$string['invalidsemanticsjson'] = 'semantics.json dosyası kitaplık {$a->%name} içìn geçersiz';
$string['invalidlanguagefile'] = 'Dil dosyası {$a->%file} kitaplık {$a->%library} için geçersiz';
$string['invalidlanguagefile2'] = '{$a->%name} kitaplığında geçersiz dil dosyası {$a->%languageFile}';
$string['missinglibraryfile'] = '"{$a->%file}" dosyası bu kitaplıkta yok: "{$a->%name}"';
$string['missingcoreversion'] = 'Sistem bu paketten <em>{$a->%component}</em> içeriğini yükleyemedi; H5P eklentisinin daha üst bir sürümü gerekiyor. Bu sitede şu an kullanılan sürüm {$a->%current}; gereken sürüm ise en az {$a->%required}. Yükseltip yeniden deneyebilirsiniz.';
$string['invalidlibrarydataboolean'] = '{$a->%library} kitaplığında {$a->%property} için geçersiz veri. Boolean olmalı.';
$string['invalidlibrarydata'] = '{$a->%library} kitaplığında {$a->%property} için geçersiz veri sağlandı';
$string['invalidlibraryproperty'] = '{$a->%library} kitaplığında {$a->%property} okunamıyor';
$string['missinglibraryproperty'] = '{$a->%library} kitaplığında gerekli {$a->%property} özelliği yok';
$string['invalidlibraryoption'] = '{$a->%library} kitaplığında geçersiz seçenek {$a->%option}';
$string['addedandupdatedss'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old one.';
$string['addedandupdatedsp'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old ones.';
$string['addedandupdatedps'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old one.';
$string['addedandupdatedpp'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old ones.';
$string['addednewlibrary'] = 'Added {$a->%new} new H5P library.';
$string['addednewlibraries'] = 'Added {$a->%new} new H5P libraries.';
$string['updatedlibrary'] = 'Updated {$a->%old} H5P library.';
$string['updatedlibraries'] = 'Updated {$a->%old} H5P libraries.';
$string['missingdependency'] = '{$a->@lib} için gereken bağımlılık {$a->@dep} yok.';
$string['invalidstring'] = 'Semantikteki regexp değerine göre sağlanan dizge geçersiz. (value: \"{$a->%value}\", regexp: \"{$a->%regexp}\")';
$string['invalidfile'] = 'Dosya "{$a->%filename}" için izin yok. İzin verilen dosya uzantıları: {$a->%files-allowed}.';
$string['invalidmultiselectoption'] = 'Birden fazla seçenekte geçersiz seçili unsur.';
$string['invalidselectoption'] = 'Seçimde geçersiz seçili unsur.';
$string['invalidsemanticstype'] = 'H5P dahili hatası: semantikte bilinmeyen içerik türü "{$a->@type}". İçerik kaldırılıyor!';
$string['invalidsemantics'] = 'Semantiğe göre, içerikte kullanılan kitaplık geçerli bir kitaplık değil';
$string['unabletocreatedir'] = 'Dizin oluşturulamadı.';
$string['unabletogetfieldtype'] = 'Alan türü alınamadı.';
$string['filetypenotallowed'] = 'Dosya türüne izin yok.';
$string['invalidfieldtype'] = 'Alan türü geçersiz.';
$string['invalidimageformat'] = 'Resim dosyası türü geçersiz.jpg, png ya da gif kullanın.';
$string['filenotimage'] = 'Bu bir resim dosyası değil.';
$string['invalidaudioformat'] = 'Ses dosyası türü geçersiz. mp3 ya da wav kullanın.';
$string['invalidvideoformat'] = 'Video dosyası türü geçersiz. mp4 ya da webm kullanın.';
$string['couldnotsave'] = 'Dosya kaydedilemedi.';
$string['couldnotcopy'] = 'Dosya kopyalanamadı.';
$string['librarynotselected'] = 'You must select a content type.';

// Welcome messages.
$string['welcomeheader'] = 'H5P dünyasına hoşgeldiniz!';
$string['welcomegettingstarted'] = 'H5P ve Moodle kullanımına bakmak için <a {$a->moodle_tutorial}>kullanım</a> turumuza bakabilir ve h5p.org üzerinde <a {$a->example_content}>örnek içerik</a> le bir fikir edinebilirsiniz.';
$string['welcomecommunity'] = 'Umarız H5P kullanmaktan memnun kalır ve sürekli büyüyen topluluğumuza<a {$a->forums}>forumlarımız</a>.';
$string['welcomecontactus'] = 'Herhangi bir geribildiriminiz varsa<a {$a}>bize iletin</a>. Geribildirimleri titizlikle ele alıyor ve her geçen gün H5P yazılımını geliştirmeye çabalıyoruz!';
$string['invalidlibrarynamed'] = 'The H5P library {$a->%library} used in the content is not valid';

// Licensing.
$string['copyrightinfo'] = 'Telif hakkı bilgisi';
$string['years'] = 'Yıl';
$string['undisclosed'] = 'Belirtilmedi';
$string['attribution'] = 'Attribution 4.0';
$string['attributionsa'] = 'Attribution-ShareAlike 4.0';
$string['attributionnd'] = 'Attribution-NoDerivs 4.0';
$string['attributionnc'] = 'Attribution-NonCommercial 4.0';
$string['attributionncsa'] = 'Attribution-NonCommercial-ShareAlike 4.0';
$string['attributionncnd'] = 'Attribution-NonCommercial-NoDerivs 4.0';
$string['gpl'] = 'General Public License v3';
$string['pd'] = 'Public Domain';
$string['pddl'] = 'Public Domain Dedication and Licence';
$string['pdm'] = 'Public Domain Mark';
$string['copyrightstring'] = 'Telif hakkı';
$string['by'] = 'by';
$string['showmore'] = 'Show more';
$string['showless'] = 'Show less';
$string['sublevel'] = 'Sublevel';
$string['noversionattribution'] = 'Attribution';
$string['noversionattributionsa'] = 'Attribution-ShareAlike';
$string['noversionattributionnd'] = 'Attribution-NoDerivs';
$string['noversionattributionnc'] = 'Attribution-NonCommercial';
$string['noversionattributionncsa'] = 'Attribution-NonCommercial-ShareAlike';
$string['noversionattributionncnd'] = 'Attribution-NonCommercial-NoDerivs';
$string['licenseCC40'] = '4.0 International';
$string['licenseCC30'] = '3.0 Unported';
$string['licenseCC25'] = '2.5 Generic';
$string['licenseCC20'] = '2.0 Generic';
$string['licenseCC10'] = '1.0 Generic';
$string['licenseGPL'] = 'General Public License';
$string['licenseV3'] = 'Version 3';
$string['licenseV2'] = 'Version 2';
$string['licenseV1'] = 'Version 1';
$string['licenseCC010'] = 'CC0 1.0 Universal (CC0 1.0) Public Domain Dedication';
$string['licenseCC010U'] = 'CC0 1.0 Universal';
$string['licenseversion'] = 'License Version';

// Embed.
$string['embedloginfailed'] = 'You do not have access to this content. Try logging in.';
