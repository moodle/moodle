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

namespace Google\Service\DatabaseMigrationService;

class SqlServerDagConfig extends \Google\Model
{
  /**
   * Required. The name of the linked server that points to the source SQL
   * Server instance. Only used by DAG migrations.
   *
   * @var string
   */
  public $linkedServer;
  /**
   * Required. The name of the source availability group. Only used by DAG
   * migrations.
   *
   * @var string
   */
  public $sourceAg;

  /**
   * Required. The name of the linked server that points to the source SQL
   * Server instance. Only used by DAG migrations.
   *
   * @param string $linkedServer
   */
  public function setLinkedServer($linkedServer)
  {
    $this->linkedServer = $linkedServer;
  }
  /**
   * @return string
   */
  public function getLinkedServer()
  {
    return $this->linkedServer;
  }
  /**
   * Required. The name of the source availability group. Only used by DAG
   * migrations.
   *
   * @param string $sourceAg
   */
  public function setSourceAg($sourceAg)
  {
    $this->sourceAg = $sourceAg;
  }
  /**
   * @return string
   */
  public function getSourceAg()
  {
    return $this->sourceAg;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerDagConfig::class, 'Google_Service_DatabaseMigrationService_SqlServerDagConfig');
