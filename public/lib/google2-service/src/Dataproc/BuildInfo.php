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

namespace Google\Service\Dataproc;

class BuildInfo extends \Google\Model
{
  /**
   * Optional. Build key.
   *
   * @var string
   */
  public $buildKey;
  /**
   * Optional. Build value.
   *
   * @var string
   */
  public $buildValue;

  /**
   * Optional. Build key.
   *
   * @param string $buildKey
   */
  public function setBuildKey($buildKey)
  {
    $this->buildKey = $buildKey;
  }
  /**
   * @return string
   */
  public function getBuildKey()
  {
    return $this->buildKey;
  }
  /**
   * Optional. Build value.
   *
   * @param string $buildValue
   */
  public function setBuildValue($buildValue)
  {
    $this->buildValue = $buildValue;
  }
  /**
   * @return string
   */
  public function getBuildValue()
  {
    return $this->buildValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildInfo::class, 'Google_Service_Dataproc_BuildInfo');
