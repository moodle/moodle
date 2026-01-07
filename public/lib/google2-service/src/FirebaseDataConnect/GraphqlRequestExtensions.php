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

namespace Google\Service\FirebaseDataConnect;

class GraphqlRequestExtensions extends \Google\Model
{
  protected $impersonateType = Impersonation::class;
  protected $impersonateDataType = '';

  /**
   * Optional. If set, impersonate a request with given Firebase Auth context
   * and evaluate the auth policies on the operation. If omitted, bypass any
   * defined auth policies.
   *
   * @param Impersonation $impersonate
   */
  public function setImpersonate(Impersonation $impersonate)
  {
    $this->impersonate = $impersonate;
  }
  /**
   * @return Impersonation
   */
  public function getImpersonate()
  {
    return $this->impersonate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GraphqlRequestExtensions::class, 'Google_Service_FirebaseDataConnect_GraphqlRequestExtensions');
