Fonts
-----

Đây là thư mục chứa các font được dùng khi tạo ảnh bằng text.

Font duy nhất được sử dụng hiện tại là default.ttf

Nếu một ngôn ngữ không chứa font ở đây thì font trong
/lang/en/fonts/default.ttf được dùng thay thế.

Chuỗi nhiều byte sẽ cần được giải mã, bởi vì Truetype 
routines yêu cầu các font ISO  hoặc các chuỗi Unicode.  Nếu có một file ở đây 
gọi lang_decode.php, chứa hàm 
lang_decode(), thì nó được sử dụng đối với mỗi chuỗi.

