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

namespace Google\Service\VMwareEngine;

class AcceleratePrivateCloudDeletionRequest extends \Google\Model
{
  /**
   * Optional. Checksum used to ensure that the user-provided value is up to
   * date before the server processes the request. The server compares provided
   * checksum with the current checksum of the resource. If the user-provided
   * value is out of date, this request returns an `ABORTED` error.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. Checksum used to ensure that the user-provided value is up to
   * date before the server processes the request. The server compares provided
   * checksum with the current checksum of the resource. If the user-provided
   * value is out of date, this request returns an `ABORTED` error.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
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
class_alias(AcceleratePrivateCloudDeletionRequest::class, 'Google_Service_VMwareEngine_AcceleratePrivateCloudDeletionRequest');
