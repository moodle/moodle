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

namespace Google\Service\ShoppingContent;

class AttributionSettingsConversionType extends \Google\Model
{
  /**
   * Output only. Option indicating if the type should be included in Merchant
   * Center reporting.
   *
   * @var bool
   */
  public $includeInReporting;
  /**
   * Output only. Conversion event name, as it'll be reported by the client.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Option indicating if the type should be included in Merchant
   * Center reporting.
   *
   * @param bool $includeInReporting
   */
  public function setIncludeInReporting($includeInReporting)
  {
    $this->includeInReporting = $includeInReporting;
  }
  /**
   * @return bool
   */
  public function getIncludeInReporting()
  {
    return $this->includeInReporting;
  }
  /**
   * Output only. Conversion event name, as it'll be reported by the client.
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
class_alias(AttributionSettingsConversionType::class, 'Google_Service_ShoppingContent_AttributionSettingsConversionType');
