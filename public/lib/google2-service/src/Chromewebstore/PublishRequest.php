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

namespace Google\Service\Chromewebstore;

class PublishRequest extends \Google\Model
{
  /**
   * @var int
   */
  public $deployPercentage;
  /**
   * @var bool
   */
  public $reviewExemption;
  /**
   * @var string
   */
  public $target;

  /**
   * @param int
   */
  public function setDeployPercentage($deployPercentage)
  {
    $this->deployPercentage = $deployPercentage;
  }
  /**
   * @return int
   */
  public function getDeployPercentage()
  {
    return $this->deployPercentage;
  }
  /**
   * @param bool
   */
  public function setReviewExemption($reviewExemption)
  {
    $this->reviewExemption = $reviewExemption;
  }
  /**
   * @return bool
   */
  public function getReviewExemption()
  {
    return $this->reviewExemption;
  }
  /**
   * @param string
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishRequest::class, 'Google_Service_Chromewebstore_PublishRequest');
