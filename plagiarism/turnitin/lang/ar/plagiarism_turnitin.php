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
$string['pluginname'] = 'المكون الإضافي الخاص بسرقة محتوى Turnitin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'وظيفة المكون الإضافي الخاص بسرقة محتوى Turnitin';
$string['connecttesterror'] = 'حدث خطأ أثناء الاتصال بـ Turnitin وهذه رسالة الخطأ:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'تمكين Turnitin';
$string['excludebiblio'] = 'استثناء المراجع';
$string['excludequoted'] = 'استثناء نصوص الاقتباسات';
$string['excludevalue'] = 'استبعاد التطابقات الصغيرة';
$string['excludewords'] = 'كلمات';
$string['excludepercent'] = 'النسبة المئوية';
$string['norubric'] = 'لا توجد معايير قياسية';
$string['otherrubric'] = 'إستخدام المعيار القياسي العائد لمدربين أخرين';
$string['attachrubric'] = 'إلحاق باب أجوبة قياسية لهذه المهمة';
$string['launchrubricmanager'] = 'أطلق معالج الأجوبة القياسية';
$string['attachrubricnote'] = 'ملاحظة: سيكون بمقدور الطلاب مشاهدة المعايير القياسية الملحقة و محتوياتها قبل القيام بالتسليم.';
$string['anonblindmarkingnote'] = 'ملحوظة: تم إزالة الإعداد المنفصل لوضع العلامات دون إظهار الأسماء بـ Turnitin. ستستخدم Turnitin إعداد وضع العلامات دون معرفة أسماء الطلاب التابع لـ Moodle لتحديد إعداد وضع العلامات دون إظهار الأسماء.';
$string['transmatch'] = 'تطابق الترجمة';
$string["reportgen_immediate_add_immediate"] = "إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع على الفور (إذا تم تعيين المستودع).";
$string["reportgen_immediate_add_duedate"] = "إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع).";
$string["reportgen_duedate_add_duedate"] = "إنشاء التقارير في تاريخ الاستحقاق. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع).";
$string['launchquickmarkmanager'] = 'إطلاق معالج Quickmark';
$string['launchpeermarkmanager'] = 'أطلق معالج Peermark';
$string['studentreports'] = 'عرض تقارير الاصالة للطلبة';
$string['studentreports_help'] = 'يسمح لك بعرض تقارير الاصالة للطلبة المستخدين. اذا ما تم تحديد نعم سيكون تقرير الاصالة متوفر للطلبة للعرض';
$string['submitondraft'] = 'أرسل الملف عند تحميل أول ملف.';
$string['submitonfinal'] = 'ارسل الملف عندما يقوم الطالب للتقييم';
$string['draftsubmit'] = 'متى يجب إرسال الملف إلى Turnitin؟';
$string['allownonor'] = 'السماح إرسال أي نوع من الملفات؟';
$string['allownonor_help'] = 'هذا الوضع يسمح بإرسال أي نوع من الملفات. مع تعيين هذا الخيار إلى &#34;نعم&#34;، سيتم فحص أصالة الإرسالات حيثما كان ذلك ممكنًا، وسوف تكون متاحة للتنزيل وستتوفر أدوات GradeMark للملاحظات حيثما كان ذلك ممكنًا.';
$string['norepository'] = 'لا توجد مستودعات';
$string['standardrepository'] = 'المستودع القياسي';
$string['submitpapersto'] = 'حفظ مستندات الطلبة';
$string['institutionalrepository'] = 'المستودعات المؤسسية (عند الاقتضاء)';
$string['checkagainstnote'] = 'ملحوظة: إذا لم تحدد "نعم" لخيار "التحقق مقابل..." واحد على الأقل الموجود أدناه، فلن يتم إنشاء تقرير الأصالة.';
$string['spapercheck'] = 'قحص مقابل مستندات الطلبة المخزونة';
$string['internetcheck'] = 'فحص مقابل الإنترنت';
$string['journalcheck'] = 'فحص مقابل المجلات <br />والدوريات والمنشورات';
$string['compareinstitution'] = 'مقارنة الملفات المسلمة مع المستندات المسلمة داخل هذه المؤسسة';
$string['reportgenspeed'] = 'تقرير سرعة التكوين';
$string['locked_message'] = 'رسالة الإعدادات المغلقة';
$string['locked_message_help'] = 'إذا كانت أي من الإعدادات مغلقة، تظهر هذه الرسالة للتعريف بالسبب.';
$string['locked_message_default'] = 'تم إغلاق هذا الإعداد على مستوى الموقع';
$string['sharedrubric'] = 'معايير قياسية مشتركة';
$string['turnitinrefreshsubmissions'] = 'تحديث الإرسالات';
$string['turnitinrefreshingsubmissions'] = 'جاري تحديث الإرسالات';
$string['turnitinppulapre'] = 'لإرسال ملف إلى Turnitin، يجب عليك أولاً قبول اتفاقية ترخيص المستخدم النهائي الخاصة بنا. سيؤدي اختيار عدم قبول اتفاقية ترخيص المستخدم النهائي الخاصة بنا إلى إرسال ملفك إلى Moodle فقط. يُرجى النقر هنا لقراءة الاتفاقية وقبولها.';
$string['noscriptula'] = '"(لأن Javascript غير مفعل لديك سيتوجب عليك تحديث هذه الصفحة يدويًا قبل أن تتمكن من الإرسال و بعد الموافقة على اتفاقية المستخدم لـ Turnitin)"';
$string['filedoesnotexist'] = 'تم حذف الملف';
$string['reportgenspeed_resubmission'] = 'لقد أرسلت بالفعل مستندًا لهذه المهمة وتم إنشاء تقرير تشابه للإرسال. إذا اخترت إعادة إرسال المستند، فإن الإرسال السابق سيتم استبداله وسيتم إنشاء تقرير جديد. بعد {$a->num_resubmissions} من عمليات إعادة الإرسال، سينبغي الانتظار لمدة {$a->num_hours} من الساعات بعد إعادة الإرسال للاطلاع على تقرير تشابه جديد.';

