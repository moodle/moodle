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

class BooksAnnotationsRange extends \Google\Model
{
  /**
   * The offset from the ending position.
   *
   * @var string
   */
  public $endOffset;
  /**
   * The ending position for the range.
   *
   * @var string
   */
  public $endPosition;
  /**
   * The offset from the starting position.
   *
   * @var string
   */
  public $startOffset;
  /**
   * The starting position for the range.
   *
   * @var string
   */
  public $startPosition;

  /**
   * The offset from the ending position.
   *
   * @param string $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * The ending position for the range.
   *
   * @param string $endPosition
   */
  public function setEndPosition($endPosition)
  {
    $this->endPosition = $endPosition;
  }
  /**
   * @return string
   */
  public function getEndPosition()
  {
    return $this->endPosition;
  }
  /**
   * The offset from the starting position.
   *
   * @param string $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
  /**
   * The starting position for the range.
   *
   * @param string $startPosition
   */
  public function setStartPosition($startPosition)
  {
    $this->startPosition = $startPosition;
  }
  /**
   * @return string
   */
  public function getStartPosition()
  {
    return $this->startPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BooksAnnotationsRange::class, 'Google_Service_Books_BooksAnnotationsRange');
