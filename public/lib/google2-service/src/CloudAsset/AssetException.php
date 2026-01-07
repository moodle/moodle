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

namespace Google\Service\CloudAsset;

class AssetException extends \Google\Model
{
  /**
   * exception_type is not applicable for the current asset.
   */
  public const EXCEPTION_TYPE_EXCEPTION_TYPE_UNSPECIFIED = 'EXCEPTION_TYPE_UNSPECIFIED';
  /**
   * The asset content is truncated.
   */
  public const EXCEPTION_TYPE_TRUNCATION = 'TRUNCATION';
  /**
   * The details of the exception.
   *
   * @var string
   */
  public $details;
  /**
   * The type of exception.
   *
   * @var string
   */
  public $exceptionType;

  /**
   * The details of the exception.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The type of exception.
   *
   * Accepted values: EXCEPTION_TYPE_UNSPECIFIED, TRUNCATION
   *
   * @param self::EXCEPTION_TYPE_* $exceptionType
   */
  public function setExceptionType($exceptionType)
  {
    $this->exceptionType = $exceptionType;
  }
  /**
   * @return self::EXCEPTION_TYPE_*
   */
  public function getExceptionType()
  {
    return $this->exceptionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetException::class, 'Google_Service_CloudAsset_AssetException');
