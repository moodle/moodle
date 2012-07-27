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

class NamespaceMap
{
	const DELIMITER = "_";
	
	private $namespaceMap = array(
		"d" => "http://www.alfresco.org/model/dictionary/1.0",
		"sys" => "http://www.alfresco.org/model/system/1.0",
		"cm" => "http://www.alfresco.org/model/content/1.0",
		"app" => "http://www.alfresco.org/model/application/1.0",
		"bpm" => "http://www.alfresco.org/model/bpm/1.0",
		"wf" => "http://www.alfresco.org/model/workflow/1.0",
		"fm" => "http://www.alfresco.org/model/forum/1.0",
		"view" => "http://www.alfresco.org/view/repository/1.0",
		"security" => "http://www.alfresco.org/model/security/1.0",
		"wcm" => "http://www.alfresco.org/model/wcmmodel/1.0",
		"wca" => "http://www.alfresco.org/model/wcmappmodel/1.0");
	
	public function isShortName($shortName)
	{
		return ($shortName != $this->getFullName($shortName));
	}
		
	public function getFullName($shortName)
	{
		$result = $shortName;
		
		$index = strpos($shortName, NamespaceMap::DELIMITER);
		if ($index !== false)
		{
			$prefix = substr($shortName, 0, $index);
						
			if (isset($this->namespaceMap[$prefix]) == true)	
			{
				$url = $this->namespaceMap[$prefix];
				$name = substr($shortName, $index+1);
				$name = str_replace("_", "-", $name);
				if ($name != null && strlen($name) != 0)
				{
					$result = "{".$url."}".$name;
				}
			}
		}
		
		return $result;
	}
	
	public function getFullNames($fullNames)
	{
		$result = array();
		
		foreach ($fullNames as $fullName)
		{
			$result[] = $this->getFullName($fullName);			
		}		
		return $result;
	}
	
	public function getShortName($fullName)
	{
		$result = $fullName;
		
		$index = strpos($fullName, "}");
		if ($index !== false)
		{
			$url = substr($fullName, 1, $index-1);
			$prefix = $this->lookupPrefix($url);
			if ($prefix != null)
			{
				$name = substr($fullName, $index+1);
				if ($name != null && strlen($name) != 0)
				{					
					$name = str_replace("-", "_", $name);			
					$result = $prefix.NamespaceMap::DELIMITER.$name;
				}
			}
		}
		
		return $result;	
	}
	
	private function lookupPrefix($value)
	{
		$result = null;
		foreach($this->namespaceMap as $prefix => $url)
		{
			if ($url == $value)
			{
				$result = $prefix;
			}
		}
		return $result;
	} 
}

?>
