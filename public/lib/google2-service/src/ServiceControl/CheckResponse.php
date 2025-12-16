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

namespace Google\Service\ServiceControl;

class CheckResponse extends \Google\Model
{
  /**
   * Optional response metadata that will be emitted as dynamic metadata to be
   * consumed by the caller of ServiceController. For compatibility with the
   * ext_authz interface.
   *
   * @var array[]
   */
  public $dynamicMetadata;
  /**
   * Returns a set of request contexts generated from the `CheckRequest`.
   *
   * @var string[]
   */
  public $headers;
  protected $statusType = Status::class;
  protected $statusDataType = '';

  /**
   * Optional response metadata that will be emitted as dynamic metadata to be
   * consumed by the caller of ServiceController. For compatibility with the
   * ext_authz interface.
   *
   * @param array[] $dynamicMetadata
   */
  public function setDynamicMetadata($dynamicMetadata)
  {
    $this->dynamicMetadata = $dynamicMetadata;
  }
  /**
   * @return array[]
   */
  public function getDynamicMetadata()
  {
    return $this->dynamicMetadata;
  }
  /**
   * Returns a set of request contexts generated from the `CheckRequest`.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Operation is allowed when this field is not set. Any non-'OK' status
   * indicates a denial; google.rpc.Status.details would contain additional
   * details about the denial.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckResponse::class, 'Google_Service_ServiceControl_CheckResponse');
