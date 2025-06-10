<?php

class com_wiris_quizzes_service_PhpServiceProxy {
	
	public static function dispatch() {
		$proxy = new com_wiris_quizzes_service_PhpServiceProxy();
		$proxy->doPost();
	}
	
	private function doPost() {
		$conf = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getConfiguration();
		// In PHP, the referrer is automatically set to the config when the QuizzesBuilder is created.
		
		$request = new com_wiris_system_service_HttpRequest();
		$res = new com_wiris_system_service_HttpResponse();

		// Uploaded files.
		foreach ($_FILES as $key => $file) {
			if ($file['size'] > 0) {
				if ($file['size'] > com_wiris_quizzes_service_ServiceRouter::$MAX_UPLOAD_SIZE) {
					$res->sendError(400, "File too big.");
				}
				$content = '';
				$fh = fopen($file['tmp_name'], 'rb');
				if ($fh !== false) {
					while (!feof($fh)) {
						$content .= fread($fh, 4096);
					}
					fclose($fh);
				}
				$request->setParameter($key, $content);
			}
		}
		
		$service = new com_wiris_quizzes_service_ServiceRouter();
		$service->service($request, $res);
	}
}
?>