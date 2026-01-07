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

namespace Google\Service\SecurityCommandCenter;

class IamPolicy extends \Google\Model
{
  /**
   * The JSON representation of the Policy associated with the asset. See
   * https://cloud.google.com/iam/reference/rest/v1/Policy for format details.
   *
   * @var string
   */
  public $policyBlob;

  /**
   * The JSON representation of the Policy associated with the asset. See
   * https://cloud.google.com/iam/reference/rest/v1/Policy for format details.
   *
   * @param string $policyBlob
   */
  public function setPolicyBlob($policyBlob)
  {
    $this->policyBlob = $policyBlob;
  }
  /**
   * @return string
   */
  public function getPolicyBlob()
  {
    return $this->policyBlob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicy::class, 'Google_Service_SecurityCommandCenter_IamPolicy');
