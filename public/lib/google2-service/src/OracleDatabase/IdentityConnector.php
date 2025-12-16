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

class IdentityConnector extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const CONNECTION_STATE_CONNECTION_STATE_UNSPECIFIED = 'CONNECTION_STATE_UNSPECIFIED';
  /**
   * The identity pool connection is connected.
   */
  public const CONNECTION_STATE_CONNECTED = 'CONNECTED';
  /**
   * The identity pool connection is partially connected.
   */
  public const CONNECTION_STATE_PARTIALLY_CONNECTED = 'PARTIALLY_CONNECTED';
  /**
   * The identity pool connection is disconnected.
   */
  public const CONNECTION_STATE_DISCONNECTED = 'DISCONNECTED';
  /**
   * The identity pool connection is in an unknown state.
   */
  public const CONNECTION_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Output only. The connection state of the identity connector.
   *
   * @var string
   */
  public $connectionState;
  /**
   * Output only. A google managed service account on which customers can grant
   * roles to access resources in the customer project. Example:
   * `p176944527254-55-75119d87fd8f@gcp-sa-oci.iam.gserviceaccount.com`
   *
   * @var string
   */
  public $serviceAgentEmail;

  /**
   * Output only. The connection state of the identity connector.
   *
   * Accepted values: CONNECTION_STATE_UNSPECIFIED, CONNECTED,
   * PARTIALLY_CONNECTED, DISCONNECTED, UNKNOWN
   *
   * @param self::CONNECTION_STATE_* $connectionState
   */
  public function setConnectionState($connectionState)
  {
    $this->connectionState = $connectionState;
  }
  /**
   * @return self::CONNECTION_STATE_*
   */
  public function getConnectionState()
  {
    return $this->connectionState;
  }
  /**
   * Output only. A google managed service account on which customers can grant
   * roles to access resources in the customer project. Example:
   * `p176944527254-55-75119d87fd8f@gcp-sa-oci.iam.gserviceaccount.com`
   *
   * @param string $serviceAgentEmail
   */
  public function setServiceAgentEmail($serviceAgentEmail)
  {
    $this->serviceAgentEmail = $serviceAgentEmail;
  }
  /**
   * @return string
   */
  public function getServiceAgentEmail()
  {
    return $this->serviceAgentEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityConnector::class, 'Google_Service_OracleDatabase_IdentityConnector');
