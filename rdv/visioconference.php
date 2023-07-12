<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez vous</title>
</head>
<body>
    <?php
    require_once('../config.php');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $PAGE->set_heading('Visioconférence partenaire');

    // Require login to access the page
    require_login();

    // Check if user is a guest
    if (isguestuser()) {
        // User is a guest, display message and exit script
        header('Location: https://moodle.infans.fr/login/index.php');
        exit();
    }
        echo $OUTPUT->header();
    ?>
    <section>
        <iframe  src="https://www.smartagenda.fr/pro/infans/rendez-vous/"></iframe> 
    </section>
    <?php
    echo $OUTPUT->footer();    
    ?>
<style>
section {
    position: relative;
    height: 100vh; /* Sets the height of the section to the height of the viewport */
    overflow: hidden;
}

iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    scrollbar-width: none; /* For Firefox */
    -ms-overflow-style: none; /* For Internet Explorer and Edge */
}

iframe::-webkit-scrollbar {
    display: none; /* For Chrome, Safari, and Opera */
}
<style>
section {
    position: relative;
    height: 100vh; /* Sets the height of the section to the height of the viewport */
    overflow: hidden;
}

iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    scrollbar-width: none; /* For Firefox */
    -ms-overflow-style: none; /* For Internet Explorer and Edge */
}

iframe::-webkit-scrollbar {
    display: none; /* For Chrome, Safari, and Opera */
}
</style>
</body>
</html>

