<?php
global $CFG;
$fname = basename($_SERVER['PHP_SELF']);

function main_api_brandsettings() {
    global $CFG;
    static $abrandsettings;
    $defaultprimary = "#a88746"; // #a88746
    $defaultsecondary = "#9b51e0"; // #544B44
    $defaultaccent = "#000000"; // #F4F3EF

    // if (empty($abrandsettings)) {
    //     $brandingresult = @file_get_contents($CFG->mainbrandingapiurl);
    //     $abrandingresult = json_decode($brandingresult, true);
    //     if (!empty($abrandingresult) && $abrandingresult["success"] == true) {
    //         $abrandsettings["colorprimary"] = (isset($abrandsettings["colorprimary"]) && !empty($abrandsettings["colorprimary"])) ? $abrandsettings["colorprimary"] : $defaultprimary;
    //         $abrandsettings["colorsecondary"] = (isset($abrandsettings["colorsecondary"]) && !empty($abrandsettings["colorsecondary"])) ? $abrandsettings["colorsecondary"] : $defaultsecondary;
    //         $abrandsettings["coloraccent"] = (isset($abrandsettings["coloraccent"]) && !empty($abrandsettings["coloraccent"])) ? $abrandsettings["coloraccent"] : $defaultaccent;
    //        // $abrandsettings["logo"] = (isset($abrandsettings["kbl_branding"]["logo"]) && !empty($abrandsettings["kbl_branding"]["logo"])) ? $abrandsettings["kbl_branding"]["logo"] : "";
    //     } else {
    //         $abrandsettings = array(
    //             "logo" => "",
    //             "favicon" => "",
    //             "colorprimary" => $defaultprimary,
    //             "colorsecondary" => $defaultsecondary,
    //             "coloraccent" => $defaultaccent,
    //         );
    //     }
    // }
    $abrandsettings = array(
        "logo" => "https://test.qubitsedu.com/pluginfile.php/1/core_admin/logo/0x200/1674221642/Final%20logos%20combined-05.png",
        "favicon" => "",
        "colorprimary" => $defaultprimary,
        "colorsecondary" => $defaultsecondary,
        "coloraccent" => $defaultaccent,
    );
    return $abrandsettings;
}

function render_not_found_html() {
    global $CFG;
    $osettings = main_api_brandsettings();
    $logo = $osettings["logo"];
    $copytxt = "Qubits";
    $homeurl = $CFG->maindomainwwwroot . '/course';
    $cssurl = $CFG->maindomainwwwroot . '/local/qubitssite/css/';
    $html = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Not Found</title>
    <link rel="stylesheet" href="{$cssurl}bootstrap.min.css">
    <link rel="stylesheet" href="{$cssurl}styles.css">
</head>
<body>
    <nav class="topnavbar navbar navbar-expand-lg navbar-light shadow-sm bg-white  py-0 sticky-top">
        <a class="navbar-brand" href="{$homeurl}"><img src="{$logo}" alt="logo" class="logo" /></a>
    </nav>
    <div class="container">
        <div class="row page_error_msg" style="min-height:450px;">
            <div class="col-md-12">
                <div class="alert alert-danger text-center" role="alert">
                    <h1>404 Error</h1>
                    <h3>The site not found</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="footer aftersignin">
        <div class="container">
            <div class="text-center">
                <p class="m-0 text-center">Copyright © 2023 {$copytxt}. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
EOD;
    return $html;
}


if (SITE_MAIN_DOMAIN != $_SERVER['HTTP_HOST']) {
    $servername = $CFG->dbhost;
    $username = $CFG->dbuser;
    $password = $CFG->dbpass;
    $dbname = $CFG->dbname;

    $tbl_pfx = $CFG->prefix;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $tblname = $tbl_pfx . 'local_qubits_sites';
    $hostname = $_SERVER['HTTP_HOST'];
    $sql = "SELECT s.* FROM $tblname as s WHERE s.hostname = '$hostname' ";
    $result = $conn->query($sql);
    if (!$result || $result->num_rows == "0") {
        $html = render_not_found_html(); // Render Not found default content
        echo $html;
        exit;
    } else if ($result->num_rows > 0) {
        $row = $result->fetch_object();
        $isvaliddomain = ($row->status == "1");
        if (!$isvaliddomain) {
            $html = render_not_found_html(); // Render Not found default content
            echo $html;
            exit;
        }
        $CFG->cursitesettings = $row;
        $CFG->cursitesettings->ismainsite = "no";
    }
    $conn->close();
}

if (SITE_MAIN_DOMAIN == $_SERVER['HTTP_HOST']) {
    $defaultrow = new stdClass;
    $defaultrow->id = "0";
    $defaultrow->name = "mainsite";
    $defaultrow->hostname = SITE_MAIN_DOMAIN;
    $defaultrow->color1 = "#5b3f36";
    $defaultrow->color2 = "#ced4da";
    $defaultrow->ismainsite = "yes";
    $CFG->cursitesettings = $defaultrow;
}