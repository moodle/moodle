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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaKeyEvent extends \Google\Model
{
  /**
   * Counting method not specified.
   */
  public const COUNTING_METHOD_COUNTING_METHOD_UNSPECIFIED = 'COUNTING_METHOD_UNSPECIFIED';
  /**
   * Each Event instance is considered a Key Event.
   */
  public const COUNTING_METHOD_ONCE_PER_EVENT = 'ONCE_PER_EVENT';
  /**
   * An Event instance is considered a Key Event at most once per session per
   * user.
   */
  public const COUNTING_METHOD_ONCE_PER_SESSION = 'ONCE_PER_SESSION';
  /**
   * Required. The method by which Key Events will be counted across multiple
   * events within a session.
   *
   * @var string
   */
  public $countingMethod;
  /**
   * Output only. Time when this key event was created in the property.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. If set to true, this key event refers to a custom event. If
   * set to false, this key event refers to a default event in GA. Default
   * events typically have special meaning in GA. Default events are usually
   * created for you by the GA system, but in some cases can be created by
   * property admins. Custom events count towards the maximum number of custom
   * key events that may be created per property.
   *
   * @var bool
   */
  public $custom;
  protected $defaultValueType = GoogleAnalyticsAdminV1betaKeyEventDefaultValue::class;
  protected $defaultValueDataType = '';
  /**
   * Output only. If set to true, this event can be deleted.
   *
   * @var bool
   */
  public $deletable;
  /**
   * Immutable. The event name for this key event. Examples: 'click', 'purchase'
   *
   * @var string
   */
  public $eventName;
  /**
   * Output only. Resource name of this key event. Format:
   * properties/{property}/keyEvents/{key_event}
   *
   * @var string
   */
  public $name;

  /**
   * Required. The method by which Key Events will be counted across multiple
   * events within a session.
   *
   * Accepted values: COUNTING_METHOD_UNSPECIFIED, ONCE_PER_EVENT,
   * ONCE_PER_SESSION
   *
   * @param self::COUNTING_METHOD_* $countingMethod
   */
  public function setCountingMethod($countingMethod)
  {
    $this->countingMethod = $countingMethod;
  }
  /**
   * @return self::COUNTING_METHOD_*
   */
  public function getCountingMethod()
  {
    return $this->countingMethod;
  }
  /**
   * Output only. Time when this key event was created in the property.
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
   * Output only. If set to true, this key event refers to a custom event. If
   * set to false, this key event refers to a default event in GA. Default
   * events typically have special meaning in GA. Default events are usually
   * created for you by the GA system, but in some cases can be created by
   * property admins. Custom events count towards the maximum number of custom
   * key events that may be created per property.
   *
   * @param bool $custom
   */
  public function setCustom($custom)
  {
    $this->custom = $custom;
  }
  /**
   * @return bool
   */
  public function getCustom()
  {
    return $this->custom;
  }
  /**
   * Optional. Defines a default value/currency for a key event.
   *
   * @param GoogleAnalyticsAdminV1betaKeyEventDefaultValue $defaultValue
   */
  public function setDefaultValue(GoogleAnalyticsAdminV1betaKeyEventDefaultValue $defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaKeyEventDefaultValue
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Output only. If set to true, this event can be deleted.
   *
   * @param bool $deletable
   */
  public function setDeletable($deletable)
  {
    $this->deletable = $deletable;
  }
  /**
   * @return bool
   */
  public function getDeletable()
  {
    return $this->deletable;
  }
  /**
   * Immutable. The event name for this key event. Examples: 'click', 'purchase'
   *
   * @param string $eventName
   */
  public function setEventName($eventName)
  {
    $this->eventName = $eventName;
  }
  /**
   * @return string
   */
  public function getEventName()
  {
    return $this->eventName;
  }
  /**
   * Output only. Resource name of this key event. Format:
   * properties/{property}/keyEvents/{key_event}
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
class_alias(GoogleAnalyticsAdminV1betaKeyEvent::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaKeyEvent');
