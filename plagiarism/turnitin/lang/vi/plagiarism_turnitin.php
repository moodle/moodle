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
$string['pluginname'] = 'Phần bổ trợ chống đạo văn của Turnitin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Tác vụ của Phần bổ trợ Chống đạo văn của Turnitin';
$string['connecttesterror'] = 'Xảy ra lỗi khi kết nối với Turnitin, thông báo lỗi như sau:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Cho phép Turnitin';
$string['excludebiblio'] = 'Loại trừ Mục lục tham khảo';
$string['excludequoted'] = 'Loại trừ Tài liệu Trích dẫn';
$string['excludevalue'] = 'Loại trừ Trùng khớp Nhỏ';
$string['excludewords'] = 'Từ';
$string['excludepercent'] = 'Phần trăm';
$string['norubric'] = 'Không có thang đánh giá';
$string['otherrubric'] = 'Dùng thang đánh giá thuộc về một người hướng dẫn khác';
$string['attachrubric'] = 'Đính kèm một thang đánh giá vào bài tập này';
$string['launchrubricmanager'] = 'Mở Trình quản lý Thang đánh giá';
$string['attachrubricnote'] = 'Lưu ý: Học sinh sẽ có thể xem các thang đánh giá đính kèm và nội dung của mình trước khi nộp bài.';
$string['anonblindmarkingnote'] = 'Lưu ý: Cài đặt nhận xét ẩn danh riêng của Turnitin đã được gỡ bỏ. Turnitin sẽ sử dụng cài đặt nhận xét ẩn của Moodle để xác định cài đặt nhận xét ẩn danh.';
$string['transmatch'] = 'Đối chiếu Bản dịch';
$string["reportgen_immediate_add_immediate"] = "Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ ngay lập tức (nếu kho lưu trữ được thiết lập).";
$string["reportgen_immediate_add_duedate"] = "Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập).";
$string["reportgen_duedate_add_duedate"] = "Tạo báo cáo vào ngày đến hạn. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập).";
$string['launchquickmarkmanager'] = 'Mở Trình Quản lý Quickmark';
$string['launchpeermarkmanager'] = 'Mở Trình Quản lý Peermark';
$string['studentreports'] = 'Hiển thị Báo cáo Độc sáng cho Học sinh';
$string['studentreports_help'] = 'Cho phép bạn hiển thị các báo cáo độc sáng của Turnitin cho những người dùng là học sinh. Nếu bạn đặt là chấp thuận, báo cáo độc sáng do Turnitin tổng hợp sẽ được hiển thị cho học sinh xem.';
$string['submitondraft'] = 'Nộp tập tin khi vừa tải lên';
$string['submitonfinal'] = 'Nộp tập tin khi học sinh gửi để chấm điểm';
$string['draftsubmit'] = 'Khi nào thì tập tin sẽ được nộp cho Turnitin?';
$string['allownonor'] = 'Cho phép nộp tập tin có định dạng bất kỳ?';
$string['allownonor_help'] = 'Cài đặt này cho phép nộp tập tin có định dạng bất kỳ. Nếu tùy chọn được chọn là &#34;Đồng ý&#34;, các bài nộp sẽ được kiểm tra về tính độc sáng ở những chỗ có thể, bài nộp sẽ khả dụng để tải về và các công cụ phản hồi GradeMark sẽ khả dụng ở những phần có thể.';
$string['norepository'] = 'Không có Kho dữ liệu';
$string['standardrepository'] = 'Kho dữ liệu Chuẩn';
$string['submitpapersto'] = 'Lưu trữ Bài của Học sinh';
$string['institutionalrepository'] = 'Kho dữ liệu của Tổ chức (Nếu có)';
$string['checkagainstnote'] = 'Lưu ý: Nếu bạn không chọn "Có" cho ít nhất một trong các tùy chọn "Đối chiếu với..." bên dưới thì Báo cáo độc sáng sẽ KHÔNG được tổng hợp.';
$string['spapercheck'] = 'Đối chiếu với những bài đã lưu của học sinh';
$string['internetcheck'] = 'Đối chiếu với Internet';
$string['journalcheck'] = 'Đối chiếu với các tạp chí chuyên ngành, <br />tạp chí định kỳ và các ấn phẩm xuất bản';
$string['compareinstitution'] = 'Đối chiếu các tập tin đã nộp với các bài đã nộp vào bên trong tổ chức này';
$string['reportgenspeed'] = 'Tốc độ Tổng hợp Báo cáo';
$string['locked_message'] = 'Thông báo đã khóa';
$string['locked_message_help'] = 'Nếu có bất kỳ cài đặt nào bị khóa, thông báo này sẽ hiển thị để cho biết lý do.';
$string['locked_message_default'] = 'Cài đặt này bị khóa ở cấp độ trang mạng';
$string['sharedrubric'] = 'Thang đánh giá chung';
$string['turnitinrefreshsubmissions'] = 'Làm mới các Bài nộp';
$string['turnitinrefreshingsubmissions'] = 'Làm mới các Bài nộp';
$string['turnitinppulapre'] = 'Để gửi tệp đến Turnitin, trước tiên bạn phải chấp nhận EULA của chúng tôi. Nếu chọn không chấp nhận EULA của chúng tôi, bạn sẽ chỉ gửi được tệp đến Moodle. Vui lòng nhấp vào đây để đọc và chấp nhận Thỏa thuận.';
$string['noscriptula'] = '(Do bạn không cho phép Javascript, bạn sẽ phải làm mới một cách thủ công trang này để có thể thực hiện nộp bài sau khi đã chấp nhận Thỏa thuận Người Dùng Turnitin)';
$string['filedoesnotexist'] = 'Tập tin đã được xóa';
$string['reportgenspeed_resubmission'] = 'Bạn đã nộp bài tập này và một Báo cáo Tính Tương đồng đã được tạo cho bài bạn nộp. Nếu bạn chọn nộp lại bài, bài tập bạn đã nộp trước đây sẽ được thay thế và một báo cáo mới sẽ được tạo. Sau {$a->num_resubmissions} lần nộp lại bài tập, bạn sẽ cần phải đợi {$a->num_hours} giờ sau mỗi lần nộp lại bài để xem Báo cáo Tính Tương đồng mới.';

