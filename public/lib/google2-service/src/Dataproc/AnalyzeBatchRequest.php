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

class AnalyzeBatchRequest extends \Google\Model
{
  /**
   * Optional. A unique ID used to identify the request. If the service receives
   * two AnalyzeBatchRequest (http://cloud/dataproc/docs/reference/rpc/google.cl
   * oud.dataproc.v1#google.cloud.dataproc.v1.AnalyzeBatchRequest)s with the
   * same request_id, the second request is ignored and the Operation that
   * corresponds to the first request created and stored in the backend is
   * returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The value
   * must contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. The requestor ID is used to identify if the request comes from a
   * GCA investigation or the old Ask Gemini Experience.
   *
   * @deprecated
   * @var string
   */
  public $requestorId;

  /**
   * Optional. A unique ID used to identify the request. If the service receives
   * two AnalyzeBatchRequest (http://cloud/dataproc/docs/reference/rpc/google.cl
   * oud.dataproc.v1#google.cloud.dataproc.v1.AnalyzeBatchRequest)s with the
   * same request_id, the second request is ignored and the Operation that
   * corresponds to the first request created and stored in the backend is
   * returned.Recommendation: Set this value to a UUID
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
  /**
   * Optional. The requestor ID is used to identify if the request comes from a
   * GCA investigation or the old Ask Gemini Experience.
   *
   * @deprecated
   * @param string $requestorId
   */
  public function setRequestorId($requestorId)
  {
    $this->requestorId = $requestorId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getRequestorId()
  {
    return $this->requestorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeBatchRequest::class, 'Google_Service_Dataproc_AnalyzeBatchRequest');
