<?php

require_once('query.php');

//se obtiene la instancia

$idinstancia = $_GET['idinstancia'];
$infoinstancia = consultInstance($idinstancia);


//se lee el archivo 
$string = file_get_contents('../files/infostudents'.$infoinstancia->cod_univalle.'.json');
$json_a = json_decode($string, true);




$programa = $json_a['students'];



//se obtiene el valor del input actualmente
$q = $_GET['q'];



//se realiza la busqueda
$results = array('students'=> array());

foreach ($programa as $username => $data)
{
	if ((stripos($username, $q) !== false) || (stripos($data['firstname'], $q) !== false) || (stripos($data['lastname'], $q) !== false) || (stripos($data['num_doc'], $q) !== false) )
	{
		$results['students'][$username] = $data;
	}
}

// se  foramatea la informcion para que lo reconosca el script


//para los talentos
$final_talentos = array('header' => array(), 'data' => array());
$final_talentos['header'] = array(
								'title' => $infoinstancia->nombre,					//lo que aparece en la parte superior de esta categoria
								'num' => count($results['students']),			// el numero de resultados encontrados
								'limit' => 3									// número de resultados qeu van a aparecer en la sugerencia
							);
foreach ($results['students'] as $username => $data)
{
	$final_talentos['data'][] = array(
									'primary' => $username,					// titulo del  resultado actual
									'secondary' => $data['firstname']." ".$data['lastname']." ".$data['tipo_doc']." ".$data['num_doc'],    // Descripcion del resultado actual
									'image' => $data['image'],								// imagen
									'onclick' => '',	# JavaScript se llama en case de este elemento sea cliqueado
									'codigo' => substr ($username, 0 , -5)	 // se usa para autocompletar
								);
}



//se envia el resultado
$final = array($final_talentos);
header('Content-type: application/json');
echo json_encode($final);
die();
?>