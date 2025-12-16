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

namespace Google\Service\CloudTasks;

class HeaderOverride extends \Google\Model
{
  protected $headerType = Header::class;
  protected $headerDataType = '';

  /**
   * Header embodying a key and a value. Do not put business sensitive or
   * personally identifying data in the HTTP Header Override Configuration or
   * other similar fields in accordance with Section 12 (Resource Fields) of the
   * [Service Specific Terms](https://cloud.google.com/terms/service-terms).
   *
   * @param Header $header
   */
  public function setHeader(Header $header)
  {
    $this->header = $header;
  }
  /**
   * @return Header
   */
  public function getHeader()
  {
    return $this->header;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HeaderOverride::class, 'Google_Service_CloudTasks_HeaderOverride');
