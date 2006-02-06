<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.6 development (2005101200)


$string['description'] = '<p>ท่านสามารถใช้เซิร์ฟเวอร์ LDAP สำหรับอนุมัติให้เข้าเรียน โดยเซิร์ฟเวอร์ดังกล่าวจะต้องประกอบไปด้วยกลุ่มที่ชี้ไปยังรายวิชาต่าง ๆ  และ กลุ่มหรือรายวิชานั้น ๆ จะมีรายชื่อสมาชิกเพื่อยันกับข้อมูลในเซิร์ฟเวอร์ </p>
<p>ภายใน LDAP นั้นจะมีการจำกัดความรายวิชาให้เป็นกลุ่มหนึ่งกลุ่ม โดยแต่ละกลุ่มจะมีฟิลด์สมาชิกหลายฟิลด์ด้วยกันเช่น 
(<em>member</em> or <em>memberUid</em>)
ซึ่งจะเป็นข้อมูลเฉพาะสำหรับการยืนยันตัวผู้ใช้แต่ละคน
</p>
<p>ในการใช้การอนุมัติผ่าน LDAP นั้น< ผู้ใช้ต้องมีฟิลด์หมายเลขประจำตัว (ID Number) ที่ถูกต้อง  โดยในกลุ่มแต่ละกลุ่มใน LDAP จะต้องมีฟิลด์หมายเลขประจำตัวในฟิลด์สมาชิกที่สร้างขึ้นสำหรับสมาชิกในการจะเข้าเป็นนักเรียนในรายวิชานั้น ๆ  จะใช้งานได้ดีหากท่านใช้การอนุมัติผ่าน LDAP อยู่ก่อนแล้ว</p>

<p>ระบบจะทำการอัพเดทข้อมูลการเข้าเป็นนักเรียนทุกครั้งที่สมาชิกเข้าสู่ระบบ คุณสามารถใช้งานสคริปต์เพื่อให้ข้อมูลการเป็นนักเรียนนั้นตรงกัน ให้ดูใน <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>

<p>ปลั๊กอินนี้สามารถสร้างรายวิชาใหม่ขึ้นทันทีที่ปรากฎชื่อกลุ่มใหม่ขึ้นภายใน LDAP</P>';
$string['enrol_ldap_autocreate'] = 'ระบบจะสร้างรายวิชาขึ้นโดยอัตโนมัติถ้าหากมีการสมัครเข้าเป็นนักเรียนในวิชาใด ๆ ถึงแม้จะยังไม่มีการสร้างรายวิชาดังกล่าวใน Moodle';
$string['enrol_ldap_autocreation_settings'] = 'การตั้งค่าการสร้างรายวิชาอัตโนมัติ';
$string['enrol_ldap_bind_dn'] = 'ถ้าหากต้องการใช้ bind-user ในการค้นหาผู้ใช้ให้ระบุค่าที่นี่ เช่น 
\'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'รหัสผ่านสำหรับ bind-user';
$string['enrol_ldap_category'] = 'ประเภทสำหรับรายวิชาที่สร้างขึ้นอัตโนมัติ';
$string['enrol_ldap_course_fullname'] = 'ตัวเลือก :  ฟิลด์ LDAP ที่จะดึงข้อมูลชื่อเต็ม';
$string['enrol_ldap_course_idnumber'] = 'ชี้ไปยัง unique identifier ใน LDAP โดยทั่วไปแล้วจะเป็น <em>cn</em> หรือ<em>uid</em>  คุณควรจะทำการล็อคค่านี้เอาไว้หากเลือกใช้วิธีการสร้างรายวิชาอัตโนมัติ';
$string['enrol_ldap_course_settings'] = 'การตั้งค่าการรับเข้าเป็นนักเรียนในรายวิชา';
$string['enrol_ldap_course_shortname'] = 'ตัวเลือก : ฟิลด์ LDAP ที่จะดึงข้อมูลชื่อย่อ';
$string['enrol_ldap_course_summary'] = 'ตัวเลือก : ฟิลด์ LDAP ที่จะดึงข้อมูลบทคัดย่อ';
$string['enrol_ldap_editlock'] = 'ล็อคค่า';
$string['enrol_ldap_general_options'] = 'ตัวเลือกทั่วไป';
$string['enrol_ldap_host_url'] = 'ระบุ Host LDAP ในรูปแบบของ url เช่น \'ldap://ldap.myorg.com/\' 
หรือ \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = ' objectClass ที่ใช้ในการค้นหารายวิชา โดยทั่วไปใช้ \'posixGroup\'';
$string['enrol_ldap_search_sub'] = 'ค้นหาสมาชิกภายในกลุ่มจากบริบท';
$string['enrol_ldap_server_settings'] = 'การตั้งค่าเซิร์ฟเวอร์ LDAP';
$string['enrol_ldap_student_contexts'] = 'รายการบริบท  ที่รายวิชาที่มีนักเรียนตั้งอยู่ ใช้เซมิโคล่อนแยกแต่ละบริบท \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'การตั้งค่าสมาชิก เมื่อผู้ใช้อยู่ภายในกลุ่มหรือสมัครเป็นสมาชิกของกลุ่ม เราจะเรียกว่า \'member\' หรือ \'memberUid\'';
$string['enrol_ldap_student_settings'] = 'การตั้งค่าการรับเข้าเป็นนักเรียน';
$string['enrol_ldap_teacher_contexts'] = 'รายการบริบท  ที่รายวิชาที่มีอาจารย์ตั้งอยู่ ใช้เซมิโคล่อนแยกแต่ละบริบท \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'การตั้งค่าสมาชิก เมื่อผู้ใช้อยู่ภายในกลุ่มหรือสมัครเป็นสมาชิกของกลุ่ม เราจะเรียกว่า \'member\' หรือ \'memberUid\'';
$string['enrol_ldap_teacher_settings'] = 'การตั้งค่าการรับเป็นอาจารย์';
$string['enrol_ldap_template'] = 'ตัวเลือก :  การสร้างรายวิชาอัตโนมัติสามารถทำการสำเนาค่าต่าง ๆ จากรายวิชาต้นแบบ';
$string['enrol_ldap_updatelocal'] = 'อัพเดทข้อมูลในเครื่อง';
$string['enrol_ldap_version'] = 'เวอร์ชันของ LDAP ที่ใช้อยู่';
$string['enrolname'] = 'LDAP';

?>
