<?php

interface com_wiris_quizzes_api_assertion_Comparison extends com_wiris_quizzes_api_Serializable{
	function removeParameter($name);
	function getParameter($name);
	function setParameter($name, $value);
	function getName();
}
