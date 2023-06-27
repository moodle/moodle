<?php

	require_once('../config.php');		// contient le token ($CFG->tokenAlter) et la creation de $USER
		
		$url = 'https://www.altercampus.fr/nj/users/getAllInList';
		$postData = array(
			'identifiant' => $USER->username
		); 
		$jsondata = json_encode($postData);

		
		$array_header = array (
			'Content-Type: application/json',
			'token:'.$CFG->tokenAlter
		);
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
		$idUtilisateur = $output[0]->idUtilisateur;
	var_dump($idUtilisateur);	
		
		if( $idUtilisateur )	{
			$postData = array(
				'idUtilisateur' => $idUtilisateur,
			); 
			$url = 'https://www.altercampus.fr/nj/autres/genLienDirectPlatItems';
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
			if($output->url)	{
				header('Location: ' . $output->url);
				exit;
			}
		}


?>