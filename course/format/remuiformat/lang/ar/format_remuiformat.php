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
 * Strings for component 'format_remuiformat'
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin Name.
$string['pluginname'] = 'تنسيقات الدورات Edwiser';
$string['plugin_description'] = 'تُعرض الدورات كقوائم قابلة للطي أو كبطاقات أقسام بتصميم متجاوب لتسهيل التنقل.';
// Settings.
$string['defaultcoursedisplay'] = 'عرض الدورة الافتراضي';
$string['defaultcoursedisplay_desc'] = "إما عرض جميع الأقسام في صفحة واحدة أو القسم صفر والقسم المختار في الصفحة.";

$string['defaultbuttoncolour'] = 'لون زر عرض الموضوع الافتراضي';
$string['defaultbuttoncolour_desc'] = 'لون زر عرض الموضوع.';

$string['defaultoverlaycolour'] = 'لون التراكب الافتراضي عند تحريك المؤشر على الأنشطة';
$string['defaultoverlaycolour_desc'] = 'لون التراكب عند تحريك المؤشر على الأنشطة';

$string['enablepagination'] = 'تمكين الترقيم';
$string['enablepagination_desc'] = 'سيُمكّن هذا عرض الصفحات المتعددة عندما يكون عدد الأقسام/الأنشطة كبيرًا جدًا.';

$string['defaultnumberoftopics'] = 'عدد المواضيع الافتراضي لكل صفحة';
$string['defaultnumberoftopics_desc'] = 'عدد المواضيع المعروضة في صفحة واحدة';

$string['defaultnumberofactivities'] = 'عدد الأنشطة الافتراضي لكل صفحة';
$string['defaultnumberofactivities_desc'] = 'عدد الأنشطة المعروضة في صفحة واحدة';

$string['off'] = 'إيقاف';
$string['on'] = 'تشغيل';

$string['defaultshowsectiontitlesummary'] = 'عرض ملخص عنوان القسم عند التحريك';
$string['defaultshowsectiontitlesummary_desc'] = 'عرض ملخص عنوان القسم عند تحريك المؤشر فوق صندوق الشبكة.';
$string['sectiontitlesummarymaxlength'] = 'تحديد الحد الأقصى لطول ملخص القسم/الأنشطة.';
$string['sectiontitlesummarymaxlength_help'] = 'تحديد الحد الأقصى لطول ملخص عنوان القسم/الأنشطة المعروض على البطاقة.';
$string['defaultsectionsummarymaxlength'] = 'تحديد الحد الأقصى لطول ملخص القسم/الأنشطة.';
$string['defaultsectionsummarymaxlength_desc'] = 'تحديد الحد الأقصى لطول ملخص القسم/الأنشطة المعروض على البطاقة.';
$string['hidegeneralsectionwhenempty'] = 'إخفاء القسم العام عند فراغه';
$string['hidegeneralsectionwhenempty_help'] = 'عندما لا يحتوي القسم العام على أي نشاط أو ملخص، يمكنك إخفاؤه.';

// Section.
$string['sectionname'] = 'القسم';
$string['sectionnamecaps'] = 'القسم';
$string['section0name'] = 'المقدمة';
$string['hidefromothers'] = 'إخفاء القسم';
$string['showfromothers'] = 'عرض القسم';
$string['viewtopic'] = 'عرض';
$string['editsection'] = 'تحرير القسم';
$string['editsectionname'] = 'تحرير اسم القسم';
$string['newsectionname'] = 'الاسم الجديد للقسم {$a}';
$string['currentsection'] = 'هذا القسم';
$string['addnewsection'] = 'إضافة قسم';
$string['moveresource'] = 'نقل المورد';

// Activity.
$string['viewactivity'] = 'عرض النشاط';
$string['markcomplete'] = 'وضع علامة إتمام';
$string['grade'] = 'الدرجة';
$string['notattempted'] = 'لم يُحاول';
$string['subscribed'] = "مشترك";
$string['notsubscribed'] = "غير مشترك";
$string['completed'] = "مكتمل";
$string['notcompleted'] = 'غير مكتمل';
$string['progress'] = 'التقدم';
$string['showinrow'] = 'جعل صف';
$string['showincard'] = 'جعل بطاقة';
$string['moveto'] = 'نقل إلى';
$string['changelayoutnotify'] = 'قم بتحديث الصفحة لمشاهدة التغييرات.';
$string['generalactivities'] = 'الأنشطة';
$string['coursecompletionprogress'] = 'تقدم إتمام الدورة';
$string['resumetoactivity'] = 'استئناف';

// For list format.
$string['remuicourseformat'] = 'اختيار التخطيط';
$string['remuicourseformat_card'] = 'تخطيط البطاقة';
$string['remuicourseformat_list'] = 'تخطيط القائمة';
$string['remuicourseformat_help'] = 'اختيار تخطيط الدورة';
$string['remuicourseimage_filemanager'] = 'صورة رأس الدورة';
$string['remuicourseimage_filemanager_help'] = 'سيتم عرض هذه الصورة في بطاقة القسم العام في تخطيط البطاقة وكخلفية للقسم العام في تخطيط القائمة. <strong>حجم الصورة الموصى به 1272x288.<strong>';
$string['addsections'] = 'إضافة أقسام';
$string['teacher'] = 'المعلم';
$string['teachers'] = 'المعلمين';
$string['remuiteacherdisplay'] = 'عرض صورة المعلم';
$string['remuiteacherdisplay_help'] = 'عرض صورة المعلم في رأس الدورة.';
$string['defaultremuiteacherdisplay'] = 'عرض صورة المعلم';
$string['defaultremuiteacherdisplay_desc'] = 'عرض صورة المعلم في رأس الدورة.';

