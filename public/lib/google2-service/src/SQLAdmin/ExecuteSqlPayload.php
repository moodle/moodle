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

namespace Google\Service\SQLAdmin;

class ExecuteSqlPayload extends \Google\Model
{
  /**
   * Unspecified mode, effectively the same as `FAIL_PARTIAL_RESULT`.
   */
  public const PARTIAL_RESULT_MODE_PARTIAL_RESULT_MODE_UNSPECIFIED = 'PARTIAL_RESULT_MODE_UNSPECIFIED';
  /**
   * Throw an error if the result exceeds 10 MB or if only a partial result can
   * be retrieved. Don't return the result.
   */
  public const PARTIAL_RESULT_MODE_FAIL_PARTIAL_RESULT = 'FAIL_PARTIAL_RESULT';
  /**
   * Return a truncated result and set `partial_result` to true if the result
   * exceeds 10 MB or if only a partial result can be retrieved due to error.
   * Don't throw an error.
   */
  public const PARTIAL_RESULT_MODE_ALLOW_PARTIAL_RESULT = 'ALLOW_PARTIAL_RESULT';
  /**
   * Optional. When set to true, the API caller identity associated with the
   * request is used for database authentication. The API caller must be an IAM
   * user in the database.
   *
   * @var bool
   */
  public $autoIamAuthn;
  /**
   * Optional. Name of the database on which the statement will be executed.
   *
   * @var string
   */
  public $database;
  /**
   * Optional. Controls how the API should respond when the SQL execution result
   * is incomplete due to the size limit or another error. The default mode is
   * to throw an error.
   *
   * @var string
   */
  public $partialResultMode;
  /**
   * Optional. The maximum number of rows returned per SQL statement.
   *
   * @var string
   */
  public $rowLimit;
  /**
   * Required. SQL statements to run on the database. It can be a single
   * statement or a sequence of statements separated by semicolons.
   *
   * @var string
   */
  public $sqlStatement;
  /**
   * Optional. The name of an existing database user to connect to the database.
   * When `auto_iam_authn` is set to true, this field is ignored and the API
   * caller's IAM user is used.
   *
   * @var string
   */
  public $user;

  /**
   * Optional. When set to true, the API caller identity associated with the
   * request is used for database authentication. The API caller must be an IAM
   * user in the database.
   *
   * @param bool $autoIamAuthn
   */
  public function setAutoIamAuthn($autoIamAuthn)
  {
    $this->autoIamAuthn = $autoIamAuthn;
  }
  /**
   * @return bool
   */
  public function getAutoIamAuthn()
  {
    return $this->autoIamAuthn;
  }
  /**
   * Optional. Name of the database on which the statement will be executed.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Optional. Controls how the API should respond when the SQL execution result
   * is incomplete due to the size limit or another error. The default mode is
   * to throw an error.
   *
   * Accepted values: PARTIAL_RESULT_MODE_UNSPECIFIED, FAIL_PARTIAL_RESULT,
   * ALLOW_PARTIAL_RESULT
   *
   * @param self::PARTIAL_RESULT_MODE_* $partialResultMode
   */
  public function setPartialResultMode($partialResultMode)
  {
    $this->partialResultMode = $partialResultMode;
  }
  /**
   * @return self::PARTIAL_RESULT_MODE_*
   */
  public function getPartialResultMode()
  {
    return $this->partialResultMode;
  }
  /**
   * Optional. The maximum number of rows returned per SQL statement.
   *
   * @param string $rowLimit
   */
  public function setRowLimit($rowLimit)
  {
    $this->rowLimit = $rowLimit;
  }
  /**
   * @return string
   */
  public function getRowLimit()
  {
    return $this->rowLimit;
  }
  /**
   * Required. SQL statements to run on the database. It can be a single
   * statement or a sequence of statements separated by semicolons.
   *
   * @param string $sqlStatement
   */
  public function setSqlStatement($sqlStatement)
  {
    $this->sqlStatement = $sqlStatement;
  }
  /**
   * @return string
   */
  public function getSqlStatement()
  {
    return $this->sqlStatement;
  }
  /**
   * Optional. The name of an existing database user to connect to the database.
   * When `auto_iam_authn` is set to true, this field is ignored and the API
   * caller's IAM user is used.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecuteSqlPayload::class, 'Google_Service_SQLAdmin_ExecuteSqlPayload');
