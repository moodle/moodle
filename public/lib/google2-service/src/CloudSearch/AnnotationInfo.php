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

namespace Google\Service\CloudSearch;

class AnnotationInfo extends \Google\Collection
{
  protected $collection_key = 'coannotatorDeviceIds';
  /**
   * @var string[]
   */
  public $coannotatorDeviceIds;
  /**
   * @var bool
   */
  public $isActive;

  /**
   * @param string[]
   */
  public function setCoannotatorDeviceIds($coannotatorDeviceIds)
  {
    $this->coannotatorDeviceIds = $coannotatorDeviceIds;
  }
  /**
   * @return string[]
   */
  public function getCoannotatorDeviceIds()
  {
    return $this->coannotatorDeviceIds;
  }
  /**
   * @param bool
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotationInfo::class, 'Google_Service_CloudSearch_AnnotationInfo');
