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
$string['pluginname'] = 'Turnitin 剽窃 Plugin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin 剽窃 Plugin 任务';
$string['connecttesterror'] = '连接至 Turnitin 时出错。返回的错误消息如下：<br />';

// Assignment Settings.
$string['turnitin:enable'] = '启用 Turnitin';
$string['excludebiblio'] = '不含参考书目';
$string['excludequoted'] = '排除引用资料';
$string['excludevalue'] = '排除小型匹配结果';
$string['excludewords'] = '字';
$string['excludepercent'] = '百分比';
$string['norubric'] = '无评分表';
$string['otherrubric'] = '使用属于其他导师的评分表';
$string['attachrubric'] = '将评分表附加至此作业';
$string['launchrubricmanager'] = '启动评分表管理工具';
$string['attachrubricnote'] = '注意：学生将可以在提交前查看附加的评分表及其内容。';
$string['anonblindmarkingnote'] = '注意：已删除单独的 Turnitin 匿名标记设置。Turnitin 将使用 Moodle 隐蔽标记设置确定匿名标记设置。';
$string['transmatch'] = '已翻译的相符功能';
$string["reportgen_immediate_add_immediate"] = "立即生成报告。提交项将立即添加到存储库中（如果设置了存储库）。";
$string["reportgen_immediate_add_duedate"] = "立即生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。";
$string["reportgen_duedate_add_duedate"] = "在截止日期生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。";
$string['launchquickmarkmanager'] = '启动 Quickmark 管理工具';
$string['launchpeermarkmanager'] = '启动 Peermark 管理工具';
$string['studentreports'] = '显示原创性报告给学生';
$string['studentreports_help'] = '允许您向学生用户显示 Turnitin 原创性报告。如果设置为“确定”，则 Turnitin 生成的原创性报告将可供学生查看。';
$string['submitondraft'] = '在首次上传时提交文件';
$string['submitonfinal'] = '当学生发送以供标记时提交文件';
$string['draftsubmit'] = '文件应在何时提交至 Turnitin？';
$string['allownonor'] = '允许提交任何文件类型吗？';
$string['allownonor_help'] = '此设置将允许提交任何文件类型。如果此选项设为“是”，则在可行的前提下，系统会检查提交内容的原创性，提交内容将可供下载并且 GradeMark 反馈工具将可供使用。';
$string['norepository'] = '无存储库';
$string['standardrepository'] = '标准存储库';
$string['submitpapersto'] = '存储学生论文';
$string['institutionalrepository'] = '机构存储库（适用时）';
$string['checkagainstnote'] = '注意：如果您没有为下面至少一个“做比较...”选项选择“是”，则不会生成“原创性”报告。';
$string['spapercheck'] = '与已存储的学生论文做比较';
$string['internetcheck'] = '与网络比较';
$string['journalcheck'] = '与杂志、<br />期刊与刊物比较';
$string['compareinstitution'] = '将已提交的文件与在此机构内提交的论文进行比较';
$string['reportgenspeed'] = '报告生成速度';
$string['locked_message'] = '锁定的消息';
$string['locked_message_help'] = '如果锁定了任何设置，将显示此消息说明原因。';
$string['locked_message_default'] = '此设置已锁定在站点级别';
$string['sharedrubric'] = '已共享评分表';
$string['turnitinrefreshsubmissions'] = '更新提交内容';
$string['turnitinrefreshingsubmissions'] = '更新提交内容';
$string['turnitinppulapre'] = '要向 Turnitin 提交文件，您必须先接受我们的 EULA（最终用户许可协议）。如果不接受我们的 EULA，您的文件将只会提交给 Moodle。请单击此处阅读并接受本协议。';
$string['noscriptula'] = '（由于您没有启用 javascript，因此在接受 Turnitin 用户协议后，您必须手动更新此页面才能提交）';
$string['filedoesnotexist'] = '文件已被删除';
$string['reportgenspeed_resubmission'] = '您已针对该作业提交论文，所提交论文的相似度报告已生成。如果您选择重新提交论文，那么之前提交的内容将被替换，并将生成新的报告。在 {$a->num_resubmissions} 次重新提交后，您需要在提交后等待 {$a->num_hours} 小时才能查看新报告。';

