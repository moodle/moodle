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

class GoogleAnalyticsAdminV1betaConversionEvent extends \Google\Model
{
  /**
   * Counting method not specified.
   */
  public const COUNTING_METHOD_CONVERSION_COUNTING_METHOD_UNSPECIFIED = 'CONVERSION_COUNTING_METHOD_UNSPECIFIED';
  /**
   * Each Event instance is considered a Conversion.
   */
  public const COUNTING_METHOD_ONCE_PER_EVENT = 'ONCE_PER_EVENT';
  /**
   * An Event instance is considered a Conversion at most once per session per
   * user.
   */
  public const COUNTING_METHOD_ONCE_PER_SESSION = 'ONCE_PER_SESSION';
  /**
   * Optional. The method by which conversions will be counted across multiple
   * events within a session. If this value is not provided, it will be set to
   * `ONCE_PER_EVENT`.
   *
   * @var string
   */
  public $countingMethod;
  /**
   * Output only. Time when this conversion event was created in the property.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. If set to true, this conversion event refers to a custom
   * event. If set to false, this conversion event refers to a default event in
   * GA. Default events typically have special meaning in GA. Default events are
   * usually created for you by the GA system, but in some cases can be created
   * by property admins. Custom events count towards the maximum number of
   * custom conversion events that may be created per property.
   *
   * @var bool
   */
  public $custom;
  protected $defaultConversionValueType = GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue::class;
  protected $defaultConversionValueDataType = '';
  /**
   * Output only. If set, this event can currently be deleted with
   * DeleteConversionEvent.
   *
   * @var bool
   */
  public $deletable;
  /**
   * Immutable. The event name for this conversion event. Examples: 'click',
   * 'purchase'
   *
   * @var string
   */
  public $eventName;
  /**
   * Output only. Resource name of this conversion event. Format:
   * properties/{property}/conversionEvents/{conversion_event}
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The method by which conversions will be counted across multiple
   * events within a session. If this value is not provided, it will be set to
   * `ONCE_PER_EVENT`.
   *
   * Accepted values: CONVERSION_COUNTING_METHOD_UNSPECIFIED, ONCE_PER_EVENT,
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
   * Output only. Time when this conversion event was created in the property.
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
   * Output only. If set to true, this conversion event refers to a custom
   * event. If set to false, this conversion event refers to a default event in
   * GA. Default events typically have special meaning in GA. Default events are
   * usually created for you by the GA system, but in some cases can be created
   * by property admins. Custom events count towards the maximum number of
   * custom conversion events that may be created per property.
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
   * Optional. Defines a default value/currency for a conversion event.
   *
   * @param GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue $defaultConversionValue
   */
  public function setDefaultConversionValue(GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue $defaultConversionValue)
  {
    $this->defaultConversionValue = $defaultConversionValue;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaConversionEventDefaultConversionValue
   */
  public function getDefaultConversionValue()
  {
    return $this->defaultConversionValue;
  }
  /**
   * Output only. If set, this event can currently be deleted with
   * DeleteConversionEvent.
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
   * Immutable. The event name for this conversion event. Examples: 'click',
   * 'purchase'
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
   * Output only. Resource name of this conversion event. Format:
   * properties/{property}/conversionEvents/{conversion_event}
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
class_alias(GoogleAnalyticsAdminV1betaConversionEvent::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaConversionEvent');
