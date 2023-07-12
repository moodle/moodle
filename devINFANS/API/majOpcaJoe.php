<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

include('../../report/trainingsessions/INC/connexion.php');

$array_header = array(
    'Content-Type: application/json',
    'token:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhYmFzZSI6ImFsdGVyY2FtcHVzdjRmb3JtYXNzbWF0IiwiaWF0IjoxNTgyODIzNTcwfQ.JQPK9EA3OjDx-TOO80NukUbyP-ljGadZwTUrq02shYo'
);

$sql = "SELECT *
            FROM opcajoe
            WHERE checked = 0";
$candidats = $bdd->query($sql)->fetchall(PDO::FETCH_ASSOC);

foreach ($candidats as $candidat) {
    echo '<hr />' . $candidat['nom'] . ' // ' . $candidat['rowid'];

    // On verifie si l'utilisateur existe déjà, via l'api https://www.altercampus.fr/nj/users/getAllInList
    $postData = array(
        'identifiant' => $candidat['login']
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

    if (empty($output)) {
        echo 'Utilisateur introuvable ' . $candidat['nom'] . ' // ' . $candidat['rowid'] . '<br />';
    } else {
        $idUtilisateur = $output[0]->idUtilisateur;
        $postData = array(
            "idUtilisateur" => $idUtilisateur,
            "matricule" => $candidat['num_OPCA_file']
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

        if (empty($output)) {
            echo 'Echec de l\'update pour : ' . $candidat['nom'] . ' // ' . $candidat['rowid'] . '<br />';
        } else {
            $sql = "UPDATE opcajoe SET checked = 1 WHERE login ='" . $candidat['login'] . "'";
            $bdd->query($sql);

            echo '<pre>';
            print_r($output);
        }
    }
//echo count($candidats);
}

