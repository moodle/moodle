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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityActionFlag extends \Google\Collection
{
  protected $collection_key = 'headers';
  protected $headersType = GoogleCloudApigeeV1SecurityActionHttpHeader::class;
  protected $headersDataType = 'array';

  /**
   * Optional. A list of HTTP headers to be sent to the target in case of a FLAG
   * SecurityAction. Limit 5 headers per SecurityAction. At least one is
   * mandatory.
   *
   * @param GoogleCloudApigeeV1SecurityActionHttpHeader[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityActionHttpHeader[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityActionFlag::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityActionFlag');
