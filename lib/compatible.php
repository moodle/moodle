<?php // $Id$ ?>

<html>
<head>
<title>Moodle Environment Test</title>
</head>
<body bgcolor=white>

<?php

function print_row($name, $value, $badcomment='') {
    echo "<tr>";
    echo "<th align=\"right\">$name</th>";
    echo "<td align=\"left\">$value</td>";
    if ($badcomment) {
        echo "<td align=\"left\"><font color=red>$badcomment</font></td>";
    } else {
        echo "<td align=\"left\"><font color=green>Looks good</font></td>";
    }
    echo "</tr>";
}

function ini_get_bool($ini_get_arg) {
/// This function makes the return value of ini_get consistent if you are
/// setting server directives through the .htaccess file in apache.
/// Current behavior for value set from php.ini On = 1, Off = [blank]
/// Current behavior for value set from .htaccess On = On, Off = Off
/// Contributed by jdell@unr.edu

    $temp = ini_get($ini_get_arg);

    if ($temp == "1" or strtolower($temp) == "on") {
        return true;
    }
    return false;
}

function check_php_version($version="4.1.0") {
/// Returns true is the current version of PHP is greater that the specified one
    $minversion = intval(str_replace(".", "", $version));
    $curversion = intval(str_replace(".", "", phpversion()));
    return ($curversion >= $minversion);
}

function check_gd_version() {
/// Hack to find out the GD version by parsing phpinfo output
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], "2.")) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], "1.")) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(8);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n",$phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>',$text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == "GD Version") {
                if (substr_count($parts[1], "2.0")) {
                    $parts[1] = "2.0";
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}



/////////////////////////////////////////////////////////////////////////////////////

    $error = 0;

    echo "<h2 align=\"center\">Moodle compatibility tester</h2>";

    echo "<table align=\"center\" border=1>";

/// Check that PHP is of a sufficient version

    if (!check_php_version("4.1.0")) {
        print_row("PHP Version", "Old", "Moodle requires PHP 4.1.0 or later");
    } else {
        print_row("PHP Version", "OK");
    }

/// Check some PHP server settings

    if (ini_get_bool('safe_mode')) {
        print_row("safe_mode", "On", "Moodle can not handle files properly with safe mode on");
        $error++;
    } else {
        print_row("safe_mode", "Off");
    }

    if (ini_get_bool('session.auto_start')) {
        print_row("session.auto_start", "On", "This should be Off");
        $error++;
    } else {
        print_row("session.auto_start", "Off");
    }

    if (ini_get_bool('magic_quotes_runtime')) {
        print_row("magic_quotes_runtime", "On", "This should be Off");
        $error++;
    } else {
        print_row("magic_quotes_runtime", "Off");
    }

    if (!ini_get_bool('file_uploads')) {
        print_row("file_uploads", "Off", "This should be On");
        $error++;
    } else {
        print_row("file_uploads", "On");
    }

    if (!is_readable(ini_get('session.save_path'))) {
        print_row("session.save_path", "Broken", "It seems your server does not support sessions");
        $error++;
    } else {
        print_row("session.save_path", "Works");
    }

    if (!$gdversion = check_gd_version())  {
        print_row("GD Library", "No", "The GD library should be present to process and create images");
        $error++;
    } else {
        print_row("GD Library", $gdversion);
    }


    echo "</table>";

    if ($error == 1) {
        echo "<h2 align=\"center\"><font color=red>$error error was found.  See <a href=\"http://moodle.org/doc\">http://moodle.org/doc</a></font></h2>";
    } else if ($error) {
        echo "<h2 align=\"center\"><font color=red>$error errors were found.  See <a href=\"http://moodle.org/doc\">http://moodle.org/doc</a></font></h2>";
    } else {
        echo "<h2 align=\"center\"><font color=green>Server looks good - clear to install!</font></a></h2>";
    }
?>