// Plugin settings.
$string['config'] = 'Cấu hình';
$string['defaults'] = 'Cài đặt Mặc định';
$string['showusage'] = 'Hiển thị Kết xuất Dữ liệu';
$string['saveusage'] = 'Lưu Kết xuất Dữ liệu';
$string['errors'] = 'Lỗi';
$string['turnitinconfig'] = 'Cấu hình phần Bổ trợ về Đạo văn của Turnitin';
$string['tiiexplain'] = 'Turnitin là một sản phẩm thương mại và bạn phải trả phí thuê bao đăng ký để sử dụng dịch vụ này. Để biết thêm thông tin, vui lòng xem <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Cho phép Turnitin';
$string['useturnitin_mod'] = 'Cho phép Turnitin cho {$a}';
$string['turnitindefaults'] = 'Cài đặt mặc định phần bổ trợ chống đạo văn của Turnitin';
$string['defaultsdesc'] = 'Các cài đặt sau đây là mặc định khi cho phép Turnitin bên trong một Mô-đun Hoạt động';
$string['turnitinpluginsettings'] = 'Cài đặt phần bổ trợ chống đạo văn của Turnitin';
$string['pperrorsdesc'] = 'Xảy ra sự cố khi cố gắng tải các tập tin dưới đây lên Turnitin. Để nộp lại, hãy chọn tập tin bạn muốn nộp lại rồi nhấn nút nộp lại. Sau đó, những tập tin này sẽ được xử lý vào lần chạy cron tiếp theo.';
$string['pperrorssuccess'] = 'Các tập tin bạn chọn đã được nộp lại và sẽ được cron xử lý.';
$string['pperrorsfail'] = 'Xảy ra sự cố với một số tập tin bạn chọn, không thể tạo một sự kiện cron mới cho chúng.';
$string['resubmitselected'] = 'Nộp lại Tập tin Đã chọn';
$string['deleteconfirm'] = 'Bạn có chắc muốn xóa bài nộp này không?\n\nThao tác này sẽ không thể hoàn tác.';
$string['deletesubmission'] = 'Xóa Bài nộp';
$string['semptytable'] = 'Không tìm thấy kết quả nào.';
$string['configupdated'] = 'Đã cập nhật cấu hình';
$string['defaultupdated'] = 'Đã cập nhật các cài đặt mặc định Turnitin';
$string['notavailableyet'] = 'Không khả dụng';
$string['resubmittoturnitin'] = 'Nộp lại cho Turnitin';
$string['resubmitting'] = 'Đang nộp lại';
$string['id'] = 'ID';
$string['student'] = 'Học sinh';
$string['course'] = 'Khóa học';
$string['module'] = 'Mô-đun';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Xem Báo cáo Độc Sáng';
$string['launchrubricview'] = 'Xem Thang đánh giá dùng để chấm điểm';
$string['turnitinppulapost'] = 'Tập tin của bạn chưa được nộp vào Turnitin. Vui lòng nhấp vào đây để chấp thuận EULA của chúng tôi.';
$string['ppsubmissionerrorseelogs'] = 'Tập tin này chưa được nộp cho Turnitin, vui lòng tư vấn quản trị viên hệ thống của bạn';
$string['ppsubmissionerrorstudent'] = 'Tập tin này chưa được nộp vào Turnitin, vui lòng tư vấn trợ giảng của bạn để biết thêm chi tiết';

