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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'عنوان URL للتشغيل';
$string['adminurldesc'] = 'عنوان URL للتشغيل الخاص بـ LTI المستخدم للوصول إلى تقرير "إمكانية وصول ذوي الاحتياجات الخاصة".';
$string['allyclientconfig'] = 'تكوين Ally';
$string['ally:clientconfig'] = 'الوصول وتحديث تكوين العميل';
$string['ally:viewlogs'] = 'عارض سجلات Ally';
$string['clientid'] = 'معرف العميل';
$string['clientiddesc'] = 'معرف عميل Ally';
$string['code'] = 'التعليمات البرمجية';
$string['contentauthors'] = 'مؤلفو المحتوى';
$string['contentauthorsdesc'] = 'سيقوم المسؤولون والمستخدمون المعينون لهذه الأدوار المحددة بتقييم ملفات المقرر الدراسي التي تم تنزيلها لإتاحة إمكانية وصول ذوي الاحتياجات الخاصة. ويتم إعطاء الملفات تصنيف بشأن إمكانية وصول ذوي الاحتياجات الخاصة. تعني التصنيفات المنخفضة احتياج الملف إلى إجراء تغييرات ليكون الوصول إليه أكثر سهولة.';
$string['contentupdatestask'] = 'مهمة تحديثات المحتوى';
$string['curlerror'] = 'خطأ cURL: {$a}';
$string['curlinvalidhttpcode'] = 'رمز حالة HTTP غير صالح: {$a}';
$string['curlnohttpcode'] = 'يتعذر التحقق من رمز حالة HTTP';
$string['error:invalidcomponentident'] = 'معرف المكون {$a} غير صالح';
$string['error:pluginfilequestiononly'] = 'يتم دعم مكونات السؤال فقط لعنوان URL هذا';
$string['error:componentcontentnotfound'] = 'لم يتم العثور على محتوى لـ {$a}';
$string['error:wstokenmissing'] = 'الرمز المميز لخدمة الويب مفقود. ربما يتعين على مستخدم مسؤول تشغيل التكوين التلقائي؟';
$string['excludeunused'] = 'استبعاد الملفات غير المستخدمة';
$string['excludeunuseddesc'] = 'حذف الملفات المرفقة بمحتوى HTML لكنها مرتبطة بـ/مُشار إليها في HTML.';
$string['filecoursenotfound'] = 'الملف الذي تم تمريره لا ينتمي إلى أي مقرر دراسي';
$string['fileupdatestask'] = 'دفع تحديثات الملف إلى Ally';
$string['id'] = 'المعرف';
$string['key'] = 'المفتاح';
$string['keydesc'] = 'مفتاح مستهلك LTI.';
$string['level'] = 'المستوى';
$string['message'] = 'الرسالة';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'عنوان URL الخاص بتحديثات الملف';
$string['pushurldesc'] = 'الإعلامات المؤقتة حول تحديثات الملف إلى عنوان URL هذا.';
$string['queuesendmessagesfailure'] = 'حدث خطأ أثناء إرسال الرسائل إلى AWS SQS. بيانات الخطأ: $a';
$string['secret'] = 'كلمة السر';
$string['secretdesc'] = 'كلمة سر LTI.';
$string['showdata'] = 'إظهار البيانات';
$string['hidedata'] = 'إخفاء البيانات';
$string['showexplanation'] = 'إظهار الشرح';
$string['hideexplanation'] = 'إخفاء الشرح';
$string['showexception'] = 'إظهار الاستثناء';
$string['hideexception'] = 'إخفاء الاستثناء';
$string['usercapabilitymissing'] = 'لا توجد لدى المستخدم المتقدم الإمكانية الخاصة بحذف هذا الملف.';
$string['autoconfigure'] = 'التكوين التلقائي لخدمة الويب Ally';
$string['autoconfiguredesc'] = 'قم بإنشاء دور خدمة الويب والمستخدم تلقائيًا لـ ally.';
$string['autoconfigureconfirmation'] = 'قم بإنشاء دور ومستخدم لخدمة الويب تلقائيًا من أجل ally. وسيتم اتخاذ الإجراءات الآتية:<ul dir="rtl"><li>إنشاء دور معنون بـ "ally_webservice" ومستخدم باسم المستخدم "ally_webuser"</li><li>إضافة مستخدم "ally_webuser" إلى الدور "ally_webservice"</li><li>تمكين خدمات الويب</li><li>تمكين بروتوكول خدمة الويب للاختبار</li><li>تمكين خدمة الويب Ally</li><li>إنشاء رمز مميز لحساب "ally_webuser"</li></ul>';
$string['autoconfigsuccess'] = 'نجاح - تم تكوين خدمة الويب Ally تلقائيًا.';
$string['autoconfigtoken'] = 'الرمز المميز لخدمة الويب كما يلي:';
$string['autoconfigapicall'] = 'يمكنك اختبار عمل خدمة الويب من خلال عنوان url التالي:';
$string['privacy:metadata:files:action'] = 'الإجراء المتخذ بشأن الملف، EG: تم إنشاؤه أو تحديثه أو حذفه.';
$string['privacy:metadata:files:contenthash'] = 'تجزئة محتوى الملف لتحديد التفرد.';
$string['privacy:metadata:files:courseid'] = '"معرف" المقرر الدراسي الذي ينتمي إليه الملف.';
$string['privacy:metadata:files:externalpurpose'] = 'للتكامل مع Ally، يتعين تبادل الملفات مع Ally.';
$string['privacy:metadata:files:filecontents'] = 'تم إرسال المحتوى الفعلي للملف إلى Ally لتقييمه بشأن إمكانية وصول ذوي الاحتياجات الخاصة إليه.';
$string['privacy:metadata:files:mimetype'] = 'نوع الملف MIME، EG: نص/عادي، صورة/jpeg، وما إلى ذلك.';
$string['privacy:metadata:files:pathnamehash'] = 'قم بتجزئة اسم مسار الملف لتعريفه بشكل فريد.';
$string['privacy:metadata:files:timemodified'] = 'الوقت الذي تم فيه التعديل الأخير للحقل.';
$string['cachedef_annotationmaps'] = 'تخزين بيانات التعليق التوضيحي للمقررات الدراسية';
$string['cachedef_fileinusecache'] = 'التخزين المؤقت لملفات Ally المستخدمة';
$string['cachedef_pluginfilesinhtml'] = 'التخزين المؤقت ملفات Ally بتنسيق HTML';
$string['cachedef_request'] = 'التخزين المؤقت لطلب عامل تصفية Ally';
$string['pushfilessummary'] = 'ملخص تحديثات ملف Ally.';
$string['pushfilessummary:explanation'] = 'ملخص تحديثات الملفات المرسلة إلى Ally.';
$string['section'] = 'القسم {$a}';
$string['lessonanswertitle'] = 'الإجابة عن الدرس "{$a}"';
$string['lessonresponsetitle'] = 'إجابة عن درس "{$a}"';
$string['logs'] = 'سجلات Ally';
$string['logrange'] = 'نطاق السجل';
$string['loglevel:none'] = 'بلا';
$string['loglevel:light'] = 'بسيط';
$string['loglevel:medium'] = 'متوسط';
$string['loglevel:all'] = 'الكل';
$string['logcleanuptask'] = 'مهمة تنظيف سجل Ally';
$string['loglifetimedays'] = 'احتفظ بالسجلات لهذه الأيام عديدة';
$string['loglifetimedaysdesc'] = 'احتفظ بسجلات Ally لهذه الأيام العديدة. قم بالتعيين إلى 0 لعدم حذف السجلات مطلقًا. ويتم تعيين مهمة مجدولة (افتراضيًا) للتشغيل يوميًا، وستحذف إدخالات السجل التي مضى عليها أكثر من هذه الأيام العديدة.';
$string['logger:filtersetupdebugger'] = 'سجل إعداد عامل تصفية Ally';
$string['logger:pushtoallysuccess'] = 'دفع ناجح إلى نقطة نهاية Ally';
$string['logger:pushtoallyfail'] = 'دفع غير ناجح إلى نقطة نهاية Ally';
$string['logger:pushfilesuccess'] = 'دفع ناجح للملف (الملفات) إلى نقطة نهاية Ally';
$string['logger:pushfileliveskip'] = 'فشل دفع الملف المباشر';
$string['logger:pushfileliveskip_exp'] = 'يتم الآن تخطي الملف (الملفات) المباشرة بسبب وجود مشكلات في الاتصال. ستتم استعادة دفع الملف المباشر عند نجاح مهمة تحديثات الملفات. يرجى مراجعة التكوين الخاص بك.';
$string['logger:pushfileserror'] = 'دفع غير ناجح إلى نقطة نهاية Ally';
$string['logger:pushfileserror_exp'] = 'دفع الأخطاء المرتبطة بتحديثات المحتوى إلى خدمات Ally.';
$string['logger:pushcontentsuccess'] = 'دفع ناجح من المحتوى إلى نقطة النهاية Ally';
$string['logger:pushcontentliveskip'] = 'فشل دفع المحتوى المباشر';
$string['logger:pushcontentliveskip_exp'] = 'يتم الآن تخطي دفع المحتوى المباشر بسبب وجود مشكلات في الاتصال. ستتم استعادة دفع المحتوى المباشر عند نجاح مهمة تحديثات المحتوى. يرجى مراجعة التكوين الخاص بك.';
$string['logger:pushcontentserror'] = 'دفع غير ناجح إلى نقطة نهاية Ally';
$string['logger:pushcontentserror_exp'] = 'دفع الأخطاء المرتبطة بتحديثات المحتوى إلى خدمات Ally.';
$string['logger:addingconenttoqueue'] = 'إضافة محتوى إلى قائمة انتظار الدفع';
$string['logger:annotationmoderror'] = 'فشل التعليق التوضيحي لمحتوى الوحدة النمطية Ally.';
$string['logger:annotationmoderror_exp'] = 'لم يتم تعريف الوحدة النمطية بشكل صحيح.';
$string['logger:failedtogetcoursesectionname'] = 'فشل في الحصول على اسم قسم المقرر الدراسي';
$string['logger:moduleidresolutionfailure'] = 'فشل في حل معرف الوحدة النمطية';
$string['logger:cmidresolutionfailure'] = 'فشل في حل معرف الوحدة النمطية للمقرر الدراسي';
$string['logger:cmvisibilityresolutionfailure'] = 'فشل في حل إمكانية الاطلاع على الوحدة النمطية للمقرر الدراسي';
$string['courseupdatestask'] = 'دفع أحداث المقرر الدراسي إلى Ally.';
$string['logger:pushcoursesuccess'] = 'دفع ناجح لحدث (أحداث) المقرر الدراسي إلى نقطة نهاية ally';
$string['logger:pushcourseliveskip'] = 'فشل دفع حدث المقرر الدراسي المباشر';
$string['logger:pushcourseerror'] = 'فشل دفع حدث المقرر الدراسي المباشر';
$string['logger:pushcourseliveskip_exp'] = 'يتم الآن تخطي حدث (أحداث) المقرر الدراسي المباشر بسبب وجود مشكلات في الاتصال. ستتم استعادة دفع حدث المقرر الدراسي المباشر عند نجاح مهمة تحديثات حدث المقرر الدراسي. يرجى مراجعة التكوين الخاص بك.';
$string['logger:pushcourseserror'] = 'دفع غير ناجح إلى نقطة نهاية Ally';
$string['logger:pushcourseserror_exp'] = 'دفع الأخطاء المرتبطة بتحديثات المقرر الدراسي إلى خدمات Ally.';
$string['logger:addingcourseevttoqueue'] = 'إضافة حدث المقرر الدراسي لقائمة انتظار الدفع';
$string['logger:cmiderraticpremoddelete'] = 'يواجه معرف الوحدة النمطية للمقرر الدراسي مشاكل قبل حذفه.';
$string['logger:cmiderraticpremoddelete_exp'] = 'لم يتم تحديد الوحدة النمطية بشكل صحيح، إما أنها غير موجودة بسبب حذف القسم أو أن هناك عاملًا آخر تسبب في تشغيل حذف موضع الإضافة ولم يتم العثور عليه.';
$string['logger:servicefailure'] = 'فشلت العملية عند استهلاك الخدمة.';
$string['logger:servicefailure_exp'] = '<br>الفئة: {‎$a->class}<br>المعلمات: {‎$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'فشلت العملية عند تعيين إمكانية النموذج الأولي للمدرس إلى دور خدمة ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>القدرة: {‎$a->cap}<br>الإذن: {‎$a->permission}';
$string['deferredcourseevents'] = 'إرسال أحداث المقرر الدراسي المؤجلة';
$string['deferredcourseeventsdesc'] = 'السماح بإرسال أحداث المقرر الدراسي المخزنة التي تراكمت أثناء فشل الاتصال بـ Ally';
