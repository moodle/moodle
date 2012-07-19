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
 
require_once $CFG->libdir.'/alfresco/Service/BaseObject.php';
require_once $CFG->libdir.'/alfresco/Service/Node.php';

class Store extends BaseObject
{
	protected $_session;
	protected $_address;
	protected $_scheme;
	protected $_rootNode;

	public function __construct($session, $address, $scheme = "workspace")
	{
		$this->_session = $session;
		$this->_address = $address;
		$this->_scheme = $scheme;
	}

	public function __toString()
	{
		return $this->scheme . "://" . $this->address;
	}
	
	public function __toArray()
	{
		return array(
			"scheme" => $this->_scheme,
			"address" => $this->_address);
	}

	public function getAddress()
	{
		return $this->_address;
	}

	public function getScheme()
	{
		return $this->_scheme;
	}

	public function getRootNode()
	{
		if (isset ($this->_rootNode) == false)
		{
			$result = $this->_session->repositoryService->get(
				array(
					"where" => array(
						"store" => $this->__toArray())));

			$this->_rootNode = Node::createFromWebServiceData($this->_session, $result->getReturn);
		}

		return $this->_rootNode;
	}
}
?>