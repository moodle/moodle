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
 
require_once $CFG->libdir.'/alfresco/Service/Store.php';
require_once $CFG->libdir.'/alfresco/Service/Node.php';

class SpacesStore extends Store
{
	private $_companyHome;

	public function __construct($session)
	{
		parent::__construct($session, "SpacesStore");
	}

	public function __toString()
	{
		return $this->scheme . "://" . $this->address;
	}
	
	public function getCompanyHome()
	{
		if ($this->_companyHome == null)
		{
			$nodes = $this->_session->query($this, 'PATH:"app:company_home"');
	        $this->_companyHome = $nodes[0];
		}
		return $this->_companyHome;
	}
}
?>