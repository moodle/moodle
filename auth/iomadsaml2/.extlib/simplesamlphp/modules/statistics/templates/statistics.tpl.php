<?php
$this->data['header'] = 'SimpleSAMLphp Statistics';

$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/css/statistics.css").'" />'."\n";
$this->data['head'] .= '<link rel="stylesheet" media="screen" href="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/css/uitheme1.12.1/jquery-ui.min.css").'" />'."\n";

$this->includeAtTemplateBase('includes/header.php');

$this->data['htmlinject']['htmlContentPost'][] = '<script src="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/js/jquery-1.12.4.min.js").'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script src="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/js/jquery-ui-1.12.1.min.js").'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL("statistics/assets/js/statistics.js").'"></script>'."\n";

echo '<h1>'.$this->data['available_rules'][$this->data['selected_rule']]['name'].'</h1>';
echo '<p>'.$this->data['available_rules'][$this->data['selected_rule']]['descr'].'</p>';

// Report settings
echo '<table class="selecttime">';
echo '<tr><td class="selecttime_icon"><img src="'.SimpleSAML\Utils\HTTP::getBaseURL().
    'resources/icons/crystal_project/kchart.32x32.png" alt="Report settings" /></td>';

// Select report
echo '<td>';
echo '<form action="#">';

foreach ($this->data['post_rule'] as $k => $v) {
    echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />'."\n";
}

if (!empty($this->data['available_rules'])) {
    echo '<select name="rule">';
    foreach ($this->data['available_rules'] as $key => $rule) {
        if ($key === $this->data['selected_rule']) {
            echo '<option selected="selected" value="'.$key.'">'.$rule['name'].'</option>';
        } else {
            echo '<option value="'.$key.'">'.$rule['name'].'</option>';
        }
    }
    echo '</select>';
}
echo '</form></td>';

// Select delimiter
echo '<td class="td_right">';
echo '<form action="#">';

foreach ($this->data['post_d'] as $k => $v) {
    echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />'."\n";
}

if (!empty($this->data['availdelimiters'])) {
    echo '<select name="d">';
    foreach ($this->data['availdelimiters'] as $key => $delim) {
        $delimName = $delim;
        if (array_key_exists($delim, $this->data['delimiterPresentation'])) {
            $delimName = $this->data['delimiterPresentation'][$delim];
        }

        if ($key == '_') {
            echo '<option value="_">Total</option>';
        } elseif (isset($_REQUEST['d']) && $delim == $_REQUEST['d']) {
            echo '<option selected="selected" value="'.htmlspecialchars($delim).'">'.
                htmlspecialchars($delimName).'</option>';
        } else {
            echo '<option  value="'.htmlspecialchars($delim).'">'.htmlspecialchars($delimName).'</option>';
        }
    }
    echo '</select>';
}
echo '</form></td></tr>';

echo '</table>';

// End report settings


// Select time and date
echo '<table class="selecttime">';
echo '<tr><td class="selecttime_icon"><img src="'.SimpleSAML\Utils\HTTP::getBaseURL().
    'resources/icons/crystal_project/date.32x32.png" alt="Select date and time" /></td>';

if (isset($this->data['available_times_prev'])) {
    echo '<td><a href="'.$this->data['get_times_prev'].'">« Previous</a></td>';
} else {
    echo '<td class="selecttime_link_grey">« Previous</td>';
}

echo '<td class="td_right">';
echo '<form action="#">';

foreach ($this->data['post_res'] as $k => $v) {
    echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />'."\n";
}

if (!empty($this->data['available_timeres'])) {
    echo '<select name="res">';
    foreach ($this->data['available_timeres'] as $key => $timeresname) {
        if ($key == $this->data['selected_timeres']) {
            echo '<option selected="selected" value="'.$key.'">'.$timeresname.'</option>';
        } else {
            echo '<option  value="'.$key.'">'.$timeresname.'</option>';
        }
    }
    echo '</select>';
}
echo '</form></td>';

echo '<td class="td_left">';
echo '<form action="#">';

foreach ($this->data['post_time'] as $k => $v) {
    echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />'."\n";
}

