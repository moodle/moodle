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

namespace Google\Service\CloudIAP;

class PolicyDelegationSettings extends \Google\Model
{
  /**
   * Permission to check in IAM.
   *
   * @var string
   */
  public $iamPermission;
  /**
   * The DNS name of the service (e.g. "resourcemanager.googleapis.com"). This
   * should be the domain name part of the full resource names (see
   * https://aip.dev/122#full-resource-names), which is usually the same as
   * IamServiceSpec.service of the service where the resource type is defined.
   *
   * @var string
   */
  public $iamServiceName;
  protected $policyNameType = PolicyName::class;
  protected $policyNameDataType = '';
  protected $resourceType = IapResource::class;
  protected $resourceDataType = '';

  /**
   * Permission to check in IAM.
   *
   * @param string $iamPermission
   */
  public function setIamPermission($iamPermission)
  {
    $this->iamPermission = $iamPermission;
  }
  /**
   * @return string
   */
  public function getIamPermission()
  {
    return $this->iamPermission;
  }
  /**
   * The DNS name of the service (e.g. "resourcemanager.googleapis.com"). This
   * should be the domain name part of the full resource names (see
   * https://aip.dev/122#full-resource-names), which is usually the same as
   * IamServiceSpec.service of the service where the resource type is defined.
   *
   * @param string $iamServiceName
   */
  public function setIamServiceName($iamServiceName)
  {
    $this->iamServiceName = $iamServiceName;
  }
  /**
   * @return string
   */
  public function getIamServiceName()
  {
    return $this->iamServiceName;
  }
  /**
   * Policy name to be checked
   *
   * @param PolicyName $policyName
   */
  public function setPolicyName(PolicyName $policyName)
  {
    $this->policyName = $policyName;
  }
  /**
   * @return PolicyName
   */
  public function getPolicyName()
  {
    return $this->policyName;
  }
  /**
   * IAM resource to check permission on
   *
   * @param IapResource $resource
   */
  public function setResource(IapResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return IapResource
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyDelegationSettings::class, 'Google_Service_CloudIAP_PolicyDelegationSettings');
