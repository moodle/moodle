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

class GoogleCloudApigeeV1ReferenceConfig extends \Google\Model
{
  /**
   * Name of the reference in the following format:
   * `organizations/{org}/environments/{env}/references/{reference}`
   *
   * @var string
   */
  public $name;
  /**
   * Name of the referenced resource in the following format:
   * `organizations/{org}/environments/{env}/keystores/{keystore}` Only
   * references to keystore resources are supported.
   *
   * @var string
   */
  public $resourceName;

  /**
   * Name of the reference in the following format:
   * `organizations/{org}/environments/{env}/references/{reference}`
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
   * Name of the referenced resource in the following format:
   * `organizations/{org}/environments/{env}/keystores/{keystore}` Only
   * references to keystore resources are supported.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ReferenceConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ReferenceConfig');