// Receipts.
$string['messageprovider:submission'] = 'Thông báo về Biên lai Điện tử trong Phần bổ trợ Chống đạo văn của Turnitin';
$string['digitalreceipt'] = 'Biên lai Điện tử';
$string['digital_receipt_subject'] = 'Đây là Biên lai Điện tử Turnitin của bạn';
$string['pp_digital_receipt_message'] = '{$a->firstname} {$a->lastname} thân mến!<br /><br />Bạn đã nộp thành công tập tin <strong>{$a->submission_title}</strong> cho bài tập <strong>{$a->assignment_name}{$a->assignment_part}</strong> trong lớp <strong>{$a->course_fullname}</strong> trên <strong>{$a->submission_date}</strong>. Id bài nộp của bạn là <strong>{$a->submission_id}</strong>. Bạn có thể xem và in biên lai điện tử của mình từ nút in/tải về trong Trình xem Tài liệu.<br /><br />Cảm ơn bạn đã sử dụng Turnitin!<br /><br />Nhóm Turnitin';

// Paper statuses.
$string['turnitinid'] = 'ID Turnitin';
$string['turnitinstatus'] = 'Trạng thái Turnitin';
$string['pending'] = 'Đang chờ';
$string['similarity'] = 'Tương đồng';
$string['notorcapable'] = 'Không thể tổng hợp một Báo cáo Độc sáng cho tập tin này.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Học sinh đã xem bài vào:';
$string['student_notread'] = 'Học sinh vẫn chưa xem bài này.';
$string['launchpeermarkreviews'] = 'Mở Bình duyệt Peermark';

// Cron.
$string['ppqueuesize'] = 'Số lượng sự kiện trong hàng đợi sự kiện của Phần bổ trợ Chống đạo văn';
$string['ppcronsubmissionlimitreached'] = 'Sẽ không có thêm bài nộp nào được gửi tới Turnitin bằng lệnh thực thi cron này vì chỉ có {$a} được xử lý trên mỗi lần chạy';
$string['cronsubmittedsuccessfully'] = 'Bài nộp: {$a->title} (ID TII: {$a->submissionid}) cho bài tập {$a->assignmentname} trên khóa học {$a->coursename} đã được nộp thành công cho Turnitin.';
$string['pp_submission_error'] = 'Turnitin đã trả về một lỗi với bài nộp của bạn:';
$string['turnitindeletionerror'] = 'Xóa bài nộp Turnitin không thành công. Bản lưu cục bộ trong Moodle đã được gỡ bỏ nhưng không thể xóa bài nộp trong Turnitin.';
$string['ppeventsfailedconnection'] = 'Sẽ không có sự kiện nào được phần bổ trợ chống đạo văn của Turnitin xử lý bằng lệnh thực thi cron này vì không thể thiết lập kết nối tới Turnitin.';

