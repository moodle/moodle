<?PHP //$Id$

class block_blog_tags extends block_base {
    function init() {
        $this->title = get_string('blogtags', 'blog');
        $this->version = 2006032000;
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {

        global $CFG;

        $timewithin = time() - 7776000; // last 90 days
        $topentries = 20; // get the 20 most popular tags

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';


        /// Get a list of tags
        
        $sql  = 'SELECT t.*, COUNT(DISTINCT bt.id) as ct ';
        $sql .= "FROM {$CFG->prefix}tags as t, {$CFG->prefix}blog_tag_instance as bt ";
        $sql .= 'WHERE t.id = bt.tagid ';
        $sql .= "AND bt.timemodified > $timewithin ";
        $sql .= 'GROUP BY bt.tagid ';
        $sql .= 'ORDER BY ct DESC, t.text ASC ';
        $sql .= "LIMIT $topentries ";

        if ($tags = get_records_sql($sql)) {

        /// There are 2 things to do:
        /// 1. tags with the same count should have the same size class
        /// 2. however many tags we have should be spread evenly over the
        ///    20 size classes
        
            $totaltags  = count($tags);
            $currenttag = 0;

            $size = 20;
            $lasttagct = -1;
            
            $etags = array();
            foreach ($tags as $tag) {
            
                $currenttag++;

                if ($currenttag == 1) {
                    $lasttagct = $tag->ct;
                    $size = 20;
                } else if ($tag->ct != $lasttagct) {
                    $lasttagct = $tag->ct;
                    $size = 20 - ( (int)((($currenttag - 1) / $totaltags) * 20) );
                }
                
                $tag->class = "$tag->type s$size";
                $etags[] = $tag;

            }

        /// Now we sort the tag display order
            usort($etags, "blog_tags_sort");
            
        /// Finally we create the output
            foreach ($etags as $tag) {
                $link = $CFG->wwwroot.'/blog/index.php?courseid='.
                        $this->instance->pageid.'&filtertype=site&tagid='.$tag->id;
                $this->content->text .= '<a href="'.$link.'" class="'.
                                        $tag->class.'">'.$tag->text.'</a> ';
            }

        }
        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false);
    }

}

function blog_tags_sort($a, $b) {
    return strcmp($a->text, $b->text);
}

?>
