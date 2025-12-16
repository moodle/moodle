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

namespace Google\Service\FirebaseManagement;

class RemoveIosAppRequest extends \Google\Model
{
  /**
   * If set to true, and the App is not found, the request will succeed but no
   * action will be taken on the server.
   *
   * @var bool
   */
  public $allowMissing;
  /**
   * Checksum provided in the IosApp resource. If provided, this checksum
   * ensures that the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Determines whether to _immediately_ delete the IosApp. If set to true, the
   * App is immediately deleted from the Project and cannot be undeleted (that
   * is, restored to the Project). If not set, defaults to false, which means
   * the App will be set to expire in 30 days. Within the 30 days, the App may
   * be restored to the Project using UndeleteIosApp
   *
   * @var bool
   */
  public $immediate;
  /**
   * If set to true, the request is only validated. The App will _not_ be
   * removed.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * If set to true, and the App is not found, the request will succeed but no
   * action will be taken on the server.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Checksum provided in the IosApp resource. If provided, this checksum
   * ensures that the client has an up-to-date value before proceeding.
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
   * Determines whether to _immediately_ delete the IosApp. If set to true, the
   * App is immediately deleted from the Project and cannot be undeleted (that
   * is, restored to the Project). If not set, defaults to false, which means
   * the App will be set to expire in 30 days. Within the 30 days, the App may
   * be restored to the Project using UndeleteIosApp
   *
   * @param bool $immediate
   */
  public function setImmediate($immediate)
  {
    $this->immediate = $immediate;
  }
  /**
   * @return bool
   */
  public function getImmediate()
  {
    return $this->immediate;
  }
  /**
   * If set to true, the request is only validated. The App will _not_ be
   * removed.
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
class_alias(RemoveIosAppRequest::class, 'Google_Service_FirebaseManagement_RemoveIosAppRequest');