// Plugin settings.
$string['config'] = '配置';
$string['defaults'] = '默认设置';
$string['showusage'] = '显示数据转储';
$string['saveusage'] = '保存数据转储';
$string['errors'] = '错误';
$string['turnitinconfig'] = 'Turnitin 剽窃 Plugin 配置';
$string['tiiexplain'] = 'Turnitin 为商务产品。您必须付订购费才能使用此服务。有关更多信息，请访问 <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = '启用 Turnitin';
$string['useturnitin_mod'] = '启用 Turnitin {$a}';
$string['turnitindefaults'] = 'Turnitin 剽窃 Plugin 默认设置';
$string['defaultsdesc'] = '以下设置为在活动单元内启用 Turnitin 时设置的默认值';
$string['turnitinpluginsettings'] = 'Turnitin 剽窃 Plugin 设置';
$string['pperrorsdesc'] = '尝试将以下文件上传至 Turnitin 时出现问题。要重新提交，请选择您要重新提交的文件并按“重新提交”按钮。随后在下次运行 cron 时将处理这些文件。';
$string['pperrorssuccess'] = '您选择的文件已重新提交并将由 cron 进行处理。';
$string['pperrorsfail'] = '您选择的一些文件有误，无法为其创建新的 cron 事件。';
$string['resubmitselected'] = '重新提交所选文件';
$string['deleteconfirm'] = '是否确定要删除此提交内容？\n\n此操作无法撤消。';
$string['deletesubmission'] = '删除提交内容';
$string['semptytable'] = '未找到任何结果。';
$string['configupdated'] = '配置已更新';
$string['defaultupdated'] = 'Turnitin 默认值已更新';
$string['notavailableyet'] = '不可用';
$string['resubmittoturnitin'] = '重新提交至 Turnitin';
$string['resubmitting'] = '重新提交';
$string['id'] = '代码';
$string['student'] = '学生';
$string['course'] = '课程';
$string['module'] = '单元';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = '查看原创性报告';
$string['launchrubricview'] = '查看用于标记的评分表';
$string['turnitinppulapost'] = '您的文件尚未提交至 Turnitin。请单击此处接受我们的 EULA。';
$string['ppsubmissionerrorseelogs'] = '此文件尚未提交至 Turnitin，请咨询您的系统管理员';
$string['ppsubmissionerrorstudent'] = '此文件尚未提交至 Turnitin，请问您的导师登记来查询更多详情';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin 剽窃 Plugin 数字回执通知';
$string['digitalreceipt'] = '数字回执';
$string['digital_receipt_subject'] = '这是您的 Turnitin 数字回执';
$string['pp_digital_receipt_message'] = '尊敬的 {$a->firstname} {$a->lastname}，<br /><br />您已于 <strong>{$a->submission_date}</strong>将文件 <strong>{$a->submission_title}</strong> 成功提交至 <strong>{$a->course_fullname}</strong> 课堂的分配 <strong>{$a->assignment_name}{$a->assignment_part}</strong>。您的提交 ID 为 <strong>{$a->submission_id}</strong>。可以通过文档查看器中的“打印/下载”按钮查看并打印您的完整数字回执。<br /><br />感谢您使用 Turnitin，<br /><br />Turnitin 团队敬上';

// Paper statuses.
$string['turnitinid'] = 'Turnitin 代码';
$string['turnitinstatus'] = 'Turnitin 状态';
$string['pending'] = '未决';
$string['similarity'] = '相似度';
$string['notorcapable'] = '无法为此文件生成原创性报告。';
$string['grademark'] = 'GradeMark';
$string['student_read'] = '学生查看论文的时间：';
$string['student_notread'] = '学生尚未查看此论文。';
$string['launchpeermarkreviews'] = '启动 Peermark 评价';

