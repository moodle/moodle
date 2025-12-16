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

namespace Google\Service\Monitoring;

class ServiceAgentAuthentication extends \Google\Model
{
  /**
   * Default value, will result in OIDC Authentication.
   */
  public const TYPE_SERVICE_AGENT_AUTHENTICATION_TYPE_UNSPECIFIED = 'SERVICE_AGENT_AUTHENTICATION_TYPE_UNSPECIFIED';
  /**
   * OIDC Authentication
   */
  public const TYPE_OIDC_TOKEN = 'OIDC_TOKEN';
  /**
   * Type of authentication.
   *
   * @var string
   */
  public $type;

  /**
   * Type of authentication.
   *
   * Accepted values: SERVICE_AGENT_AUTHENTICATION_TYPE_UNSPECIFIED, OIDC_TOKEN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAgentAuthentication::class, 'Google_Service_Monitoring_ServiceAgentAuthentication');
