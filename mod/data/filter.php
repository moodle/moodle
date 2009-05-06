<?php // $Id$
    //
    // This function provides automatic linking to data contents of text
    // fields where these fields have autolink enabled.
    //
    // Original code by Williams, Stronk7, Martin D.
    // Modified for data module by Vy-Shane SF.

    function data_filter($courseid, $text) {
        global $CFG;

        static $nothingtodo;
        static $contentlist;

        if (!empty($nothingtodo)) {   // We've been here in this page already
            return $text;
        }

        // if we don't have a courseid, we can't run the query, so
        if (empty($courseid)) {
            return $text;
        }

        // Create a list of all the resources to search for. It may be cached already.
        if (empty($contentlist)) {
            // We look for text field contents only, and only if the field has
            // autolink enabled (param1).
            $sql = 'SELECT dc.id AS contentid, ' .
                   'dr.id AS recordid, ' .
                   'dc.content AS content, ' .
                   'd.id AS dataid ' .
                        'FROM '.$CFG->prefix.'data d, ' .
                        $CFG->prefix.'data_fields df, ' .
                        $CFG->prefix.'data_records dr, ' .
                        $CFG->prefix.'data_content dc ' .
                            "WHERE (d.course = '$courseid' or d.course = '".SITEID."')" .
                            'AND d.id = df.dataid ' .
                            'AND df.id = dc.fieldid ' .
                            'AND d.id = dr.dataid ' .
                            'AND dr.id = dc.recordid ' .
                            "AND df.type = 'text' " .
                            "AND " . sql_compare_text('df.param1', 1) . " = '1'";

            if (!$datacontents = get_records_sql($sql)) {
                return $text;
            }

            $contentlist = array();

            foreach ($datacontents as $datacontent) {
                $currentcontent = trim($datacontent->content);
                $strippedcontent = strip_tags($currentcontent);

                if (!empty($strippedcontent)) {
                    $contentlist[] = new filterobject(
                                            $currentcontent,
                                            '<a class="data autolink" title="'.
                                            $strippedcontent.'" href="'.
                                            $CFG->wwwroot.'/mod/data/view.php?d='. $datacontent->dataid .
                                            '&amp;rid='. $datacontent->recordid .'" '.$CFG->frametarget.'>',
                                            '</a>', false, true);
                }
            } // End foreach
        }
        return  filter_phrases($text, $contentlist);  // Look for all these links in the text
    }

?>
