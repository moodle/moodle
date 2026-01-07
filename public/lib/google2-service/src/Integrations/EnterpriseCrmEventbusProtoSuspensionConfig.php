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

class EnterpriseCrmEventbusProtoSuspensionConfig extends \Google\Collection
{
  protected $collection_key = 'whoMayResolve';
  /**
   * Optional information to provide recipients of the suspension in addition to
   * the resolution URL, typically containing relevant parameter values from the
   * originating workflow.
   *
   * @var string
   */
  public $customMessage;
  protected $notificationsType = EnterpriseCrmEventbusProtoNotification::class;
  protected $notificationsDataType = 'array';
  protected $suspensionExpirationType = EnterpriseCrmEventbusProtoSuspensionExpiration::class;
  protected $suspensionExpirationDataType = '';
  protected $whoMayResolveType = EnterpriseCrmEventbusProtoSuspensionAuthPermissions::class;
  protected $whoMayResolveDataType = 'array';

  /**
   * Optional information to provide recipients of the suspension in addition to
   * the resolution URL, typically containing relevant parameter values from the
   * originating workflow.
   *
   * @param string $customMessage
   */
  public function setCustomMessage($customMessage)
  {
    $this->customMessage = $customMessage;
  }
  /**
   * @return string
   */
  public function getCustomMessage()
  {
    return $this->customMessage;
  }
  /**
   * @param EnterpriseCrmEventbusProtoNotification[] $notifications
   */
  public function setNotifications($notifications)
  {
    $this->notifications = $notifications;
  }
  /**
   * @return EnterpriseCrmEventbusProtoNotification[]
   */
  public function getNotifications()
  {
    return $this->notifications;
  }
  /**
   * Indicates the next steps when no external actions happen on the suspension.
   *
   * @param EnterpriseCrmEventbusProtoSuspensionExpiration $suspensionExpiration
   */
  public function setSuspensionExpiration(EnterpriseCrmEventbusProtoSuspensionExpiration $suspensionExpiration)
  {
    $this->suspensionExpiration = $suspensionExpiration;
  }
  /**
   * @return EnterpriseCrmEventbusProtoSuspensionExpiration
   */
  public function getSuspensionExpiration()
  {
    return $this->suspensionExpiration;
  }
  /**
   * Identities able to resolve this suspension.
   *
   * @param EnterpriseCrmEventbusProtoSuspensionAuthPermissions[] $whoMayResolve
   */
  public function setWhoMayResolve($whoMayResolve)
  {
    $this->whoMayResolve = $whoMayResolve;
  }
  /**
   * @return EnterpriseCrmEventbusProtoSuspensionAuthPermissions[]
   */
  public function getWhoMayResolve()
  {
    return $this->whoMayResolve;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoSuspensionConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoSuspensionConfig');
