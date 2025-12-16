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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoConnectorsGenericConnectorTaskConfig extends \Google\Model
{
  public const OPERATION_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  public const OPERATION_EXECUTE_ACTION = 'EXECUTE_ACTION';
  public const OPERATION_LIST_ENTITIES = 'LIST_ENTITIES';
  public const OPERATION_GET_ENTITY = 'GET_ENTITY';
  public const OPERATION_CREATE_ENTITY = 'CREATE_ENTITY';
  public const OPERATION_UPDATE_ENTITY = 'UPDATE_ENTITY';
  public const OPERATION_DELETE_ENTITY = 'DELETE_ENTITY';
  public const OPERATION_EXECUTE_QUERY = 'EXECUTE_QUERY';
  protected $connectionType = EnterpriseCrmEventbusProtoConnectorsConnection::class;
  protected $connectionDataType = '';
  /**
   * Operation to perform using the configured connection.
   *
   * @var string
   */
  public $operation;

  /**
   * User-selected connection.
   *
   * @param EnterpriseCrmEventbusProtoConnectorsConnection $connection
   */
  public function setConnection(EnterpriseCrmEventbusProtoConnectorsConnection $connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return EnterpriseCrmEventbusProtoConnectorsConnection
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * Operation to perform using the configured connection.
   *
   * Accepted values: OPERATION_UNSPECIFIED, EXECUTE_ACTION, LIST_ENTITIES,
   * GET_ENTITY, CREATE_ENTITY, UPDATE_ENTITY, DELETE_ENTITY, EXECUTE_QUERY
   *
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoConnectorsGenericConnectorTaskConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoConnectorsGenericConnectorTaskConfig');
