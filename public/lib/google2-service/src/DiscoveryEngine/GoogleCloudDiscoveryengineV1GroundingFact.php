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

class GoogleCloudDiscoveryengineV1GroundingFact extends \Google\Model
{
  /**
   * Attributes associated with the fact. Common attributes include `source`
   * (indicating where the fact was sourced from), `author` (indicating the
   * author of the fact), and so on.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Text content of the fact. Can be at most 10K characters long.
   *
   * @var string
   */
  public $factText;

  /**
   * Attributes associated with the fact. Common attributes include `source`
   * (indicating where the fact was sourced from), `author` (indicating the
   * author of the fact), and so on.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Text content of the fact. Can be at most 10K characters long.
   *
   * @param string $factText
   */
  public function setFactText($factText)
  {
    $this->factText = $factText;
  }
  /**
   * @return string
   */
  public function getFactText()
  {
    return $this->factText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GroundingFact::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GroundingFact');
