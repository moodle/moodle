<?php

interface com_wiris_quizzes_api_QuestionInstance extends com_wiris_quizzes_api_Serializable{
	function startMultiStepSession();
	function setParameter($name, $value);
	function getProperty($name);
	function setProperty($name, $value);
	function areVariablesReady();
	function getChecks($slot, $authorAnswer);
	function getAssertionChecks($correctAnswer, $studentAnswer);
	function getStudentAnswersLength();
	function setSlotAnswer($slot, $answer);
	function getSlotAnswer($slot);
	function getStudentAnswer($index);
	function setStudentAnswer($index, $answer);
	function setAuxiliaryText($text);
	function setAuxiliarText($text);
	function setCasSession($session);
	function setRandomSeed($seed);
	function getStudentQuestionInstance();
	function getCompoundGrade($slot, $authorAnswer, $index);
	function getCompoundAnswerGrade($correctAnswer, $studentAnswer, $index, $question);
	function getGrade($slot, $authorAnswer);
	function getAnswerGrade($correctAnswer, $studentAnswer, $question);
	function expandVariablesText($text);
	function expandVariablesMathML($mathml);
	function expandVariables($html);
	function isSlotAnswerCorrect($slot);
	function areAllAnswersCorrect();
	function isAnswerCorrect($studentAnswer);
	function updateFromStudentQuestionInstance($qi);
	function update($response);
	//;
}
