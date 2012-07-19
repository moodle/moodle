<?php
/*
 * Copyright (C) 2005-2010 Alfresco Software Limited.
 *
 * This file is part of Alfresco
 *
 * Alfresco is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Alfresco is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Alfresco. If not, see <http://www.gnu.org/licenses/>.
 */
 
 require_once $CFG->libdir."/alfresco/Service/Logger/LoggerConfig.php";
 
 class Logger
 { 	
 	private $componentName;
 	
 	public function __construct($componentName = null)
 	{
 		$this->componentName = $componentName;
 	}	
 	
 	public function isDebugEnabled()
 	{
 		return $this->isEnabled(DEBUG);
 	}
 	
 	public function debug($message)
 	{
 		$this->write(DEBUG, $message);	
 	}
 	
 	public function isWarningEnabled()
 	{
 		return $this->isEnabled(WARNING);
 	}
 	
 	public function warning($message)
 	{
 		$this->write(WARNING, $message);	
 	}
 	
 	public function isInformationEnabled()
 	{
 		return $this->isEnabled(INFORMATION); 		
 	}
 	
 	public function information($message)
 	{
 		$this->write(INFORMATION, $message);	
 	}
 	
 	private function isEnabled($logLevel)
 	{
 		global $componentLogLevels, $defaultLogLevel;
 		
 		$logLevels = $defaultLogLevel;
 		if ($this->componentName != null && isset($componentLogLevels[$this->componentName]) == true)
 		{
 			$logLevels = $componentLogLevels[$this->componentName];
 		}
 		
 		return in_array($logLevel, $logLevels);
 	}
 	
 	private function write($logLevel, $message)
 	{
 		global $logFile;
 		
		$handle = fopen($logFile, "a");
		fwrite($handle, $logLevel." ".date("G:i:s d/m/Y")." - ".$message."\r\n");
		fclose($handle);
 	}
 }
?>