// Error codes.
$string['tii_submission_failure'] = 'Vui lòng tư vấn trợ giảng hoặc quản trị viên hệ thống của bạn để biết thêm chi tiết';
$string['faultcode'] = 'Mã Lỗi';
$string['line'] = 'Dòng';
$string['message'] = 'Thông báo';
$string['code'] = 'Mã';
$string['tiisubmissionsgeterror'] = 'Xảy ra lỗi khi đang cố gắng lấy bài nộp cho bài tập này từ Turnitin';
$string['errorcode0'] = 'Tập tin này chưa được nộp cho Turnitin, vui lòng tư vấn quản trị viên hệ thống của bạn';
$string['errorcode1'] = 'Tập tin này chưa được gửi tới Turnitin vì tập tin không có đủ nội dung để tổng hợp Báo cáo Độc sáng.';
$string['errorcode2'] = 'Tập tin này sẽ không được nộp cho Turnitin vì vượt quá kích cỡ tối đa cho phép là {$a->maxfilesize}';
$string['errorcode3'] = 'Tập tin này chưa được nộp cho Turnitin vì người dùng chưa chấp thuận Thỏa thuận Giấy phép Người dùng Cuối của Turnitin.';
$string['errorcode4'] = 'Bạn phải tải lên một tập tin ở định dạng được hỗ trợ cho bài tập này. Định dạng tập tin được chấp nhận gồm: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps và .rtf';
$string['errorcode5'] = 'Tập tin này chưa được nộp cho Turnitin vì có sự cố khi tạo mô-đun trong Turnitin khiến các bài nộp bị chặn, vui lòng xem các bản ghi API của bạn để biết thêm thông tin';
$string['errorcode6'] = 'Tập tin này chưa được nộp cho Turnitin vì có sự cố khi chỉnh sửa cài đặt mô-đun trong Turnitin khiến không nộp được, vui lòng xem các bản ghi API của bạn để biết thêm thông tin';
$string['errorcode7'] = 'Tập tin này chưa được nộp cho Turnitin vì có sự cố khi tạo người dùng trong Turnitin khiến không nộp được, vui lòng xem các bản ghi API của bạn để biết thêm thông tin';
$string['errorcode8'] = 'Tập tin này chưa được nộp cho Turnitin vì có sự cố khi tạo tập tin tạm thời. Rất có thể nguyên nhân là do tên tập tin không hợp lệ. Vui lòng đổi tên tập tin và tải lên lại bằng chức năng Hiệu chỉnh Bài nộp.';
$string['errorcode9'] = 'Không thể nộp tập tin vì không có nội dung truy cập được trong vùng lưu trữ tập tin để nộp.';
$string['coursegeterror'] = 'Không thể lấy dữ liệu khóa học';
$string['configureerror'] = 'Bạn phải định cấu hình toàn bộ cho mô-đun này trong vai trò Quản trị viên trước khi sử dụng mô-đun trong một khóa học. Vui lòng liên lạc quản trị viên Moodle của bạn.';
$string['turnitintoolofflineerror'] = 'Chúng tôi hiện đang gặp một sự cố tạm thời. Vui lòng thử lại trong giây lát.';
$string['defaultinserterror'] = 'Xảy ra lỗi khi cố gắng chèn một giá trị cài đặt mặc định vào cơ sở dữ liệu';
$string['defaultupdateerror'] = 'Xảy ra lỗi khi cố gắng cập nhật một giá trị cài đặt mặc định vào cơ sở dữ liệu';
$string['tiiassignmentgeterror'] = 'Xảy ra lỗi khi cố gắng lấy một bài tập từ Turnitin';
$string['assigngeterror'] = 'Không thể lấy dữ liệu Turnitin';
$string['classupdateerror'] = 'Không thể cập nhật dữ liệu Lớp Turnitin';
$string['pp_createsubmissionerror'] = 'Xảy ra lỗi khi đang cố gắng tạo bài nộp trên Turnitin';
$string['pp_updatesubmissionerror'] = 'Xảy ra lỗi khi đang cố gắng nộp lại bài nộp trên Turnitin';
$string['tiisubmissiongeterror'] = 'Xảy ra lỗi khi đang cố gắng lấy bài nộp từ Turnitin';