// Cron.
$string['ppqueuesize'] = '剽窃 Plugin 事件队列中的事件数';
$string['ppcronsubmissionlimitreached'] = '此 cron 执行不会向 Turnitin 发送其他任何提交，因为每次运行只会处理 {$a}';
$string['cronsubmittedsuccessfully'] = '提交：课程 {$a->coursename} 中分配 {$a->assignmentname} 的 {$a->title}（TII ID：{$a->submissionid}）已成功提交至 Turnitin。';
$string['pp_submission_error'] = 'Turnitin 为您的提交返回了一个错误：';
$string['turnitindeletionerror'] = 'Turnitin 提交内容刪除失败。计算机上的 Moodle 副本已移除，但 Turnitin 內的提交内容无法刪除。';
$string['ppeventsfailedconnection'] = 'Turnitin 剽窃 Plugin 的此 cron 执行不会处理任何事件，因为无法建立到 Turnitin 的连接。';

// Error codes.
$string['tii_submission_failure'] = '请咨询您的辅导或系统管理员以获得更多资讯';
$string['faultcode'] = '错误代号';
$string['line'] = '列';
$string['message'] = '信息';
$string['code'] = '代号';
$string['tiisubmissionsgeterror'] = '尝试从 Turnitin 中获取此作业的提交内容时出错';
$string['errorcode0'] = '此文件尚未提交至 Turnitin，请咨询您的系统管理员';
$string['errorcode1'] = '这个文件尚未发送至 Turnitin，因为它没有足够内容生成原创性报告。';
$string['errorcode2'] = '这个文件将不会被提交至 Turnitin，因为它超过允许的文件大小上限 {$a->maxfilesize}';
$string['errorcode3'] = '这个文件尚未被提交至 Turnitin，因为用户尚未接受 Turnitin 终端用户许可证协议。';
$string['errorcode4'] = '您必须为此分配上传受支持的文件类型。接受的文件类型包括：.doc、.docx、.ppt、.pptx、.pps、.ppsx、.pdf、.txt、.htm、.html、.hwp、.odt、.wpd、.ps 和 .rtf';
$string['errorcode5'] = '这个文件尚未被提交至 Turnitin，因为在 Turnitin 中创建单元时出现问题，这将阻止提交，请查看您的 API 日志了解更多信息';
$string['errorcode6'] = '这个文件尚未被提交至 Turnitin，因为在 Turnitin 中编辑单元设置时出现问题，这将阻止提交，请查看您的 API 日志了解更多信息';
$string['errorcode7'] = '这个文件尚未被提交至 Turnitin，因为在 Turnitin 中创建用户时出现问题，这将阻止提交，请查看您的 API 日志了解更多信息';
$string['errorcode8'] = '这个文件尚未被提交至 Turnitin，因为创建临时文件时出现问题。最可能的原因是文件名无效。请重命名文件并使用“编辑提交”重新上传。';
$string['errorcode9'] = '无法提交文件，因为文件池中没有可访问的内容供提交。';
$string['coursegeterror'] = '无法获得课程数据';
$string['configureerror'] = '您必须完全以管理员身份配置此单元才能在课程内使用它。请联系您的 Moodle 管理员。';
$string['turnitintoolofflineerror'] = '我们遇到临时问题。请稍后再试。';
$string['defaultinserterror'] = '尝试将默认设置值插入数据库时出错';
$string['defaultupdateerror'] = '尝试更新数据库中的默认设置值时出错';
$string['tiiassignmentgeterror'] = '尝试从 Turnitin 中获取作业时出错';
$string['assigngeterror'] = '无法获得 Turnitin 数据';
$string['classupdateerror'] = '无法更新 Turnitin 课程数据';
$string['pp_createsubmissionerror'] = '尝试在 Turnitin 中创建提交内容时出错';
$string['pp_updatesubmissionerror'] = '尝试将提交内容重新提交至 Turnitin 时出错';
$string['tiisubmissiongeterror'] = '尝试从 Turnitin 中获取提交内容时出错';

