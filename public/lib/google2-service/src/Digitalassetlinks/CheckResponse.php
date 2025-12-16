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

class CheckResponse extends \Google\Collection
{
  protected $collection_key = 'relationExtensions';
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
   * Error codes that describe the result of the Check operation. NOTE: Error
   * codes may be populated even when `linked` is true. The error codes do not
   * necessarily imply that the request failed, but rather, specify any errors
   * encountered in the statements file(s) which may or may not impact whether
   * the server determines the requested source and target to be linked.
   *
   * @var string[]
   */
  public $errorCode;
  /**
   * Set to true if the assets specified in the request are linked by the
   * relation specified in the request.
   *
   * @var bool
   */
  public $linked;
  /**
   * From serving time, how much longer the response should be considered valid
   * barring further updates. REQUIRED
   *
   * @var string
   */
  public $maxAge;
  /**
   * Statements may specify relation level extensions/payloads to express more
   * details when declaring permissions to grant from the source asset to the
   * target asset. When requested, the API will return relation_extensions
   * specified in any and all statements linking the requested source and target
   * assets by the relation specified in the request.
   *
   * @var array[]
   */
  public $relationExtensions;

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
   * Error codes that describe the result of the Check operation. NOTE: Error
   * codes may be populated even when `linked` is true. The error codes do not
   * necessarily imply that the request failed, but rather, specify any errors
   * encountered in the statements file(s) which may or may not impact whether
   * the server determines the requested source and target to be linked.
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
   * Set to true if the assets specified in the request are linked by the
   * relation specified in the request.
   *
   * @param bool $linked
   */
  public function setLinked($linked)
  {
    $this->linked = $linked;
  }
  /**
   * @return bool
   */
  public function getLinked()
  {
    return $this->linked;
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
   * Statements may specify relation level extensions/payloads to express more
   * details when declaring permissions to grant from the source asset to the
   * target asset. When requested, the API will return relation_extensions
   * specified in any and all statements linking the requested source and target
   * assets by the relation specified in the request.
   *
   * @param array[] $relationExtensions
   */
  public function setRelationExtensions($relationExtensions)
  {
    $this->relationExtensions = $relationExtensions;
  }
  /**
   * @return array[]
   */
  public function getRelationExtensions()
  {
    return $this->relationExtensions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckResponse::class, 'Google_Service_Digitalassetlinks_CheckResponse');
