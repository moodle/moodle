<?php
global $CFG, $DB;
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
    $hostname = $_SERVER['HTTP_HOST'];
    $result = $DB->get_record_sql('SELECT * FROM {local_qubits_sites} WHERE hostname = :hostname ', array('hostname' => $hostname));
    $isvaliddomain = ($result->status == "1");
    if (!$isvaliddomain) {
        $html = render_not_found_html(); // Render Not found default content
        echo $html;
        exit;
    }
    $result->ismainsite = "no";
    $CFG->cursitesettings = $result;
}

if (SITE_MAIN_DOMAIN == $_SERVER['HTTP_HOST']) {
    $defaultrow = new stdClass;
    $defaultrow->id = "0";
    $defaultrow->name = "mainsite";
    $defaultrow->hostname = SITE_MAIN_DOMAIN;
    $defaultrow->color1 = "#5b3f36";
    $defaultrow->color2 = "#ced4da";
    $defaultrow->ismainsite = "yes";
    $cohort = $DB->get_record('cohort', array('idnumber'=>'maincohort'));
    $defaultrow->cohortid = !empty($cohort->id) ? $cohort->id : 0;
    $CFG->cursitesettings = $defaultrow;
}