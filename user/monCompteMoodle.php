<?php
	include('../../custom/agefodd/session/connexion.php');
	include_once('../lib/lib.php');
	$base = "/home/infansdocuments/docs/sessions/";

	if( isset($_POST['demandeLiens']) && isset($_POST['id_session']) && isset($_POST['id_stagiaire']) )	{
		$idSession = $_POST['id_session'];
		$idStagiaire = $_POST['id_stagiaire'];
		
		$sql="SELECT st.nom, st.prenom FROM
		llx_agefodd_stagiaire as st
		where st.rowid= :id_stagiaire";
		
		$req = $bdd->prepare($sql);
		$req->bindParam('id_stagiaire',$idStagiaire);
		$req->execute();
		$infostagiaire = $req->fetch(PDO::FETCH_ASSOC);

		$doss = nomDossierSpecial($base, $idSession);
		$nom = suppCaracteres($infostagiaire['nom']);
		$prenom = suppCaracteres($infostagiaire['prenom']);
		/************Recuperer le chemin du dossier***********************/
		$chemin = $base . $doss . "/" . $idSession . "_" . $idStagiaire . "_" . $nom . "_" . $prenom;
		/************Supprimer l'espace dans le chemin(lien)*************/
		$chemin = str_replace(" ", "", $chemin);
		$listFile = array();
		$listFile['chemin'] =  $chemin;
	    $listFile['contenu']=scandir($chemin);
        echo json_encode($listFile);
		
	}
?>