<?php

interface com_wiris_quizzes_api_AuthorAnswer extends com_wiris_quizzes_api_Serializable{
	function getValueAsMathML();
	function copy($model);
	function setWeight($weight);
	function getWeight();
	function removeValidation($validation);
	function getValidation($name);
	function addNewValidation($name);
	function getValidations();
	function getComparison();
	function setComparison($name);
	function setValue($value);
	function getFilterableValue();
	function getValue();
}
