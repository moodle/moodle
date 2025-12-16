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

class ScriptStatistics extends \Google\Collection
{
  /**
   * Default value.
   */
  public const EVALUATION_KIND_EVALUATION_KIND_UNSPECIFIED = 'EVALUATION_KIND_UNSPECIFIED';
  /**
   * The statement appears directly in the script.
   */
  public const EVALUATION_KIND_STATEMENT = 'STATEMENT';
  /**
   * The statement evaluates an expression that appears in the script.
   */
  public const EVALUATION_KIND_EXPRESSION = 'EXPRESSION';
  protected $collection_key = 'stackFrames';
  /**
   * Whether this child job was a statement or expression.
   *
   * @var string
   */
  public $evaluationKind;
  protected $stackFramesType = ScriptStackFrame::class;
  protected $stackFramesDataType = 'array';

  /**
   * Whether this child job was a statement or expression.
   *
   * Accepted values: EVALUATION_KIND_UNSPECIFIED, STATEMENT, EXPRESSION
   *
   * @param self::EVALUATION_KIND_* $evaluationKind
   */
  public function setEvaluationKind($evaluationKind)
  {
    $this->evaluationKind = $evaluationKind;
  }
  /**
   * @return self::EVALUATION_KIND_*
   */
  public function getEvaluationKind()
  {
    return $this->evaluationKind;
  }
  /**
   * Stack trace showing the line/column/procedure name of each frame on the
   * stack at the point where the current evaluation happened. The leaf frame is
   * first, the primary script is last. Never empty.
   *
   * @param ScriptStackFrame[] $stackFrames
   */
  public function setStackFrames($stackFrames)
  {
    $this->stackFrames = $stackFrames;
  }
  /**
   * @return ScriptStackFrame[]
   */
  public function getStackFrames()
  {
    return $this->stackFrames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScriptStatistics::class, 'Google_Service_Bigquery_ScriptStatistics');
