<?php // $Id$

class flexible_table {

    var $uniqueid        = NULL;
    var $attributes      = array();
    var $headers         = array();
    var $columns         = array();
    var $column_style    = array();
    var $column_class    = array();
    var $column_suppress = array();
    var $setup           = false;
    var $sess            = NULL;
    var $baseurl         = NULL;

    var $is_collapsible = false;
    var $is_sortable    = false;
    var $use_pages      = false;
    var $use_initials   = false;

    var $pagesize  = 30;
    var $currpage  = 0;
    var $totalrows = 0;

    function flexible_table($uniqueid) {
        $this->uniqueid = $uniqueid;
    }

    function sortable($bool) {
        $this->is_sortable = $bool;
    }

    function collapsible($bool) {
        $this->is_collapsible = $bool;
    }

    function pageable($bool) {
        $this->use_pages = $bool;
    }

    function initialbars($bool) {
        $this->use_initials = $bool;
    }

    function pagesize($perpage, $total) {
        $this->pagesize  = $perpage;
        $this->totalrows = $total;
        $this->use_pages = true;
    }

    function set_attribute($attribute, $value) {
        $this->attributes[$attribute] = $value;
    }

    function column_suppress($column) {
        if(isset($this->column_suppress[$column])) {
            $this->column_suppress[$column] = true;
        }
    }

    function column_class($column, $classname) {
        if(isset($this->column_class[$column])) {
            $this->column_class[$column] = ' '.$classname; // This space needed so that classnames don't run together in the HTML
        }
    }

    function column_style($column, $property, $value) {
        if(isset($this->column_style[$column])) {
            $this->column_style[$column][$property] = $value;
        }
    }

    function column_style_all($property, $value) {
        foreach(array_keys($this->columns) as $column) {
            $this->column_style[$column][$property] = $value;
        }
    }

    function define_baseurl($url) {
        $this->reseturl = $url;
        if(!strpos($url, '?')) {
            $this->baseurl = $url.'?';
        }
        else {
            $this->baseurl = $url.'&amp;';
        }
    }

    function define_columns($columns) {
        $this->columns = array();
        $this->column_style = array();
        $this->column_class = array();
        $colnum = 0;

        foreach($columns as $column) {
            $this->columns[$column]         = $colnum++;
            $this->column_style[$column]    = array();
            $this->column_class[$column]    = '';
            $this->column_suppress[$column] = false;
        }
    }

    function define_headers($headers) {
        $this->headers = $headers;
    }

    function make_styles_string(&$styles) {
        if(empty($styles)) {
            return '';
        }

        $string = ' style="';
        foreach($styles as $property => $value) {
            $string .= $property.':'.$value.';';
        }
        $string .= '"';
        return $string;
    }

    function make_attributes_string(&$attributes) {
        if(empty($attributes)) {
            return '';
        }
    
        $string = ' ';
        foreach($attributes as $attr => $value) {
            $string .= ($attr.'="'.$value.'" ');
        }
    
        return $string;
    }