// Plugin settings.
$string['config'] = 'التكوين';
$string['defaults'] = 'الإعدادات الافتراضية';
$string['showusage'] = 'إظهار النسخة الاحتياطية للبيانات';
$string['saveusage'] = 'حفظ النسخة الاحتياطية للبيانات';
$string['errors'] = 'أخطاء';
$string['turnitinconfig'] = 'تكوين المكون الإضافي الخاص بسرقة محتوى Turnitin';
$string['tiiexplain'] = 'Turnitin هو منتج تجاري، ويجب أن يكون لديك اشتراك مدفوع لاستخدام هذه الخدمة، لمزيد من المعلومات راجع <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'تمكين Turnitin';
$string['useturnitin_mod'] = 'تمكين Turnitin لـ {$a}';
$string['turnitindefaults'] = 'الاعدادات الافتراضية لمكون الانتحال في Turnitin';
$string['defaultsdesc'] = 'الاعدادات التالية هي الاعدادات الإفتراضية التي تم تحديدها حين تم تفعيل Turnitinضمن نموذج فعالية';
$string['turnitinpluginsettings'] = 'الاعدادات لمكون الانتحال في Turnitin';
$string['pperrorsdesc'] = 'ظهرت مشكلة ما أثناء المحاولة رفع الملفات أدناه إلى Turnitin. لإعادة الإرسال، قم بتحديد الملفات التي ترغب في إعادة إرسالها واضغط على زر إعادة إرسال. سيتم معالجة هذه الملفات عند تشغيل كرون (cron) المرة القادمة.';
$string['pperrorssuccess'] = 'تم إعادة إرسال الملفات التي قمت بتحديدها وسيتم معالجتها من خلال كرون (cron).';
$string['pperrorsfail'] = 'ظهرت مشكلة ببعض الملفات التي قمت بتحديدها، ولم يمكن إنشاء حدث cron جديد.';
$string['resubmitselected'] = 'إعادة إرسال ملفات محددة';
$string['deleteconfirm'] = 'هل أنت متأكد من حذف هذا الإرسال؟\n\nلا يمكن الرجوع عن هذا الإجراء.';
$string['deletesubmission'] = 'حذف الإرسال';
$string['semptytable'] = 'لا توجد نتائج.';
$string['configupdated'] = 'تم تحديث التكوين';
$string['defaultupdated'] = 'تم تحديث الخيارات الافتراضية لـ Turnitin';
$string['notavailableyet'] = 'غير متوفر';
$string['resubmittoturnitin'] = 'إعادة الإرسال إلى Turnitin ';
$string['resubmitting'] = 'جاري إعادة الإرسال';
$string['id'] = 'معرف';
$string['student'] = 'طالب';
$string['course'] = 'الدرس';
$string['module'] = 'نموذج';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'عرض تقرير الاصالة';
$string['launchrubricview'] = 'مشاهدة المعيار القياسي المستخدم لتحديد العلامات';
$string['turnitinppulapost'] = 'لم يتم إرسال ملفك إلى Turnitin. الرجاء النقر هنا لقبول اتفاقية ترخيص المستخدم النهائي.';
$string['ppsubmissionerrorseelogs'] = 'لم يتم إرسال هذا الملف إلى Turnitin، يُرجى استشارة مسؤول المدرسة لديك.';
$string['ppsubmissionerrorstudent'] = 'لم يُرسل هذا الملف إلى Turnitin، يُرجى استشارة المدرس للحصول على مزيد من التفاصيل.';

