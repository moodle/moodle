<?PHP // $Id$

// Code fragment called by /courses/new.php
// Prints out a formatted table of all the new things that have happened 
// with this module (since the user's last login).  Suitable for students.

// Assumes $course and $USER are defined and page has been started.

    if ($journals = get_all_instances_in_course("journal", $course->id)) {

        foreach ($journals as $journal) {
            if ($entry = get_record_sql("SELECT * FROM journal_entries
                                          WHERE timemarked > '$USER->lastlogin' 
                                                AND journal = '$journal->id'
                                                AND user = $USER->id")) {
                echo "<P><B>Journal feedback: $journal->name</B></P>";
                echo "<FONT SIZE=2><UL>";
                echo "<LI><A HREF=\"/mod/journal/view.php?id=$journal->coursemodule\">Your journal entry</A> has some feedback!";
                echo ", ".userdate($entry->timemarked);
                echo "</UL></FONT>";
            }

            if ($entries = get_records_sql("SELECT j.*, u.id as userid, u.firstname, u.lastname 
                                            FROM journal_entries j, user u
                                            WHERE modified > '$USER->lastlogin' 
                                                  AND journal = '$journal->id'
                                                  AND j.user = u.id  ORDER by j.modified")) {
                echo "<P><B>Journal entries: <A HREF=\"/mod/journal/view.php?id=$journal->coursemodule\">$journal->name</A></B></P>";
                echo "<FONT SIZE=2><UL>";
                foreach ($entries as $entry) {
                    echo "<LI>$entry->firstname $entry->lastname edited their journal";
                    echo ", ".userdate($entry->modified);
                }
                echo "</UL></FONT>";
            }
        }
    }

?>
