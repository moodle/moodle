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

class GoogleCloudApigeeV1EnvironmentGroupAttachment extends \Google\Model
{
  /**
   * Output only. The time at which the environment group attachment was created
   * as milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Required. ID of the attached environment.
   *
   * @var string
   */
  public $environment;
  /**
   * Output only. ID of the environment group.
   *
   * @var string
   */
  public $environmentGroupId;
  /**
   * ID of the environment group attachment.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The time at which the environment group attachment was created
   * as milliseconds since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Required. ID of the attached environment.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Output only. ID of the environment group.
   *
   * @param string $environmentGroupId
   */
  public function setEnvironmentGroupId($environmentGroupId)
  {
    $this->environmentGroupId = $environmentGroupId;
  }
  /**
   * @return string
   */
  public function getEnvironmentGroupId()
  {
    return $this->environmentGroupId;
  }
  /**
   * ID of the environment group attachment.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentGroupAttachment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentGroupAttachment');
