<?PHP // $Id$
include( "lib/psxlsgen.php" );

$myxls = new PhpSimpleXlsGen();
$myxls->totalcol = 2;
for ($i=0; $i<10; $i++) {
    $myxls->WriteText_pos($i, $i, "$i stuff");
}
$myxls->SendFile();
?>
