<?PHP // $Id$

    $logs = $db->Execute("SELECT l.*, u.firstname, u.lastname, u.email FROM log l, user u WHERE l.user = u.id ORDER BY l.time ASC");

    echo "<TABLE>"
    while (! $logs->EOF) {
        $log = (object)$logs->fields;

        echo "<TR>";
        echo "<TD>".date("l, j F Y, g:i A T", $log->time);
        echo "<TD><A HREF=\"mailto:$log->email\">$log->firstname $log->lastname</A>";
        echo "<TD>$log->ip";
        echo "<TD>$log->url";
        echo "<TD>$log->message";
        echo "</TR>";

        $logs->MoveNext();
    }

    echo "</TABLE>";

?>
