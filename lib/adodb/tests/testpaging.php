<?php
/* 
V2.00 13 May 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. 
  Set tabs to 4 for best viewing.
    
  Latest version is available at http://php.weblogs.com/
*/

error_reporting(E_ALL);

$PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];

include_once('../adodb-pear.inc.php');
include_once('../tohtml.inc.php');
session_register('curr_page');

$db = NewADOConnection('mysql');
$db->debug = true;
//$db->Connect('localhost:4321','scott','tiger','natsoft.domain');
$db->Connect('localhost','root','','xphplens');

$num_of_rows_per_page = 7;
$sql = "select * from adoxyz ";

if (isset($HTTP_GET_VARS['next_page']))
	$curr_page = $HTTP_GET_VARS['next_page'];
if (empty($curr_page)) $curr_page = 1; ## at first page

$rs = $db->PageExecute($sql, $num_of_rows_per_page, $curr_page);
if (!$rs) die('Query Failed');

if (!$rs->EOF && (!$rs->AtFirstPage() || !$rs->AtLastPage())) {
	if (!$rs->AtFirstPage()) {
?>
<a href="<?php echo $PHP_SELF;?>?next_page=1">First page</a> &nbsp;
<a href="<?php echo $PHP_SELF,'?next_page=',$rs->AbsolutePage() - 1 ?>">Previous page</a> &nbsp;
<?php
	} else {
	echo " First Page &nbsp; Previous Page &nbsp; ";
	}
	if (!$rs->AtLastPage()) {
?>
<a href="<?php echo $PHP_SELF,'?next_page=',$rs->AbsolutePage() + 1 ?>">Next Page</a>
<?php
	} else
		print "Next Page";
	rs2html($rs);
}


?>
