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

namespace Google\Service\Spanner;

class CreateInstanceConfigRequest extends \Google\Model
{
  protected $instanceConfigType = InstanceConfig::class;
  protected $instanceConfigDataType = '';
  /**
   * Required. The ID of the instance configuration to create. Valid identifiers
   * are of the form `custom-[-a-z0-9]*[a-z0-9]` and must be between 2 and 64
   * characters in length. The `custom-` prefix is required to avoid name
   * conflicts with Google-managed configurations.
   *
   * @var string
   */
  public $instanceConfigId;
  /**
   * An option to validate, but not actually execute, a request, and provide the
   * same response.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. The `InstanceConfig` proto of the configuration to create.
   * `instance_config.name` must be `/instanceConfigs/`.
   * `instance_config.base_config` must be a Google-managed configuration name,
   * e.g. /instanceConfigs/us-east1, /instanceConfigs/nam3.
   *
   * @param InstanceConfig $instanceConfig
   */
  public function setInstanceConfig(InstanceConfig $instanceConfig)
  {
    $this->instanceConfig = $instanceConfig;
  }
  /**
   * @return InstanceConfig
   */
  public function getInstanceConfig()
  {
    return $this->instanceConfig;
  }
  /**
   * Required. The ID of the instance configuration to create. Valid identifiers
   * are of the form `custom-[-a-z0-9]*[a-z0-9]` and must be between 2 and 64
   * characters in length. The `custom-` prefix is required to avoid name
   * conflicts with Google-managed configurations.
   *
   * @param string $instanceConfigId
   */
  public function setInstanceConfigId($instanceConfigId)
  {
    $this->instanceConfigId = $instanceConfigId;
  }
  /**
   * @return string
   */
  public function getInstanceConfigId()
  {
    return $this->instanceConfigId;
  }
  /**
   * An option to validate, but not actually execute, a request, and provide the
   * same response.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateInstanceConfigRequest::class, 'Google_Service_Spanner_CreateInstanceConfigRequest');
