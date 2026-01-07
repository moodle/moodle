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

namespace Google\Service\Digitalassetlinks;

class ListResponse extends \Google\Collection
{
  protected $collection_key = 'statements';
  /**
   * Human-readable message containing information intended to help end users
   * understand, reproduce and debug the result. The message will be in English
   * and we are currently not planning to offer any translations. Please note
   * that no guarantees are made about the contents or format of this string.
   * Any aspect of it may be subject to change without notice. You should not
   * attempt to programmatically parse this data. For programmatic access, use
   * the error_code field below.
   *
   * @var string
   */
  public $debugString;
  /**
   * Error codes that describe the result of the List operation.
   *
   * @var string[]
   */
  public $errorCode;
  /**
   * From serving time, how much longer the response should be considered valid
   * barring further updates. REQUIRED
   *
   * @var string
   */
  public $maxAge;
  protected $statementsType = Statement::class;
  protected $statementsDataType = 'array';

  /**
   * Human-readable message containing information intended to help end users
   * understand, reproduce and debug the result. The message will be in English
   * and we are currently not planning to offer any translations. Please note
   * that no guarantees are made about the contents or format of this string.
   * Any aspect of it may be subject to change without notice. You should not
   * attempt to programmatically parse this data. For programmatic access, use
   * the error_code field below.
   *
   * @param string $debugString
   */
  public function setDebugString($debugString)
  {
    $this->debugString = $debugString;
  }
  /**
   * @return string
   */
  public function getDebugString()
  {
    return $this->debugString;
  }
  /**
   * Error codes that describe the result of the List operation.
   *
   * @param string[] $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return string[]
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * From serving time, how much longer the response should be considered valid
   * barring further updates. REQUIRED
   *
   * @param string $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
  }
  /**
   * @return string
   */
  public function getMaxAge()
  {
    return $this->maxAge;
  }
  /**
   * A list of all the matching statements that have been found.
   *
   * @param Statement[] $statements
   */
  public function setStatements($statements)
  {
    $this->statements = $statements;
  }
  /**
   * @return Statement[]
   */
  public function getStatements()
  {
    return $this->statements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListResponse::class, 'Google_Service_Digitalassetlinks_ListResponse');