// Javascript.
$string['closebutton'] = '关闭';
$string['loadingdv'] = '正在加载 Turnitin 文档查看器...';
$string['changerubricwarning'] = '更改或分离评分表将从此作业的论文中移除所有现有的评分表分数，包括之前已标记的评分卡。之前已评分的论文的总成绩将会被保留。';
$string['messageprovider:submission'] = 'Turnitin 剽窃 Plugin 数字回执通知';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin 状态';
$string['deleted'] = '已删除';
$string['pending'] = '未决';
$string['because'] = '这是因为，管理员从处理队列中删除了待处理的作业并中止向 Turnitin 提交内容。<br /><strong>相应文件仍存在于 Moodle 中，请联系您的导师。</strong><br />请看下面的错误代码：';
$string['submitpapersto_help'] = '<strong>无存储库: </strong><br />Turnitin 被设定为不将上传文件储存至任何知识库。文件仅用于初始查重。<br /><br /><strong>标准存储库: </strong><br />Turnitin 将只在标准知识库中储存上传文件的副本。选择此选项，Turnitin 对日后上传文件的查重工作将只使用已储存文件。<br /><br /><strong>机构存储库（适用时）: </strong><br />选择此选项，将 Turnitin 设定为只添加文件至您机构的私有知识库。上传文件的查重工作将只由您机构的其他教员完成。';
$string['errorcode12'] = '该文件未能上传至 Turnitin，因其所在任务课程已删除。行 ID: ({$a->id}) | 课程模块 ID: ({$a->cm}) | 用户 ID: ({$a->userid})';
$string['errorcode15'] = '此文件尚未提交给 Turnitin，因为找不到它所属的活动模块';
$string['tiiaccountconfig'] = 'Turnitin 帐户配置';
$string['turnitinaccountid'] = 'Turnitin 帐户代号';
$string['turnitinsecretkey'] = 'Turnitin 共享密钥';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = '调试和记录';
$string['turnitindiagnostic'] = '启用诊断模式';
$string['turnitindiagnostic_desc'] = '<b>[警告]</b><br />启用诊断模式来追踪 Turnitin API 的问题。';
$string['tiiaccountsettings_desc'] = '请确保这些设置与您的 Turnitin 帐户中配置的相符，否则您可能会在作业创建和/或学生提交时遇到问题。';
$string['tiiaccountsettings'] = 'Turnitin 帐户设置';
$string['turnitinusegrademark'] = '使用 GradeMark';
$string['turnitinusegrademark_desc'] = '选择是否使用 GradeMark 或 Moodle 为提交内容评分。<br /><i>（仅适用于已为其帐户配置了 GradeMark 的用户）</i>';
$string['turnitinenablepeermark'] = '启用 PeerMark 作业';
$string['turnitinenablepeermark_desc'] = '选择是否允许创建 Peermark 作业。<br/><i>（仅适用于已为其帐户配置了 Peermark 的用户）</i>';
$string['transmatch_desc'] = '确定已翻译的相符功能是否将作为作业设置屏幕上的设置来提供。<br /><i>（只有在您的 Turnitin 帐户中启用了已翻译的相符功能时，才会启用此选项）</i>';
$string['repositoryoptions_0'] = '启用导师标准存储库选项';
$string['repositoryoptions_1'] = '启用导师扩展存储库选项';
$string['repositoryoptions_2'] = '将所有论文提交至标准存储库';
$string['repositoryoptions_3'] = '请勿将任何论文提交至存储库';
$string['turnitinrepositoryoptions'] = '论文存储库作业';
$string['turnitinrepositoryoptions_desc'] = '为 Turnitin 作业选择存储库选项。<br /><i>（机构存储库仅用于为其帐户启用了此选项的用户）</i>';
$string['tiimiscsettings'] = '其他插件设置';
$string['pp_agreement_default'] = '我确认此提交内容是我的作品，并且接受所有可能因此提交而产生的侵权的责任。';
$string['pp_agreement_desc'] = '<b>[可选]</b><br />输入协议确认声明以供提交。<br />（<b>注意：</b>如果协议完全留空，则学生在提交时就无需确认协议）';
$string['pp_agreement'] = '免责声明/协议';
$string['studentdataprivacy'] = '学生数据隐私设置';
$string['studentdataprivacy_desc'] = '可以配置以下设置以确保学生的个人数据不会通过 API 传送至 Turnitin。';
$string['enablepseudo'] = '启用学生隐私';
$string['enablepseudo_desc'] = '如果选择此选项，学生电子邮件地址将转换为 Turnitin API 调用的伪等效内容。<br /><i>（<b>注意：</b>如果有任何 Moodle 用户数据已与 Turnitin 同步，则无法更改此选项）</i>';
$string['pseudofirstname'] = '学生的假名';
$string['pseudofirstname_desc'] = '<b>[可选]</b><br />要显示在 Turnitin 文档查看器中的学生名字';
$string['pseudolastname'] = '学生的假姓';
$string['pseudolastname_desc'] = '学生的姓在Turnitin 文档查看器内显示';
$string['pseudolastnamegen'] = '自动生成姓氏';
$string['pseudolastnamegen_desc'] = '如果设为“是”并且假姓设为用户个人资料字段，则将自动用唯一标识符填充该字段。';
$string['pseudoemailsalt'] = '拟加密盐';
$string['pseudoemailsalt_desc'] = '<b>[可选]</b><br />可选的盐旨在增强生成的假学生电子邮件地址的复杂性。<br />（<b>注意：</b>盐应该保存不变，以确保一致的假电子邮件地址）';
$string['pseudoemaildomain'] = '假的电子邮件网域';
$string['pseudoemaildomain_desc'] = '<b>[选择性的]</b><br />假的电子邮件地址的可选域。（如果留空，则默认为 @tiimoodle.com）';
$string['pseudoemailaddress'] = '假电子邮件地址';
$string['connecttest'] = '测试 Turnitin 连接';
$string['connecttestsuccess'] = 'Moodle 已成功地连线至 Turnitin。';
$string['diagnosticoptions_0'] = '关闭';
$string['diagnosticoptions_1'] = '标准';
$string['diagnosticoptions_2'] = '调试';
$string['repositoryoptions_4'] = '将所有文件上传至机构资源库';
$string['turnitinrepositoryoptions_help'] = '<strong>启用导师标准存储库选项: </strong><br />教员可将 Turnitin 设定为添加文件至标准知识库、至机构的私有知识库、或不添加至知识库。<br /><br /><strong>启用导师扩展存储库选项: </strong><br />这一选项将允许教员查看作业设置，该作业设置允许学生通过 Turnitin 设定文件的储存位置。学生可以选择添加文件至标准学生知识库或添加至您机构的私有知识库。<br /><br /><strong>将所有论文提交至标准存储库: </strong><br />所有文件都默认添加至标准学生知识库。<br /><br /><strong>请勿将任何论文提交至存储库: </strong><br />文件通过 Turnitin 将只用于供教员查看评分和初始查重。<br /><br /><strong>将所有文件上传至机构资源库: </strong><br />Turnitin 被设定为将所有论文储存至机构论文知识库。上传文件的查重工作将由您机构内的其他教员完成。';
$string['turnitinuseanon'] = '使用匿名标记';
$string['createassignmenterror'] = '尝试在 Turnitin 中创建作业时出错';
$string['editassignmenterror'] = '尝试在 Turnitin 中编辑作业时出错';
$string['ppassignmentediterror'] = '单元 {$a->title}（TII ID：{$a->assignmentid}）无法在 Turnitin 上编辑，请查看您的 API 日志了解更多信息';
$string['pp_classcreationerror'] = '此课程无法在 Turnitin 上创建，请查阅您的 API 日志以获得更多信息';
$string['unlinkusers'] = '停止链接用户';
$string['relinkusers'] = '重新链接用户';
$string['unlinkrelinkusers'] = '解除链接/重新链接 Turnitin 用户';
$string['nointegration'] = '无整合';
$string['sprevious'] = '前';
$string['snext'] = '次';
$string['slengthmenu'] = '显示 _MENU_ 条目';
$string['ssearch'] = '搜索：';
$string['sprocessing'] = '正在从 Turnitin 加载数据...';
$string['szerorecords'] = '无法显示任何记录。';
$string['sinfo'] = '正在显示第 _START_ 到 _END_ 个条目，共 _TOTAL_ 个条目。';
$string['userupdateerror'] = '无法更新用户数据';
$string['connecttestcommerror'] = '无法连线至 Turnitin。请再次检查您的 API URL 设置。';
$string['userfinderror'] = '尝试在 Turnitin 中查找用户时出错';
$string['tiiusergeterror'] = '尝试从 Turnitin 中获取用户细节时出错';
$string['usercreationerror'] = 'Turnitin 用户创建失败';
$string['ppassignmentcreateerror'] = '此单元无法在 Turnitin 上创建，请查阅您的 API 日志以获得更多信息';
$string['excludebiblio_help'] = '此设置允许指导教师选择排除在学生论文内的参考书目、引述的作品，或参考文献内出现的内文，使其在生成原创性报告时不会被检查。此设置可以在各个原创性报告内撤消。';
$string['excludequoted_help'] = '在生成原创性报告时，此设置允许导师选择排除引述的文字，使其不被检查。此设置可以在各个原创性报告内撤消。';
$string['excludevalue_help'] = '此设置允许指导教师在生成原创性报告时选择排除长度不够长的相符处（由指导教师设置）使其不被考虑。此设置可以在各个原创性报告内撤消。';
$string['spapercheck_help'] = '当为论文处理原创性报告时与 Turnitin 学生论文存储库比较。如果这没有被选择的话，相似处指数百分比可能下降。';
$string['internetcheck_help'] = '当为论文处理原创性报告时与 Turnitin 网络存储库比较。如果这没有被选择的话，相似处指数百分比可能下降。';
$string['journalcheck_help'] = '当为论文处理原创性报告时与 Turnitin 杂誌、期刊与刊物存储库比较。如果这没有被选择的话，相似处指数百分比可能下降。';
$string['reportgenspeed_help'] = '此作业设置有三个选项：“立即生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。”、“立即生成报告。提交项将立即添加到存储库中（如果设置了存储库）。”和“在截止日期生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。”<br /><br />“立即生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。”选项可在学生进行提交时立即生成原创性报告。选择此选项后，您的学生将无法重新提交作业。<br /><br />要允许重新提交，请选择“立即生成报告。提交项将立即添加到存储库中（如果设置了存储库）。”选项。这允许学生在截止日期之前继续向作业重新提交报告。处理重新提交的原创性报告可能最长需要 24 小时。<br /><br />“在截止日期生成报告。提交项将在截止日期添加到存储库中（如果设置了存储库）。”选项只会在作业的截止日期生成原创性报告。此设置将允许系统在创建原创性报告后对提交至作业的所有论文进行相互比较。';
$string['turnitinuseanon_desc'] = '选择在为提交内容评分时是否允许匿名标记。<br /><i>（仅适用于已为其帐户配置了匿名标记的用户）</i>';
