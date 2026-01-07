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

namespace Google\Service\Books;

class AnnotationLayerSummary extends \Google\Model
{
  /**
   * Maximum allowed characters on this layer, especially for the "copy" layer.
   *
   * @var int
   */
  public $allowedCharacterCount;
  /**
   * Type of limitation on this layer. "limited" or "unlimited" for the "copy"
   * layer.
   *
   * @var string
   */
  public $limitType;
  /**
   * Remaining allowed characters on this layer, especially for the "copy"
   * layer.
   *
   * @var int
   */
  public $remainingCharacterCount;

  /**
   * Maximum allowed characters on this layer, especially for the "copy" layer.
   *
   * @param int $allowedCharacterCount
   */
  public function setAllowedCharacterCount($allowedCharacterCount)
  {
    $this->allowedCharacterCount = $allowedCharacterCount;
  }
  /**
   * @return int
   */
  public function getAllowedCharacterCount()
  {
    return $this->allowedCharacterCount;
  }
  /**
   * Type of limitation on this layer. "limited" or "unlimited" for the "copy"
   * layer.
   *
   * @param string $limitType
   */
  public function setLimitType($limitType)
  {
    $this->limitType = $limitType;
  }
  /**
   * @return string
   */
  public function getLimitType()
  {
    return $this->limitType;
  }
  /**
   * Remaining allowed characters on this layer, especially for the "copy"
   * layer.
   *
   * @param int $remainingCharacterCount
   */
  public function setRemainingCharacterCount($remainingCharacterCount)
  {
    $this->remainingCharacterCount = $remainingCharacterCount;
  }
  /**
   * @return int
   */
  public function getRemainingCharacterCount()
  {
    return $this->remainingCharacterCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotationLayerSummary::class, 'Google_Service_Books_AnnotationLayerSummary');
