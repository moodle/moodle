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

namespace Google\Service\Storagetransfer;

class ErrorLogEntry extends \Google\Collection
{
  protected $collection_key = 'errorDetails';
  /**
   * Optional. A list of messages that carry the error details.
   *
   * @var string[]
   */
  public $errorDetails;
  /**
   * Output only. A URL that refers to the target (a data source, a data sink,
   * or an object) with which the error is associated.
   *
   * @var string
   */
  public $url;

  /**
   * Optional. A list of messages that carry the error details.
   *
   * @param string[] $errorDetails
   */
  public function setErrorDetails($errorDetails)
  {
    $this->errorDetails = $errorDetails;
  }
  /**
   * @return string[]
   */
  public function getErrorDetails()
  {
    return $this->errorDetails;
  }
  /**
   * Output only. A URL that refers to the target (a data source, a data sink,
   * or an object) with which the error is associated.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorLogEntry::class, 'Google_Service_Storagetransfer_ErrorLogEntry');
