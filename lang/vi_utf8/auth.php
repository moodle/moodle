<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005031000)


$string['auth_common_settings'] = 'Các thiết lập thông thường';
$string['auth_data_mapping'] = 'Bản đồ dữ liệu ';
$string['auth_dbdescription'] = 'Phương pháp này sử dụng một bảng cơ sở dũ liệu bên ngoài để kiêm tra khi nào một tên đăng nhập và mật khẩu đưa ra là hợp lệ. Nếu tài khoản là một cái mới thì thông tin từ các trường khác cũng có thể được copy sang Moodle.';
$string['auth_dbextrafields'] = 'Những trường này là tuỳ chọn. Bạn có thể chọn điền vào trước một số thông tin người sử dụng Moodle với thông tin từ <b> các trường cơ sở dữ liệu bên ngoài </b> mà bạn chỉ ra ở đây. <p>Nếu bạn để lại những chỗ trống, thì các thông tin mặc định sẽ đươc sử dụng.</p><p> Trong trường hợp khác, người sử dụng sẽ có khả năng soạn thảo tất cả các trường đó sau khi họ đăng nhập.</p>';
$string['auth_dbfieldpass'] = 'Tên của trường đang chứa các mật khẩu';
$string['auth_dbfielduser'] = 'Tên của trường đang chứa các tên đăng nhập';
$string['auth_dbhost'] = 'The computer hosting the database server.';
$string['auth_dbname'] = 'Tên của chính cơ sở dữ liệu ';
$string['auth_dbpass'] = 'Mật khẩu phù hợp với tên đăng nhập ở trên';
$string['auth_dbpasstype'] = 'Chỉ rõ định dạng mà trường mật khẩu đang sử dụng. Mã hoá MD5 thì hữu ích đối với sự kết nối tới các ứng dụng web thông thường khác giống như PostNuke';
$string['auth_dbtable'] = 'Tên của bảng trong cơ sở dữ liệu ';
$string['auth_dbtitle'] = 'Sử dụng một cơ sở dữ liệu bên ngoài';
$string['auth_dbtype'] = 'Kiểu cơ sở dữ liệu (Xem <a href=\"../lib/adodb/readme.htm#drivers\"> tài liệu hướng dẫn ADOdb </a> chi tiết)';
$string['auth_dbuser'] = 'Ghi tên đăng nhập truy cập cơ sở dữ liệu';
$string['auth_editlock'] = 'Khoá giá trị';
$string['auth_editlock_expl'] = '<p><b>Khoá giá trị:</b> Nếu có thể, sẽ ngăn chặn những người quản trị và những người sử dụng Moodle soạn thảo trường dữ liệu trực tiếp. Sử dụng tuỳ chọn này nếu bạn đang duy trì dữ liệu này trong hệ thống chứng thực bên ngoài. </p>';
$string['auth_emaildescription'] = 'Sự xác nhận Email là phương pháp chứng thực mặc đinh. Khi người dùng đăng ký, chọn tên đăng nhập và mật khẩu mới của riêng họ, một Email xác nhận được gửi tới địa chỉ Email của người dùng. Email này bao gồm một đường kết nối bảo đảm tới một trang mà ở đó người dùng có thể xác nhận tài khoản của họ. Các đăng nhập trong tương lai sẽ kiểm tra tên đăng nhập và mật khẩu lại, các giá trị được lưu trữ trong cơ sở dữ liệu của Moodle.';
$string['auth_emailtitle'] = 'Chứng thực dựa trên Email';
$string['auth_fccreators'] = 'Danh sách các nhóm mà các thành viên của chúng được cho  phép tạo các cua học mới. Ngăn cách các nhóm bởi dấu \';\'. Các tên phải được viết theo đúng chính tả như trên máy chủ loại nhất. Hệ thống phân biệt dạng chữ .';
$string['auth_fcdescription'] = 'Phương pháp này sử dụng một máy chủ hạng nhất để kiểm tra khi nào một tên đăng nhập và mật khẩu đưa ra hợp lệ.';
$string['auth_fcfppport'] = 'Cổng máy chủ(3333 là phổ biến nhất )';
$string['auth_fchost'] = 'Địa chỉ máy chủ hạng nhất. Sử dụng địa chỉ IP hoặc tên DNS.';
$string['auth_fcpasswd'] = 'Mật khẩu đối với tài khoản ở trên.';
$string['auth_fctitle'] = 'Sử dụng một máy chủ hạng nhất';
$string['auth_fcuserid'] = 'Userid for FirstClass account with privilege \'Subadministrator\' set.';
$string['auth_imapdescription'] = 'Phương pháp này sử dụng một máy chủ IMAP để kiểm tra khi nào tên đăng nhập và mật khẩu là hợp lệ.';
$string['auth_imaphost'] = 'Đia chỉ máy chủ IMAP. Sử dụng địa chỉ IP, không sử dụng tên DNS.';
$string['auth_imapport'] = 'Số cổng máy chủ IMAP. Thường thì nó là 143 hoặc 993.';
$string['auth_imaptitle'] = 'Sử dụng một máy chủ IMAP';
$string['auth_imaptype'] = 'Kiểu máy chủ IMAP. Các máy chủ IMAP có thể có các kiểu chứng thực và thoả thuận khác nhau.';
$string['auth_ldap_bind_dn'] = 'Nếu bạn muốn sử dụng ràng buộc người dùng để tìm kiếm các người dùng, chỉ ra nó ở đây. Đôi khi nó giống như \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Mật khẩu đối với ràng buộc người dùng .';
$string['auth_ldap_bind_settings'] = 'Các thiết lập ràng buộc ';
$string['auth_ldap_contexts'] = 'Danh sách các ngữ cảnh mà ở đó những người sử dụng được xác định. Ngăn cách các ngữ cảnh khác nhau bởi dấu \';\'. Ví dụ : \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Nếu bạn có khả năng tạo người dùng bằng sự chứng thực qua Email, chỉ ra ngữ cảnh mà ở đó những người dùng được tạo. Ngữ cảnh này nên khác nhau đối với những người dùng khác nhau để ngăn chặn các vấn đề bảo mật. Bạn không cần thêm ngữ cảnh này tới  ldap_context-variable, Moodle sẽ tìm kiếm người dùng từ ngữ cảnh này một cách tự động .<br/><b>Chú ý!</b> Bạn phải thay đổi hàm auth_user_create() trong file auth/ldap/lib.php để yêu cầu người dùng sáng tạo ra công việc ';
$string['auth_ldap_creators'] = 'Danh sách các nhóm mà các thành viên của chúng được cho phép để tạo các cua học mới. Ngăn cách các nhóm bởi \';\'. Theo cách thông thường thì điều đó giống như \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_expiration_desc'] = 'Chọn  Không để vô hiệu hoá kiểm tra mật khẩu hoặc LDAP để biết được thời gian vô hiệu hoá mật khẩu ngay tức khắc từ LDAP';
$string['auth_ldap_expiration_warning_desc'] = 'Số ngày trước khi cảnh báo vô hiệu hoá mật khẩu được đưa ra.';
$string['auth_ldap_expireattr_desc'] = 'Tuỳ chọn: Ghi đè ldap-attribute what stores password expiration time asswordAxpirationTime';
$string['auth_ldap_graceattr_desc'] = 'Tuỳ chọn: Ghi đè thuộc tính  gracelogin ';
$string['auth_ldap_gracelogins_desc'] = 'Enable LDAP gracelogin support. After password has expired user can login until gracelogin count is 0. Enabling this setting displays grace login message if password is exprired.';
$string['auth_ldap_host_url'] = 'Chỉ ra máy chủ LDAP trong biểu mẫu URL giống như \'ldap://ldap.myorg.com/\' hoặc \'ldaps://ldap.myorg.com/\' Ngăn cách các máy chủ bởi dấu \';\' để nhận được các trợ giúp khi bị thất bại. ';
$string['auth_ldap_login_settings'] = 'Các thiết lập đăng nhập.';
$string['auth_ldap_memberattribute'] = 'Tuỳ chọn: Ghi đè thuộc tính về người dùng, khi những người dùng có liên quan tới một nhóm. Thông thường là \'thành viên\'';
$string['auth_ldap_objectclass'] = 'Tuỳ chọn: Ghi đè lớp đối tượng sử dụng để chỉ định/tìm kiếm người dùng trên kiểu người dùng ldap_user_type. Thông thường bạn không cần thay đổi điều này.';
$string['auth_ldap_opt_deref'] = 'Quyết định bao nhiêu bí danh được sử dụng trong quá trình tìm kiếm. Chọn một cái trong số các giá trị sau: \"Không\" (LDAP_DEREF_NEVER) hoặc \"Có\" (LDAP_DEREF_ALWAYS) ';
$string['auth_ldap_passwdexpire_settings'] = ' Các thiết lập vô hiệu hoá mật khẩu LDAP.';
$string['auth_ldap_search_sub'] = 'Đặt giá trị khác 0. Nếu bạn muốn tìm kiếm người dùng từ ngữ cảnh phụ.';
$string['auth_ldap_server_settings'] = 'Các thiết lập máy chủ LDAP';
$string['auth_ldap_update_userinfo'] = 'Cập nhật thông tin người dùng (Tên, Họ, Địa chỉ..)từ LDAP tới Moodle. Chỉ ra các thiết lập \" Bản đồ dữ liệu \" khi bạn cần.';
$string['auth_ldap_user_attribute'] = 'Các tuỳ chọn: Ghi đè thuộc tính sử dụng để chỉ ra/tìm kiếm người dùng. Thông thường \'cn\'.';
$string['auth_ldap_user_settings'] = 'Các thiết lập tra cứu người dùng';
$string['auth_ldap_user_type'] = 'Chọn những người dùng thế nào được lưu trữ trong LDAP. Các thiết lập này cũng chỉ ra sự vô hiệu hoá đăng nhập như thế nào, tạo người dùng và các cuộc đăng nhập sẽ hoạt động như thế nào. ';
$string['auth_ldap_version'] = 'Phiên bản của LDAP giao thức máy chủ của bạn đang được sử dụng.';
$string['auth_ldapdescription'] = 'Phương pháp này đưa ra sự chứng thực lại một máy chủ LDAP bên ngoài.

                                  Nếu tên đăng nhập và mật khẩu đưa ra hợp lệ, Moodle tạo một người dùng mới 
                                  nhập vào trong cơ sở dữ liệu của nó. Môđun này có thể đọc thuộc tính người dùng từ LDAP 
                                  và  các trường được yêu cầu điền trước trong Moodle.
                                  Các đăng nhập sau chỉ sử dụng tên đăng nhập và mật khẩu được kiểm tra.';
