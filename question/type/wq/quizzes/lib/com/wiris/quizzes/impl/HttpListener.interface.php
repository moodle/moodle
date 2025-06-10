<?php

interface com_wiris_quizzes_impl_HttpListener {
	function onError($error);
	function onData($data);
}
