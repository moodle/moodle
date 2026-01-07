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

namespace Google\Service\AIPlatformNotebooks;

class GenerateAccessTokenRequest extends \Google\Model
{
  /**
   * Required. The VM identity token (a JWT) for authenticating the VM.
   * https://cloud.google.com/compute/docs/instances/verifying-instance-identity
   *
   * @var string
   */
  public $vmToken;

  /**
   * Required. The VM identity token (a JWT) for authenticating the VM.
   * https://cloud.google.com/compute/docs/instances/verifying-instance-identity
   *
   * @param string $vmToken
   */
  public function setVmToken($vmToken)
  {
    $this->vmToken = $vmToken;
  }
  /**
   * @return string
   */
  public function getVmToken()
  {
    return $this->vmToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateAccessTokenRequest::class, 'Google_Service_AIPlatformNotebooks_GenerateAccessTokenRequest');
