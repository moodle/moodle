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

namespace Google\Service\Looker;

class UserMetadata extends \Google\Model
{
  /**
   * Optional. The number of additional developer users the instance owner has
   * purchased.
   *
   * @var int
   */
  public $additionalDeveloperUserCount;
  /**
   * Optional. The number of additional standard users the instance owner has
   * purchased.
   *
   * @var int
   */
  public $additionalStandardUserCount;
  /**
   * Optional. The number of additional viewer users the instance owner has
   * purchased.
   *
   * @var int
   */
  public $additionalViewerUserCount;

  /**
   * Optional. The number of additional developer users the instance owner has
   * purchased.
   *
   * @param int $additionalDeveloperUserCount
   */
  public function setAdditionalDeveloperUserCount($additionalDeveloperUserCount)
  {
    $this->additionalDeveloperUserCount = $additionalDeveloperUserCount;
  }
  /**
   * @return int
   */
  public function getAdditionalDeveloperUserCount()
  {
    return $this->additionalDeveloperUserCount;
  }
  /**
   * Optional. The number of additional standard users the instance owner has
   * purchased.
   *
   * @param int $additionalStandardUserCount
   */
  public function setAdditionalStandardUserCount($additionalStandardUserCount)
  {
    $this->additionalStandardUserCount = $additionalStandardUserCount;
  }
  /**
   * @return int
   */
  public function getAdditionalStandardUserCount()
  {
    return $this->additionalStandardUserCount;
  }
  /**
   * Optional. The number of additional viewer users the instance owner has
   * purchased.
   *
   * @param int $additionalViewerUserCount
   */
  public function setAdditionalViewerUserCount($additionalViewerUserCount)
  {
    $this->additionalViewerUserCount = $additionalViewerUserCount;
  }
  /**
   * @return int
   */
  public function getAdditionalViewerUserCount()
  {
    return $this->additionalViewerUserCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserMetadata::class, 'Google_Service_Looker_UserMetadata');
