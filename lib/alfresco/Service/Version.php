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

 
 /**
  * Version class
  * 
  * @author Roy Wetherall
  */
 class Version extends BaseObject 
 {
 	private $_session;
 	private $_store;
 	private $_id;
 	private $_description;
 	private $_major;
 	private $_properties;
 	private $_type;
 	private $_aspects;
 	
 	/**
 	 * Constructor
 	 * 
 	 * @param	$session		the session that the version is tied to
 	 * @param	@store			the store that the forzen node is stored in
 	 * @prarm	@id				the id of the frozen node
 	 * @param   @description	the description of the version
 	 * @param	@major			indicates whether this is a major or minor revision	
 	 */
 	public function __construct($session, $store, $id, $description=null, $major=false)
 	{
		$this->_session = $session;
		$this->_store = $store;
		$this->_id = $id;
		$this->_description = $description;
		$this->_major = $major; 	
		$this->_properties = null;
		$this->_aspects = null;
		$this->_type = null;	
 	}	
 	
	/**
	 *	__get override.
	 *
	 * If called with a valid property short name, the frozen value of that property is returned. 
	 * 
	 * @return 	String	the appropriate property value, null if none found
	 */
	public function __get($name)
	{
		$fullName = $this->_session->namespaceMap->getFullName($name);
		if ($fullName != $name)
		{
			$this->populateProperties();	
			if (array_key_exists($fullName, $this->_properties) == true)
			{
				return $this->_properties[$fullName];
			}	
			else
			{	
				return null;	
			} 	
		}	
		else
		{
			return parent::__get($name);
		}
	}
 	
 	/**
 	 * Gets session
 	 * 
 	 * @return Session	the session
 	 */
 	public function getSession()
 	{
 		return $this->_session;
 	}
 	
 	/**
 	 * Get the frozen nodes store
 	 * 
 	 * @return Store	the store
 	 */
 	public function getStore()
 	{
 		return $this->_store;
 	}
 	
 	public function getId()
 	{
 		return $this->_id;
 	}
 	
 	public function getDescription()
 	{
 		return $this->_description;
 	}
 	
 	public function getMajor()
 	{
 		return $this->_major;
 	}
 	
 	public function getType()
 	{
 		return $this->_type;
 	}
 	
 	public function getProperties()
 	{
 		return $this->_properties;
 	}
 	
 	public function getAspects()
 	{
 		return $this->_aspects;
 	}
 	
 	private function populateProperties()
	{
		if ($this->_properties == null)
		{	
			$result = $this->_session->repositoryService->get(array (
					"where" => array (
						"nodes" => array(
							"store" => $this->_store->__toArray(),
							"uuid" => $this->_id))));	
							
			$this->populateFromWebServiceNode($result->getReturn);
		}	
	}
	
	private function populateFromWebServiceNode($webServiceNode)
	{
		$this->_type = $webServiceNode->type;

		// Get the aspects
		$this->_aspects = array();
		$aspects = $webServiceNode->aspects;
		if (is_array($aspects) == true)
		{
			foreach ($aspects as $aspect)
			{
				$this->_aspects[] = $aspect;
			}
		}
		else
		{
			$this->_aspects[] = $aspects;	
		}		

		// Set the property values
		$this->_properties = array();
		foreach ($webServiceNode->properties as $propertyDetails) 
		{
			$name = $propertyDetails->name;
			$isMultiValue = $propertyDetails->isMultiValue;
			$value = null;
			if ($isMultiValue == false)
			{
				$value = $propertyDetails->value;
				if ($this->isContentData($value) == true)
				{
					$value = new ContentData($this, $name);
				}
			}
			else
			{
				$value = $propertyDetails->values;
			}
			
			$this->_properties[$name] = $value;
		}		
	}
 }
?>
