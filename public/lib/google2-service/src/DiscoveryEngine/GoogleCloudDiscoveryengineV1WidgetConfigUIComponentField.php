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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField extends \Google\Collection
{
  protected $collection_key = 'deviceVisibility';
  /**
   * The field visibility on different types of devices.
   *
   * @var string[]
   */
  public $deviceVisibility;
  /**
   * The template to customize how the field is displayed. An example value
   * would be a string that looks like: "Price: {value}".
   *
   * @var string
   */
  public $displayTemplate;
  /**
   * Required. Registered field name. The format is `field.abc`.
   *
   * @var string
   */
  public $field;

  /**
   * The field visibility on different types of devices.
   *
   * @param string[] $deviceVisibility
   */
  public function setDeviceVisibility($deviceVisibility)
  {
    $this->deviceVisibility = $deviceVisibility;
  }
  /**
   * @return string[]
   */
  public function getDeviceVisibility()
  {
    return $this->deviceVisibility;
  }
  /**
   * The template to customize how the field is displayed. An example value
   * would be a string that looks like: "Price: {value}".
   *
   * @param string $displayTemplate
   */
  public function setDisplayTemplate($displayTemplate)
  {
    $this->displayTemplate = $displayTemplate;
  }
  /**
   * @return string
   */
  public function getDisplayTemplate()
  {
    return $this->displayTemplate;
  }
  /**
   * Required. Registered field name. The format is `field.abc`.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigUIComponentField');
