<?php
/*
 * Copyright (C) 2005 Alfresco, Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

 * As a special exception to the terms and conditions of version 2.0 of 
 * the GPL, you may redistribute this Program in connection with Free/Libre 
 * and Open Source Software ("FLOSS") applications as described in Alfresco's 
 * FLOSS exception.  You should have recieved a copy of the text describing 
 * the FLOSS exception, and it is also available here: 
 * http://www.alfresco.com/legal/licensing"
 */
 
require_once 'BaseObject.php';
require_once 'Node.php';

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