// Receipts.
$string['messageprovider:submission'] = 'إشعارات الإيصال الرقمي للمكون الإضافي الخاص بسرقة محتوى Turnitin';
$string['digitalreceipt'] = 'إيصال رقمي';
$string['digital_receipt_subject'] = 'هذا هو الإيصال الرقمي الخاص بك';
$string['pp_digital_receipt_message'] = 'عزيزي {$a->firstname} {$a->lastname}،<br /><br />لقد أرسلت بنجاح ملف <strong>{$a->submission_title}</strong> إلى مهمة <strong>{$a->assignment_name}{$a->assignment_part}</strong> في الفصل الدراسي <strong>{$a->course_fullname}</strong> في <strong>{$a->submission_date}</strong>. معرف الإرسال هو <strong>{$a->submission_id}</strong>. يمكن رؤية الإيصال الرقمي الكامل الخاص بك وطباعته من زر الطباعة/التنزيل في عارض الوثائق.<br /><br />شكرًا لاستخدامك Turnitin،<br /><br />فريق Turnitin.';

// Paper statuses.
$string['turnitinid'] = 'معرف Turnitin ';
$string['turnitinstatus'] = 'حالة Turnitin ';
$string['pending'] = 'قيد الانتظار';
$string['similarity'] = 'التشابه';
$string['notorcapable'] = 'لا يمكن إنشاء تقرير أصالة لهذا الملف.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'قام الطالب بعرض هذا المستند في:';
$string['student_notread'] = 'لم يقم الطالب بعرض هذا المستند.';
$string['launchpeermarkreviews'] = 'ابدأ تشغيل مراجعات Peermark';

// Cron.
$string['ppqueuesize'] = 'عدد الأحداث في قائمة أحداث المكون الإضافي الخاص بسرقة المحتوى';
$string['ppcronsubmissionlimitreached'] = 'لن يتم إرسال إرسالات إضافية إلى Turnitin من خلال تنفيذ هذا الكرون، حيث يتم معالجة {$a} فقط لكل تشغيل.';
$string['cronsubmittedsuccessfully'] = 'إرسال: تم إرسال{$a->title} (معرف TII: {$a->submissionid}) للمهمة {$a->assignmentname} على الدرس {$a->coursename} بنجاح إلى Turnitin.';
$string['pp_submission_error'] = 'أعادت Turnitin خطأ يتعلق بإرسالك:';
$string['turnitindeletionerror'] = 'فشل في حذف ارسال Turnitin. تك حذف نسخة Moodle المحلية لكن الإرسال إلى Turnitin لم يمكن حذفه.';
$string['ppeventsfailedconnection'] = 'لن يتم معالجة أي أحداث بواسطة المكون الإضافي الخاص بسرقة محتويات Turnitin من خلال تنفيذ هذا الكرون، حيث يتعذر إنشاء اتصال بـ Turnitin.';

