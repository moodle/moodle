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

class RecommendationDescription extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const TYPE_DESCRIPTION_TYPE_UNSPECIFIED = 'DESCRIPTION_TYPE_UNSPECIFIED';
  /**
   * Short description.
   */
  public const TYPE_SHORT = 'SHORT';
  /**
   * Long description.
   */
  public const TYPE_LONG = 'LONG';
  /**
   * Output only. Text of the description.
   *
   * @var string
   */
  public $text;
  /**
   * Output only. Type of the description.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Text of the description.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Output only. Type of the description.
   *
   * Accepted values: DESCRIPTION_TYPE_UNSPECIFIED, SHORT, LONG
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecommendationDescription::class, 'Google_Service_ShoppingContent_RecommendationDescription');
