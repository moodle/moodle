<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
require_once(dirname(__FILE__) . '/../KalturaClient.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'KalturaTestConfiguration.php');

class TestMain implements IKalturaLogger
{
	public function log($message)
	{
		echo date('Y-m-d H:i:s') . ' ' .  $message . "\n";
	}

	public static function run()
	{
		$test = new TestMain();
		$test->listActions();
		$test->multiRequest();
		$test->add();
		echo "\nFinished running client library tests\n";
	}
	
	private function getKalturaClient($partnerId, $adminSecret, $isAdmin)
	{
		$kConfig = new KalturaConfiguration($partnerId);
		$kConfig->serviceUrl = KalturaTestConfiguration::SERVICE_URL;
		$kConfig->setLogger($this);
		$client = new KalturaClient($kConfig);
		
		$userId = "SomeUser";
		$sessionType = ($isAdmin)? KalturaSessionType::ADMIN : KalturaSessionType::USER; 
		try
		{
			$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
			$client->setKs($ks);
		}
		catch(Exception $ex)
		{
			die("could not start session - check configurations in KalturaTestConfiguration class");
		}
		
		return $client;
	}
	
	public function listActions()
	{
		try
		{
			$client = $this->getKalturaClient(KalturaTestConfiguration::PARTNER_ID, KalturaTestConfiguration::ADMIN_SECRET, true);
			$results = $client->media->listAction();
			$entry = $results->objects[0];
			echo "\nGot an entry: [{$entry->name}]";
		}
		catch(Exception $ex)
		{
			die($ex->getMessage());
		}
	}

	public function multiRequest()
	{
		try
		{
			$client = $this->getKalturaClient(KalturaTestConfiguration::PARTNER_ID, KalturaTestConfiguration::ADMIN_SECRET, true);
			$client->startMultiRequest();
			$client->baseEntry->count();
			$client->partner->getInfo();
			$client->partner->getUsage(2011);
			$multiRequest = $client->doMultiRequest();
			$partner = $multiRequest[1];
			if(!is_object($partner) || get_class($partner) != 'KalturaPartner')
			{
				throw new Exception("UNEXPECTED_RESULT");
			}
			echo "\nGot Admin User email: [{$partner->adminEmail}]";
		}
		catch(Exception $ex)
		{
			die($ex->getMessage()); 
		}
	}	

	public function add()
	{
		try 
		{
			echo "\nUploading test video...";
			$client = $this->getKalturaClient(KalturaTestConfiguration::PARTNER_ID, KalturaTestConfiguration::ADMIN_SECRET, false);
			$filePath = KalturaTestConfiguration::UPLOAD_FILE;
			
			$token = $client->baseEntry->upload($filePath);
			$entry = new KalturaMediaEntry();
			$entry->name = "my upload entry";
			$entry->mediaType = KalturaMediaType::VIDEO;
			$newEntry = $client->media->addFromUploadedFile($entry, $token);
			echo "\nUploaded a new Video entry " . $newEntry->id;
			$client->media->delete($newEntry->id);
			try {
				$entry = null;
				$entry = $client->media->get($newEntry->id);
			} catch (KalturaException $exApi) {
				if ($entry == null) {
					echo "\nDeleted the entry (" . $newEntry->id .") successfully!";
				}
			}
		} catch (KalturaException $ex) {
			die($ex->getMessage());
		}	
	}
}

TestMain::run();
