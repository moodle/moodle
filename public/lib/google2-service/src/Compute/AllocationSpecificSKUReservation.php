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

namespace Google\Service\Compute;

class AllocationSpecificSKUReservation extends \Google\Model
{
  /**
   * Output only. [Output Only] Indicates how many instances are actually usable
   * currently.
   *
   * @var string
   */
  public $assuredCount;
  /**
   * Specifies the number of resources that are allocated.
   *
   * @var string
   */
  public $count;
  /**
   * Output only. [Output Only] Indicates how many instances are in use.
   *
   * @var string
   */
  public $inUseCount;
  protected $instancePropertiesType = AllocationSpecificSKUAllocationReservedInstanceProperties::class;
  protected $instancePropertiesDataType = '';
  /**
   * Specifies the instance template to create the reservation. If you use this
   * field, you must exclude the instanceProperties field.
   *
   * This field is optional, and it can be a full or partial URL. For example,
   * the following are all valid URLs to an instance template:                -
   * https://www.googleapis.com/compute/v1/projects/project/global/instanceTempl
   * ates/instanceTemplate       -
   * projects/project/global/instanceTemplates/instanceTemplate       -
   * global/instanceTemplates/instanceTemplate
   *
   * @var string
   */
  public $sourceInstanceTemplate;

  /**
   * Output only. [Output Only] Indicates how many instances are actually usable
   * currently.
   *
   * @param string $assuredCount
   */
  public function setAssuredCount($assuredCount)
  {
    $this->assuredCount = $assuredCount;
  }
  /**
   * @return string
   */
  public function getAssuredCount()
  {
    return $this->assuredCount;
  }
  /**
   * Specifies the number of resources that are allocated.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. [Output Only] Indicates how many instances are in use.
   *
   * @param string $inUseCount
   */
  public function setInUseCount($inUseCount)
  {
    $this->inUseCount = $inUseCount;
  }
  /**
   * @return string
   */
  public function getInUseCount()
  {
    return $this->inUseCount;
  }
  /**
   * The instance properties for the reservation.
   *
   * @param AllocationSpecificSKUAllocationReservedInstanceProperties $instanceProperties
   */
  public function setInstanceProperties(AllocationSpecificSKUAllocationReservedInstanceProperties $instanceProperties)
  {
    $this->instanceProperties = $instanceProperties;
  }
  /**
   * @return AllocationSpecificSKUAllocationReservedInstanceProperties
   */
  public function getInstanceProperties()
  {
    return $this->instanceProperties;
  }
  /**
   * Specifies the instance template to create the reservation. If you use this
   * field, you must exclude the instanceProperties field.
   *
   * This field is optional, and it can be a full or partial URL. For example,
   * the following are all valid URLs to an instance template:                -
   * https://www.googleapis.com/compute/v1/projects/project/global/instanceTempl
   * ates/instanceTemplate       -
   * projects/project/global/instanceTemplates/instanceTemplate       -
   * global/instanceTemplates/instanceTemplate
   *
   * @param string $sourceInstanceTemplate
   */
  public function setSourceInstanceTemplate($sourceInstanceTemplate)
  {
    $this->sourceInstanceTemplate = $sourceInstanceTemplate;
  }
  /**
   * @return string
   */
  public function getSourceInstanceTemplate()
  {
    return $this->sourceInstanceTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationSpecificSKUReservation::class, 'Google_Service_Compute_AllocationSpecificSKUReservation');
