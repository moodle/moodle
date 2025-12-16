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

namespace Google\Service\ParameterManager;

class ResourcePolicyMember extends \Google\Model
{
  /**
   * Output only. IAM policy binding member referring to a Google Cloud resource
   * by user-assigned name (https://google.aip.dev/122). If a resource is
   * deleted and recreated with the same name, the binding will be applicable to
   * the new resource. Example: `principal://parametermanager.googleapis.com/pro
   * jects/12345/name/locations/us-central1-a/parameters/my-parameter`
   *
   * @var string
   */
  public $iamPolicyNamePrincipal;
  /**
   * Output only. IAM policy binding member referring to a Google Cloud resource
   * by system-assigned unique identifier (https://google.aip.dev/148#uid). If a
   * resource is deleted and recreated with the same name, the binding will not
   * be applicable to the new resource Example: `principal://parametermanager.go
   * ogleapis.com/projects/12345/uid/locations/us-
   * central1-a/parameters/a918fed5`
   *
   * @var string
   */
  public $iamPolicyUidPrincipal;

  /**
   * Output only. IAM policy binding member referring to a Google Cloud resource
   * by user-assigned name (https://google.aip.dev/122). If a resource is
   * deleted and recreated with the same name, the binding will be applicable to
   * the new resource. Example: `principal://parametermanager.googleapis.com/pro
   * jects/12345/name/locations/us-central1-a/parameters/my-parameter`
   *
   * @param string $iamPolicyNamePrincipal
   */
  public function setIamPolicyNamePrincipal($iamPolicyNamePrincipal)
  {
    $this->iamPolicyNamePrincipal = $iamPolicyNamePrincipal;
  }
  /**
   * @return string
   */
  public function getIamPolicyNamePrincipal()
  {
    return $this->iamPolicyNamePrincipal;
  }
  /**
   * Output only. IAM policy binding member referring to a Google Cloud resource
   * by system-assigned unique identifier (https://google.aip.dev/148#uid). If a
   * resource is deleted and recreated with the same name, the binding will not
   * be applicable to the new resource Example: `principal://parametermanager.go
   * ogleapis.com/projects/12345/uid/locations/us-
   * central1-a/parameters/a918fed5`
   *
   * @param string $iamPolicyUidPrincipal
   */
  public function setIamPolicyUidPrincipal($iamPolicyUidPrincipal)
  {
    $this->iamPolicyUidPrincipal = $iamPolicyUidPrincipal;
  }
  /**
   * @return string
   */
  public function getIamPolicyUidPrincipal()
  {
    return $this->iamPolicyUidPrincipal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyMember::class, 'Google_Service_ParameterManager_ResourcePolicyMember');
