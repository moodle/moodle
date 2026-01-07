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

namespace Google\Service\MyBusinessBusinessInformation;

class AttributeValueMetadata extends \Google\Model
{
  /**
   * The display name for this value, localized where available; otherwise, in
   * English. The value display name is intended to be used in context with the
   * attribute display name. For example, for a "WiFi" enum attribute, this
   * could contain "Paid" to represent paid Wi-Fi.
   *
   * @var string
   */
  public $displayName;
  /**
   * The attribute value.
   *
   * @var array
   */
  public $value;

  /**
   * The display name for this value, localized where available; otherwise, in
   * English. The value display name is intended to be used in context with the
   * attribute display name. For example, for a "WiFi" enum attribute, this
   * could contain "Paid" to represent paid Wi-Fi.
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
   * The attribute value.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttributeValueMetadata::class, 'Google_Service_MyBusinessBusinessInformation_AttributeValueMetadata');
