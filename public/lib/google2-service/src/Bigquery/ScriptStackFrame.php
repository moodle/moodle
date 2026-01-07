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

namespace Google\Service\Bigquery;

class ScriptStackFrame extends \Google\Model
{
  /**
   * Output only. One-based end column.
   *
   * @var int
   */
  public $endColumn;
  /**
   * Output only. One-based end line.
   *
   * @var int
   */
  public $endLine;
  /**
   * Output only. Name of the active procedure, empty if in a top-level script.
   *
   * @var string
   */
  public $procedureId;
  /**
   * Output only. One-based start column.
   *
   * @var int
   */
  public $startColumn;
  /**
   * Output only. One-based start line.
   *
   * @var int
   */
  public $startLine;
  /**
   * Output only. Text of the current statement/expression.
   *
   * @var string
   */
  public $text;

  /**
   * Output only. One-based end column.
   *
   * @param int $endColumn
   */
  public function setEndColumn($endColumn)
  {
    $this->endColumn = $endColumn;
  }
  /**
   * @return int
   */
  public function getEndColumn()
  {
    return $this->endColumn;
  }
  /**
   * Output only. One-based end line.
   *
   * @param int $endLine
   */
  public function setEndLine($endLine)
  {
    $this->endLine = $endLine;
  }
  /**
   * @return int
   */
  public function getEndLine()
  {
    return $this->endLine;
  }
  /**
   * Output only. Name of the active procedure, empty if in a top-level script.
   *
   * @param string $procedureId
   */
  public function setProcedureId($procedureId)
  {
    $this->procedureId = $procedureId;
  }
  /**
   * @return string
   */
  public function getProcedureId()
  {
    return $this->procedureId;
  }
  /**
   * Output only. One-based start column.
   *
   * @param int $startColumn
   */
  public function setStartColumn($startColumn)
  {
    $this->startColumn = $startColumn;
  }
  /**
   * @return int
   */
  public function getStartColumn()
  {
    return $this->startColumn;
  }
  /**
   * Output only. One-based start line.
   *
   * @param int $startLine
   */
  public function setStartLine($startLine)
  {
    $this->startLine = $startLine;
  }
  /**
   * @return int
   */
  public function getStartLine()
  {
    return $this->startLine;
  }
  /**
   * Output only. Text of the current statement/expression.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScriptStackFrame::class, 'Google_Service_Bigquery_ScriptStackFrame');
