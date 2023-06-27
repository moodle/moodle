<?php

	foreach($_GET as $g)	{
		if(strstr($g, 'img_temp') !== false)
			unlink($g);
	}


?>