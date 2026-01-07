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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2CancelExecutionRequest extends \Google\Model
{
  /**
   * A system-generated fingerprint for this version of the resource. This may
   * be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * Indicates that the request should be validated without actually cancelling
   * any resources.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * A system-generated fingerprint for this version of the resource. This may
   * be used to detect modification conflict during updates.
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
   * Indicates that the request should be validated without actually cancelling
   * any resources.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2CancelExecutionRequest::class, 'Google_Service_CloudRun_GoogleCloudRunV2CancelExecutionRequest');
