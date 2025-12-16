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

namespace Google\Service\ServiceUsage;

class ValueInfo extends \Google\Model
{
  protected $groupValueType = GroupValue::class;
  protected $groupValueDataType = '';
  /**
   * @var string
   */
  public $learnmoreLink;
  protected $serviceValueType = ServiceValue::class;
  protected $serviceValueDataType = '';
  /**
   * @var string
   */
  public $summary;
  /**
   * @var string
   */
  public $title;

  /**
   * @param GroupValue
   */
  public function setGroupValue(GroupValue $groupValue)
  {
    $this->groupValue = $groupValue;
  }
  /**
   * @return GroupValue
   */
  public function getGroupValue()
  {
    return $this->groupValue;
  }
  /**
   * @param string
   */
  public function setLearnmoreLink($learnmoreLink)
  {
    $this->learnmoreLink = $learnmoreLink;
  }
  /**
   * @return string
   */
  public function getLearnmoreLink()
  {
    return $this->learnmoreLink;
  }
  /**
   * @param ServiceValue
   */
  public function setServiceValue(ServiceValue $serviceValue)
  {
    $this->serviceValue = $serviceValue;
  }
  /**
   * @return ServiceValue
   */
  public function getServiceValue()
  {
    return $this->serviceValue;
  }
  /**
   * @param string
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * @param string
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueInfo::class, 'Google_Service_ServiceUsage_ValueInfo');
