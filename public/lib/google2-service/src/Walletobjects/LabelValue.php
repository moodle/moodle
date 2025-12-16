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

namespace Google\Service\Walletobjects;

class LabelValue extends \Google\Model
{
  /**
   * The label for a specific row and column. Recommended maximum is 15
   * characters for a two-column layout and 30 characters for a one-column
   * layout.
   *
   * @var string
   */
  public $label;
  protected $localizedLabelType = LocalizedString::class;
  protected $localizedLabelDataType = '';
  protected $localizedValueType = LocalizedString::class;
  protected $localizedValueDataType = '';
  /**
   * The value for a specific row and column. Recommended maximum is 15
   * characters for a two-column layout and 30 characters for a one-column
   * layout.
   *
   * @var string
   */
  public $value;

  /**
   * The label for a specific row and column. Recommended maximum is 15
   * characters for a two-column layout and 30 characters for a one-column
   * layout.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Translated strings for the label. Recommended maximum is 15 characters for
   * a two-column layout and 30 characters for a one-column layout.
   *
   * @param LocalizedString $localizedLabel
   */
  public function setLocalizedLabel(LocalizedString $localizedLabel)
  {
    $this->localizedLabel = $localizedLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedLabel()
  {
    return $this->localizedLabel;
  }
  /**
   * Translated strings for the value. Recommended maximum is 15 characters for
   * a two-column layout and 30 characters for a one-column layout.
   *
   * @param LocalizedString $localizedValue
   */
  public function setLocalizedValue(LocalizedString $localizedValue)
  {
    $this->localizedValue = $localizedValue;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedValue()
  {
    return $this->localizedValue;
  }
  /**
   * The value for a specific row and column. Recommended maximum is 15
   * characters for a two-column layout and 30 characters for a one-column
   * layout.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelValue::class, 'Google_Service_Walletobjects_LabelValue');
