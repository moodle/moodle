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

namespace Google\Service\DisplayVideo;

class FloodlightGroup extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const WEB_TAG_TYPE_WEB_TAG_TYPE_UNSPECIFIED = 'WEB_TAG_TYPE_UNSPECIFIED';
  /**
   * No tag type.
   */
  public const WEB_TAG_TYPE_WEB_TAG_TYPE_NONE = 'WEB_TAG_TYPE_NONE';
  /**
   * Image tag.
   */
  public const WEB_TAG_TYPE_WEB_TAG_TYPE_IMAGE = 'WEB_TAG_TYPE_IMAGE';
  /**
   * Dynamic tag.
   */
  public const WEB_TAG_TYPE_WEB_TAG_TYPE_DYNAMIC = 'WEB_TAG_TYPE_DYNAMIC';
  protected $activeViewConfigType = ActiveViewVideoViewabilityMetricConfig::class;
  protected $activeViewConfigDataType = '';
  /**
   * User-defined custom variables owned by the Floodlight group. Use custom
   * Floodlight variables to create reporting data that is tailored to your
   * unique business needs. Custom Floodlight variables use the keys `U1=`,
   * `U2=`, and so on, and can take any values that you choose to pass to them.
   * You can use them to track virtually any type of data that you collect about
   * your customers, such as the genre of movie that a customer purchases, the
   * country to which the item is shipped, and so on. Custom Floodlight
   * variables may not be used to pass any data that could be used or recognized
   * as personally identifiable information (PII). Example: `custom_variables {
   * fields { "U1": value { number_value: 123.4 }, "U2": value { string_value:
   * "MyVariable2" }, "U3": value { string_value: "MyVariable3" } } }`
   * Acceptable values for keys are "U1" through "U100", inclusive. String
   * values must be less than 64 characters long, and cannot contain the
   * following characters: `"<>`.
   *
   * @var array[]
   */
  public $customVariables;
  /**
   * Required. The display name of the Floodlight group.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The unique ID of the Floodlight group. Assigned by the system.
   *
   * @var string
   */
  public $floodlightGroupId;
  protected $lookbackWindowType = LookbackWindow::class;
  protected $lookbackWindowDataType = '';
  /**
   * Output only. The resource name of the Floodlight group.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The web tag type enabled for the Floodlight group.
   *
   * @var string
   */
  public $webTagType;

  /**
   * The Active View video viewability metric configuration for the Floodlight
   * group.
   *
   * @param ActiveViewVideoViewabilityMetricConfig $activeViewConfig
   */
  public function setActiveViewConfig(ActiveViewVideoViewabilityMetricConfig $activeViewConfig)
  {
    $this->activeViewConfig = $activeViewConfig;
  }
  /**
   * @return ActiveViewVideoViewabilityMetricConfig
   */
  public function getActiveViewConfig()
  {
    return $this->activeViewConfig;
  }
  /**
   * User-defined custom variables owned by the Floodlight group. Use custom
   * Floodlight variables to create reporting data that is tailored to your
   * unique business needs. Custom Floodlight variables use the keys `U1=`,
   * `U2=`, and so on, and can take any values that you choose to pass to them.
   * You can use them to track virtually any type of data that you collect about
   * your customers, such as the genre of movie that a customer purchases, the
   * country to which the item is shipped, and so on. Custom Floodlight
   * variables may not be used to pass any data that could be used or recognized
   * as personally identifiable information (PII). Example: `custom_variables {
   * fields { "U1": value { number_value: 123.4 }, "U2": value { string_value:
   * "MyVariable2" }, "U3": value { string_value: "MyVariable3" } } }`
   * Acceptable values for keys are "U1" through "U100", inclusive. String
   * values must be less than 64 characters long, and cannot contain the
   * following characters: `"<>`.
   *
   * @param array[] $customVariables
   */
  public function setCustomVariables($customVariables)
  {
    $this->customVariables = $customVariables;
  }
  /**
   * @return array[]
   */
  public function getCustomVariables()
  {
    return $this->customVariables;
  }
  /**
   * Required. The display name of the Floodlight group.
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
   * Output only. The unique ID of the Floodlight group. Assigned by the system.
   *
   * @param string $floodlightGroupId
   */
  public function setFloodlightGroupId($floodlightGroupId)
  {
    $this->floodlightGroupId = $floodlightGroupId;
  }
  /**
   * @return string
   */
  public function getFloodlightGroupId()
  {
    return $this->floodlightGroupId;
  }
  /**
   * Required. The lookback window for the Floodlight group. Both click_days and
   * impression_days are required. Acceptable values for both are `0` to `90`,
   * inclusive.
   *
   * @param LookbackWindow $lookbackWindow
   */
  public function setLookbackWindow(LookbackWindow $lookbackWindow)
  {
    $this->lookbackWindow = $lookbackWindow;
  }
  /**
   * @return LookbackWindow
   */
  public function getLookbackWindow()
  {
    return $this->lookbackWindow;
  }
  /**
   * Output only. The resource name of the Floodlight group.
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
   * Required. The web tag type enabled for the Floodlight group.
   *
   * Accepted values: WEB_TAG_TYPE_UNSPECIFIED, WEB_TAG_TYPE_NONE,
   * WEB_TAG_TYPE_IMAGE, WEB_TAG_TYPE_DYNAMIC
   *
   * @param self::WEB_TAG_TYPE_* $webTagType
   */
  public function setWebTagType($webTagType)
  {
    $this->webTagType = $webTagType;
  }
  /**
   * @return self::WEB_TAG_TYPE_*
   */
  public function getWebTagType()
  {
    return $this->webTagType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightGroup::class, 'Google_Service_DisplayVideo_FloodlightGroup');
