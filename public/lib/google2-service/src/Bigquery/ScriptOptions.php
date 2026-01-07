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

class ScriptOptions extends \Google\Model
{
  /**
   * Default value.
   */
  public const KEY_RESULT_STATEMENT_KEY_RESULT_STATEMENT_KIND_UNSPECIFIED = 'KEY_RESULT_STATEMENT_KIND_UNSPECIFIED';
  /**
   * The last result determines the key result.
   */
  public const KEY_RESULT_STATEMENT_LAST = 'LAST';
  /**
   * The first SELECT statement determines the key result.
   */
  public const KEY_RESULT_STATEMENT_FIRST_SELECT = 'FIRST_SELECT';
  /**
   * Determines which statement in the script represents the "key result", used
   * to populate the schema and query results of the script job. Default is
   * LAST.
   *
   * @var string
   */
  public $keyResultStatement;
  /**
   * Limit on the number of bytes billed per statement. Exceeding this budget
   * results in an error.
   *
   * @var string
   */
  public $statementByteBudget;
  /**
   * Timeout period for each statement in a script.
   *
   * @var string
   */
  public $statementTimeoutMs;

  /**
   * Determines which statement in the script represents the "key result", used
   * to populate the schema and query results of the script job. Default is
   * LAST.
   *
   * Accepted values: KEY_RESULT_STATEMENT_KIND_UNSPECIFIED, LAST, FIRST_SELECT
   *
   * @param self::KEY_RESULT_STATEMENT_* $keyResultStatement
   */
  public function setKeyResultStatement($keyResultStatement)
  {
    $this->keyResultStatement = $keyResultStatement;
  }
  /**
   * @return self::KEY_RESULT_STATEMENT_*
   */
  public function getKeyResultStatement()
  {
    return $this->keyResultStatement;
  }
  /**
   * Limit on the number of bytes billed per statement. Exceeding this budget
   * results in an error.
   *
   * @param string $statementByteBudget
   */
  public function setStatementByteBudget($statementByteBudget)
  {
    $this->statementByteBudget = $statementByteBudget;
  }
  /**
   * @return string
   */
  public function getStatementByteBudget()
  {
    return $this->statementByteBudget;
  }
  /**
   * Timeout period for each statement in a script.
   *
   * @param string $statementTimeoutMs
   */
  public function setStatementTimeoutMs($statementTimeoutMs)
  {
    $this->statementTimeoutMs = $statementTimeoutMs;
  }
  /**
   * @return string
   */
  public function getStatementTimeoutMs()
  {
    return $this->statementTimeoutMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScriptOptions::class, 'Google_Service_Bigquery_ScriptOptions');
