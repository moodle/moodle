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

class BaseObject
{
	public function __get($name)
	{	
		$methodName = $name;
		$methodName[0] = strtoupper($methodName[0]);
		$methodName = 'get' . $methodName;
		
		if (method_exists($this, $methodName) == true)
		{
		    return $this->$methodName();			
		}
	}
	
	public function __set($name, $value)
	{
		$methodName = $name;
		$methodName[0] = strtoupper($methodName[0]);
		$methodName = 'set' . $methodName;
		
		if (method_exists($this, $methodName) == true)
		{
			return $this->$methodName($value);
		}	
	}
	
	protected function resultSetToNodes($session, $store, $resultSet)
	{
		$return = array();		
        if (isset($resultSet->rows) == true)
        {
			if (is_array($resultSet->rows) == true)
			{		
				foreach($resultSet->rows as $row)
				{
					$id = $row->node->id;
					$return[] = $session->getNode($store, $id);				
				}
			}
			else
			{
				$id = $resultSet->rows->node->id;
				$return[] = $session->getNode($store, $id);
			}
        }
		
		return $return;
	}
	
	protected function resultSetToMap($resultSet)
	{
		$return = array();	
        if (isset($resultSet->rows) == true)
        {        
			if (is_array($resultSet->rows) == true)
			{
				foreach($resultSet->rows as $row)
				{
					$return[] = $this->columnsToMap($row->columns);				
				}
			}
			else
			{
				$return[] = $this->columnsToMap($resultSet->rows->columns);
			}

        }
		
		return $return;	
	}
	
	private function columnsToMap($columns)
	{
		$return = array();		
      
		foreach ($columns as $column)
		{
			$return[$column->name] = $column->value;
		}
		
		return $return;	
	}
	
	protected function remove_array_value($value, &$array)
	{
		if ($array != null)
		{
			if (in_array($value, $array) == true)
			{
				foreach ($array as $index=>$value2)
				{
					if ($value == $value2)
					{
						unset($array[$index]);
					}
				}
			}
		}
	} 
	
	protected function isContentData($value)
	{		
		$index = strpos($value, "contentUrl=");
		if ($index === false)
		{
			return false;
		}	
		else
		{	
			if ($index == 0)
			{	
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>