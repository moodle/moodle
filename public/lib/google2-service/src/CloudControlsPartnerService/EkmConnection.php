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

namespace Google\Service\CloudControlsPartnerService;

class EkmConnection extends \Google\Model
{
  /**
   * Unspecified EKM connection state
   */
  public const CONNECTION_STATE_CONNECTION_STATE_UNSPECIFIED = 'CONNECTION_STATE_UNSPECIFIED';
  /**
   * Available EKM connection state
   */
  public const CONNECTION_STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Not available EKM connection state
   */
  public const CONNECTION_STATE_NOT_AVAILABLE = 'NOT_AVAILABLE';
  /**
   * Error EKM connection state
   */
  public const CONNECTION_STATE_ERROR = 'ERROR';
  /**
   * Permission denied EKM connection state
   */
  public const CONNECTION_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  protected $connectionErrorType = ConnectionError::class;
  protected $connectionErrorDataType = '';
  /**
   * Resource name of the EKM connection in the format:
   * projects/{project}/locations/{location}/ekmConnections/{ekm_connection}
   *
   * @var string
   */
  public $connectionName;
  /**
   * Output only. The connection state
   *
   * @var string
   */
  public $connectionState;

  /**
   * The connection error that occurred if any
   *
   * @param ConnectionError $connectionError
   */
  public function setConnectionError(ConnectionError $connectionError)
  {
    $this->connectionError = $connectionError;
  }
  /**
   * @return ConnectionError
   */
  public function getConnectionError()
  {
    return $this->connectionError;
  }
  /**
   * Resource name of the EKM connection in the format:
   * projects/{project}/locations/{location}/ekmConnections/{ekm_connection}
   *
   * @param string $connectionName
   */
  public function setConnectionName($connectionName)
  {
    $this->connectionName = $connectionName;
  }
  /**
   * @return string
   */
  public function getConnectionName()
  {
    return $this->connectionName;
  }
  /**
   * Output only. The connection state
   *
   * Accepted values: CONNECTION_STATE_UNSPECIFIED, AVAILABLE, NOT_AVAILABLE,
   * ERROR, PERMISSION_DENIED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EkmConnection::class, 'Google_Service_CloudControlsPartnerService_EkmConnection');
