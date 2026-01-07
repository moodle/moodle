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

namespace Google\Service\SecurityCommandCenter;

class CreateResourceValueConfigRequest extends \Google\Model
{
  /**
   * Required. Resource name of the new ResourceValueConfig's parent.
   *
   * @var string
   */
  public $parent;
  protected $resourceValueConfigType = GoogleCloudSecuritycenterV1ResourceValueConfig::class;
  protected $resourceValueConfigDataType = '';

  /**
   * Required. Resource name of the new ResourceValueConfig's parent.
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
   * Required. The resource value config being created.
   *
   * @param GoogleCloudSecuritycenterV1ResourceValueConfig $resourceValueConfig
   */
  public function setResourceValueConfig(GoogleCloudSecuritycenterV1ResourceValueConfig $resourceValueConfig)
  {
    $this->resourceValueConfig = $resourceValueConfig;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceValueConfig
   */
  public function getResourceValueConfig()
  {
    return $this->resourceValueConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateResourceValueConfigRequest::class, 'Google_Service_SecurityCommandCenter_CreateResourceValueConfigRequest');