    function setup() {
        global $SESSION, $CFG;

        if(empty($this->columns) || empty($this->uniqueid)) {
            return false;
        }
    
        if(!isset($SESSION->flextable) || isset($SESSION->flextable) && $SESSION->flextable->uniqueid != $this->uniqueid) {
            $SESSION->flextable = new stdClass;
            $SESSION->flextable->uniqueid = $this->uniqueid;
            $SESSION->flextable->collapse = array();
            $SESSION->flextable->sortby   = '';
            $SESSION->flextable->sortdir  = '';
            $SESSION->flextable->i_first  = '';
            $SESSION->flextable->i_last   = '';
        }
    
        $this->sess = &$SESSION->flextable;

        if(!empty($_GET['tshow']) && isset($this->columns[$_GET['tshow']])) {
            // Show this column
            $this->sess->collapse[$_GET['tshow']] = false;
        }
        else if(!empty($_GET['thide']) && isset($this->columns[$_GET['thide']])) {
            // Hide this column
            $this->sess->collapse[$_GET['thide']] = true;
            if($this->sess->sortby == $_GET['thide']) {
                $this->sess->sortby  = '';
                $this->sess->sortdir = '';
            }
        }
    
        // Now, update the column attributes for collapsed columns
        foreach(array_keys($this->columns) as $column) {
            if(!empty($this->sess->collapse[$column])) {
                $this->column_style[$column]['width'] = '10px';
            }
        }

        if(
            !empty($_GET['tsort']) && 
            (isset($this->columns[$_GET['tsort']]) || 
                (($_GET['tsort'] == 'firstname' || $_GET['tsort'] == 'lastname') && isset($this->columns['fullname']))
            ))
        {
            if(empty($this->sess->collapse[$_GET['tsort']])) {
                if($this->sess->sortby == $_GET['tsort']) {
                    $this->sess->sortdir = $this->sess->sortdir == SORT_ASC ? SORT_DESC : SORT_ASC;
                }
                else {
                    $this->sess->sortby  = $_GET['tsort'];
                    $this->sess->sortdir = SORT_ASC;
                }
            }
        }

        if(isset($_GET['tilast'])) {
            if(empty($_GET['tilast']) || is_numeric(strpos(get_string('alphabet'), $_GET['tilast']))) {
                $this->sess->i_last = $_GET['tilast'];
            }
        }

        if(isset($_GET['tifirst'])) {
            if(empty($_GET['tifirst']) || is_numeric(strpos(get_string('alphabet'), $_GET['tifirst']))) {
                $this->sess->i_first = $_GET['tifirst'];
            }
        }

        if(empty($this->baseurl)) {
            $getcopy  = $_GET;
            unset($getcopy['tshow']);
            unset($getcopy['thide']);
            unset($getcopy['tsort']);
            unset($getcopy['tifirst']);
            unset($getcopy['tilast']);
            unset($getcopy['page']);

            $strippedurl = strip_querystring(qualified_me());

            if(!empty($getcopy)) {
                $first = false;
                $querystring = '';
                foreach($getcopy as $var => $val) {
                    if(!$first) {
                        $first = true;
                        $querystring .= '?'.$var.'='.$val;
                    }
                    else {
                        $querystring .= '&amp;'.$var.'='.$val;
                    }
                }
                $this->reseturl =  $strippedurl.$querystring;
                $querystring .= '&amp;';
            }
            else {
                $this->reseturl =  $strippedurl.$querystring;
                $querystring = '?';
            }
    
            $this->baseurl = strip_querystring(qualified_me()) . $querystring;
        }

        // If it's "the first time" we 've been here, forget the previous initials filters
        if(qualified_me() == $this->reseturl) {
            $this->sess->i_first = '';
            $this->sess->i_last  = '';
        }

        $this->currpage = optional_param('page', 0);
        $this->setup = true;
    }

    function get_sql_sort() {
        if(!$this->setup) {
            return false;
        }
        if(!empty($this->sess->sortby)) {
            return $this->sess->sortby.($this->sess->sortdir == SORT_ASC ? ' ASC' : ' DESC');
        }
    }

    function get_page_start() {
        if(!$this->use_pages) {
            return '';
        }
        return $this->currpage * $this->pagesize;
    }

    function get_page_size() {
        if(!$this->use_pages) {
            return '';
        }
        return $this->pagesize;
    }

    function get_sql_where() {
        if(!isset($this->columns['fullname'])) {
            return '';
        }

        $LIKE = sql_ilike();
        if(!empty($this->sess->i_first) && !empty($this->sess->i_last)) {    
            return 'firstname '.$LIKE.' \''.$this->sess->i_first.'%\' AND lastname '.$LIKE.' \''.$this->sess->i_last.'%\'';
        }
        else if(!empty($this->sess->i_first)) {
            return 'firstname '.$LIKE.' \''.$this->sess->i_first.'%\'';
        }
        else if(!empty($this->sess->i_last)) {
            return 'lastname '.$LIKE.' \''.$this->sess->i_last.'%\'';
        }

        return '';
    }

    function print_html() {
        global $CFG;

        if(!$this->setup) {
            return false;
        }

        $colcount = count($this->columns);

        // Do we need to print initial bars?

        if($this->use_initials && isset($this->columns['fullname'])) {

            $strall = get_string('all');
            $alpha  = explode(',', get_string('alphabet'));

            // Bar of first initials

            echo '<div class="initialbar">'.get_string('firstname').' : ';
            if(!empty($this->sess->i_first)) {
                echo '<a href="'.$this->baseurl.'tifirst=">'.$strall.'</a>';
            } else {
                echo '<strong>'.$strall.'</strong>';
            }
            foreach ($alpha as $letter) {
                if ($letter == $this->sess->i_first) {
                    echo ' <strong>'.$letter.'</strong>';
                } else {
                    echo ' <a href="'.$this->baseurl.'tifirst='.$letter.'">'.$letter.'</a>';
                }
            }
            echo '</div>';
    
            // Bar of last initials

            echo '<div class="initialbar">'.get_string('lastname').' : ';
            if(!empty($this->sess->i_last)) {
                echo '<a href="'.$this->baseurl.'tilast=">'.$strall.'</a>';
            } else {
                echo '<strong>'.$strall.'</strong>';
            }
            foreach ($alpha as $letter) {
                if ($letter == $this->sess->i_last) {
                    echo ' <strong>'.$letter.'</strong>';
                } else {
                    echo ' <a href="'.$this->baseurl.'tilast='.$letter.'">'.$letter.'</a>';
                }
            }
            echo '</div>';

        }

        // End of initial bars code

        // Paging bar
        if($this->use_pages) {
            print_paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl);
        }

