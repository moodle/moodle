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

namespace Google\Service\OracleDatabase;

class PluggableDatabaseNodeLevelDetails extends \Google\Model
{
  /**
   * The open mode is unspecified.
   */
  public const OPEN_MODE_PLUGGABLE_DATABASE_OPEN_MODE_UNSPECIFIED = 'PLUGGABLE_DATABASE_OPEN_MODE_UNSPECIFIED';
  /**
   * The pluggable database is opened in read-only mode.
   */
  public const OPEN_MODE_READ_ONLY = 'READ_ONLY';
  /**
   * The pluggable database is opened in read-write mode.
   */
  public const OPEN_MODE_READ_WRITE = 'READ_WRITE';
  /**
   * The pluggable database is mounted.
   */
  public const OPEN_MODE_MOUNTED = 'MOUNTED';
  /**
   * The pluggable database is migrated.
   */
  public const OPEN_MODE_MIGRATE = 'MIGRATE';
  /**
   * Required. The Node name of the Database home.
   *
   * @var string
   */
  public $nodeName;
  /**
   * Required. The mode that the pluggable database is in to open it.
   *
   * @var string
   */
  public $openMode;
  /**
   * Required. The OCID of the Pluggable Database.
   *
   * @var string
   */
  public $pluggableDatabaseId;

  /**
   * Required. The Node name of the Database home.
   *
   * @param string $nodeName
   */
  public function setNodeName($nodeName)
  {
    $this->nodeName = $nodeName;
  }
  /**
   * @return string
   */
  public function getNodeName()
  {
    return $this->nodeName;
  }
  /**
   * Required. The mode that the pluggable database is in to open it.
   *
   * Accepted values: PLUGGABLE_DATABASE_OPEN_MODE_UNSPECIFIED, READ_ONLY,
   * READ_WRITE, MOUNTED, MIGRATE
   *
   * @param self::OPEN_MODE_* $openMode
   */
  public function setOpenMode($openMode)
  {
    $this->openMode = $openMode;
  }
  /**
   * @return self::OPEN_MODE_*
   */
  public function getOpenMode()
  {
    return $this->openMode;
  }
  /**
   * Required. The OCID of the Pluggable Database.
   *
   * @param string $pluggableDatabaseId
   */
  public function setPluggableDatabaseId($pluggableDatabaseId)
  {
    $this->pluggableDatabaseId = $pluggableDatabaseId;
  }
  /**
   * @return string
   */
  public function getPluggableDatabaseId()
  {
    return $this->pluggableDatabaseId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PluggableDatabaseNodeLevelDetails::class, 'Google_Service_OracleDatabase_PluggableDatabaseNodeLevelDetails');
