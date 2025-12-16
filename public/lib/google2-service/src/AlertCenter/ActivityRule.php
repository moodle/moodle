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

namespace Google\Service\AlertCenter;

class ActivityRule extends \Google\Collection
{
  protected $collection_key = 'supersededAlerts';
  /**
   * List of action names associated with the rule threshold.
   *
   * @var string[]
   */
  public $actionNames;
  /**
   * Rule create timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Description of the rule.
   *
   * @var string
   */
  public $description;
  /**
   * Alert display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Rule name.
   *
   * @var string
   */
  public $name;
  /**
   * Query that is used to get the data from the associated source.
   *
   * @var string
   */
  public $query;
  /**
   * List of alert IDs superseded by this alert. It is used to indicate that
   * this alert is essentially extension of superseded alerts and we found the
   * relationship after creating these alerts.
   *
   * @var string[]
   */
  public $supersededAlerts;
  /**
   * Alert ID superseding this alert. It is used to indicate that superseding
   * alert is essentially extension of this alert and we found the relationship
   * after creating both alerts.
   *
   * @var string
   */
  public $supersedingAlert;
  /**
   * Alert threshold is for example “COUNT > 5”.
   *
   * @var string
   */
  public $threshold;
  /**
   * The trigger sources for this rule. * GMAIL_EVENTS * DEVICE_EVENTS *
   * USER_EVENTS
   *
   * @var string
   */
  public $triggerSource;
  /**
   * The timestamp of the last update to the rule.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Rule window size. Possible values are 1 hour or 24 hours.
   *
   * @var string
   */
  public $windowSize;

  /**
   * List of action names associated with the rule threshold.
   *
   * @param string[] $actionNames
   */
  public function setActionNames($actionNames)
  {
    $this->actionNames = $actionNames;
  }
  /**
   * @return string[]
   */
  public function getActionNames()
  {
    return $this->actionNames;
  }
  /**
   * Rule create timestamp.
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
   * Description of the rule.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Alert display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Rule name.
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
   * Query that is used to get the data from the associated source.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * List of alert IDs superseded by this alert. It is used to indicate that
   * this alert is essentially extension of superseded alerts and we found the
   * relationship after creating these alerts.
   *
   * @param string[] $supersededAlerts
   */
  public function setSupersededAlerts($supersededAlerts)
  {
    $this->supersededAlerts = $supersededAlerts;
  }
  /**
   * @return string[]
   */
  public function getSupersededAlerts()
  {
    return $this->supersededAlerts;
  }
  /**
   * Alert ID superseding this alert. It is used to indicate that superseding
   * alert is essentially extension of this alert and we found the relationship
   * after creating both alerts.
   *
   * @param string $supersedingAlert
   */
  public function setSupersedingAlert($supersedingAlert)
  {
    $this->supersedingAlert = $supersedingAlert;
  }
  /**
   * @return string
   */
  public function getSupersedingAlert()
  {
    return $this->supersedingAlert;
  }
  /**
   * Alert threshold is for example “COUNT > 5”.
   *
   * @param string $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return string
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * The trigger sources for this rule. * GMAIL_EVENTS * DEVICE_EVENTS *
   * USER_EVENTS
   *
   * @param string $triggerSource
   */
  public function setTriggerSource($triggerSource)
  {
    $this->triggerSource = $triggerSource;
  }
  /**
   * @return string
   */
  public function getTriggerSource()
  {
    return $this->triggerSource;
  }
  /**
   * The timestamp of the last update to the rule.
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
  /**
   * Rule window size. Possible values are 1 hour or 24 hours.
   *
   * @param string $windowSize
   */
  public function setWindowSize($windowSize)
  {
    $this->windowSize = $windowSize;
  }
  /**
   * @return string
   */
  public function getWindowSize()
  {
    return $this->windowSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityRule::class, 'Google_Service_AlertCenter_ActivityRule');