// Javascript.
$string['closebutton'] = 'Đóng';
$string['loadingdv'] = 'Đang tải Trình xem Tài liệu Turnitin...';
$string['changerubricwarning'] = 'Thay đổi hoặc hủy đính kèm một thang đánh giá sẽ gỡ bỏ tất cả điểm đánh giá hiện có theo thang đánh giá đó khỏi các bài nộp trong bài tập này, kể cả các thẻ điểm đã được chấm trước đây. Điểm tổng quát cho những bài đã chấm trước đây sẽ được duy trì.';
$string['messageprovider:submission'] = 'Thông báo về Biên lai Điện tử trong Phần bổ trợ Chống đạo văn của Turnitin';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Trạng thái Turnitin';
$string['deleted'] = 'Đã xóa';
$string['pending'] = 'Đang chờ';
$string['because'] = 'Điều này là do quản trị viên đã xóa bài tập đang chờ khỏi hàng đợi xử lý và hủy bỏ bài nộp cho Turnitin.<br /><strong>Tập tin vẫn tồn tại trong Moodle, vui lòng liên lạc người hướng dẫn của bạn.</strong><br />Vui lòng xem thông tin dưới đây để biết mọi mã lỗi:';
$string['submitpapersto_help'] = '<strong>Không có Kho dữ liệu: </strong><br />Turnitin được hướng dẫn không lưu trữ các tài liệu đã nộp vào bất kỳ kho dữ liệu nào. Chúng tôi sẽ chỉ xử lý bài nộp giấy để thực hiện hoạt động kiểm tra tính tương đồng ban đầu.<br /><br /><strong>Kho dữ liệu Chuẩn: </strong><br />Turnitin sẽ chỉ lưu trữ một bản sao của tài liệu đã nộp trong Kho dữ liệu tiêu chuẩn. Bằng việc chọn tùy chọn này, Turnitin được hướng dẫn chỉ sử dụng tài liệu đã lưu trữ để thực hiện các hoạt động kiểm tra tính tương đồng đối với mọi tài liệu được nộp trong tương lai.<br /><br /><strong>Kho dữ liệu của Tổ chức (Nếu có): </strong><br />Việc chọn tùy chọn này sẽ hướng dẫn Turnitin chỉ thêm tài liệu đã nộp vào kho dữ liệu riêng của trường bạn. Các hoạt động kiểm tra tính tương đồng với tài liệu đã nộp sẽ do những người hướng dẫn khác trong trường của bạn thực hiện.';
$string['errorcode12'] = 'Tệp này chưa được gửi đến Turnitin vì tệp thuộc một bài tập trong khóa học đã bị xóa. ID hàng: ({$a->id}) | ID mô-đun khóa học: ({$a->cm}) | ID người dùng: ({$a->userid})';
$string['errorcode15'] = 'Tệp này chưa được gửi đến Turnitin vì không tìm thấy mô-đun hoạt động có chứa tệp này';
$string['tiiaccountconfig'] = 'Cấu hình Tài khoản Turnitin';
$string['turnitinaccountid'] = 'ID Tài khoản Turnitin';
$string['turnitinsecretkey'] = 'Khóa Chia sẻ Turnitin';
$string['turnitinapiurl'] = 'URL API Turnitin';
$string['tiidebugginglogs'] = 'Gỡ lỗi và Ghi nhật ký';
$string['turnitindiagnostic'] = 'Bật Cho phép Chế độ Chẩn đoán';
$string['turnitindiagnostic_desc'] = '<b>[Cẩn thận]</b><br />Bật chế độ Chẩn đoán chỉ để tìm các sự cố với API Turnitin.';
$string['tiiaccountsettings_desc'] = 'Vui lòng đảm bảo rằng các cài đặt này phù hợp với những cài đặt được định cấu hình trong tài khoản Turnitin của bạn, nếu không bạn có thể gặp sự cố với việc tạo bài tập và/hoặc bài nộp của học sinh.';
$string['tiiaccountsettings'] = 'Cài đặt Tài khoản Turnitin';
$string['turnitinusegrademark'] = 'Sử dụng GradeMark';
$string['turnitinusegrademark_desc'] = 'Chọn có hay không sử dụng GradeMark để chấm điểm các bài nộp.<br /><i>(Tùy chọn này chỉ khả dụng cho những ai có tài khoản được định cấu hình GradeMark)</i>';
$string['turnitinenablepeermark'] = 'Cho phép Bài tập Peermark';
$string['turnitinenablepeermark_desc'] = 'Chọn có hay không cho phép Bài tập PeerMark .<br/><i>(Tùy chọn này chỉ khả dụng cho những ai có tài khoản được định cấu hình Peermark)</i>';
$string['transmatch_desc'] = 'Quyết định có cho hay không cho phép Đối chiếu Bản dịch có sẵn như một cài đặt trên màn hình cài đặt bài tập.<br /><i>(Chỉ bật tùy chọn này nếu tài khoản Turnitin của bạn cho phép Đối chiếu Bản dịch)</i>';
$string['repositoryoptions_0'] = 'Cho phép các tùy chọn kho dữ liệu chuẩn cho người hướng dẫn';
$string['repositoryoptions_1'] = 'Cho phép người hướng dẫn mở rộng các tùy chọn kho lưu trữ';
$string['repositoryoptions_2'] = 'Nộp tất cả các bài vào kho lưu trữ chuẩn';
$string['repositoryoptions_3'] = 'Không nộp bất kỳ bài nào vào kho lưu trữ';
$string['turnitinrepositoryoptions'] = 'Các bài tập từ kho lưu trữ bài';
$string['turnitinrepositoryoptions_desc'] = 'Chọn các tùy chọn kho dữ liệu cho Bài tập Turnitin.<br /><i>(Kho dữ liệu của Tổ chức chỉ khả dụng cho những ai có tài khoản được bật tùy chọn này)</i>';
$string['tiimiscsettings'] = 'Cài đặt Phần bổ trợ Khác';
$string['pp_agreement_default'] = 'Tôi xác nhận bài nộp này là bài làm của chính tôi và tôi nhận trách nhiệm về mọi vi phạm bản quyền có thể xảy ra do nộp bài này.';
$string['pp_agreement_desc'] = '<b>[Tùy chọn]</b><br />Nhập một câu xác nhận chấp thuận cho các bài nộp.<br />(<b>Lưu ý:</b> Nếu thỏa thuận bị bỏ trống hoàn toàn thì học sinh sẽ không cần xác nhận chấp thuận trong quá trình nộp)';
$string['pp_agreement'] = 'Tuyên bố Miễn trừ trách nhiệm/ Thỏa thuận';
$string['studentdataprivacy'] = 'Cài đặt Bảo mật Dữ liệu Học sinh';
$string['studentdataprivacy_desc'] = 'Có thể định cấu hình các cài đặt sau đây để đảm bảo rằng dữ liệu cá nhân của học sinh sẽ không được chuyển đến Turnitin thông qua API.';
$string['enablepseudo'] = 'Cho phép Bảo mật Học sinh';
$string['enablepseudo_desc'] = 'Nếu chọn tùy chọn này, địa chỉ email của học sinh sẽ được chuyển thành một địa chỉ ảo tương đương cho các cuộc gọi vào API Turnitin.<br /><i>(<b>Lưu ý:</b> Không thể thay đổi tùy chọn này nếu dữ liệu người dùng Moodle bất kỳ đã được đồng bộ hóa với Turnitin)</i>';
$string['pseudofirstname'] = 'Tên Ảo của Học sinh';
$string['pseudofirstname_desc'] = '<b>[Tùy chọn]</b><br />Tên của học sinh sẽ hiển thị trên trình xem tài liệu Turnitin';
$string['pseudolastname'] = 'Họ Ảo của Học sinh';
$string['pseudolastname_desc'] = 'Họ của học sinh sẽ hiển thị trên trình xem tài liệu Turnitin';
$string['pseudolastnamegen'] = 'Tự động tạo Họ';
$string['pseudolastnamegen_desc'] = 'Nếu đặt thành có và họ ảo được đặt cho một trường hồ sơ người dùng thì trường đó sẽ tự động được gán một id duy nhất.';
$string['pseudoemailsalt'] = 'Chìa khóa Mã hóa Ảo';
$string['pseudoemailsalt_desc'] = '<b>[Tùy chọn]</b><br />Một chìa khóa tùy chọn để tăng độ phức tạp cho địa chỉ email Ảo đã tạo của Học sinh.<br />(<b>Lưu ý:</b> Chìa khóa này nên được giữ nguyên để duy trì các địa chỉ email ảo nhất quán)';
$string['pseudoemaildomain'] = 'Tên Miền Email Ảo';
$string['pseudoemaildomain_desc'] = '<b>[Tùy chọn]</b><br />Một tên miền tùy chọn cho các địa chỉ email ảo. (Mặc định thành @tiimoodle.com nếu bị để trống)';
$string['pseudoemailaddress'] = 'Địa chỉ Email Ảo';
$string['connecttest'] = 'Kiểm tra Kết nối với Turnitin';
$string['connecttestsuccess'] = 'Moodle đã nối kết thành công với Turnitin.';
$string['diagnosticoptions_0'] = 'Tắt';
$string['diagnosticoptions_1'] = 'Chuẩn';
$string['diagnosticoptions_2'] = 'Gỡ lỗi';
$string['repositoryoptions_4'] = 'Gửi tất cả bài giấy đến kho lưu trữ của trường học';
$string['turnitinrepositoryoptions_help'] = '<strong>Cho phép các tùy chọn kho dữ liệu chuẩn cho người hướng dẫn: </strong><br />Người hướng dẫn có thể hướng dẫn Turnitin thêm tài liệu vào kho dữ liệu tiêu chuẩn, kho dữ liệu riêng của trường hoặc không thêm vào kho dữ liệu.<br /><br /><strong>Cho phép người hướng dẫn mở rộng các tùy chọn kho lưu trữ: </strong><br />Tùy chọn này sẽ cho phép người hướng dẫn xem mục cài đặt bài tập để giúp học sinh hướng dẫn Turnitin nơi sẽ lưu trữ tài liệu. Học sinh có thể chọn thêm tài liệu của họ vào kho dữ liệu tiêu chuẩn dành cho học sinh hoặc vào kho dữ liệu riêng của trường.<br /><br /><strong>Nộp tất cả các bài vào kho lưu trữ chuẩn: </strong><br />Theo mặc định, tất cả tài liệu sẽ được thêm vào kho dữ liệu tiêu chuẩn dành cho học sinh.<br /><br /><strong>Không nộp bất kỳ bài nào vào kho lưu trữ: </strong><br />Các tài liệu sẽ chỉ được Turnitin dùng để thực hiện hoạt động kiểm tra ban đầu và để hiển thị cho người hướng dẫn chấm điểm.<br /><br /><strong>Gửi tất cả bài giấy đến kho lưu trữ của trường học: </strong><br />Turnitin được hướng dẫn lưu trữ tất cả bài nộp giấy trong kho dữ liệu bài nộp giấy của trường học. Các hoạt động kiểm tra tính tương đồng đối với tài liệu đã nộp sẽ chỉ do những người hướng dẫn khác trong trường học của bạn  thực hiện.';
$string['turnitinuseanon'] = 'Sử dụng Chấm điểm Ẩn danh';
$string['createassignmenterror'] = 'Xảy ra lỗi khi đang cố gắng tạo bài tập trên Turnitin';
$string['editassignmenterror'] = 'Xảy ra lỗi khi đang cố gắng hiệu chỉnh bài tập trên Turnitin';
$string['ppassignmentediterror'] = 'Không thể hiệu chỉnh Mô-đun {$a->title} (ID TII: {$a->assignmentid}) trên Turnitin, vui lòng xem bản ghi API của bạn để biết thêm thông tin';
$string['pp_classcreationerror'] = 'Không thể tạo lớp này trên Turnitin, vui lòng xem log API của bạn để biết thêm thông tin.';
$string['unlinkusers'] = 'Hủy kết nối Người dùng';
$string['relinkusers'] = 'Kết nối lại người dùng';
$string['unlinkrelinkusers'] = 'Hủy Kết nối / Kết nối lại Người Dùng Turnitin';
$string['nointegration'] = 'Không có Liên kết';
$string['sprevious'] = 'Trước';
$string['snext'] = 'Tiếp theo';
$string['slengthmenu'] = 'Xem _MENU_ mục';
$string['ssearch'] = 'Tìm:';
$string['sprocessing'] = 'Đang tải dữ liệu từ Turnitin...';
$string['szerorecords'] = 'Không có kết quả nào để hiển thị.';
$string['sinfo'] = 'Hiển thị _START_ đến _END_ trong _TOTAL_ mục.';
$string['userupdateerror'] = 'Không thể cập nhật dữ liệu người dùng';
$string['connecttestcommerror'] = 'Không thể kết nối với Turnitin. Hãy kiểm tra lại cài đặt URL API của bạn.';
$string['userfinderror'] = 'Xảy ra lỗi khi đang cố gắng tìm một người dùng trên Turnitin';
$string['tiiusergeterror'] = 'Xảy ra lỗi khi đang cố gắng lấy thông tin người dùng từ Turnitin';
$string['usercreationerror'] = 'Tạo Người dùng Turnitin không thành công';
$string['ppassignmentcreateerror'] = 'Không thể tạo Mô đun này trên Turnitin, vui lòng xem log API của bạn để biết thêm thông tin';
$string['excludebiblio_help'] = 'Cài đặt này cho phép người hướng dẫn chọn loại trừ phần văn bản xuất hiện trong mục lục tham khảo, các công trình có trích dẫn hoặc phần tài liệu tham khảo trong các bài của học sinh khỏi phép đối chiếu tìm sự trùng khớp khi tổng hợp các Báo cáo Độc sáng. Cài đặt này có thể hủy bỏ được trong từng Báo cáo Độc sáng.';
$string['excludequoted_help'] = 'Cài đặt này cho phép người hướng dẫn chọn loại trừ phần văn bản xuất hiện trong các trích dẫn khỏi phép đối chiếu tìm sự trùng khớp khi tổng hợp các Báo cáo Độc sáng. Cài đặt này có thể hủy bỏ được trong từng Báo cáo Độc sáng.';
$string['excludevalue_help'] = 'Cài đặt này cho phép người hướng dẫn chọn bỏ qua những trùng khớp không đủ dài (độ dài do người hướng dẫn quyết định) trong khi tổng hợp Báo cáo Độc sáng. Cài đặt này có thể được hủy bỏ trong từng Báo cáo Độc sáng riêng.';
$string['spapercheck_help'] = 'Đối chiếu với kho dữ liệu bài học sinh của Turnitin trong quá trình xử lý Báo cáo Độc sáng cho bài nộp. Chỉ số tương đồng có thể giảm nếu tùy chọn này được hủy chọn.';
$string['internetcheck_help'] = 'Đối chiếu với kho lưu trữ trên internet của Turnitin khi xử lý Báo cáo Độc sáng cho bài nộp. Chỉ số về tính tương đồng có thể giảm nếu hủy chọn tùy chọn này.';
$string['journalcheck_help'] = 'Đối chiếu với kho dữ liệu các tạp chí chuyên ngành, tạp chí định kỳ và các ấn phẩm xuất bản của Turnitin khi xử lý Báo cáo Độc sáng cho bài nộp. Chỉ số về tính tương đồng có thể giảm nếu hủy chọn tùy chọn này.';
$string['reportgenspeed_help'] = 'Có 3 tùy chọn cho cài đặt bài tập này: &#39;Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập).&#39;, &#39;Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ ngay lập tức (nếu kho lưu trữ được thiết lập).&#39; và &#39;Tạo báo cáo vào ngày đến hạn. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập)&#39;<br /><br />Tùy chọn &#39;Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập).&#39; sẽ tổng hợp Báo cáo Độc sáng ngay khi học sinh nộp bài. Khi chọn tùy chọn này, học sinh của bạn sẽ không thể nộp lại bài cho bài tập. <br /><br />Để cho phép học sinh được nộp bài lại, hãy chọn tùy chọn &#39;Tạo báo cáo ngay lập tức. Bài nộp sẽ được thêm vào kho lưu trữ ngay lập tức (nếu kho lưu trữ được thiết lập).&#39;. Tùy chọn này cho phép học sinh tiếp tục nộp lại bài tập cho đến hết ngày hạn nộp. Có thể mất đến 24 giờ để tổng hợp các Báo cáo Độc sáng cho các lần nộp lại. <br /><br />Tạo báo cáo vào ngày đến hạn. Bài nộp sẽ được thêm vào kho lưu trữ vào ngày đến hạn (nếu kho lưu trữ được thiết lập) sẽ chỉ tổng hợp Báo cáo Độc sáng vào ngày hạn nộp bài tập. Cài đặt này sẽ so sánh tất cả các bài nộp với nhau khi tổng hợp Báo cáo Độc sáng.';
$string['turnitinuseanon_desc'] = 'Chọn có hay không cho phép Chấm điểm Ẩn danh khi chấm các bài nộp.<br /><i>(Tùy chọn này chỉ khả dụng cho những ai có tài khoản được định cấu hình Chấm điểm Ẩn danh)</i>';