// Error codes.
$string['tii_submission_failure'] = 'يرجى التشاور مع معلمك أو مسؤول النظام للحصول على مزيد من التفاصيل.';
$string['faultcode'] = 'رمز الخطأ';
$string['line'] = 'خط';
$string['message'] = 'رسالة';
$string['code'] = 'رمز';
$string['tiisubmissionsgeterror'] = 'حدث خطأ عند محاولة الحصول على الإرسالات لهذه المهمة من Turnitin';
$string['errorcode0'] = 'لم يتم إرسال هذا الملف إلى Turnitin، يُرجى استشارة مسؤول المدرسة لديك.';
$string['errorcode1'] = 'لم يتم إرسال هذا الملف إلى Turnitin، حيث أنه لا يوجد به محتوى كاف لإنتاج تقرير الأصالة.';
$string['errorcode2'] = 'لن يتم إرسال هذا الملف إلى Turnitin، حيث أنه يتجاوز الحد الأقصى لحجم {$a->maxfilesize} المسموح به.';
$string['errorcode3'] = 'لم يُرسل هذا الملف إلى Turnitin لأن المستخدم لم يوافق على اتفاقية ترخيص المستخدم النهائي.';
$string['errorcode4'] = 'يجب عليك رفع ملفًا مدعومًا لهذه المهمة. أنواع الملفات المقبولة: doc وdocx وppt وpptx وpps وppsx وpdf وtxt وhtm وhtml وhwp وodt وwpd وps وrtf';
$string['errorcode5'] = 'لم يتم إرسال هذا الملف إلى Turnitin لوجود مشكلة في إنشاء الوحدة النمطية في Turnitin التي تمنع الإرسالات، الرجاء الرجوع إلى سجلات API للحصول على مزيد من المعلومات.';
$string['errorcode6'] = 'لم يتم إرسال هذا الملف إلى Turnitin لوجود مشكلة في تحرير إعدادات الوحدة النمطية في Turnitin التي تمنع الإرسالات، الرجاء الرجوع إلى سجلات API للحصول على مزيد من المعلومات.';
$string['errorcode7'] = 'لم يتم إرسال هذا الملف إلى Turnitin لوجود مشكلة في إنشاء المستخدم في Turnitin التي تمنع الإرسالات، الرجاء الرجوع إلى سجلات API للحصول على مزيد من المعلومات.';
$string['errorcode8'] = 'لم يتم إرسال هذا الملف إلى Turnitin لوجود مشكلة في إنشاء ملف temp. السبب الأكثر احتمالاً هو أن اسم الملف غير صالح. الرجاء إعادة تسمية الملف إعادة رفعه باستخدام تحرير الإرسال.';
$string['errorcode9'] = 'يتعذر إرسال الملف بسبب عدم وجود محتوى قابل للوصول في مجموعة الملفات ليتم إرسالها.';
$string['coursegeterror'] = 'لا يمكن الحصول على بيانات الدورة';
$string['configureerror'] = 'يجب تكوين هذه الوحدة بالكامل كمسؤول قبل استخدامها في الدورة، يرجى الاتصال بمسؤول Moodle.';
$string['turnitintoolofflineerror'] = 'نحن نواجه مشكلة في الوقت الحالي. يرجى المحاولة مجددًا بعد قليل.';
$string['defaultinserterror'] = 'حدث خطأ عند محاولة إدخال قيمة إعداد إفتراضي لقاعدة البيانات';
$string['defaultupdateerror'] = 'حدث خطأ عند محاولة تحديث قيمة إعداد إفتراضي في قاعدة البيانات';
$string['tiiassignmentgeterror'] = 'حدث خطأ عند محاولة الحصول على مهمة من Turnitin';
$string['assigngeterror'] = 'لا يمكن الحصول على بيانات أدوات Turnitin';
$string['classupdateerror'] = 'لا يمكن تحديث بيانات درس Turnitin';
$string['pp_createsubmissionerror'] = 'حدث خطأ عند محاولة إنشاء التسليم في Turnitin';
$string['pp_updatesubmissionerror'] = 'حدث خطأ عند محاولة إعادة تسليم ما قمت بتسليمه الى Turnitin';
$string['tiisubmissiongeterror'] = 'حدث خطأ عند محاولة خلق المهمة في Turnitin';