$string['auth_ldapextrafields'] = 'Những trường này là tuỳ chọn. Bạn có thể chọn điền trước một số thông tin người dùng Moodle với thông tin từ <b> các trường LDAP</b> được chỉ ra ở đây. <p> Nếu bạn để các trường này trống, thì không có cái gì được chuyển đổi từ LDAP và các giá trị mặc định của Moodle sẽ được sử dụng để thay thế</p> <p> Trong trường hợp khác, người dùng sẽ có khả năng soạn thảo tất cả các trường này sau khi chúng bắt dầu.</p>';
$string['auth_ldaptitle'] = 'Sử dụng một máy chủ LDAP';
$string['auth_manualdescription'] = 'Phương pháp này xoá bỏ bất kỳ cách thức nào của những người sử dụng để tạo các tài khoản của riêng họ. Tất cả các tài khoản phải được tạo bằng tay bởi người quản trị.';
$string['auth_manualtitle'] = 'Chỉ các kê khai bằng tay';
$string['auth_multiplehosts'] = 'Nhiều máy chủ hoặc các địa chỉ có thể được chỉ ra (ví dụ host1.com;host2.com;host3.com) hoặc (eg xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_nntpdescription'] = 'Phương pháp này sử dụng một máy chủ NNTP để kiểm tra khi nào tên đăng nhập và mật khẩu đưa ra là hợp lệ.';
$string['auth_nntphost'] = 'Các địa chỉ máy chủ NNTP. Sử dụng địa chỉ IP, không sử dụng tên DNS.';
$string['auth_nntpport'] = 'Cổng máy chủ (119 là phổ biến nhất )';
$string['auth_nntptitle'] = 'Sử dụng một máy chủ NNTP';
$string['auth_nonedescription'] = 'Người dùng có thể đăng ký va tạo một tài khoản hợp lệ ngay lập tức, không chứng thực dựa vào một máy chủ ở bên ngoài và không xác nhận qua Email. Cẩn thận sử dụng tuỳ chọn này - suy nghĩ về các vấn đề bảo mật và quản trị điều này có thể là nguyên nhân.';
$string['auth_nonetitle'] = 'Không chứng thực';
$string['auth_pamdescription'] = 'Phương pháp này sử dụng PAM để truy cập tên đăng nhập tự nhiên trên máy chủ này. Bạn phải cài đặt <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\" target=\"_blank\">PHP4 PAM Authentication</a> theo thứ tự sử dụng môdun này.';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_passwordisexpired'] = 'Mật khẩu của bạn bị vô hiệu hoá. Bạn có muốn thay đổi mật khẩu của bạn bây giờ ?';
$string['auth_passwordwillexpire'] = 'Mật khẩu của bạn sẽ bị vô hiệu hóa trong $a ngày. Bạn có muốn thay đổi mật khẩu của bạn bây giờ không ?';
$string['auth_pop3description'] = 'Phương pháp này sử dụng một POP3 server để kiểm tra khi nào tên đăng nhập và mật khẩu đưa ra là hợp lệ.';
$string['auth_pop3host'] = 'Địa chỉ POP3 server. Sử dụng địa chỉ IP, không sử dụng tên DNS.';
$string['auth_pop3mailbox'] = 'Tên của hộp thư để thử một kết nối.  (thông thường INBOX)';
$string['auth_pop3port'] = 'Cổng máy chủ (110 là phổ biến nhât, 995 thì thông dụng đối với SSL)';
$string['auth_pop3title'] = 'Sử dụng một POP3 server';
$string['auth_pop3type'] = 'Kiểu máy chủ. Nếu máy chủ của bạn sử dụng bảo mật hợp lý, chọn pop3cert.';
$string['auth_updatelocal'] = 'Cập nhật dữ liệu cục bộ';
$string['auth_updatelocal_expl'] = '<p><b>Cập nhật dữ liệu cục bộ </b> Nếu có thể, trường này sẽ được cập nhật (từ chứng thực bên ngoài)every time the user logs in or there is a user synchronization. Fields set to update locally should be locked.</p>';
$string['auth_updateremote'] = 'Cập nhật dữ liệu bên ngoài';
$string['auth_updateremote_expl'] = '<p><b>Cập nhật dữ liệu bên ngoài:</b> Nếu có thể, chứng thực bên ngoài sẽ được cập nhật khi bản ghi người dùng được cập nhật. Các trường không nên được khoá để cho phép soạn thảo.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Chú ý:</b> Update data requires that you set binddn and bindpw to a bind-user with editing privileges to all the user records. It currently does not preserve multi-valued attributes, and will remove extra values on update. </p>';
$string['auth_user_create'] = 'Có khả năng tạo người dùng';
$string['auth_user_creation'] = 'Những người dùng mới có thể tạo tài khoản người dùng dựa trên nguồn chứng thực bên ngoài và được chứng thực qua Email. Nếu bạn có thể làm điều này, ghi nhớ các tuỳ chọn môđun cấu hình đối với việc tạo người dùng.';
$string['auth_usernameexists'] = 'Tên đăng nhập này đã tồn tại. Vui lòng chọn một cái tên khác.';
$string['authenticationoptions'] = 'Các tuỳ chọn chứng thực';
$string['authinstructions'] = 'Ở đây bạn có thể cung cấp các hướng dẫn cho người dùng của bạn, vì thế họ biết được tên đăng nhập và mật khẩu gì họ nên sử dụng. Văn bản bạn gõ vào ở đây sẽ xuất hiện trên trang đăng nhập. Nếu bạn để trống thì không có hướng dẫn nào sẽ được xuất hiện.';
$string['changepassword'] = 'Thay đổi URL mật khẩu';
$string['changepasswordhelp'] = 'Ở đây bạn có thể chỉ ra một vị trí mà ở đó người dùng của bạn có thể khôi phục hoặc thay đổi tên đăng nhập/mật khẩu của họ khi họ quên nó. Điều này sẽ được cung cấp cho người sử dụng với một nút trên trang đăng nhập và trang người dùng của họ. Nếu bạn để trống nút sẽ không được xuất hiện ra.';
$string['chooseauthmethod'] = 'Chọn một phương pháp chứng thực : ';
$string['forcechangepassword'] = 'Thuyết phục thay đổi mật khẩu ';
$string['forcechangepassword_help'] = 'Thuyết phục người dùng thay đổi mật khẩu dựa vào lần đăng nhập tiếp theo của họ theo Moodle.';
$string['forcechangepasswordfirst_help'] = 'Thuyết phục người dùng thay đổi mật khẩu dựa vào đăng nhập lần đầu của họ theo Moodle.';
$string['guestloginbutton'] = 'Nút khách đăng nhập';
$string['instructions'] = 'Các hướng dẫn';
$string['md5'] = 'Mã hoá MD5';
$string['plaintext'] = 'Văn bản thuần tuý';
$string['showguestlogin'] = 'Bạn có thể ẩn hoặc hiện nút đăng nhập khách trên trang đăng nhập.';
$string['stdchangepassword'] = 'Sử dụng trang thay đổi mật khẩu chuẩn';
$string['stdchangepassword_expl'] = 'Nếu hệ thống chứng thực bên ngoài cho phép mật khẩu thay đổi thông qua Moodle, chuyển điều này sang CÓ. Các thiết lập này ghi đè \' URL thay đổi mật khẩu \'.';
$string['stdchangepassword_explldap'] = 'Chú ý: Nó nhận ra rằng bạn sử dụng LDAP qua một đường mã hoá SSL (ldaps://) khi  máy chủ LDAP ở xa.';

?>
