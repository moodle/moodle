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

namespace Google\Service\ServiceUsage;

class GoogleApiServiceusageV1Service extends \Google\Model
{
  /**
   * The default value, which indicates that the enabled state of the service is
   * unspecified or not meaningful. Currently, all consumers other than projects
   * (such as folders and organizations) are always in this state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The service cannot be used by this consumer. It has either been explicitly
   * disabled, or has never been enabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The service has been explicitly enabled for use by this consumer.
   */
  public const STATE_ENABLED = 'ENABLED';
  protected $configType = GoogleApiServiceusageV1ServiceConfig::class;
  protected $configDataType = '';
  /**
   * The resource name of the consumer and service. A valid name would be: -
   * projects/123/services/serviceusage.googleapis.com
   *
   * @var string
   */
  public $name;
  /**
   * The resource name of the consumer. A valid name would be: - projects/123
   *
   * @var string
   */
  public $parent;
  /**
   * Whether or not the service has been enabled for use by the consumer.
   *
   * @var string
   */
  public $state;

  /**
   * The service configuration of the available service. Some fields may be
   * filtered out of the configuration in responses to the `ListServices`
   * method. These fields are present only in responses to the `GetService`
   * method.
   *
   * @param GoogleApiServiceusageV1ServiceConfig $config
   */
  public function setConfig(GoogleApiServiceusageV1ServiceConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleApiServiceusageV1ServiceConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * The resource name of the consumer and service. A valid name would be: -
   * projects/123/services/serviceusage.googleapis.com
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The resource name of the consumer. A valid name would be: - projects/123
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Whether or not the service has been enabled for use by the consumer.
   *
   * Accepted values: STATE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiServiceusageV1Service::class, 'Google_Service_ServiceUsage_GoogleApiServiceusageV1Service');