        if(empty($this->data)) {
            return;
        }


        $suppress_enabled = array_sum($this->column_suppress);
        $suppress_lastrow = NULL;
        // Start of main data table

        echo '<table'.$this->make_attributes_string($this->attributes).'>';

        echo '<tr>';
        foreach($this->columns as $column => $index) {
            $icon_hide = '';
            $icon_sort = '';

            if($this->is_collapsible) {
                if(!empty($this->sess->collapse[$column])) {
                    $icon_hide = ' <a href="'.$this->baseurl.'tshow='.$column.'"><img src="'.$CFG->pixpath.'/t/switch_plus.gif" title="'.get_string('show').' '.$this->headers[$index].'" /></a>';
                }
                else if($this->headers[$index] !== NULL) {
                    $icon_hide = ' <a href="'.$this->baseurl.'thide='.$column.'"><img src="'.$CFG->pixpath.'/t/switch_minus.gif" title="'.get_string('hide').' '.$this->headers[$index].'" /></a>';
                }
            }

            switch($column) {

                case 'fullname':
                if($this->is_sortable) {
                    $icon_sort_first = $icon_sort_last = '';
                    if($this->sess->sortby == 'firstname') {
                        if($this->sess->sortdir == SORT_ASC) {
                            $icon_sort_first = ' <img src="'.$CFG->pixpath.'/t/down.gif" />';
                        }
                        else {
                            $icon_sort_first = ' <img src="'.$CFG->pixpath.'/t/up.gif" />';
                        }
                    }
                    else if($this->sess->sortby == 'lastname') {
                        if($this->sess->sortdir == SORT_ASC) {
                            $icon_sort_last = ' <img src="'.$CFG->pixpath.'/t/down.gif" />';
                        }
                        else {
                            $icon_sort_last = ' <img src="'.$CFG->pixpath.'/t/up.gif" />';
                        }
                    }
                    $this->headers[$index] = '<a href="'.$this->baseurl.'tsort=firstname">'.get_string('firstname').'</a> '.$icon_sort_first.' / '.
                                          '<a href="'.$this->baseurl.'tsort=lastname">'.get_string('lastname').'</a> '.$icon_sort_last;
                }
                break;

                default:
                if($this->is_sortable) {
                    if($this->sess->sortby == $column) {
                        if($this->sess->sortdir == SORT_ASC) {
                            $icon_sort = ' <img src="'.$CFG->pixpath.'/t/down.gif" />';
                        }
                        else {
                            $icon_sort = ' <img src="'.$CFG->pixpath.'/t/up.gif" />';
                        }
                    }
                    $this->headers[$index] = '<a href="'.$this->baseurl.'tsort='.$column.'">'.$this->headers[$index].'</a>';
                }
            }
            
            if($this->headers[$index] === NULL) {
                echo '<th class="header c'.$index.$this->column_class[$column].'">&nbsp;</th>';
            }
            else if(!empty($this->sess->collapse[$column])) {
                echo '<th class="header c'.$index.$this->column_class[$column].'">'.$icon_hide.'</th>';
            }
            else {
                echo '<th class="header c'.$index.$this->column_class[$column].'" nowrap="nowrap"'.$this->make_styles_string($this->column_style[$column]).'>'.$this->headers[$index].$icon_sort.'<div class="commands">'.$icon_hide.'</div></th>';
            }

        }
        echo '</tr>';

        if(!empty($this->data)) {
            $oddeven = 1;
            $colbyindex = array_flip($this->columns);
            foreach($this->data as $row) {
                $oddeven = $oddeven ? 0 : 1;
                echo '<tr class="r'.$oddeven.'">';

                // If we have a separator, print it
                if($row === NULL && $colcount) {
                    echo '<td colspan="'.$colcount.'"><div class="tabledivider"></div></td>';
                }
                else {
                    foreach($row as $index => $data) {
                        if($index >= $colcount) {
                            break;
                        }
                        $column = $colbyindex[$index];
                        echo '<td class="cell c'.$index.$this->column_class[$column].'"'.$this->make_styles_string($this->column_style[$column]).'>';
                        if(empty($this->sess->collapse[$column])) {
                            if($this->column_suppress[$column] && $suppress_lastrow !== NULL && $suppress_lastrow[$index] === $data) {
                                echo '&nbsp;';
                            }
                            else {
                                echo $data;
                            }
                        }
                        else {
                            echo '&nbsp;';
                        }
                        echo '</td>';
                    }
                }
                echo '</tr>';
                if($suppress_enabled) {
                    $suppress_lastrow = $row;
                }
            }
        }

        echo '</table>';

    }

    function add_data($row) {
        if(!$this->setup) {
            return false;
        }
        $this->data[] = $row;
    }

    function add_separator() {
        if(!$this->setup) {
            return false;
        }
        $this->data[] = NULL;
    }

}

?>
