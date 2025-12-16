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

namespace Google\Service\Firestore;

class BitSequence extends \Google\Model
{
  /**
   * The bytes that encode the bit sequence. May have a length of zero.
   *
   * @var string
   */
  public $bitmap;
  /**
   * The number of bits of the last byte in `bitmap` to ignore as "padding". If
   * the length of `bitmap` is zero, then this value must be `0`. Otherwise,
   * this value must be between 0 and 7, inclusive.
   *
   * @var int
   */
  public $padding;

  /**
   * The bytes that encode the bit sequence. May have a length of zero.
   *
   * @param string $bitmap
   */
  public function setBitmap($bitmap)
  {
    $this->bitmap = $bitmap;
  }
  /**
   * @return string
   */
  public function getBitmap()
  {
    return $this->bitmap;
  }
  /**
   * The number of bits of the last byte in `bitmap` to ignore as "padding". If
   * the length of `bitmap` is zero, then this value must be `0`. Otherwise,
   * this value must be between 0 and 7, inclusive.
   *
   * @param int $padding
   */
  public function setPadding($padding)
  {
    $this->padding = $padding;
  }
  /**
   * @return int
   */
  public function getPadding()
  {
    return $this->padding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BitSequence::class, 'Google_Service_Firestore_BitSequence');
