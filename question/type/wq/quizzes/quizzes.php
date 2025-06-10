<?php
if(version_compare(PHP_VERSION, '5.1.0', '<')) {
    exit('Your current PHP version is: ' . PHP_VERSION . '. Wiris Quizzes needs version 5.1.0 or later');
}
;
if (!class_exists('com_wiris_system_CallWrapper')) {
	require_once dirname(__FILE__).'/lib/com/wiris/system/CallWrapper.class.php';
}
com_wiris_system_CallWrapper::getInstance()->init(dirname(__FILE__));
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/Quizzes.class.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/QuizzesBuilder.class.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/QuizzesConstants.class.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/PropertyName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/SyntaxName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/SyntaxParameterName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/ComparisonName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/ComparisonParameterName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/ValidationName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/assertion/ValidationParameterName.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/ui/AuthoringFieldType.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/ui/AnswerFieldType.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/api/ui/EmbeddedAnswersEditorMode.enum.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/wrap/QuizzesWrap.class.php';
require_once dirname(__FILE__).'/lib/com/wiris/quizzes/wrap/QuizzesBuilderWrap.class.php';

?>