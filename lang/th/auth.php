<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 (2004052500)


$string['auth_dbdescription'] = 'วิธีนี้เป็นการใช้ฐานข้อมูลนอกในการตรวจสอบว่าชื่อและรหัสผ่านนั้นถูกต้องหรือไม่ ถ้าหาก account ดังกล่าวเป็น ข้อมูลใหม่ ข้อมูลจะถูกส่งไปยังส่วนต่าง ๆ ใน Moodle';
$string['auth_dbextrafields'] = 'ช่องนี้จะเติมหรือไม่ก็ได้  คุณสามารถเลือกใช้ ค่าที่ระบบ ตั้งไว้ก่อน จาก  <b>ฐานข้อมูลนอก</b><p>  ถ้าหาก ปล่อยว่าง ไม่เติม ระบบจะเลือกใช้ ค่า default  <p> และ ทั้งสองกรณี สมาชิกสามารถที่จะแก้ไขค่าต่างๆ ได้ ภายหลังจาก ล็อกอิน';
$string['auth_dbfieldpass'] = 'ส่วนที่มีข้อมูลของ  password ';
$string['auth_dbfielduser'] = 'ส่วนที่มีข้อมูลของ username';
$string['auth_dbhost'] = 'คอมพิวเตอร์ที่ใช้ เก็บฐานข้อมูล';
$string['auth_dbname'] = 'ชื่อของฐานข้อมูล';
$string['auth_dbpass'] = 'password ตรงกับ username';
$string['auth_dbpasstype'] = 'ระบุรูปแบบที่จะใช้ในช่องใส่ password  การใช้ MD5 encrัyption มีประโยชน์ในการติดต่อกับโปรแกรมการจัดการเว็บอื่นๆ เช่น PostNuke';
$string['auth_dbtable'] = 'ชื่อของตารางในฐานข้อมูล';
$string['auth_dbtitle'] = 'ใช้ฐานข้อมูลนอก';
$string['auth_dbtype'] = 'ประเภทของฐานข้อมูล(ดูข้อมูลเพิ่มเติมจาก  <A HREF=../lib/adodb/readme.htm#drivers>การใช้ ADOdb </A> )';
$string['auth_dbuser'] = 'Username ที่สามารถเข้าไปอ่านฐานข้อมูลได้';
$string['auth_emaildescription'] = 'ในการสมัครเป็นสมาชิกนั้น ผู้สมัครจะได้รับการอนุมัติ ผ่านอีเมล ซึ่งเป็นค่าที่ตั้งไว้ของระบบ เมื่อผู้สมัครเลือก ชื่อ และ รหัสผ่านแล้ว ระบบจะทำการส่งอีเมลไปยัง อีเมลของสมาชิกนั้น อีเมลนี้จะมีลิงก์กลับไปยังหน้าหลักของหน้า ซึ่งจะเป็นการยืนยันว่า อีเมลดังกล่าวใช้ได้จริง  หลังจากนั้นสมาชิก สามารถล็อกอินโดยใช้ชื่อและรหัสผ่านเว็บ';
$string['auth_emailtitle'] = 'ใช้วิธีอนุมัติผ่านอีเมล';
$string['auth_imapdescription'] = 'ใช้วิธีการ ชื่อและรหัส โดย IMAP เซิร์ฟเวอร์';
$string['auth_imaphost'] = 'IMAP เซิร์ฟเวอร์นั้น ใช้ เลข  IP ไม่ใช่เลข DNS ';
$string['auth_imapport'] = 'หมายเลขพอร์ต IMAP โดยปกติ คือ  143 หรือ 993.';
$string['auth_imaptitle'] = 'ใช้ IMAP server';
$string['auth_imaptype'] = 'IMAP servers  สามารถมี วิธี authentication และ negotiation ที่แตกต่างไป';
$string['auth_ldap_bind_dn'] = 'ถ้าหากต้องการใช้ bind-user เพื่อค้นห้าสมาชิกอื่นได้ สามารถ ระบุดังต่อไปนี้  \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'รหัสสำหรับ bind-user.';
$string['auth_ldap_contexts'] = 'รายการที่มีรายชื่อของสมาชิกในนั้น  สามารถ แยก หัวข้อเรื่อง โดยใช้ โค้ด เช่น \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'เปิดให้สมาชิกสามารถสร้างข้อความตอบรับทางอีเมลด้วยตนเองได้ 
คุณไม่จำเป็นต้องใส่ข้อความนี้ที่ ldap_context-variable, Moodle จะค้นหาให้อัตโนมัติ
';
$string['auth_ldap_creators'] = 'รายการกลุ่มสมาชิกที่อนุญาตให้สามารถสร้างหลักสูตรใหม่ได้ สามารถใส่ได้หลายกลุ่ม โดยใช้เครื่องหมาย \';\' 
ดังตัวอย่าง  \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'ระบุ LDAP host เช่น  \'ldap://ldap.myorg.com/\' หรือ  \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'คุณสมบัติของสมาชิกใหม่ของกลุ่ม ปกติใช้  \'member\'';
$string['auth_ldap_search_sub'] = 'ใส่ค่า <> 0 ถ้าหากต้องการ ค้นหาสมาชิกผ่านหัวข้อย่อย ';
$string['auth_ldap_update_userinfo'] = 'อัพเดทข้อมูลสมาชิก (ชื่อ,นามสกุล,ที่อยู่..) จาก LDAP ถึง  Moodle. ดูเพิ่มเติมที่  /auth/ldap/attr_mappings.php ';
$string['auth_ldap_user_attribute'] = 'attribute ที่ใช้ในการค้นหาชื่อสมาชิก ส่วนใหญ่จะใช้  \'cn\'.';
$string['auth_ldap_version'] = 'รุ่นของ LDAP ที่ใช้อยู่';
$string['auth_ldapdescription'] = 'วิธีการอนุมัติการใช้งานผ่าน  external LDAP server ถ้าหาก ชื่อ และ รหัสที่ใส่มานั้นถูกต้อง Moodle จะทำการสร้าง รายชื่อสมาชิกใหม่ในฐานข้อมูล  โมดูลดังกล่าว สามารถ อ่าน attribute ของสมาชิกจาก LDAP  และ ใส่ค่าที่ต้องการใน moodle ล่วงหน้า  หลังจากนั้น เวลาล็อกอิน ก็จะมีการเช็ค แค่ชื่อและรหัสผ่านเท่านั้น ';
$string['auth_ldapextrafields'] = 'ช่องนี้จะเติมหรือไม่ก็ได้  คุณสามารถเลือกใช้ ค่าที่ระบบ ตั้งไว้ก่อน จาก  <b>LDAP fileds</b><p>  ถ้าหาก ปล่อยว่าง ไม่เติม จะไม่มีการดึงข้อมูลจาก LDAP ระบบจะเลือกใช้ ค่า default ใน moodle <p> และ ทั้งสองกรณี สมาชิกสามารถที่จะแก้ไขค่าต่างๆ ได้ ภายหลังจาก ล็อกอิน';
$string['auth_ldaptitle'] = 'ใช้ LDAP server';
$string['auth_manualdescription'] = 'วิธีการนี้จะไม่อนุญาตให้สมาชิกสามารถสร้างบัญชีสมาชิกด้วยตนเองได้ นั่นคือ ผู้ดูแลจะเป็นคนลงทะเบียนสมาชิกให้';
$string['auth_manualtitle'] = 'ผู้ดูแลระบบเท่านั้น';
$string['auth_multiplehosts'] = 'สามารถใส่โฮสต์หลาย ๆ ตัวลงไป เช่น host1.com;host2.com;host3.com';
$string['auth_nntpdescription'] = 'วิธีนี้เช็ค ชื่อ และรหัสผ่านว่าถูกต้องหรือไม่ โดยใช้ NNTP server ';
$string['auth_nntphost'] = 'NNTP server ใช้ เลข IP  ไม่ใช่ DNS ';
$string['auth_nntpport'] = 'Server port (119 เป็นส่วนใหญ่)';
$string['auth_nntptitle'] = 'ใช้ NNTP server';
$string['auth_nonedescription'] = 'สมาชิกสามารถ ล็อกอิน และสร้าง account ใหม่ทันที โดยไม่ต้องใช้วิธีการขออนุมัติ การเป็นสมาชิกจากฐานข้อมูลนอก ไม่ต้องยืนยันผ่านอีเมล  ควรระวังในการเลือกใช้วิีธี นี้ เพราะ ว่า ระบบความปลอดภัยนั้นมีน้อย ';
$string['auth_nonetitle'] = 'ไม่ต้องขออนุมัติ อนุญาตทันที';
$string['auth_pop3description'] = 'เช็คชื่อ และรหัสว่าถูกต้องหรือไม่ ผ่านทาง  POP3 server ';
$string['auth_pop3host'] = 'POP3 server ใช้ เลข IP  ไม่ใช่ DNS ';
$string['auth_pop3port'] = 'Server port (110 โดยทั่วไป)';
$string['auth_pop3title'] = 'ใช้ POP3 server';
$string['auth_pop3type'] = 'ประเภทของเซิร์ฟเวอร์ ถ้าเซิร์ฟเวอร์ ใช้  certificate security ให้เลือก pop3cert.';
$string['auth_user_create'] = 'อนุญาตให้เพิ่มสมาชิกได้';
$string['auth_user_creation'] = 'อนุญาตให้สมาชิกทั่วไปสามารถสร้างบัญชีสมาชิกและตอบยืนยันได้ ถ้าอนุญาต โปรดอย่าลืมไปปรับแก้ระบบ moodule-specific ตัวเลือก user creation ด้วย';
$string['auth_usernameexists'] = 'มีสมาชิกชื่อนี้ในระบบแล้ว กรุณาเลือกชื่อใหม่';
$string['authenticationoptions'] = 'วิธีการอนุมัติการเป็นสมาชิก';
$string['authinstructions'] = 'คุณสามารถให้ข้อมูลกับสมาชิก และแนะนำวิธีการใช้ ผ่านส่วนนี้ ทำให้สมาชิกทราบว่า username และ password ของตัวเองคืออะไร ข้อความที่คุณระบุในส่วนนี้จะปรากฎ ใน หน้าล็อกอิน  ถ้าหากคุณปล่อยว่างไว้ จะไม่มีวิธีการใช้ปรากฎ';
$string['changepassword'] = 'เปลี่ยนรหัส URL';
$string['changepasswordhelp'] = 'คุณสามารถระบุลิงก์ ที่สมาชิกสามารถจะเปลี่ยน หรือ หา ชื่อ และ passwordได้ เมื่อมีการลืม ลิงก์ดังกล่าวจะนำสมาชิกไปยังหน้า ล็อกอิน และหน้าข้อมูลส่วนตัว แต่หากไม่เติมอะไร ปุ่มดังกล่าวจะไม่ปรากฎ';
$string['chooseauthmethod'] = 'เลือกวิธีการอนุมัติ';
$string['guestล็อกอินbutton'] = 'ปุ่ม ล็อกอิน สำหรับบุคคลทั่วไป';
$string['instructions'] = 'วิธีใช้';
$string['md5'] = 'เข้ารหัสแบบ MD5  ';
$string['plaintext'] = 'ตัวหนังสือธรรมดา(Plain Text)';
$string['showguestล็อกอิน'] = 'คุณสามารถซ่อนหรือแสดงปุ่ม ล็อกอิน สำหรับบุคคลทั่วไปในหน้าล็อกอินได้ ';

?>
