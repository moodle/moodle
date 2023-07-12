<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

include('../../report/trainingsessions/INC/connexion.php');

define("SUCCES_AFFEC", "ALTERCAMPUS - Mise à jour de la session");


$array_header = array(
    'Content-Type: application/json',
    'token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhYmFzZSI6ImFsdGVyY2FtcHVzdjRmb3JtYXNzbWF0IiwiaWF0IjoxNTgyODIzNTcwfQ.JQPK9EA3OjDx-TOO80NukUbyP-ljGadZwTUrq02shYo'
);

$sqlInit = "SELECT *
            FROM extractDolibar1an
            WHERE checked = 0";
$modifs = $bdd->query($sqlInit)->fetchall(PDO::FETCH_ASSOC);

foreach ($modifs as $modif) {
    echo '<hr />' . $modif['nom'] . ' // ' . $modif['session'];

    $sql = "SELECT u.firstname, u.lastname, mue.timestart, mue.timeend
            FROM mdl_user AS u
            INNER JOIN mdl_user_enrolments AS mue ON mue.userid = u.id
            INNER JOIN mdl_enrol AS e ON e.id = mue.enrolid
            INNER JOIN mdl_course AS c ON c.id = e.courseid
            WHERE u.username = '" . $modif['login'] . "' AND mue.enrolid IN (210, 212) AND c.shortname = '" . $modif['codeMoodle'] . "'";

    $req = $bdd->query($sql);
    if( !$req->rowCount())  echo ' : pas dans moodle';
    while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
        $dateToUpdate = date("Y-m-d", $row['timestart']);
        $update = "UPDATE mdl_user_enrolments AS mue
                           INNER JOIN mdl_user AS u ON u.id = mue.userid
                           SET mue.timeend = UNIX_TIMESTAMP(ADDDATE('" . $dateToUpdate . "' , INTERVAL 1 YEAR))
                           WHERE u.username = '" . $modif['login'] . "'";
        $bdd->query($update);

        // Convertir date en format JSON
        $dateactif = date("Y-m-d", strtotime($modif['dated'])) . 'T' . date("H:i:s", strtotime($modif['dated'])) . '.000Z';
        $d = $row['timestart'] + 3600 * 24 * 360;
        $dateactifjusqa = date("Y-m-d", $d) . 'T' . date("H:i:s", $d) . '.000Z';

        // On verifie si l'utilisateur existe déjà, via l'api https://www.altercampus.fr/nj/users/getAllInList
        $postData = array(
            'identifiant' => $modif['login']
        );
        $url = 'https://www.altercampus.fr/nj/users/getAllInList';
        $jsondata = json_encode($postData);
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $array_header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsondata,
            CURLOPT_FOLLOWLOCATION => true
        ));
        $output = curl_exec($ch);
        $output = json_decode($output);


        if (is_null($output[0]->idUtilisateur)) {
            echo 'Utilisateur introuvable ' . $modif['nom'] . ' // ' . $modif['session'] . '<br />';
        } else {
            $idUtilisateur = $output[0]->idUtilisateur;
            $postData = array(
                'idUtilisateur' => $idUtilisateur,
                'actifJusqua' => $dateactifjusqa
            );
            $url = 'https://www.altercampus.fr/nj/users/update';
            $jsondata = json_encode($postData);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $array_header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsondata,
                CURLOPT_FOLLOWLOCATION => true
            ));
            $output = curl_exec($ch);
            $output = json_decode($output, true);


            if (empty($output[0]['actifJusqua'])) {
                echo 'Date de fin introuvable 1' . $modif['nom'] . ' // ' . $modif['session'] . '<br />';
            } else {
                $codef = $modif['AUT'];

                if ($codef != "") {
                    $list_codef = explode(";", $codef);
                    $nb_codef = count($list_codef);

                    for ($k = 0; $k < $nb_codef; $k++) {
                        $url = "https://www.altercampus.fr/nj/affectation/updateDateFin";
                        $postData = array(
                            'idUtilisateur' => $idUtilisateur,
                            'idSession' => $list_codef[$k],
                            'dateFin' => $dateactifjusqa
                        );
                        $jsondata = json_encode($postData);
                        $ch = curl_init();
                        curl_setopt_array($ch, array(
                            CURLOPT_URL => $url,
                            CURLOPT_HTTPHEADER => $array_header,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $jsondata,
                            CURLOPT_FOLLOWLOCATION => true
                        ));
                        $output = curl_exec($ch);
                        $output = json_decode($output, true);

                        if (strpos(SUCCES_AFFEC, $output['message']) !== FALSE) {
                            echo 'echec mise a jour affectation ' . $modif['nom'] . ' // ' . $modif['session'] . '<br /><br />';
                        }
                    }
                } else {
                    echo 'Affectation introuvable ' . $modif['nom'] . ' // ' . $modif['session'] . '<br />Mise à jour annulée<br /><br />';
                }
            }
        }

        echo 'FIN ' . $modif['nom'];
        $sqlUpdate = "UPDATE extractDolibar1an
                                  SET checked = 1
                                  WHERE login = '" . $modif['login'] . "' AND session = " . $modif['session'];
        $bdd->query($sqlUpdate);
    }
}
echo '<h1>THE END !</h1>';
?>