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

namespace Google\Service\FirebaseRules;

class SourcePosition extends \Google\Model
{
  /**
   * First column on the source line associated with the source fragment.
   *
   * @var int
   */
  public $column;
  /**
   * Start position relative to the beginning of the file.
   *
   * @var int
   */
  public $currentOffset;
  /**
   * End position relative to the beginning of the file.
   *
   * @var int
   */
  public $endOffset;
  /**
   * Name of the `File`.
   *
   * @var string
   */
  public $fileName;
  /**
   * Line number of the source fragment. 1-based.
   *
   * @var int
   */
  public $line;

  /**
   * First column on the source line associated with the source fragment.
   *
   * @param int $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return int
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * Start position relative to the beginning of the file.
   *
   * @param int $currentOffset
   */
  public function setCurrentOffset($currentOffset)
  {
    $this->currentOffset = $currentOffset;
  }
  /**
   * @return int
   */
  public function getCurrentOffset()
  {
    return $this->currentOffset;
  }
  /**
   * End position relative to the beginning of the file.
   *
   * @param int $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return int
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * Name of the `File`.
   *
   * @param string $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return string
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * Line number of the source fragment. 1-based.
   *
   * @param int $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }
  /**
   * @return int
   */
  public function getLine()
  {
    return $this->line;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourcePosition::class, 'Google_Service_FirebaseRules_SourcePosition');
