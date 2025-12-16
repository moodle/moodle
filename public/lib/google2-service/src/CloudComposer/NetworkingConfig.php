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

namespace Google\Service\CloudComposer;

class NetworkingConfig extends \Google\Model
{
  /**
   * No specific connection type was requested, so the environment uses the
   * default value corresponding to the rest of its configuration.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_UNSPECIFIED = 'CONNECTION_TYPE_UNSPECIFIED';
  /**
   * Requests the use of VPC peerings for connecting the Customer and Tenant
   * projects.
   */
  public const CONNECTION_TYPE_VPC_PEERING = 'VPC_PEERING';
  /**
   * Requests the use of Private Service Connect for connecting the Customer and
   * Tenant projects.
   */
  public const CONNECTION_TYPE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * Optional. Indicates the user requested specific connection type between
   * Tenant and Customer projects. You cannot set networking connection type in
   * public IP environment.
   *
   * @var string
   */
  public $connectionType;

  /**
   * Optional. Indicates the user requested specific connection type between
   * Tenant and Customer projects. You cannot set networking connection type in
   * public IP environment.
   *
   * Accepted values: CONNECTION_TYPE_UNSPECIFIED, VPC_PEERING,
   * PRIVATE_SERVICE_CONNECT
   *
   * @param self::CONNECTION_TYPE_* $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return self::CONNECTION_TYPE_*
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkingConfig::class, 'Google_Service_CloudComposer_NetworkingConfig');
