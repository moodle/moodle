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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1SourceEnvironment extends \Google\Model
{
  /**
   * Optional. The time at which the environment was created at the source.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The name of the environment at the source. This should map to
   * Deployment.
   *
   * @var string
   */
  public $sourceEnvironment;
  /**
   * The location where additional information about source environments can be
   * found. The location should be relative path of the environment manifest
   * with respect to a plugin instance.
   *
   * @var string
   */
  public $sourceEnvironmentUri;
  /**
   * Optional. The time at which the environment was last updated at the source.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The time at which the environment was created at the source.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The name of the environment at the source. This should map to
   * Deployment.
   *
   * @param string $sourceEnvironment
   */
  public function setSourceEnvironment($sourceEnvironment)
  {
    $this->sourceEnvironment = $sourceEnvironment;
  }
  /**
   * @return string
   */
  public function getSourceEnvironment()
  {
    return $this->sourceEnvironment;
  }
  /**
   * The location where additional information about source environments can be
   * found. The location should be relative path of the environment manifest
   * with respect to a plugin instance.
   *
   * @param string $sourceEnvironmentUri
   */
  public function setSourceEnvironmentUri($sourceEnvironmentUri)
  {
    $this->sourceEnvironmentUri = $sourceEnvironmentUri;
  }
  /**
   * @return string
   */
  public function getSourceEnvironmentUri()
  {
    return $this->sourceEnvironmentUri;
  }
  /**
   * Optional. The time at which the environment was last updated at the source.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1SourceEnvironment::class, 'Google_Service_APIhub_GoogleCloudApihubV1SourceEnvironment');
