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

class ChildAssociation extends BaseObject
{
	private $_parent;
	private $_child;
	private $_type;
	private $_name;
	private $_isPrimary;
	private $_nthSibling;
	
	public function __construct($parent, $child, $type, $name, $isPrimary=false, $nthSibling=0)
	{
		$this->_parent = $parent;
		$this->_child = $child;
		$this->_type = $type;
		$this->_name = $name;
		$this->_isPrimary = $isPrimary;
		$this->_nthSibling = $nthSibling;
	}
	
	public function getParent()
	{
		return $this->_parent;
	}
	
	public function getChild()
	{
		return $this->_child;
	}
	
	public function getType()
	{
		return $this->_type;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function getIsPrimary()
	{
		return $this->_isPrimary;
	}
	
	public function getNthSibling()
	{
		return $this->_nthSibling;
	}
}
?>
