<?PHP // $Id$

$RATING = array ("3" => "Outstanding",
                 "2" => "Satisfactory",
                 "1" => "Not satisfactory");

function journaldate($date) {
    return date("l, j F Y, g:i A T", $date);
}

?>
