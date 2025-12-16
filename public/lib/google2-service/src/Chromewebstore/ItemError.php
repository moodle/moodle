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

namespace Google\Service\Chromewebstore;

class ItemError extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "errorCode" => "error_code",
        "errorDetail" => "error_detail",
  ];
  /**
   * @var string
   */
  public $errorCode;
  /**
   * @var string
   */
  public $errorDetail;

  /**
   * @param string
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return string
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * @param string
   */
  public function setErrorDetail($errorDetail)
  {
    $this->errorDetail = $errorDetail;
  }
  /**
   * @return string
   */
  public function getErrorDetail()
  {
    return $this->errorDetail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemError::class, 'Google_Service_Chromewebstore_ItemError');
