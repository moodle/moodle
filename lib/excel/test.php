<?php
  //require_once('OLEwriter.php');
  //require_once('BIFFwriter.php');
  require_once('Worksheet.php');
  require_once('Workbook.php');

  function HeaderingExcel($filename) {
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=$filename" );
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
      header("Pragma: public");
      }

  // HTTP headers
  HeaderingExcel('test.xls');

  // Creating a workbook
  $workbook = new Workbook("-");
  // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('First One');
// set the column width
  $worksheet1->set_column(1, 1, 40);
// set the row height
  $worksheet1->set_row(1, 20);
  $worksheet1->write_string(1, 1, "This worksheet's name is ".$worksheet1->get_name());
  $worksheet1->write(2,1,"http://www.phpclasses.org/browse.html/package/767.html");
  $worksheet1->write_number(3, 0, 11);
  $worksheet1->write_number(3, 1, 1);
  $worksheet1->write_string(3, 2, "by four is");
  $worksheet1->write_formula(3, 3, "=A4 * (2 + 2)");
  //$worksheet1->write_formula(3, 3, "= SUM(A4:B4)");
  $worksheet1->write(5, 4, "= POWER(2,3)");
  $worksheet1->write(4, 4, "= SUM(5, 5, 5)");
  //$worksheet1->write_formula(4, 4, "= LN(2.71428)");
  //$worksheet1->write_formula(5, 4, "= SIN(PI()/2)");

  // Creating the second worksheet
  $worksheet2 =& $workbook->add_worksheet();

  // Format for the headings
  $formatot =& $workbook->add_format();
  $formatot->set_size(10);
  $formatot->set_align('center');
  $formatot->set_color('white');
  $formatot->set_pattern();
  $formatot->set_fg_color('magenta');

  $worksheet2->set_column(0,0,15);
  $worksheet2->set_column(1,2,30);
  $worksheet2->set_column(3,3,15);
  $worksheet2->set_column(4,4,10);

  $worksheet2->write_string(1,0,"Id",$formatot);
  $worksheet2->write_string(1,1,"Name",$formatot);
  $worksheet2->write_string(1,2,"Adress",$formatot);
  $worksheet2->write_string(1,3,"Phone Number",$formatot);
  $worksheet2->write_string(1,4,"Salary",$formatot);

  $worksheet2->write(3,0,"22222222-2");
  $worksheet2->write(3,1,"John Smith");
  $worksheet2->write(3,2,"Main Street 100");
  $worksheet2->write(3,3,"02-5551234");
  $worksheet2->write(3,4,100);
  $worksheet2->write(4,0,"11111111-1");
  $worksheet2->write(4,1,"Juan Perez");
  $worksheet2->write(4,2,"Los Paltos 200");
  $worksheet2->write(4,3,"03-5552345");
  $worksheet2->write(4,4,110);
  // if you are writing a very long worksheet, you may want to use
  // write_xxx() functions, instead of write() for performance reasons.
  $worksheet2->write_string(5,0,"11111111-1");
  $worksheet2->write_string(5,1,"Another Guy");
  $worksheet2->write_string(5,2,"Somewhere 300");
  $worksheet2->write_string(5,3,"03-5553456");
  $worksheet2->write(5,4,108);


  // Calculate some statistics
  $worksheet2->write(7, 0, "Average Salary:");
  $worksheet2->write_formula(7, 4, "= AVERAGE(E4:E6)");
  $worksheet2->write(8, 0, "Minimum Salary:");
  $worksheet2->write_formula(8, 4, "= MIN(E4:E6)");
  $worksheet2->write(9, 0, "Maximum Salary:");
  $worksheet2->write_formula(9, 4, "= MAX(E4:E6)");

  //$worksheet2->insert_bitmap(0, 0, "some.bmp",10,10);

  $workbook->close();
?>