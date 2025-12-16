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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ErrorsSearchAds360Failure extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $errorsType = GoogleAdsSearchads360V0ErrorsSearchAds360Error::class;
  protected $errorsDataType = 'array';
  /**
   * The unique ID of the request that is used for debugging purposes.
   *
   * @var string
   */
  public $requestId;

  /**
   * The list of errors that occurred.
   *
   * @param GoogleAdsSearchads360V0ErrorsSearchAds360Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleAdsSearchads360V0ErrorsSearchAds360Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The unique ID of the request that is used for debugging purposes.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ErrorsSearchAds360Failure::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ErrorsSearchAds360Failure');
