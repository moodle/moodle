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

namespace Google\Service\NetworkServices;

class HttpRouteHttpDirectResponse extends \Google\Model
{
  /**
   * Optional. Response body as bytes. Maximum body size is 4096B.
   *
   * @var string
   */
  public $bytesBody;
  /**
   * Required. Status to return as part of HTTP Response. Must be a positive
   * integer.
   *
   * @var int
   */
  public $status;
  /**
   * Optional. Response body as a string. Maximum body length is 1024
   * characters.
   *
   * @var string
   */
  public $stringBody;

  /**
   * Optional. Response body as bytes. Maximum body size is 4096B.
   *
   * @param string $bytesBody
   */
  public function setBytesBody($bytesBody)
  {
    $this->bytesBody = $bytesBody;
  }
  /**
   * @return string
   */
  public function getBytesBody()
  {
    return $this->bytesBody;
  }
  /**
   * Required. Status to return as part of HTTP Response. Must be a positive
   * integer.
   *
   * @param int $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Optional. Response body as a string. Maximum body length is 1024
   * characters.
   *
   * @param string $stringBody
   */
  public function setStringBody($stringBody)
  {
    $this->stringBody = $stringBody;
  }
  /**
   * @return string
   */
  public function getStringBody()
  {
    return $this->stringBody;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteHttpDirectResponse::class, 'Google_Service_NetworkServices_HttpRouteHttpDirectResponse');
