    <html lang="fr">
        <head>
            <style>
                table {
                    border: 1px solid black;
                    border-collapse: collapse;
                }
                td {
                    border: 1px solid black;
                }
            </style>
        </head>
        <body>
        <?php  
        include_once('lib_rapport.php');

        include('INC/connexion.php'); 
        include_once('../trainingsessions/htmlrenderers.php');  
        include_once("../../config.php");

// defined('MOODLE_INTERNAL') || die();

		require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
		require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
		require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

       /* $sql_courses = "SELECT id, shortname FROM `mdl_course` WHERE id > 21 ORDER BY id";
        $requete = $bdd->prepare($sql_courses);
        $requete->execute();*/


        ?>

        <p>&nbsp;</p>
        <?php

        function temps_activite ($id, $course) {

			$logs = use_stats_extract_logs(1514761200, time(), $id, $course);
			$aggregate = use_stats_aggregate_logs($logs, 1514761200, time());
			$weekaggregate = use_stats_aggregate_logs($logs, time() - WEEKSECS, time());



			if (empty($aggregate['sessions'])) {
		    $aggregate['sessions'] = array();
			}

			// Get course structure.

			$coursestructure = report_trainingsessions_get_course_structure($course, $items);
			// Time period form.

			$str = '';
			$dataobject = report_trainingsessions_print_html($str, $coursestructure, $aggregate, $done);

			// var_dump($dataobject);
			if (empty($dataobject)) {
			    $dataobject = new stdClass();
			}


			$dataobject->items = $items;
			$dataobject->done = $done;

			if ($dataobject->done > $items) {
			    $dataobject->done = $items;
			}

			// In-activity.

			return @$aggregate['coursetotal'][$course]->elapsed;
		}

        //if( isset($_POST['validation']) AND ( isset($_POST['selectGroup']) OR isset($_POST['selectDate'])))  {
        if( isset($_GET['selectGroup']) )  {
			?>
			<table>
				<tr>
					<th>id</th>
					<th>username</th>
					<th>firstname</th>
					<th>lastname</th>
					<th>email</th>
					<th>telephone</th>
					<th>Temps equiv Formation</th>
					<th>Groupe</th>
					<!--<th>Formation</th>-->
				</tr>
			<tbody> 
			<?php

            $array_group = explode(";", $_GET['selectGroup']);

            $courseid = (int)($_POST['selectCourse']);
               
			// FRANCK : 21/09/2017 ajout du telephone
            //$sql_utilisateur =  "SELECT user.id, user.username, user.email, user.lastname, user.firstname,  groups.courseid, groups.name FROM mdl_user AS user 
            $sql_utilisateur =  "SELECT user.id, user.username, user.email, user.lastname, user.firstname, user.phone1, groups.courseid, groups.name FROM mdl_user AS user 
                                INNER JOIN mdl_groups_members as groupmember ON user.id = groupmember.userid
                                INNER JOIN mdl_groups AS groups ON groupmember.groupid = groups.id
                                WHERE user.id NOT IN (377, 1159, 1468) AND groups.name REGEXP '(_";

            // 22/11/17 retrait du filtre sur courseid

            for ($i=0; isset($array_group[$i]) ; $i++) { 
                $sql_utilisateur .= $array_group[$i];
                if (isset($array_group[$i+1])) $sql_utilisateur .= '|';
            }

            $sql_utilisateur .= ")$' AND groups.timecreated > 1483225200  GROUP BY user.id ORDER BY groups.name" ;   

            
			$reponse2 = $bdd->prepare($sql_utilisateur);
			
			// 22/11/17 retrait du filtre sur courseid
			//$reponse2->execute(array($courseid));

			$reponse2->execute();

			$lien_total = "etat_temps_liste2.php?total=1";
			$i=0;
			while($donnees = $reponse2->fetch())    {      
			
			
				$temps_eq=temps_activite($donnees['id'], $donnees['courseid']);
			

			// $temps_eq=temps_equivalent_activite($donnees['id'], $donnees['courseid']);
			//$temps_eq=temps_equivalent($donnees['id'], $donnees['courseid']);
				
				

				$class = 'black';
				
				switch ($donnees['courseid']) {
					case '23':
						$length = 40*3600;
						break;

					case '24':
						$length = 24*3600;
						break;

					case '26':
						$length = 16*3600;
						break;

					case '27':
						$length = 24*3600;
						break;

					case '28':
						$length = 40*3600;
						break;

					case '29':
						$length = 40*3600;
						break;

					case '32':
                    	$length = 40*3600;
                    	break;

					case '33':
                    	$length = 40*3600;
                    	break;

					case '34':
                    	$length = 24*3600;
                    	break;
                
               		case '35':
                   		$length = 24*3600;
                    	break;
					
					default:
						$length = 1;
						break;
				}


				if($temps_eq < $length) {
					$class = 'red';
				}

				if($temps_eq < 1) {
					$class = 'zero';
				}

				$PreNomStagiaire = $donnees['lastname'];
				$NomStagiaire = $donnees['firstname'];
				$tef = seconds_to_hours($temps_eq);
				$RefSession = substr($donnees['name'] , -4);

				$sql_nom_course = "SELECT shortname FROM `mdl_course` WHERE id = ".$donnees['courseid'];
				$requete = $bdd->prepare($sql_nom_course);
				$requete->execute();

				$nom_course = $requete->fetch();



				
				echo '<tr>';
					echo '<td>' . $donnees['id'] . '</td>';
					echo '<td>' . $donnees['username'] . '</td>';
					//echo '<td><a href="etat_temps_liste.php?id='.$donnees['id'].'?course='.$courseid.'" target="_blank">'.$donnees['firstname'].'</a></td>';
					echo '<td><a href="etat_temps_liste2.php?total=0&id0='.$donnees['id'].'&course0='.$donnees['courseid'];
					echo '&PreNomStagiaire0=' . $PreNomStagiaire . '&NomStagiaire0=' . $NomStagiaire . '&tef0=' . $tef . '&RefSession0=' . $RefSession;
					echo '" target="_blank">'.$NomStagiaire.'</a></td>';
					echo '<td>' . $PreNomStagiaire . '</td>';
					echo '<td>' . $donnees['email'] . '</td>';
					echo '<td>' . $donnees['phone1'] . '</td>';
					echo '<td class="'.$class.'">' .  $tef . '</td>';
					echo '<td>' . $donnees['name'] . '</td>';
					//echo '<td>' . $nom_course['shortname'] . '</td>';
				echo '</tr>';

				if ($temps_eq != 0) {
					$lien_total .= '&id'.$i.'='.$donnees['id'].'&course'.$i.'='.$donnees['courseid'].'&PreNomStagiaire'.$i.'=' . $PreNomStagiaire . '&NomStagiaire'.$i.'=' . $NomStagiaire . '&tef'.$i.'=' . $tef . '&RefSession'.$i.'=' . $RefSession;
					$i++;
				}

				
			}
			// echo "<a href='".$lien_total."&nb=".$i."' target='_blank'>Toutes les stagiaires en 1 pdf</a>";
			?>
			</tbody>
			</table>
			<script type="text/javascript">
				var red = document.getElementsByClassName("red");
				var i;
				for (i = 0; i < red.length; i++) {
					
					red[i].style.color = "red";
				}

				var zero = document.getElementsByClassName("zero");
				var i;
				for (i = 0; i < zero.length; i++) {
					
					zero[i].style.backgroundColor = "black";
					zero[i].style.color = "white";
				}
			</script>

			<?php
        } // le if

        ?>


        </body>
        </html>