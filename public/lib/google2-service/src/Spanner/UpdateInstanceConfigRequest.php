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

class UpdateInstanceConfigRequest extends \Google\Model
{
  protected $instanceConfigType = InstanceConfig::class;
  protected $instanceConfigDataType = '';
  /**
   * Required. A mask specifying which fields in InstanceConfig should be
   * updated. The field mask must always be specified; this prevents any future
   * fields in InstanceConfig from being erased accidentally by clients that do
   * not know about them. Only display_name and labels can be updated.
   *
   * @var string
   */
  public $updateMask;
  /**
   * An option to validate, but not actually execute, a request, and provide the
   * same response.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. The user instance configuration to update, which must always
   * include the instance configuration name. Otherwise, only fields mentioned
   * in update_mask need be included. To prevent conflicts of concurrent
   * updates, etag can be used.
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
   * Required. A mask specifying which fields in InstanceConfig should be
   * updated. The field mask must always be specified; this prevents any future
   * fields in InstanceConfig from being erased accidentally by clients that do
   * not know about them. Only display_name and labels can be updated.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
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
class_alias(UpdateInstanceConfigRequest::class, 'Google_Service_Spanner_UpdateInstanceConfigRequest');