// Javascript.
$string['closebutton'] = 'إغلاق';
$string['loadingdv'] = 'جاري تحميل عارض وثائق Turnitin ...';
$string['changerubricwarning'] = 'سيؤدي تغيير أو إزالة أي معيار قياسي إلى إزالة جميع الدرجات القياسية الحالية من المستندات في هذه المهمة، التي تشتمل على بطاقات الدرجات التي تم تعليمها مسبقًا. ستظل الدرجات الإجمالية للمستندات المعلمة مسبقًا كما هي.';
$string['messageprovider:submission'] = 'إشعارات الإيصال الرقمي للمكون الإضافي الخاص بسرقة محتوى Turnitin ';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'حالة Turnitin';
$string['deleted'] = 'تم حذف';
$string['pending'] = 'قيد الانتظار';
$string['because'] = 'هذا بسبب قيام أحد المسؤولين بحذف مهمة معلقة من قائمة المعالجة وإيقاف الإرسال إلى Turnitin. <br /><strong>ما زال الملف موجودًا في Moodle، الرجاء الاتصال بمعلمك.</strong><br />الرجاء الاطلاع أدناه على أي رموز خطأ:';
$string['submitpapersto_help'] = '<strong>لا توجد مستودعات: </strong><br />تتم مطالبة Turnitin بعدم تخزين المستندات المرسلة في أي مستودع. وسوف تقتصر معالجتنا للورق على إجراء فحص أولي على التشابه.<br /><br /><strong>المستودع القياسي: </strong><br />سوف يخزن Turnitin نُسخة من المستندات المرسلة فقط في المستودع القياسي. وباختيار هذا الخيار، يتم توجيه Turnitin باستخدام المستندات المخزنة فقط لإجراء فحوصات تشابه على المستندات التي يتم إرسالها لاحقًا.<br /><br /><strong>المستودعات المؤسسية (عند الاقتضاء): </strong><br />باختيار هذا الخيار، يتم توجيه Turnitin إلى إضافة المستندات المرسلة إلى المستودع الخاص بالمؤسسة فقط. ويتم إجراء فحوصات التشابه على المستندات المرسلة بواسطة معلمين آخرين من مؤسستك.';
$string['errorcode12'] = 'لم يتم إرسال هذا الملف إلى Turnitin، لأنه ينتمي إلى مهمة تم حذف دورتها التدريبية. معرف السطر: ({$a->id}) | معرف وحدة الدورة: ({$a->cm}) | معرف المستخدم: ({$a->userid})';
$string['errorcode15'] = "لم يتم إرسال هذا الملف إلى Turnitin لأنه لم يتم العثور على وحدة النشاط التي ينتمي إليها";
$string['tiiaccountconfig'] = 'تكوين حساب Turnitin ';
$string['turnitinaccountid'] = 'معرف الحساب الرئيسي لTurnitin';
$string['turnitinsecretkey'] = 'مفتاح Turnitin المشترك';
$string['turnitinapiurl'] = 'Turnitin API-URL';
$string['tiidebugginglogs'] = 'تصحيح الأخطاء والتسجيل';
$string['turnitindiagnostic'] = 'تفعيل الوضع التشخيصي';
$string['turnitindiagnostic_desc'] = '<b>[تحذير]</b><br />قم بتمكين الوضع التشخيصي فقط لتتبع المشكلات من خلال Turnitin API.';
$string['tiiaccountsettings_desc'] = 'يرجى التأكد أن هذه الإعدادات تطابق الإعدادات المكونة في حساب turnitin الخاص بك، وإلا فقد تواجه مشكلات مع إنشاء المهام و/أو إرسالات الطلاب.';
$string['tiiaccountsettings'] = 'إعدادات حساب Turnitin ';
$string['turnitinusegrademark'] = 'استخدم GradeMark';
$string['turnitinusegrademark_desc'] = 'اختر ما اذا كنت ستستخدم GradeMark عند تقييم الإرسالات.<br /><i>(هذا متوفر فقط للمستخدمين الذين يكون لديهم GradeMark مكونًا في حساباتهم)</i>';
$string['turnitinenablepeermark'] = 'مكن مهام Peermark';
$string['turnitinenablepeermark_desc'] = 'اختر ما اذا كنت ستسمح بإنشاء Peermark للمهام.<br/><i>(هذا متوفر فقط للمستخدمين الذين يكون لديهم Peermark مكونًا في حساباتهم)</i>';
$string['transmatch_desc'] = 'تحديد ما اذا سيتوفر تطابق الترجمة كإعداد على شاشة إنشاء المهام.<br /><i>(تمكين هذا الإعداد فقط اذا كان تطابق الترجمة مفعلاً في حساب Turnitin)</i>';
$string['repositoryoptions_0'] = 'تمكين خيارات مستودعات المدرس القياسية';
$string['repositoryoptions_1'] = 'تمكين خيارات المستودع الموسعة للمدرب';
$string['repositoryoptions_2'] = 'قم بإرسال كل الأوراق إلى المستودع المعتاد';
$string['repositoryoptions_3'] = 'لا تقم بإرسال أي أوراق إلى المستودع';
$string['turnitinrepositoryoptions'] = 'المهام الخاصة بمخزن المستندات';
$string['turnitinrepositoryoptions_desc'] = 'اختر خيارات المستودع لمهام Turnitin.<br /><i>(يتوفر المستودع المؤسسي فقط للمستخدمين الذين قاموا بتمكين هذا الخيار في حساباتهم)</i>';
$string['tiimiscsettings'] = 'إعدادات المكون الإضافي المتنوعة';
$string['pp_agreement_default'] = 'من خلال تأشير هذا المربع، اؤكد ان الارسال من عملي الخالص واتقبل جميع المسؤولية في حالة مخالفة حقوق النشر والملكية اذا ما نتج ذلك من خلال هذا الارسال';
$string['pp_agreement_desc'] = '<b>[اختياري]</b><br />أدخل بيان تأكيد الاتفاق.<br />(<b>ملحوظة:</b> إذا ترك الاتفاق فارغًا تمامًا فلن يلزم وجود تأكيد اتفاق بواسطة الطلاب أثناء الإرسال)';
$string['pp_agreement'] = 'اخلاء المسؤولية / الاتفاقية';
$string['studentdataprivacy'] = 'إعدادات الخصوصية لبيانات الطالب';
$string['studentdataprivacy_desc'] = 'يمكن تكوين الإعدادات التالية لضمان عدم نقل البيانات الشخصية للطالب&#39; إلى Turnitin عبر API.';
$string['enablepseudo'] = 'تمكين خصوصية الطلبة';
$string['enablepseudo_desc'] = 'إذا تم تحديد هذا الخيار فسيتم نقل عناوين البريد الإلكتروني للطلاب إلى معادل زائف لمكالمات Turnitin API. <br /><i>(<b>ملحوظة:</b>لا يمكن تغيير هذا الخيار إذا كانت أي بيانات لمستخدمي Moodle تم مزامنتها بالفعل مع Turnitin)</i>';
$string['pseudofirstname'] = 'اسم الطالب الأول الوهمي';
$string['pseudofirstname_desc'] = '<b>[اختياري]</b><br /> اسم الطالب الأول للعرض في عارض مستندات Turnitin';
$string['pseudolastname'] = 'اسم الطالب الأخير الوهمي';
$string['pseudolastname_desc'] = 'أسم الطالب الاخير للعرض في عارض مستندات Turnitin';
$string['pseudolastnamegen'] = 'انشاء الاسم الاخير ذاتيا';
$string['pseudolastnamegen_desc'] = 'اذا تم تعيين نعم وتعيين الاسم الأخير الوهمي لحقل ملف المستخدم، عندئذ سيتم تعبئة الحقل تلقائيًا بمعرف فريد.';
$string['pseudoemailsalt'] = 'القيمة العشوائية الوهمية للتشفير';
$string['pseudoemailsalt_desc'] = '<b>[اختياري]</b><br />قيمة عشوائية اختيارية لزيادة التعقيد لعنوان البريد الإلكتروني للطالب الوهمي المنشأ.<br />(<b>ملحوظة:</b> ينبغي أن تظل القيمة العشوائية بلا تغيير من أجل الحفاظ على عناوين بريد إلكتروني وهمية متسقة)';
$string['pseudoemaildomain'] = 'مجال البريد الإلكتروني الوهمي';
$string['pseudoemaildomain_desc'] = '<b>[اختياري]</b><br />مجال اختياري لعناوين البريد الإلكتروني الوهمية. (@tiimoodle.com بشكل افتراضي إذا تركت فارغة)';
$string['pseudoemailaddress'] = 'عنوان البريد الالكتروني الوهمي';
$string['connecttest'] = 'اختبار الاتصال بـ Turnitin';
$string['connecttestsuccess'] = 'تم توصيل Moodle مع Turnitin بنجاح.';
$string['diagnosticoptions_0'] = 'إيقاف التشغيل';
$string['diagnosticoptions_1'] = 'قياسي';
$string['diagnosticoptions_2'] = 'تصحيح الأخطاء';
$string['repositoryoptions_4'] = 'إرسال كل الورق إلى المستودع المؤسسي';
$string['turnitinrepositoryoptions_help'] = '<strong>تمكين خيارات مستودعات المدرس القياسية: </strong><br />يستطيع المعلمون مطالبة Turnitin بإضافة مستندات إلى المستودع القياسي أو المستودع الخاص بالمؤسسة التعليمية أو عدم إضافتها إلى مستودع.<br /><br /><strong>تمكين خيارات المستودع الموسعة للمدرب: </strong><br />سوف يتيح هذا الخيار للمعلمين عرض إعداد الواجبات للمساح للطلاب بإعلام Turnitin بمكان تخزين مستنداتهم. ويستطيع الطلاب اختيار إضافة مستنداتهم إلى مستودع الطلاب القياسي أو إلى المستودع الخاص بالمعلم.<br /><br /><strong>قم بإرسال كل الأوراق إلى المستودع المعتاد: </strong><br />ستتم إضافة جميع المستندات إلى مستودع الطلاب القياسي بشكل افتراضي.<br /><br /><strong>لا تقم بإرسال أي أوراق إلى المستودع: </strong><br />سوف يقتصر استخدام المستندات على إجراء فحص أولي بواسطة Turnitin والعرض على المعلم لوضع الدرجات.<br /><br /><strong>إرسال كل الورق إلى المستودع المؤسسي: </strong><br />تتم مطالبة Turnitin بتخزين جميع الأوراق في مستودع أوراق المؤسسة. ولا يتم إجراء فحوصات التشابه على المستندات المرسلة إلا بواسطة معلمين آخرين من مؤسستك.';
$string['turnitinuseanon'] = 'استخدام التعليم المجهول';
$string['createassignmenterror'] = 'حدث خطأ عند محاولة إنشاء المهمة في Turnitin';
$string['editassignmenterror'] = 'حدث خطأ عند محاولة تحرير المهمة في Turnitin';
$string['ppassignmentediterror'] = 'تعذر تحرير هذه الوحدة {$a->title} (معرف TII: {$a->assignmentid})، يُرجى الرجوع إلى سجلات واجهة برمجة التطبيقات (API) للحصول على مزيد من المعلومات.';
$string['pp_classcreationerror'] = 'تعذر إنشاء هذا الصف على Turnitin، يُرجى الرجوع إلى سجلات واجهة برمجة التطبيقات (API) للحصول على مزيد من المعلومات.';
$string['unlinkusers'] = 'فصل ارتباط المستخدمين';
$string['relinkusers'] = 'اعادة ربط المستخدمين';
$string['unlinkrelinkusers'] = 'ازالة/اعادة ربط مستخدمين Turnitin';
$string['nointegration'] = 'لا تكامل';
$string['sprevious'] = 'السابق';
$string['snext'] = 'التالي';
$string['slengthmenu'] = 'إظهار إدخالات _MENU_';
$string['ssearch'] = 'بحث:';
$string['sprocessing'] = 'جاري تحميل البيانات من Turnitin...';
$string['szerorecords'] = 'لا توجد اوليات للعرض';
$string['sinfo'] = 'يتم عرض _البداية_الى_النهاية_من_الادخالات_الكلية.';
$string['userupdateerror'] = 'لا يمكن تحديث بيانات المستخدم';
$string['connecttestcommerror'] = 'تعذر الاتصال بـ Turnitin. يرجى التأكد من إعداد API URL. ';
$string['userfinderror'] = 'حدث خطأ عند محاولة إيجاد المستخدم في Turnitin';
$string['tiiusergeterror'] = 'حدث خطأ عند محاولة الحصول على تفاصيل المستخدم من Turnitin';
$string['usercreationerror'] = 'فشل في إنشاء مستخدم Turnitin';
$string['ppassignmentcreateerror'] = 'تعذر إنشاء هذه الوحدة النمطية على Turnitin، يُرجى الرجوع إلى سجلات واجهة برمجة التطبيقات (API) للحصول على مزيد من المعلومات.';
$string['excludebiblio_help'] = 'يسمح هذا الإعداد للمدرب باختيار استبعاد النص الوارد في سجل المراجع أو الأعمال المقتبسة أو قسم الإحالة لأوراق الطلاب من أن تفحص للتطابق عند إنشاء التقارير الأصالة. قد يتم تجاوز هذا الإعداد في تقارير الأصالة الفردية.';
$string['excludequoted_help'] = 'يسمح هذا الإعداد للمدرب أن يختار استبعاد النص الوارد بين أقواس الاقتباس من التحقق من مطابقات عند إنشاء تقارير الأصالة. قد يتم تجاوز هذا الإعداد في تقارير الأصالة الفردية.';
$string['excludevalue_help'] = 'يسمح هذا الوضع للمدرس باختيار استثناء النص الذي ليس له عدد كاف من التطابقات (وفقًا لما يحدده من المدرس) من التحقق في التطابقات عند إنشاء تقارير الاصالة. يمكن حذف هذا الوضع في تقارير الأصالة المنفردة.';
$string['spapercheck_help'] = 'التحقق مقابل مستودعات Turnitin لمستندات الطلبة عند معالجة تقارير الاصالة للمستندات. نسبة التشابه المؤية قد تقل عند عدم اختيار هذه الخاصية';
$string['internetcheck_help'] = 'التحقق مقابل مستودعات Turnitin في الإنترنت عند معالجة تقارير الأصالة للمستندات. نسبة التشابه المئوية قد تقل عند عدم اختيار هذه الخاصية.';
$string['journalcheck_help'] = 'التحقق مقابل مستودعات Turnitin للمجلات والمنشورات عند معالجة تقارير الأصالة للمستندات. نسبة التشابه المئوية قد تقل عند عدم اختيار هذه الخاصية.';
$string['reportgenspeed_help'] = 'توجد ثلاثة خيارات لإعداد المهمة هذا: &#39;إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع).&#39; و&#39;إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع على الفور (إذا تم تعيين المستودع).&#39; و&#39;إنشاء إنشاء التقارير في تاريخ الاستحقاق. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع). &#39;<br /><br />يقوم خيار &#39;إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع).&#39; بإنشاء تقرير الأصالة بشكل فوري عندما يقوم الطالب بالإرسال. ومن خلال هذا الخيار لن يتمكن طلابك من إعادة الإرسال إلى المهمة.<br /><br />للسماح للإرسالات المعادة، قم بتحديد خيار &#39;إنشاء التقارير على الفور. سيتم إضافة التسليمات إلى المستودع على الفور (إذا تم تعيين المستودع).&#39;. يتيح هذا الخيار لطلاب إعادة إرسال المستندات باستمرار إلى المهمة حتى تاريخ الاستحقاق. قد تستغرق معالجة تقارير الأصالة إلى إعادة الإرسال ما يصل إلى 24 ساعة.<br /><br />سيقوم خيار &#39;إنشاء الإنشاء التقارير في تاريخ الاستحقاق. سيتم إضافة التسليمات إلى المستودع في تاريخ الاستحقاق (إذا تم تعيين المستودع).&#39; فقط بإنشاء تقرير أصالة بتاريخ استحقاق &#39;المهمة. سيؤدي هذا الإعداد إلى مقارنة جميع المستندات المرسلة إلى المهمة مقابل بعضها البعض عند إنشاء تقارير الأصالة.';
$string['turnitinuseanon_desc'] = 'اختر ما اذا كنت ستسمح للتعليم المجهول عند تقييم الإرسالات.<br /><i>(هذا متوفر فقط للمستخدمين الذين يكون لديهم التعليم المجهول مكونًا في حساباتهم)</i>';
