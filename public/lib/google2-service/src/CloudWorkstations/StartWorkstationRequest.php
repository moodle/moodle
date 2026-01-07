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

namespace Google\Service\CloudWorkstations;

class StartWorkstationRequest extends \Google\Model
{
  /**
   * Optional. If set, the workstation starts using the boost configuration with
   * the specified ID.
   *
   * @var string
   */
  public $boostConfig;
  /**
   * Optional. If set, the request will be rejected if the latest version of the
   * workstation on the server does not have this ETag.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. If set, validate the request and preview the review, but do not
   * actually apply it.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Optional. If set, the workstation starts using the boost configuration with
   * the specified ID.
   *
   * @param string $boostConfig
   */
  public function setBoostConfig($boostConfig)
  {
    $this->boostConfig = $boostConfig;
  }
  /**
   * @return string
   */
  public function getBoostConfig()
  {
    return $this->boostConfig;
  }
  /**
   * Optional. If set, the request will be rejected if the latest version of the
   * workstation on the server does not have this ETag.
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
   * Optional. If set, validate the request and preview the review, but do not
   * actually apply it.
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
class_alias(StartWorkstationRequest::class, 'Google_Service_CloudWorkstations_StartWorkstationRequest');
