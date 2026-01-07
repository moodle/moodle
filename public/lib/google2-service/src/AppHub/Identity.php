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

namespace Google\Service\AppHub;

class Identity extends \Google\Model
{
  /**
   * Output only. The principal of the identity. Supported formats: * `sa://my-
   * sa@PROJECT_ID.iam.gserviceaccount.com` for GCP Service Account * `principal
   * ://POOL_ID.global.PROJECT_NUMBER.workload.id.goog/ns/NAMESPACE_ID/sa/MANAGE
   * D_IDENTITY_ID` for Managed Workload Identity
   *
   * @var string
   */
  public $principal;

  /**
   * Output only. The principal of the identity. Supported formats: * `sa://my-
   * sa@PROJECT_ID.iam.gserviceaccount.com` for GCP Service Account * `principal
   * ://POOL_ID.global.PROJECT_NUMBER.workload.id.goog/ns/NAMESPACE_ID/sa/MANAGE
   * D_IDENTITY_ID` for Managed Workload Identity
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Identity::class, 'Google_Service_AppHub_Identity');
