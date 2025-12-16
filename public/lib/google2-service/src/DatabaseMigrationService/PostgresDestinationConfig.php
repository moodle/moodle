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

class PostgresDestinationConfig extends \Google\Model
{
  /**
   * Optional. Maximum number of connections Database Migration Service will
   * open to the destination for data migration.
   *
   * @var int
   */
  public $maxConcurrentConnections;
  /**
   * Optional. Timeout for data migration transactions.
   *
   * @var string
   */
  public $transactionTimeout;

  /**
   * Optional. Maximum number of connections Database Migration Service will
   * open to the destination for data migration.
   *
   * @param int $maxConcurrentConnections
   */
  public function setMaxConcurrentConnections($maxConcurrentConnections)
  {
    $this->maxConcurrentConnections = $maxConcurrentConnections;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentConnections()
  {
    return $this->maxConcurrentConnections;
  }
  /**
   * Optional. Timeout for data migration transactions.
   *
   * @param string $transactionTimeout
   */
  public function setTransactionTimeout($transactionTimeout)
  {
    $this->transactionTimeout = $transactionTimeout;
  }
  /**
   * @return string
   */
  public function getTransactionTimeout()
  {
    return $this->transactionTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgresDestinationConfig::class, 'Google_Service_DatabaseMigrationService_PostgresDestinationConfig');
