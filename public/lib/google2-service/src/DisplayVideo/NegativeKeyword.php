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

class NegativeKeyword extends \Google\Model
{
  /**
   * Required. Immutable. The negatively targeted keyword, for example `car
   * insurance`. Must be UTF-8 encoded with a maximum size of 255 bytes. Maximum
   * number of characters is 80. Maximum number of words is 10. Valid characters
   * are restricted to ASCII characters only. The only URL-escaping permitted is
   * for representing whitespace between words. Leading or trailing whitespace
   * is ignored.
   *
   * @var string
   */
  public $keywordValue;
  /**
   * Output only. The resource name of the negative keyword.
   *
   * @var string
   */
  public $name;

  /**
   * Required. Immutable. The negatively targeted keyword, for example `car
   * insurance`. Must be UTF-8 encoded with a maximum size of 255 bytes. Maximum
   * number of characters is 80. Maximum number of words is 10. Valid characters
   * are restricted to ASCII characters only. The only URL-escaping permitted is
   * for representing whitespace between words. Leading or trailing whitespace
   * is ignored.
   *
   * @param string $keywordValue
   */
  public function setKeywordValue($keywordValue)
  {
    $this->keywordValue = $keywordValue;
  }
  /**
   * @return string
   */
  public function getKeywordValue()
  {
    return $this->keywordValue;
  }
  /**
   * Output only. The resource name of the negative keyword.
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
class_alias(NegativeKeyword::class, 'Google_Service_DisplayVideo_NegativeKeyword');
