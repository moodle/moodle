<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Dataform;

class ActionSqlDefinition extends \Google\Model
{
  protected $errorTableType = ActionErrorTable::class;
  protected $errorTableDataType = '';
  protected $loadConfigType = ActionLoadConfig::class;
  protected $loadConfigDataType = '';
  /**
   * The SQL query representing the data preparation steps. Formatted as a Pipe
   * SQL query statement.
   *
   * @var string
   */
  public $query;

  /**
   * Error table configuration,
   *
   * @param ActionErrorTable $errorTable
   */
  public function setErrorTable(ActionErrorTable $errorTable)
  {
    $this->errorTable = $errorTable;
  }
  /**
   * @return ActionErrorTable
   */
  public function getErrorTable()
  {
    return $this->errorTable;
  }
  /**
   * Load configuration.
   *
   * @param ActionLoadConfig $loadConfig
   */
  public function setLoadConfig(ActionLoadConfig $loadConfig)
  {
    $this->loadConfig = $loadConfig;
  }
  /**
   * @return ActionLoadConfig
   */
  public function getLoadConfig()
  {
    return $this->loadConfig;
  }
  /**
   * The SQL query representing the data preparation steps. Formatted as a Pipe
   * SQL query statement.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActionSqlDefinition::class, 'Google_Service_Dataform_ActionSqlDefinition');
