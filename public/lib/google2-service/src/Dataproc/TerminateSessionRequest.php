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

namespace Google\Service\Dataproc;

class TerminateSessionRequest extends \Google\Model
{
  /**
   * Optional. A unique ID used to identify the request. If the service receives
   * two TerminateSessionRequest (https://cloud.google.com/dataproc/docs/referen
   * ce/rpc/google.cloud.dataproc.v1#google.cloud.dataproc.v1.TerminateSessionRe
   * quest)s with the same ID, the second request is ignored.Recommendation: Set
   * this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value
   * must contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. A unique ID used to identify the request. If the service receives
   * two TerminateSessionRequest (https://cloud.google.com/dataproc/docs/referen
   * ce/rpc/google.cloud.dataproc.v1#google.cloud.dataproc.v1.TerminateSessionRe
   * quest)s with the same ID, the second request is ignored.Recommendation: Set
   * this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value
   * must contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
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
class_alias(TerminateSessionRequest::class, 'Google_Service_Dataproc_TerminateSessionRequest');
