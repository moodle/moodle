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

namespace Google\Service\WorkflowExecutions;

class Position extends \Google\Model
{
  /**
   * The source code column position (of the line) the current instruction was
   * generated from.
   *
   * @var string
   */
  public $column;
  /**
   * The number of bytes of source code making up this stack trace element.
   *
   * @var string
   */
  public $length;
  /**
   * The source code line number the current instruction was generated from.
   *
   * @var string
   */
  public $line;

  /**
   * The source code column position (of the line) the current instruction was
   * generated from.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * The number of bytes of source code making up this stack trace element.
   *
   * @param string $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return string
   */
  public function getLength()
  {
    return $this->length;
  }
  /**
   * The source code line number the current instruction was generated from.
   *
   * @param string $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }
  /**
   * @return string
   */
  public function getLine()
  {
    return $this->line;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Position::class, 'Google_Service_WorkflowExecutions_Position');
