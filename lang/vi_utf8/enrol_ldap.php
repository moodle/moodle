<?php // $Id$ 

$string['enrolname'] = 'LDAP';
$string['description'] = '<p>Bạn có thể sử dụng một máy chủ LDAP để điều khiển việc kết nạp của bạn.  
                          Giả sử rằng biểu đồ hình cây LDAP của bạn chứa các nhóm mà nó sắp xếp 
                          các cua học, và mỗi cái trong số các nhóm/các cua học đó sẽ có các mục thành viên 
                          để sắp xếp theo ý kiến của các học viên.</p>
                          <p>Nó giả sử rằng các cua học được định nghĩa như các nhóm trong 
                          LDAP, với mỗi nhóm có nhiều trường hội viên 
                          (<em>member</em> hoặc <em>memberUid</em>) mà nó chứa một định danh duy nhất của người dùng
                          .</p>
                          <p>Để sử dụng việc kết nạp LDAP, những người dùng của bạn <strong>phải</strong> 
                          có trường idnumber hợp lệ. Các nhóm LDAP phải có 
                          idnumber đó trong các trường thành viên đối với một người dùng được kết nạp 
                          trong một cua học.
                          Theo cách thông thường điều này sẽ làm việc tốt khi bạn đang sẵn sàng sử dụng thẩm định LDAP 
                          .</p>
                          <p>Các kết nạp sẽ được cập nhật khi người dùng đăng nhập. Bạn
                           cũng có thể chạy một tập lệnh để duy trì các kết nạp đồng bộ. Nhìn vào 
                          <em>kết nạp /ldap/enrol_ldap_sync.php</em>.</p>
                          <p>Plugin này cũng có thể được thiết lập tự động tạo 
                          các cua học mới khi các nhóm mới xuất hiện trong LDAP.</p>';
$string['enrol_ldap_server_settings'] = ' Các thiết lập  máy chủ LDAP';
$string['enrol_ldap_host_url'] = 'Chỉ ra máy chủ LDAP trong biểu mẫu URL giống như 
                                  \'ldap://ldap.myorg.com/\' 
                                 hoặc \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_version'] = 'Phiên bản của giao thức LDAP máy chủ của bạn đang sử dụng.';
$string['enrol_ldap_bind_dn'] = 'Nếu bạn muốn sử dụng một người dùng ràng buộc (bind-user) để tìm kiếm người dùng, 
                                 chỉ ra nó ở đây. Cái gì đó giống như 
                                 \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Mật khẩu đối với người dùng ràng buộc.';
$string['enrol_ldap_student_settings'] = 'Các thiết lập kết nạp học viên';
$string['enrol_ldap_teacher_settings'] = 'Các thiết lập kết nạp giáo viên';
$string['enrol_ldap_course_settings'] = 'Các thiết lập kết nạp cua học';
$string['enrol_ldap_student_contexts'] = 'Danh sách các ngữ cảnh mà ở đó sự kết nạp các nhóm cùng với các học viên
                                          được chỉ ra. Ngăn cách các ngữ cảnh khác nhau bởi dấu 
                                           \';\'. Ví dụ: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Thuộc tính thành viên, khi người dùng là thành viên
                                          (được kết nạp) của môt nhóm. Theo cách thông thường \'member\'
                                          hoặc \'memberUid\'.';
$string['enrol_ldap_teacher_contexts'] = 'Danh sách các ngữ cảnh mà ở đó các nhóm cùng với giáo viên
                                          được xác định. Ngăn cách các ngữ cảnh khác nhau bởi dấu 
                                           \';\'. Ví dụ: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Thuộc tính thành viên, khi người sử dụng là thành viên 
	                                      của một nhóm. Thông thường \'member\'
                                          hoặc \'memberUid\'.';
$string['enrol_ldap_autocreation_settings'] = 'Các thiết lập tạo cua học một cách tự động';
$string['enrol_ldap_autocreate'] = 'Các cua học có thể được tạo một cách tự động khi
	                                có số lượng người tham gia cua học mà nó chưa tồn tại trong 
                                    Moodle.';
$string['enrol_ldap_objectclass'] = 'Lớp đối tượng được sử dụng để tìm kiếm các cua học. Thông thường
                                     \'posixGroup\'.';
$string['enrol_ldap_category'] = 'Danh mục đối với các cua học được tạo tự động.';
$string['enrol_ldap_template'] = 'Tuỳ chọn: Các cua học được tạo một cách tự động có thể copy 
                                   các thiết lập của chúng từ một cua học tạm thời.';
$string['enrol_ldap_updatelocal'] = 'Cập nhật dữ liệu cục bộ ';
$string['enrol_ldap_editlock']    = 'Giá trị khoá';

$string['enrol_ldap_course_idnumber'] = 'Sắp xếp theo một định danh duy nhất trong LDAP, thông thường
                                         <em>cn</em> hoặc <em>uid</em>. Nó được đề nghị rằng 
                                         khoá giá trị nếu bạn đang sử dụng việc tạo cua học tự động.';                                         .';
$string['enrol_ldap_course_shortname'] = 'Tuỳ chọn: Trương LDAP nhận tên rút gọn từ.';
$string['enrol_ldap_course_fullname']  = 'Tuỳ chọn: Trường LDAP nhận tên đầy đủ từ.';
$string['enrol_ldap_course_summary']   = 'Tuỳ chọn: Trường LDAP nhận tóm tắt từ.';                                                                                                                                                
                                    
?>
