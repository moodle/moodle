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

namespace Google\Service\GKEHub;

class ConfigManagementBinauthzState extends \Google\Model
{
  /**
   * Deployment's state cannot be determined.
   */
  public const WEBHOOK_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const WEBHOOK_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const WEBHOOK_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const WEBHOOK_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const WEBHOOK_PENDING = 'PENDING';
  protected $versionType = ConfigManagementBinauthzVersion::class;
  protected $versionDataType = '';
  /**
   * The state of the binauthz webhook.
   *
   * @var string
   */
  public $webhook;

  /**
   * The version of binauthz that is installed.
   *
   * @param ConfigManagementBinauthzVersion $version
   */
  public function setVersion(ConfigManagementBinauthzVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return ConfigManagementBinauthzVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * The state of the binauthz webhook.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::WEBHOOK_* $webhook
   */
  public function setWebhook($webhook)
  {
    $this->webhook = $webhook;
  }
  /**
   * @return self::WEBHOOK_*
   */
  public function getWebhook()
  {
    return $this->webhook;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementBinauthzState::class, 'Google_Service_GKEHub_ConfigManagementBinauthzState');
