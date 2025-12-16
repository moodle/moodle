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

class GoogleCloudDiscoveryengineV1CustomAttribute extends \Google\Collection
{
  protected $collection_key = 'text';
  /**
   * The numerical values of this custom attribute. For example, `[2.3, 15.4]`
   * when the key is "lengths_cm". Exactly one of CustomAttribute.text or
   * CustomAttribute.numbers should be set. Otherwise, an `INVALID_ARGUMENT`
   * error is returned.
   *
   * @var []
   */
  public $numbers;
  /**
   * The textual values of this custom attribute. For example, `["yellow",
   * "green"]` when the key is "color". Empty string is not allowed. Otherwise,
   * an `INVALID_ARGUMENT` error is returned. Exactly one of
   * CustomAttribute.text or CustomAttribute.numbers should be set. Otherwise,
   * an `INVALID_ARGUMENT` error is returned.
   *
   * @var string[]
   */
  public $text;

  public function setNumbers($numbers)
  {
    $this->numbers = $numbers;
  }
  public function getNumbers()
  {
    return $this->numbers;
  }
  /**
   * The textual values of this custom attribute. For example, `["yellow",
   * "green"]` when the key is "color". Empty string is not allowed. Otherwise,
   * an `INVALID_ARGUMENT` error is returned. Exactly one of
   * CustomAttribute.text or CustomAttribute.numbers should be set. Otherwise,
   * an `INVALID_ARGUMENT` error is returned.
   *
   * @param string[] $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string[]
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CustomAttribute::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CustomAttribute');
