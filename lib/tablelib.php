<?php // $Id$

define('TABLE_VAR_SORT',   1);
define('TABLE_VAR_HIDE',   2);
define('TABLE_VAR_SHOW',   3);
define('TABLE_VAR_IFIRST', 4);
define('TABLE_VAR_ILAST',  5);
define('TABLE_VAR_PAGE',   6);

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
    var $request         = array();

    var $is_collapsible = false;
    var $is_sortable    = false;
    var $use_pages      = false;
    var $use_initials   = false;

    var $maxsortkeys = 2;
    var $pagesize    = 30;
    var $currpage    = 0;
    var $totalrows   = 0;
    var $sort_default_column = NULL;
    var $sort_default_order  = SORT_ASC;

    function flexible_table($uniqueid) {
        $this->uniqueid = $uniqueid;
        $this->request  = array(
            TABLE_VAR_SORT    => 'tsort',
            TABLE_VAR_HIDE    => 'thide',
            TABLE_VAR_SHOW    => 'tshow',
            TABLE_VAR_IFIRST  => 'tifirst',
            TABLE_VAR_ILAST   => 'tilast',
            TABLE_VAR_PAGE    => 'page'
        );
    }

    function sortable($bool, $defaultcolumn = NULL, $defaultorder = SORT_ASC) {
        $this->is_sortable = $bool;
        $this->sort_default_column = $defaultcolumn;
        $this->sort_default_order  = $defaultorder;
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

    function set_control_variables($variables) {
        foreach($variables as $what => $variable) {
            if(isset($this->request[$what])) {
                $this->request[$what] = $variable;
            }
        }
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

        if (!isset($SESSION->flextable)) {
            $SESSION->flextable = array();
        }

        if(!isset($SESSION->flextable[$this->uniqueid])) {
            $SESSION->flextable[$this->uniqueid] = new stdClass;
            $SESSION->flextable[$this->uniqueid]->uniqueid = $this->uniqueid;
            $SESSION->flextable[$this->uniqueid]->collapse = array();
            $SESSION->flextable[$this->uniqueid]->sortby   = array();
            $SESSION->flextable[$this->uniqueid]->i_first  = '';
            $SESSION->flextable[$this->uniqueid]->i_last   = '';
        }

        $this->sess = &$SESSION->flextable[$this->uniqueid];

        if(!empty($_GET[$this->request[TABLE_VAR_SHOW]]) && isset($this->columns[$_GET[$this->request[TABLE_VAR_SHOW]]])) {
            // Show this column
            $this->sess->collapse[$_GET[$this->request[TABLE_VAR_SHOW]]] = false;
        }
        else if(!empty($_GET[$this->request[TABLE_VAR_HIDE]]) && isset($this->columns[$_GET[$this->request[TABLE_VAR_HIDE]]])) {
            // Hide this column
            $this->sess->collapse[$_GET[$this->request[TABLE_VAR_HIDE]]] = true;
            if(array_key_exists($_GET[$this->request[TABLE_VAR_HIDE]], $this->sess->sortby)) {
                unset($this->sess->sortby[$_GET[$this->request[TABLE_VAR_HIDE]]]);
            }
        }

        // Now, update the column attributes for collapsed columns
        foreach(array_keys($this->columns) as $column) {
            if(!empty($this->sess->collapse[$column])) {
                $this->column_style[$column]['width'] = '10px';
            }
        }

        if(
            !empty($_GET[$this->request[TABLE_VAR_SORT]]) &&
            (isset($this->columns[$_GET[$this->request[TABLE_VAR_SORT]]]) ||
                (($_GET[$this->request[TABLE_VAR_SORT]] == 'firstname' || $_GET[$this->request[TABLE_VAR_SORT]] == 'lastname') && isset($this->columns['fullname']))
            ))
        {
            if(empty($this->sess->collapse[$_GET[$this->request[TABLE_VAR_SORT]]])) {
                if(array_key_exists($_GET[$this->request[TABLE_VAR_SORT]], $this->sess->sortby)) {
                    // This key already exists somewhere. Change its sortorder and bring it to the top.
                    $sortorder = $this->sess->sortby[$_GET[$this->request[TABLE_VAR_SORT]]] == SORT_ASC ? SORT_DESC : SORT_ASC;
                    unset($this->sess->sortby[$_GET[$this->request[TABLE_VAR_SORT]]]);
                    $this->sess->sortby = array_merge(array($_GET[$this->request[TABLE_VAR_SORT]] => $sortorder), $this->sess->sortby);
                }
                else {
                    // Key doesn't exist, so just add it to the beginning of the array, ascending order
                    $this->sess->sortby = array_merge(array($_GET[$this->request[TABLE_VAR_SORT]] => SORT_ASC), $this->sess->sortby);
                }
                // Finally, make sure that no more than $this->maxsortkeys are present into the array
                if(!empty($this->maxsortkeys) && ($sortkeys = count($this->sess->sortby)) > $this->maxsortkeys) {
                    while($sortkeys-- > $this->maxsortkeys) {
                        array_pop($this->sess->sortby);
                    }
                }
            }
        }

        // If we didn't sort just now, then use the default sort order if one is defined and the column exists
        if(empty($this->sess->sortby) && !empty($this->sort_default_column) && (isset($this->columns[$this->sort_default_column])
                                                                                || (in_array('fullname',$this->columns)
                                                                                    && in_array($this->sort_default_column,
                                                                                                array('firstname','lastname')))))  {
            $this->sess->sortby = array ($this->sort_default_column => ($this->sort_default_order == SORT_DESC ? SORT_DESC : SORT_ASC));
        }

        if(isset($_GET[$this->request[TABLE_VAR_ILAST]])) {
            if(empty($_GET[$this->request[TABLE_VAR_ILAST]]) || is_numeric(strpos(get_string('alphabet'), $_GET[$this->request[TABLE_VAR_ILAST]]))) {
                $this->sess->i_last = $_GET[$this->request[TABLE_VAR_ILAST]];
            }
        }

        if(isset($_GET[$this->request[TABLE_VAR_IFIRST]])) {
            if(empty($_GET[$this->request[TABLE_VAR_IFIRST]]) || is_numeric(strpos(get_string('alphabet'), $_GET[$this->request[TABLE_VAR_IFIRST]]))) {
                $this->sess->i_first = $_GET[$this->request[TABLE_VAR_IFIRST]];
            }
        }

        if(empty($this->baseurl)) {
            $getcopy  = $_GET;
            unset($getcopy[$this->request[TABLE_VAR_SHOW]]);
            unset($getcopy[$this->request[TABLE_VAR_HIDE]]);
            unset($getcopy[$this->request[TABLE_VAR_SORT]]);
            unset($getcopy[$this->request[TABLE_VAR_IFIRST]]);
            unset($getcopy[$this->request[TABLE_VAR_ILAST]]);
            unset($getcopy[$this->request[TABLE_VAR_PAGE]]);

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

        $this->currpage = optional_param($this->request[TABLE_VAR_PAGE], 0);
        $this->setup = true;
    }

    function get_sql_sort($uniqueid = NULL) {
        if($uniqueid === NULL) {
            // "Non-static" function call
            if(!$this->setup) {
               return false;
            }
            $sess = &$this->sess;
        }
        else {
            // "Static" function call
            global $SESSION;
            if(empty($SESSION->flextable[$uniqueid])) {
               return '';
            }
            $sess = &$SESSION->flextable[$uniqueid];
        }

        if(!empty($sess->sortby)) {
            $sortstring = '';
            foreach($sess->sortby as $column => $order) {
                if(!empty($sortstring)) {
                    $sortstring .= ', ';
                }
                $sortstring .= $column.($order == SORT_ASC ? ' ASC' : ' DESC');
            }
            return $sortstring;
        }
        return '';
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

    function get_initial_first() {
        if(!$this->use_initials) {
            return NULL;
        }

        return $this->sess->i_first;
    }

    function get_initial_last() {
        if(!$this->use_initials) {
            return NULL;
        }

        return $this->sess->i_last;
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

            echo '<div class="initialbar firstinitial">'.get_string('firstname').' : ';
            if(!empty($this->sess->i_first)) {
                echo '<a href="'.$this->baseurl.$this->request[TABLE_VAR_IFIRST].'=">'.$strall.'</a>';
            } else {
                echo '<strong>'.$strall.'</strong>';
            }
            foreach ($alpha as $letter) {
                if ($letter == $this->sess->i_first) {
                    echo ' <strong>'.$letter.'</strong>';
                } else {
                    echo ' <a href="'.$this->baseurl.$this->request[TABLE_VAR_IFIRST].'='.$letter.'">'.$letter.'</a>';
                }
            }
            echo '</div>';

            // Bar of last initials

            echo '<div class="initialbar lastinitial">'.get_string('lastname').' : ';
            if(!empty($this->sess->i_last)) {
                echo '<a href="'.$this->baseurl.$this->request[TABLE_VAR_ILAST].'=">'.$strall.'</a>';
            } else {
                echo '<strong>'.$strall.'</strong>';
            }
            foreach ($alpha as $letter) {
                if ($letter == $this->sess->i_last) {
                    echo ' <strong>'.$letter.'</strong>';
                } else {
                    echo ' <a href="'.$this->baseurl.$this->request[TABLE_VAR_ILAST].'='.$letter.'">'.$letter.'</a>';
                }
            }
            echo '</div>';

        }

        // End of initial bars code

        // Paging bar
        if($this->use_pages) {
            print_paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl, $this->request[TABLE_VAR_PAGE]);
        }

        if (empty($this->data)) {
            print_heading(get_string('nothingtodisplay'));
            return true;
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
                    // some headers contain < br/> tags, do not include in title
                    $icon_hide = ' <a href="'.$this->baseurl.$this->request[TABLE_VAR_SHOW].'='.$column.'"><img src="'.$CFG->pixpath.'/t/switch_plus.gif" title="'.get_string('show').' '.strip_tags($this->headers[$index]).'" alt="'.get_string('show').'" /></a>';
                }
                else if($this->headers[$index] !== NULL) {
                    // some headers contain < br/> tags, do not include in title
                    $icon_hide = ' <a href="'.$this->baseurl.$this->request[TABLE_VAR_HIDE].'='.$column.'"><img src="'.$CFG->pixpath.'/t/switch_minus.gif" title="'.get_string('hide').' '.strip_tags($this->headers[$index]).'" alt="'.get_string('hide').'" /></a>';
                }
            }

            $primary_sort_column = '';
            $primary_sort_order  = '';
            if(reset($this->sess->sortby)) {
                $primary_sort_column = key($this->sess->sortby);
                $primary_sort_order  = current($this->sess->sortby);
            }

            switch($column) {

                case 'fullname':
                if($this->is_sortable) {
                    $icon_sort_first = $icon_sort_last = '';
                    if($primary_sort_column == 'firstname') {
                        $lsortorder = get_string('asc');
                        if($primary_sort_order == SORT_ASC) {
                            $icon_sort_first = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.get_string('asc').'" />';
                            $fsortorder = get_string('asc');
                        }
                        else {
                            $icon_sort_first = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.get_string('desc').'" />';
                            $fsortorder = get_string('desc');
                        }
                    }
                    else if($primary_sort_column == 'lastname') {
                        $fsortorder = get_string('asc');
                        if($primary_sort_order == SORT_ASC) {
                            $icon_sort_last = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.get_string('asc').'" />';
                            $lsortorder = get_string('asc');
                        }
                        else {
                            $icon_sort_last = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.get_string('desc').'" />';
                            $lsortorder = get_string('desc');
                        }
                    } else {
                        $fsortorder = get_string('asc');
                        $lsortorder = get_string('asc');
                    }
                    $this->headers[$index] = '<a href="'.$this->baseurl.$this->request[TABLE_VAR_SORT].'=firstname">'.get_string('firstname').'<span class="accesshide">'.get_string('sortby').' '.get_string('firstname').' '.$fsortorder.'</span></a> '.$icon_sort_first.' / '.
                                          '<a href="'.$this->baseurl.$this->request[TABLE_VAR_SORT].'=lastname">'.get_string('lastname').'<span class="accesshide">'.get_string('sortby').' '.get_string('lastname').' '.$lsortorder.'</span></a> '.$icon_sort_last;
                }
                break;

                case 'userpic':
                    // do nothing, do not display sortable links      
                break;

                default:
                if($this->is_sortable) {
                    if($primary_sort_column == $column) {
                        if($primary_sort_order == SORT_ASC) {
                            $icon_sort = ' <img src="'.$CFG->pixpath.'/t/down.gif" alt="'.get_string('asc').'" />';
                            $localsortorder = get_string('asc');
                        }
                        else {
                            $icon_sort = ' <img src="'.$CFG->pixpath.'/t/up.gif" alt="'.get_string('desc').'" />';
                            $localsortorder = get_string('desc');
                        }
                    } else {
                        $localsortorder = get_string('asc');  
                    }
                    $this->headers[$index] = '<a href="'.$this->baseurl.$this->request[TABLE_VAR_SORT].'='.$column.'">'.$this->headers[$index].'<span class="accesshide">'.get_string('sortby').' '.$this->headers[$index].' '.$localsortorder.'</span></a>';
                }
            }

            if($this->headers[$index] === NULL) {
                echo '<th class="header c'.$index.$this->column_class[$column].'" scope="col">&nbsp;</th>';
            }
            else if(!empty($this->sess->collapse[$column])) {
                echo '<th class="header c'.$index.$this->column_class[$column].'" scope="col">'.$icon_hide.'</th>';
            }
            else {
                // took out nowrap for accessibility, might need replacement
                if (!is_array($this->column_style[$column])) {
                    // $usestyles = array('white-space:nowrap');
                    $usestyles = '';
                 } else {
                    // $usestyles = $this->column_style[$column]+array('white-space'=>'nowrap');
                    $usestyles = $this->column_style[$column];
                 }
                echo '<th class="header c'.$index.$this->column_class[$column].'" '.$this->make_styles_string($usestyles).' scope="col">'.$this->headers[$index].$icon_sort.'<div class="commands">'.$icon_hide.'</div></th>';
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

        // Paging bar
        if($this->use_pages) {
            print_paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl, $this->request[TABLE_VAR_PAGE]);
        }
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