$string['remuidefaultsectionview'] = 'اختيار العرض الافتراضي للأقسام';
$string['remuidefaultsectionview_help'] = 'اختيار العرض الافتراضي لأقسام الدورة.';
$string['expanded'] = 'توسيع الكل';
$string['collapsed'] = 'طي الكل';

$string['remuienablecardbackgroundimg'] = 'صورة خلفية القسم';
$string['remuienablecardbackgroundimg_help'] = 'تمكين صورة خلفية القسم. بشكل افتراضي، تكون معطلة. يتم جلب الصورة من ملخص القسم.';
$string['enablecardbackgroundimg'] = 'عرض صورة الخلفية للقسم في البطاقة.';
$string['disablecardbackgroundimg'] = 'إخفاء صورة الخلفية للقسم في البطاقة.';
$string['next'] = 'التالي';
$string['previous'] = 'السابق';

$string['remuidefaultsectiontheme'] = 'اختيار السمة الافتراضية للأقسام';
$string['remuidefaultsectiontheme_help'] = 'اختيار السمة الافتراضية لأقسام الدورة.';

$string['dark'] = 'داكن';
$string['light'] = 'فاتح';

$string['defaultcardbackgroundcolor'] = 'تحديد لون خلفية القسم في تخطيط البطاقة.';
$string['cardbackgroundcolor_help'] = 'مساعدة لون خلفية البطاقة.';
$string['cardbackgroundcolor'] = 'تحديد لون خلفية القسم في تخطيط البطاقة.';
$string['defaultcardbackgroundcolordesc'] = 'وصف لون خلفية البطاقة';

// GDPR.
$string['privacy:metadata'] = 'لا يقوم ملحق تنسيقات الدورات Edwiser بتخزين أي بيانات شخصية.';

// Validation.
$string['coursedisplay_error'] = 'يرجى اختيار التوليفة الصحيحة من التخطيط.';

// Activities completed text.
$string['activitystart'] = "لنبدأ";
$string['outof'] = 'من';
$string['activitiescompleted'] = 'أنشطة مكتملة';
$string['activitycompleted'] = 'نشاط مكتمل';
$string['activitiesremaining'] = 'أنشطة متبقية';
$string['activityremaining'] = 'نشاط متبقي';
$string['allactivitiescompleted'] = "جميع الأنشطة مكتملة";

// Used in format.js on change course layout.
$string['showallsectionperpage'] = 'عرض جميع الأقسام لكل صفحة';

// Card format general section.
$string['showfullsummary'] = '+ عرض الملخص الكامل';
$string['showless'] = 'عرض أقل';
$string['showmore'] = 'عرض المزيد';
$string['Complete'] = 'مكتمل';

// Usage tracking.
$string['enableusagetracking'] = "تمكين تتبع الاستخدام";
$string['enableusagetrackingdesc'] = "<strong>إشعار تتبع الاستخدام</strong>

<hr class='text-muted' />

<p>سيقوم Edwiser من الآن بجمع البيانات المجهولة لتوليد إحصاءات استخدام المنتج.</p>

<p>ستساعدنا هذه المعلومات في توجيه التطوير في الاتجاه الصحيح وازدهار مجتمع Edwiser.</p>

<p>مع ذلك، لا نقوم بجمع بياناتك الشخصية أو بيانات طلابك أثناء هذه العملية. يمكنك تعطيل هذا من الملحق متى شئت الانسحاب من هذه الخدمة.</p>

<p>نظرة عامة على البيانات المجمعة متاحة <strong><a href='https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products' target='_blank'>هنا</a></strong>.</p>";

$string['edw_format_hd_bgpos'] = "موقع صورة خلفية رأس الدورة";
$string['bottom'] = "أسفل";
$string['center'] = "مركز";
$string['top'] = "أعلى";
$string['left'] = "يسار";
$string['right'] = "يمين";
$string["edw_format_hd_bgpos_help"] = "اختيار موقع صورة الخلفية";

$string['edw_format_hd_bgsize'] = "حجم صورة خلفية رأس الدورة";
$string['cover'] = "تغطية";
$string['contain'] = "احتواء";
$string['auto'] = "تلقائي";
$string['edw_format_hd_bgsize_help'] = "اختيار حجم صورة خلفية رأس الدورة";
$string['courseinformation'] = "معلومات الدورة";
$string["defaultheader"] = 'افتراضي';
$string["remuiheader"] = 'رأس';
$string["headereditingbutton"] = "اختيار موقع زر التحرير";
$string['headereditingbutton_help'] = "اختيار موقع زر التحرير. لن تعمل هذه الإعدادات في remui، تحقق من إعدادات الدورة";

$string['headeroverlayopacity'] = "تغيير شفافية تراكب الرأس";
$string['headeroverlayopacity_help'] = "القيمة الافتراضية مضبوطة بالفعل على '100'. لتعديل الشفافية، يرجى إدخال قيمة بين 0 و 100";
$string['viewalltext'] = 'عرض الكل';
