<?php // $Id$ 

$string['enrolname'] = 'File đơn giản';

$string['description'] = 'Phương pháp này sẽ lặp lại việc kiểm tra và xử lý một file văn bản được định dạng đặc biệt trong vị trí mà bạn chỉ ra. File có thể giống như sau: 
<pre>
   add, student, 5, CF101
   add, teacher, 6, CF101
   add, teacheredit, 7, CF101
   del, student, 8, CF101
   del, student, 17, CF101
   add, student, 21, CF101, 1091115000, 1091215000
</pre>
';

$string['filelockedmailsubject'] = 'Lỗi quan trọng: File được kết nạp';
$string['filelockedmail'] = 'File văn bản bạn đang sử dụng đối với các kết nạp dựa trên file ($a) không thể được xoá bởi by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Moodle can delete the file, otherwise it might be processed repeatedly.';
$string['location'] = 'Vị trí File';
$string['mailusers'] = 'Thông báo cho những người sử dụng qua Email';
$string['mailadmin'] = 'Thông báo cho người quản trị qua Email';

?>
