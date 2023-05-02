<?php
if ($PAGE->theme->settings->fonts_source ==1) {
	$font_languages = $PAGE->theme->settings->font_languages;
	if ($font_languages != '') {$font_languages = '&subset='.$font_languages;}
	$bodyfontsetting = $PAGE->theme->settings->font_body;
	$headingfontsetting = $PAGE->theme->settings->font_heading;
	$bodyweight = 400;
	$headingweight = 400;

    switch ($bodyfontsetting) {
    case "1":
		$bodyfont='Open+Sans';
		break;
	case "2":
		$bodyfont='Arimo';
		break;
	case "3":
		$bodyfont='Arvo';
		break;
	case "4":
		$bodyfont='Bree+Serif';
		break;
	case "5":
		$bodyfont='Cabin';
		break;
	case "6":
		$bodyfont='Cantata+One';
		break;
	case "7":
		$bodyfont='Crimson+Text';
		break;
	case "8":
		$bodyfont='Droid+Sans';
		break;
	case "9":
		$bodyfont='Droid+Serif';
		break;
	case "10":
		$bodyfont='Gudea';
		break;
	case "11":
		$bodyfont='Imprima';
		break;
	case "12":
		$bodyfont='Lekton';
		break;
	case "13":
		$bodyfont='Nunito';
		break;
	case "14":
		$bodyfont='Montserrat';
		break;
	case "15":
		$bodyfont='Playfair+Display';
		break;
	case "16":
		$bodyfont='Pontano+Sans';
		break;
	case "17":
		$bodyfont='PT+Sans';
		break;
    case "18":
		$bodyfont='Raleway';
		break;
    case "22":
		$bodyfont='Roboto';
		break; 
	case "19":
		$bodyfont='Ubuntu';
		break;
    case "20":
		$bodyfont='Vollkorn';
		break;
    case "21":
		$bodyfont='Work+Sans';
		break;
	default:
		$bodyfont='Open+Sans';
	}

	switch ($headingfontsetting) {
	case "1":
		$headingfont='Open+Sans';
		$headingweight = 700;
		break;
	case "2":
		$headingfont='Abril+Fatface';
		break;
	case "3":
		$headingfont='Arimo';
		$headingweight = 700;
		break;
	case "4":
		$headingfont='Arvo';
		$headingweight = 700;
		break;
	case "5":
		$headingfont='Bevan';
		break; 
	case "6":
		$headingfont='Bree+Serif';
		break;
	case "7":
		$headingfont='Cabin';
		$headingweight = 700;
		break;
	case "8":
		$headingfont='Cantata+One';
		break;
	case "9":
		$headingfont='Crimson+Text';
		$headingweight = 700;
		break;
	case "10":
		$headingfont='Encode+Sans';
		$headingweight = 700;
		break;
	case "11":
		$headingfont='Enriqueta';
		$headingweight = 700;
		break;
	case "12":
		$headingfont='Gudea';
		$headingweight = 700;
		break;
	case "13":
		$headingfont='Imprima';
		break;
	case "14":
		$headingfont='Josefin+Sans';
		$headingweight = '700';
		break;
	case "15":
		$headingfont='Lekton';
		$headingweight = 700;
		break;
	case "16":
		$headingfont='Lobster';
		break;
	case "17":
		$headingfont='Nunito';
		$headingweight = 700;
		break;
	case "18":
		$headingfont='Montserrat';
		$headingweight = 700;
		break;
	case "19":
		$headingfont='Pacifico';
		break;
	case "20":
		$headingfont='Playfair+Display';
		$headingweight = 700;
		break;
	case "21":
		$headingfont='Pontano+Sans';
		break;
	case "22":
		$headingfont='PT+Sans';
		$headingweight = 700;
		break;
    case "23":
		$headingfont='Raleway';
		$headingweight = 500;
		break;
    case "28":
		$headingfont='Roboto';
		$headingweight = 500;
		break; 
	case "24":
		$headingfont='Sansita+One';
		break;
	case "25":
		$headingfont='Ubuntu';
		$headingweight = 700;
		break;
    case "26":
		$headingfont='Vollkorn';
		$headingweight = 700;
		break;
    case "27":
		$headingfont='Work+Sans';
		$headingweight = 700;
		break;
	default:
		$headingfont='Open+Sans';
		$headingweight = 700;
	} ?>
<link href="https://fonts.googleapis.com/css?family=<?php echo $bodyfont.':'.$bodyweight.'%7C'.$headingfont.':'.$headingweight.$font_languages;?>" rel="stylesheet">
<?php } ?>

<?php if ($PAGE->theme->settings->use_fa5 == 1) { ?>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" crossorigin="anonymous">
<?php } else if ($CFG->version < 2017051500) { ?>
	<script src="https://use.fontawesome.com/c85108fa98.js"></script>
<?php } ?>