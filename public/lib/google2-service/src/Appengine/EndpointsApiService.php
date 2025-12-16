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

namespace Google\Service\Appengine;

class EndpointsApiService extends \Google\Model
{
  /**
   * Not specified. Defaults to FIXED.
   */
  public const ROLLOUT_STRATEGY_UNSPECIFIED_ROLLOUT_STRATEGY = 'UNSPECIFIED_ROLLOUT_STRATEGY';
  /**
   * Endpoints service configuration ID will be fixed to the configuration ID
   * specified by config_id.
   */
  public const ROLLOUT_STRATEGY_FIXED = 'FIXED';
  /**
   * Endpoints service configuration ID will be updated with each rollout.
   */
  public const ROLLOUT_STRATEGY_MANAGED = 'MANAGED';
  /**
   * Endpoints service configuration ID as specified by the Service Management
   * API. For example "2016-09-19r1".By default, the rollout strategy for
   * Endpoints is RolloutStrategy.FIXED. This means that Endpoints starts up
   * with a particular configuration ID. When a new configuration is rolled out,
   * Endpoints must be given the new configuration ID. The config_id field is
   * used to give the configuration ID and is required in this case.Endpoints
   * also has a rollout strategy called RolloutStrategy.MANAGED. When using
   * this, Endpoints fetches the latest configuration and does not need the
   * configuration ID. In this case, config_id must be omitted.
   *
   * @var string
   */
  public $configId;
  /**
   * Enable or disable trace sampling. By default, this is set to false for
   * enabled.
   *
   * @var bool
   */
  public $disableTraceSampling;
  /**
   * Endpoints service name which is the name of the "service" resource in the
   * Service Management API. For example "myapi.endpoints.myproject.cloud.goog"
   *
   * @var string
   */
  public $name;
  /**
   * Endpoints rollout strategy. If FIXED, config_id must be specified. If
   * MANAGED, config_id must be omitted.
   *
   * @var string
   */
  public $rolloutStrategy;

  /**
   * Endpoints service configuration ID as specified by the Service Management
   * API. For example "2016-09-19r1".By default, the rollout strategy for
   * Endpoints is RolloutStrategy.FIXED. This means that Endpoints starts up
   * with a particular configuration ID. When a new configuration is rolled out,
   * Endpoints must be given the new configuration ID. The config_id field is
   * used to give the configuration ID and is required in this case.Endpoints
   * also has a rollout strategy called RolloutStrategy.MANAGED. When using
   * this, Endpoints fetches the latest configuration and does not need the
   * configuration ID. In this case, config_id must be omitted.
   *
   * @param string $configId
   */
  public function setConfigId($configId)
  {
    $this->configId = $configId;
  }
  /**
   * @return string
   */
  public function getConfigId()
  {
    return $this->configId;
  }
  /**
   * Enable or disable trace sampling. By default, this is set to false for
   * enabled.
   *
   * @param bool $disableTraceSampling
   */
  public function setDisableTraceSampling($disableTraceSampling)
  {
    $this->disableTraceSampling = $disableTraceSampling;
  }
  /**
   * @return bool
   */
  public function getDisableTraceSampling()
  {
    return $this->disableTraceSampling;
  }
  /**
   * Endpoints service name which is the name of the "service" resource in the
   * Service Management API. For example "myapi.endpoints.myproject.cloud.goog"
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
   * Endpoints rollout strategy. If FIXED, config_id must be specified. If
   * MANAGED, config_id must be omitted.
   *
   * Accepted values: UNSPECIFIED_ROLLOUT_STRATEGY, FIXED, MANAGED
   *
   * @param self::ROLLOUT_STRATEGY_* $rolloutStrategy
   */
  public function setRolloutStrategy($rolloutStrategy)
  {
    $this->rolloutStrategy = $rolloutStrategy;
  }
  /**
   * @return self::ROLLOUT_STRATEGY_*
   */
  public function getRolloutStrategy()
  {
    return $this->rolloutStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EndpointsApiService::class, 'Google_Service_Appengine_EndpointsApiService');
