<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formulaire</title>
    <link rel="stylesheet" href="mon_compteCss.css">
   
</head>
<?php

require_once('../config.php');
include_once('../devINFANS/codoli.php');
require_once('lib.php');
require_once('function.php');
require_once('Fonction_curl.php');
$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("Ma formation");
$PAGE->set_heading($_POST['titredeformation']);
$PAGE->set_url($CFG->wwwroot . '/formulaire.php');
echo $OUTPUT->header();
?>
<div class="block_formation">   
 
    <img type="image"
      name="image" src="<?php echo $_POST['image'];?>"
      />
    <!-- <p class="paragraphe_formulaire"><span class="col1_formulaire"><strong>Titre de la formation&nbsp;:</strong></span><span class="spacer_formulaire"> <?php //echo $_POST['titredeformation'];?></span></p> -->
    <p class="paragraphe_formulaire"><span class="col1_formulaire"><strong>Votre numéro de session&nbsp;:</strong></span><span class="spacer_formulaire"> <?php echo  $_POST['numerodesession'] ;  ?></span></p>
    <p class="paragraphe_formulaire"><span class="col1_formulaire"><strong>Votre numéro de parcours&nbsp;:</strong></span><span class="spacer_formulaire"> <?php echo  $_POST['numerodeparcours'] ;  ?></span></p>
    <p><strong> Document à télécharger&nbsp;:</strong></p>
    </div>
<div class="mod1">
    <?php
      demandeLiens();
    ?>
</div>
<?php 
  echo $OUTPUT->footer(); 
?>
