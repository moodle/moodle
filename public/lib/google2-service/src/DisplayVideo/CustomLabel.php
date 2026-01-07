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

class CustomLabel extends \Google\Model
{
  /**
   * Not specified or unknown.
   */
  public const KEY_CUSTOM_LABEL_KEY_UNSPECIFIED = 'CUSTOM_LABEL_KEY_UNSPECIFIED';
  /**
   * Key index 0.
   */
  public const KEY_CUSTOM_LABEL_KEY_0 = 'CUSTOM_LABEL_KEY_0';
  /**
   * Key index 1.
   */
  public const KEY_CUSTOM_LABEL_KEY_1 = 'CUSTOM_LABEL_KEY_1';
  /**
   * Key index 2.
   */
  public const KEY_CUSTOM_LABEL_KEY_2 = 'CUSTOM_LABEL_KEY_2';
  /**
   * Key index 3.
   */
  public const KEY_CUSTOM_LABEL_KEY_3 = 'CUSTOM_LABEL_KEY_3';
  /**
   * Key index 4.
   */
  public const KEY_CUSTOM_LABEL_KEY_4 = 'CUSTOM_LABEL_KEY_4';
  /**
   * The key of the label.
   *
   * @var string
   */
  public $key;
  /**
   * The value of the label.
   *
   * @var string
   */
  public $value;

  /**
   * The key of the label.
   *
   * Accepted values: CUSTOM_LABEL_KEY_UNSPECIFIED, CUSTOM_LABEL_KEY_0,
   * CUSTOM_LABEL_KEY_1, CUSTOM_LABEL_KEY_2, CUSTOM_LABEL_KEY_3,
   * CUSTOM_LABEL_KEY_4
   *
   * @param self::KEY_* $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return self::KEY_*
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The value of the label.
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
class_alias(CustomLabel::class, 'Google_Service_DisplayVideo_CustomLabel');
