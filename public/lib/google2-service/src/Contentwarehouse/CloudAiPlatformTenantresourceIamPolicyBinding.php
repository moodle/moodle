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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceIamPolicyBinding extends \Google\Collection
{
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * The value of resource field is the ID or number of a project. Format is
   */
  public const RESOURCE_TYPE_PROJECT = 'PROJECT';
  /**
   * The value of resource field is the resource name of a service account.
   * Format is projects//serviceAccounts/
   */
  public const RESOURCE_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * The value of resource field is the name of a GCS bucket (not its resource
   * name). Format is .
   */
  public const RESOURCE_TYPE_GCS_BUCKET = 'GCS_BUCKET';
  /**
   * The value of resource field is the resource name of a service consumer.
   * Format is services//consumers/
   */
  public const RESOURCE_TYPE_SERVICE_CONSUMER = 'SERVICE_CONSUMER';
  /**
   * The value of the resource field is the AR Image Uri which identifies an AR
   * REPO. Allowed formats are:  : @
   */
  public const RESOURCE_TYPE_AR_REPO = 'AR_REPO';
  protected $collection_key = 'members';
  /**
   * Input/Output [Required]. The member service accounts with the roles above.
   * Note: placeholders are same as the resource above.
   *
   * @var string[]
   */
  public $members;
  /**
   * Input/Output [Required]. The resource name that will be accessed by
   * members, which also depends on resource_type. Note: placeholders are
   * supported in resource names. For example, ${tpn} will be used when the
   * tenant project number is not ready.
   *
   * @var string
   */
  public $resource;
  /**
   * Input/Output [Required]. Specifies the type of resource that will be
   * accessed by members.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Input/Output [Required]. The role for members below.
   *
   * @var string
   */
  public $role;

  /**
   * Input/Output [Required]. The member service accounts with the roles above.
   * Note: placeholders are same as the resource above.
   *
   * @param string[] $members
   */
  public function setMembers($members)
  {
    $this->members = $members;
  }
  /**
   * @return string[]
   */
  public function getMembers()
  {
    return $this->members;
  }
  /**
   * Input/Output [Required]. The resource name that will be accessed by
   * members, which also depends on resource_type. Note: placeholders are
   * supported in resource names. For example, ${tpn} will be used when the
   * tenant project number is not ready.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Input/Output [Required]. Specifies the type of resource that will be
   * accessed by members.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, PROJECT, SERVICE_ACCOUNT,
   * GCS_BUCKET, SERVICE_CONSUMER, AR_REPO
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Input/Output [Required]. The role for members below.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceIamPolicyBinding::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceIamPolicyBinding');
