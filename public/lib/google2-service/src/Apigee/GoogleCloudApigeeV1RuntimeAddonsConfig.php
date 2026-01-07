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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1RuntimeAddonsConfig extends \Google\Model
{
  protected $analyticsConfigType = GoogleCloudApigeeV1RuntimeAnalyticsConfig::class;
  protected $analyticsConfigDataType = '';
  protected $apiSecurityConfigType = GoogleCloudApigeeV1RuntimeApiSecurityConfig::class;
  protected $apiSecurityConfigDataType = '';
  /**
   * Name of the addons config in the format:
   * `organizations/{org}/environments/{env}/addonsConfig`
   *
   * @var string
   */
  public $name;
  /**
   * Revision number used by the runtime to detect config changes.
   *
   * @var string
   */
  public $revisionId;
  /**
   * UID is to detect if config is recreated after deletion. The add-on config
   * will only be deleted when the environment itself gets deleted, thus it will
   * always be the same as the UID of EnvironmentConfig.
   *
   * @var string
   */
  public $uid;

  /**
   * Runtime configuration for Analytics add-on.
   *
   * @param GoogleCloudApigeeV1RuntimeAnalyticsConfig $analyticsConfig
   */
  public function setAnalyticsConfig(GoogleCloudApigeeV1RuntimeAnalyticsConfig $analyticsConfig)
  {
    $this->analyticsConfig = $analyticsConfig;
  }
  /**
   * @return GoogleCloudApigeeV1RuntimeAnalyticsConfig
   */
  public function getAnalyticsConfig()
  {
    return $this->analyticsConfig;
  }
  /**
   * Runtime configuration for API Security add-on.
   *
   * @param GoogleCloudApigeeV1RuntimeApiSecurityConfig $apiSecurityConfig
   */
  public function setApiSecurityConfig(GoogleCloudApigeeV1RuntimeApiSecurityConfig $apiSecurityConfig)
  {
    $this->apiSecurityConfig = $apiSecurityConfig;
  }
  /**
   * @return GoogleCloudApigeeV1RuntimeApiSecurityConfig
   */
  public function getApiSecurityConfig()
  {
    return $this->apiSecurityConfig;
  }
  /**
   * Name of the addons config in the format:
   * `organizations/{org}/environments/{env}/addonsConfig`
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
   * Revision number used by the runtime to detect config changes.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * UID is to detect if config is recreated after deletion. The add-on config
   * will only be deleted when the environment itself gets deleted, thus it will
   * always be the same as the UID of EnvironmentConfig.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RuntimeAddonsConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RuntimeAddonsConfig');
