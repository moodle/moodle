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

namespace Google\Service\ManagedKafka;

class AclEntry extends \Google\Model
{
  /**
   * Required. The host. Must be set to "*" for Managed Service for Apache
   * Kafka.
   *
   * @var string
   */
  public $host;
  /**
   * Required. The operation type. Allowed values are (case insensitive): ALL,
   * READ, WRITE, CREATE, DELETE, ALTER, DESCRIBE, CLUSTER_ACTION,
   * DESCRIBE_CONFIGS, ALTER_CONFIGS, and IDEMPOTENT_WRITE. See
   * https://kafka.apache.org/documentation/#operations_resources_and_protocols
   * for valid combinations of resource_type and operation for different Kafka
   * API requests.
   *
   * @var string
   */
  public $operation;
  /**
   * Required. The permission type. Accepted values are (case insensitive):
   * ALLOW, DENY.
   *
   * @var string
   */
  public $permissionType;
  /**
   * Required. The principal. Specified as Google Cloud account, with the Kafka
   * StandardAuthorizer prefix "User:". For example: "User:test-kafka-
   * client@test-project.iam.gserviceaccount.com". Can be the wildcard "User:*"
   * to refer to all users.
   *
   * @var string
   */
  public $principal;

  /**
   * Required. The host. Must be set to "*" for Managed Service for Apache
   * Kafka.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Required. The operation type. Allowed values are (case insensitive): ALL,
   * READ, WRITE, CREATE, DELETE, ALTER, DESCRIBE, CLUSTER_ACTION,
   * DESCRIBE_CONFIGS, ALTER_CONFIGS, and IDEMPOTENT_WRITE. See
   * https://kafka.apache.org/documentation/#operations_resources_and_protocols
   * for valid combinations of resource_type and operation for different Kafka
   * API requests.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Required. The permission type. Accepted values are (case insensitive):
   * ALLOW, DENY.
   *
   * @param string $permissionType
   */
  public function setPermissionType($permissionType)
  {
    $this->permissionType = $permissionType;
  }
  /**
   * @return string
   */
  public function getPermissionType()
  {
    return $this->permissionType;
  }
  /**
   * Required. The principal. Specified as Google Cloud account, with the Kafka
   * StandardAuthorizer prefix "User:". For example: "User:test-kafka-
   * client@test-project.iam.gserviceaccount.com". Can be the wildcard "User:*"
   * to refer to all users.
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AclEntry::class, 'Google_Service_ManagedKafka_AclEntry');
