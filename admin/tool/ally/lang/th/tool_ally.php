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

$string['adminurl'] = 'เปิดตัว URL';
$string['adminurldesc'] = 'LTI เปิดตัว URL ที่ใช้เพื่อเข้าถึงรายงานการเข้าถึง';
$string['allyclientconfig'] = 'การกำหนดค่า Ally';
$string['ally:clientconfig'] = 'เข้าถึงและอัปเดตการกำหนดค่าไคลเอนต์';
$string['ally:viewlogs'] = 'ผู้ดูบันทึก Ally';
$string['clientid'] = 'ID ลูกค้า';
$string['clientiddesc'] = 'ID ไคลเอนต์ Ally';
$string['code'] = 'รหัส';
$string['contentauthors'] = 'ผู้เขียนเนื้อหา';
$string['contentauthorsdesc'] = 'ผู้ดูแลระบบและผู้ใช้งานที่ถูกกำหนดให้กับบทบาทที่เลือกเหล่านี้จะมีการประเมินไฟล์รายวิชาที่อัปโหลดเพื่อความสะดวกในการเข้าถึง ไฟล์จะได้รับการจัดอันดับสำหรับการเข้าถึง อันดับที่ต่ำหมายความว่าไฟล์ต้องการการเปลี่ยนแปลงเพื่อให้สามารถเข้าถึงได้มากขึ้น';
$string['contentupdatestask'] = 'งานอัปเดตเนื้อหา';
$string['curlerror'] = 'ข้อผิดพลาด cURL: {$a}';
$string['curlinvalidhttpcode'] = 'รหัสสถานะ HTTP ไม่ถูกต้อง: {$a}';
$string['curlnohttpcode'] = 'ไม่สามารถตรวจสอบรหัสสถานะ HTTP';
$string['error:invalidcomponentident'] = 'ตัวระบุองค์ประกอบไม่ถูกต้อง {$a}';
$string['error:pluginfilequestiononly'] = 'สนับสนุนเฉพาะส่วนประกอบคำถามสำหรับ URL นี้';
$string['error:componentcontentnotfound'] = 'ไม่พบเนื้อหาสำหรับ {$a}';
$string['error:wstokenmissing'] = 'Token บริการเว็บหายไป อาจเป็นเพราะผู้ใช้งานที่เป็นผู้ดูแลระบบจำเป็นต้องเรียกใช้การกำหนดค่าอัตโนมัติ';
$string['excludeunused'] = 'ไม่รวมไฟล์ที่ไม่ได้ใช้';
$string['excludeunuseddesc'] = 'ละเว้นไฟล์ที่แนบมากับเนื้อหา HTML แต่ยกเว้นลิงก์/การอ้างอิงใน HTML';
$string['filecoursenotfound'] = 'ไฟล์ที่ส่งผ่านไม่ได้เป็นของรายวิชาใดๆ';
$string['fileupdatestask'] = 'ข้อมูลอัปเดตพุชไฟล์ไปยัง Ally';
$string['id'] = 'Id';
$string['key'] = 'คีย์';
$string['keydesc'] = 'ID ผู้บริโภค LTI';
$string['level'] = 'ระดับ';
$string['message'] = 'ข้อความ';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL อัปเดตไฟล์';
$string['pushurldesc'] = 'ส่งการแจ้งเตือนเกี่ยวกับการอัปเดตไฟล์ไปยัง URL นี้';
$string['queuesendmessagesfailure'] = 'เกิดข้อผิดพลาดขณะส่งข้อความไปยัง AWS SQS ข้อมูลที่ผิดพลาด: $ a';
$string['secret'] = 'ข้อมูลลับ';
$string['secretdesc'] = 'ความลับ LTI';
$string['showdata'] = 'แสดงข้อมูล';
$string['hidedata'] = 'ซ่อนข้อมูล';
$string['showexplanation'] = 'แสดงคำอธิบาย';
$string['hideexplanation'] = 'ซ่อนคำอธิบาย';
$string['showexception'] = 'แสดงข้อยกเว้น';
$string['hideexception'] = 'ซ่อนข้อยกเว้น';
$string['usercapabilitymissing'] = 'ผู้ใช้งานที่ระบุไม่มีความสามารถในการลบไฟล์นี้';
$string['autoconfigure'] = 'กำหนดค่าบริการเว็บ Ally โดยอัตโนมัติ';
$string['autoconfiguredesc'] = 'สร้างบทบาทบริการเว็บและผู้ใช้งานโดยอัตโนมัติสำหรับ Ally';
$string['autoconfigureconfirmation'] = 'สร้างบทบาทและผู้ใช้งานสำหรับพันธมิตรโดยอัตโนมัติและเปิดใช้งานบริการบนเว็บ ระบบจะดำเนินการดังนี้:<ul><li>สร้างบทบาทที่มีชื่อว่า \'ally_webservice\' และผู้ใช้งานที่มีชื่อผู้ใช้งาน \'ally_webuser\'</li><li>เพิ่มผู้ใช้งาน \'ally_webuser\' ไปยังบทบาท \'ally_webservice\'</li><li>เปิดใช้งานบริการบนเว็บ</li><li>เปิดใช้งานโพรโทคอลบริการบนเว็บ REST</li><li>เปิดใช้งานบริการบนเว็บ Ally</li><li>สร้างโทเคนสำหรับบัญชี \'ally_webuser\'</li></ul>';
$string['autoconfigsuccess'] = 'สำเร็จ - บริการเว็บ Ally ถูกกำหนดค่าโดยอัตโนมัติ';
$string['autoconfigtoken'] = 'Token บริการเว็บเป็นดังนี้:';
$string['autoconfigapicall'] = 'คุณสามารถทดสอบว่า webservice สามารถทำงานผ่าน url ต่อไปนี้:';
$string['privacy:metadata:files:action'] = 'การทำงานที่ทำกับไฟล์ EG: สร้าง อัปเดต หรือลบ';
$string['privacy:metadata:files:contenthash'] = 'แฮชเนื้อหาของไฟล์เพื่อกำหนดเอกลักษณ์';
$string['privacy:metadata:files:courseid'] = 'ID รายวิชาที่มีไฟล์อยู่';
$string['privacy:metadata:files:externalpurpose'] = 'เพื่อที่จะรวมเข้ากับ Ally ไฟล์จะต้องมีการแลกเปลี่ยนกับ Ally';
$string['privacy:metadata:files:filecontents'] = 'เนื้อหาของไฟล์จริงจะถูกส่งไปยัง Ally เพื่อประเมินความสามารถในการเข้าถึง';
$string['privacy:metadata:files:mimetype'] = 'ไฟล์ประเภท MIME, EG: text / plain, image / jpeg เป็นต้น';
$string['privacy:metadata:files:pathnamehash'] = 'ชื่อเส้นทางของไฟล์แฮชเพื่อระบุโดยเฉพาะ';
$string['privacy:metadata:files:timemodified'] = 'เวลาที่แก้ไขฟิลด์ครั้งล่าสุด';
$string['cachedef_annotationmaps'] = 'เก็บข้อมูลคำอธิบายประกอบสำหรับรายวิชา';
$string['cachedef_fileinusecache'] = 'รวมไฟล์ในแคชที่ใช้';
$string['cachedef_pluginfilesinhtml'] = 'รวมไฟล์ในแคช HTML';
$string['cachedef_request'] = 'แคชคำขอตัวกรอง Ally';
$string['pushfilessummary'] = 'สรุปการอัปเดตไฟล์ Ally';
$string['pushfilessummary:explanation'] = 'สรุปการอัปเดตไฟล์ที่ส่งถึง Ally';
$string['section'] = 'มาตรา {$a}';
$string['lessonanswertitle'] = 'คำตอบสำหรับบทเรียน "{$a}"';
$string['lessonresponsetitle'] = 'การตอบกลับสำหรับบทเรียน "{$a}"';
$string['logs'] = 'บันทึกของ Ally';
$string['logrange'] = 'ช่วงการบันทึก';
$string['loglevel:none'] = 'ไม่มี';
$string['loglevel:light'] = 'เบา';
$string['loglevel:medium'] = 'กลาง';
$string['loglevel:all'] = 'ทั้งหมด';
$string['logcleanuptask'] = 'ภารกิจล้างข้อมูลล็อก Ally';
$string['loglifetimedays'] = 'เก็บล็อกไว้หลายวัน';
$string['loglifetimedaysdesc'] = 'เก็บล็อก Ally ไว้หลายวัน เมื่อตั้งค่าไปที่ 0 เพื่อไม่ลบล็อก หากภารกิจที่กำหนดเวลาไว้ (ค่าเริ่มต้น) ถูกตั้งไว้ที่ทำงานทุกวันซึ่งจะลบรายการล็อกที่มีอายุมากกว่าค่าหลายวันนี้ออกไป';
$string['logger:filtersetupdebugger'] = 'บันทึกการตั้งค่าตัวกรอง Ally';
$string['logger:pushtoallysuccess'] = 'พุชไปยังจุดสิ้นสุดของ Ally สำเร็จ';
$string['logger:pushtoallyfail'] = 'พุชไปยังจุดสิ้นสุดของ Ally ไม่สำเร็จ';
$string['logger:pushfilesuccess'] = 'การพุชไฟล์ไปยังจุดสิ้นสุดของ Ally สำเร็จ';
$string['logger:pushfileliveskip'] = 'การพุชไฟล์แบบสดล้มเหลว';
$string['logger:pushfileliveskip_exp'] = 'การข้ามพุชไฟล์แบบสดที่มีสาเหตุจากปัญหาการสื่อสาร การพุชไฟล์แบบสดจะถูกกู้คืนข้อมูลเมื่อการอัปเดตไฟล์สำเร็จ โปรดตรวจสอบการกำหนดค่าของคุณ';
$string['logger:pushfileserror'] = 'พุชไปยังจุดสิ้นสุดของ Ally ไม่สำเร็จ';
$string['logger:pushfileserror_exp'] = 'ข้อผิดพลาดที่เกี่ยวข้องกับการอัปเดตเนื้อหาพุชไปยังบริการ Ally';
$string['logger:pushcontentsuccess'] = 'พุชเนื้อหาไปยังจุดสิ้นสุด Ally สำเร็จ';
$string['logger:pushcontentliveskip'] = 'การพุชเนื้อหาแบบสดล้มเหลว';
$string['logger:pushcontentliveskip_exp'] = 'การข้ามพุชเนื้อหาแบบสดที่มีสาเหตุจากปัญหาการสื่อสาร พุชเนื้อหาแบบสดจะถูกกู้คืนข้อมูลเมื่อการอัปเดตเนื้อหาสำเร็จ โปรดตรวจสอบการกำหนดค่าของคุณ';
$string['logger:pushcontentserror'] = 'พุชไปยังจุดสิ้นสุดของ Ally ไม่สำเร็จ';
$string['logger:pushcontentserror_exp'] = 'ข้อผิดพลาดที่เกี่ยวข้องกับการอัปเดตเนื้อหาพุชไปยังบริการ Ally';
$string['logger:addingconenttoqueue'] = 'การเพิ่มเนื้อหาไปยังคิวพุช';
$string['logger:annotationmoderror'] = 'การทำหมายเหตุประกอบเนื้อหาโมดูล Ally ล้มเหลว';
$string['logger:annotationmoderror_exp'] = 'ระบุโมดูลไม่ถูกต้อง';
$string['logger:failedtogetcoursesectionname'] = 'การรับชื่อส่วนของรายวิชาล้มเหลว';
$string['logger:moduleidresolutionfailure'] = 'ไม่สามารถแก้ไข ID โมดูล';
$string['logger:cmidresolutionfailure'] = 'ไม่สามารถแก้ไข ID โมดูลรายวิชา';
$string['logger:cmvisibilityresolutionfailure'] = 'ไม่สามารถแก้ไขการมองเห็นโมดูลรายวิชา';
$string['courseupdatestask'] = 'พุชกิจกรรมของรายวิชาไปยัง Ally';
$string['logger:pushcoursesuccess'] = 'การพุชกิจกรรมของรายวิชาไปยังจุดสิ้นสุด Ally สำเร็จ';
$string['logger:pushcourseliveskip'] = 'การพุชกิจกรรมของรายวิชาแบบสดล้มเหลว';
$string['logger:pushcourseerror'] = 'การพุชกิจกรรมของรายวิชาแบบสดล้มเหลว';
$string['logger:pushcourseliveskip_exp'] = 'การข้ามพุชกิจกรรมของรายวิชาแบบสดที่มีสาเหตุจากปัญหาการสื่อสาร การพุชกิจกรรมของรายวิชาแบบสดจะถูกกู้คืนข้อมูลเมื่อการอัปเดตไฟล์สำเร็จ โปรดตรวจสอบการกำหนดค่าของคุณ';
$string['logger:pushcourseserror'] = 'พุชไปยังจุดสิ้นสุดของ Ally ไม่สำเร็จ';
$string['logger:pushcourseserror_exp'] = 'ข้อผิดพลาดที่เกี่ยวข้องกับพุชการอัปเดตรายวิชาไปยังบริการ Ally';
$string['logger:addingcourseevttoqueue'] = 'การเพิ่มกิจกรรมรายวิชาไปยังคิวพุช';
$string['logger:cmiderraticpremoddelete'] = 'ID โมดูลรายวิชาเกิดปัญหาเกี่ยวกับการลบล่วงหน้า';
$string['logger:cmiderraticpremoddelete_exp'] = 'ไม่ได้ระบุโมดูลให้ถูกต้อง โมดูลไม่มีอยู่ เนื่องจากการลบส่วนหรือมีปัจจัยอื่นซึ่งส่งผลให้เกิดจุดแทรกโค้ดการลบและจึงไม่พบโมดูลนั้น';
$string['logger:servicefailure'] = 'ล้มเหลว เมื่อใช้บริการ';
$string['logger:servicefailure_exp'] = '<br>คลาส: {$a->class}<br>พารามิเตอร์: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'ล้มเหลว เมื่อมอบหมายความสามารถต้นแบบของอาจารย์ไปยังบทบาท ally_webservice';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>ความสามารถ: {$a->cap}<br>สิทธิ์: {$a->permission}';
$string['deferredcourseevents'] = 'ส่งกิจกรรมรายวิชาที่ถูกเลื่อน';
$string['deferredcourseeventsdesc'] = 'อนุญาตให้การส่งกิจกรรมรายวิชาที่เก็บไว้ซึ่งได้สะสมไว้ระหว่างการสื่อสารกับ Ally ที่ล้มเหลว';
