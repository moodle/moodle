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

namespace Google\Service\Analytics;

class McfDataRowsConversionPathValue extends \Google\Model
{
  /**
   * Type of an interaction on conversion path. Such as CLICK, IMPRESSION etc.
   *
   * @var string
   */
  public $interactionType;
  /**
   * Node value of an interaction on conversion path. Such as source, medium
   * etc.
   *
   * @var string
   */
  public $nodeValue;

  /**
   * Type of an interaction on conversion path. Such as CLICK, IMPRESSION etc.
   *
   * @param string $interactionType
   */
  public function setInteractionType($interactionType)
  {
    $this->interactionType = $interactionType;
  }
  /**
   * @return string
   */
  public function getInteractionType()
  {
    return $this->interactionType;
  }
  /**
   * Node value of an interaction on conversion path. Such as source, medium
   * etc.
   *
   * @param string $nodeValue
   */
  public function setNodeValue($nodeValue)
  {
    $this->nodeValue = $nodeValue;
  }
  /**
   * @return string
   */
  public function getNodeValue()
  {
    return $this->nodeValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(McfDataRowsConversionPathValue::class, 'Google_Service_Analytics_McfDataRowsConversionPathValue');