if (!empty($this->data['available_times'])) {
    echo '<select name="time">';
    foreach ($this->data['available_times'] as $key => $timedescr) {
        if ($key == $this->data['selected_time']) {
            echo '<option selected="selected" value="'.$key.'">'.$timedescr.'</option>';
        } else {
            echo '<option  value="'.$key.'">'.$timedescr.'</option>';
        }
    }
    echo '</select>';
}
echo '</form></td>';

if (isset($this->data['available_times_next'])) {
    echo '<td class="td_right td_next_right"><a href="'.$this->data['get_times_next'].'">Next »</a></td>';
} else {
    echo '<td class="td_right selecttime_link_grey td_next_right">Next »</td>';
}

echo '</tr></table>';
echo '<div id="tabdiv">';
if (!empty($this->data['results'])) {
    echo '<ul class="tabset_tabs">
       <li><a href="#graph">Graph</a></li>
       <li><a href="#table">Summary table</a></li>
       <li><a href="#debug">Time serie</a></li>
    </ul>';
    echo '

    <div id="graph" class="tabset_content">';

    echo '<img src="'.htmlspecialchars($this->data['imgurl']).'" alt="Graph" />';

    echo '<form action="#">';
    echo '<p class="p_right">Compare with total from this dataset ';

    foreach ($this->data['post_rule2'] as $k => $v) {
        echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />'."\n";
    }

    echo '<select name="rule2">';
    echo '	<option value="_">None</option>';
    foreach ($this->data['available_rules'] as $key => $rule) {
        if ($key === $this->data['selected_rule2']) {
            echo '<option selected="selected" value="'.$key.'">'.$rule['name'].'</option>';
        } else {
            echo '<option value="'.$key.'">'.$rule['name'].'</option>';
        }
    }
    echo '</select></p></form>';

    echo '</div>'; // end graph content.

    /**
     * Handle table view - - - - - -
     */
    $classint = ['odd', 'even'];
    $i = 0;
    echo '<div id="table" class="tabset_content">';

    if (isset($this->data['pieimgurl'])) {
        echo '<img src="'.$this->data['pieimgurl'].'" alt="Pie chart" />';
    }
    echo '<table class="tableview"><tr><th class="value">Value</th><th class="category">Data range</th></tr>';

    foreach ($this->data['summaryDataset'] as $key => $value) {
        $clint = $classint[$i++ % 2];

        $keyName = $key;
        if (array_key_exists($key, $this->data['delimiterPresentation'])) {
            $keyName = $this->data['delimiterPresentation'][$key];
        }

        if ($key === '_') {
            echo '<tr class="total '.$clint.'"><td  class="value">'.
                $value.'</td><td class="category">'.$keyName.'</td></tr>';
        } else {
            echo '<tr class="'.$clint.'"><td  class="value">'.$value.
                '</td><td class="category">'.$keyName.'</td></tr>';
        }
    }

    echo '</table></div>';
    //  - - - - - - - End table view - - - - - - -

    echo '<div id="debug" >';
    echo '<table class="timeseries">';
    echo '<tr><th>Time</th><th>Total</th>';
    foreach ($this->data['topdelimiters'] as $key) {
        $keyName = $key;
        if (array_key_exists($key, $this->data['delimiterPresentation'])) {
            $keyName = $this->data['delimiterPresentation'][$key];
        }
        echo'<th>'.$keyName.'</th>';
    }
    echo '</tr>';

    $i = 0;
    foreach ($this->data['debugdata'] as $slot => $dd) {
        echo '<tr class="'.((++$i % 2) == 0 ? 'odd' : 'even').'">';
        echo '<td>'.$dd[0].'</td>';
        echo '<td class="datacontent">'.$dd[1].'</td>';

        foreach ($this->data['topdelimiters'] as $key) {
            echo '<td class="datacontent">'.(array_key_exists($key, $this->data['results'][$slot]) ?
                $this->data['results'][$slot][$key] : '&nbsp;').'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';


    echo '</div>'; // End debug tab content
} else {
    echo '<h4 align="center">'.$this->data['error'].'</h4>';
    echo '<p align="center"><a href="showstats.php">Clear selection</a></p>';
}
echo '</div>'; // End tab div

$this->includeAtTemplateBase('includes/footer.php');
