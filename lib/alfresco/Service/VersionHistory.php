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
  * Version history class.
  * 
  * @author Roy Wetherall
  */
 class VersionHistory extends BaseObject 
 {
 	/** Node to which this version history relates */
 	private $_node;
 	
 	/** Array of versions */
 	private $_versions;
 	
 	/**
 	 * Constructor
 	 * 
 	 * @param	$node	the node that this version history apples to
 	 */
 	public function __construct($node) 
	{ 
		$this->_node = $node;
		$this->populateVersionHistory();
	}
	
	/**
	 * Get the node that this version history relates to
	 */
	public function getNode()
	{
		return $this->_node;
	}
	
	/**
	 * Get a list of the versions in the version history
	 */
	public function getVersions()
	{
		return $this->_versions;
	}
	
	/**
	 * Populate the version history
	 */
	private function populateVersionHistory()
	{
		// Use the web service API to get the version history for this node
		$client = WebServiceFactory::getAuthoringService($this->_node->session->repository->connectionUrl, $this->_node->session->ticket);
		$result = $client->getVersionHistory(array("node" => $this->_node->__toArray()));
		//var_dump($result);
		
		// TODO populate the version history from the result of the web service call
	}
 }
?>
