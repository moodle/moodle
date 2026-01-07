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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ReadTensorboardUsageResponsePerUserUsageData extends \Google\Model
{
  /**
   * User's username
   *
   * @var string
   */
  public $username;
  /**
   * Number of times the user has read data within the Tensorboard.
   *
   * @var string
   */
  public $viewCount;

  /**
   * User's username
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * Number of times the user has read data within the Tensorboard.
   *
   * @param string $viewCount
   */
  public function setViewCount($viewCount)
  {
    $this->viewCount = $viewCount;
  }
  /**
   * @return string
   */
  public function getViewCount()
  {
    return $this->viewCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadTensorboardUsageResponsePerUserUsageData::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadTensorboardUsageResponsePerUserUsageData');
