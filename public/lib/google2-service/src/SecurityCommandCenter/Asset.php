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

class Asset extends \Google\Model
{
  /**
   * The canonical name of the resource. It's either
   * "organizations/{organization_id}/assets/{asset_id}",
   * "folders/{folder_id}/assets/{asset_id}" or
   * "projects/{project_number}/assets/{asset_id}", depending on the closest CRM
   * ancestor of the resource.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * The time at which the asset was created in Security Command Center.
   *
   * @var string
   */
  public $createTime;
  protected $iamPolicyType = IamPolicy::class;
  protected $iamPolicyDataType = '';
  /**
   * The relative resource name of this asset. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/assets/{asset_id}".
   *
   * @var string
   */
  public $name;
  /**
   * Resource managed properties. These properties are managed and defined by
   * the Google Cloud resource and cannot be modified by the user.
   *
   * @var array[]
   */
  public $resourceProperties;
  protected $securityCenterPropertiesType = SecurityCenterProperties::class;
  protected $securityCenterPropertiesDataType = '';
  protected $securityMarksType = SecurityMarks::class;
  protected $securityMarksDataType = '';
  /**
   * The time at which the asset was last updated or added in Cloud SCC.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The canonical name of the resource. It's either
   * "organizations/{organization_id}/assets/{asset_id}",
   * "folders/{folder_id}/assets/{asset_id}" or
   * "projects/{project_number}/assets/{asset_id}", depending on the closest CRM
   * ancestor of the resource.
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * The time at which the asset was created in Security Command Center.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Cloud IAM Policy information associated with the Google Cloud resource
   * described by the Security Command Center asset. This information is managed
   * and defined by the Google Cloud resource and cannot be modified by the
   * user.
   *
   * @param IamPolicy $iamPolicy
   */
  public function setIamPolicy(IamPolicy $iamPolicy)
  {
    $this->iamPolicy = $iamPolicy;
  }
  /**
   * @return IamPolicy
   */
  public function getIamPolicy()
  {
    return $this->iamPolicy;
  }
  /**
   * The relative resource name of this asset. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/assets/{asset_id}".
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Resource managed properties. These properties are managed and defined by
   * the Google Cloud resource and cannot be modified by the user.
   *
   * @param array[] $resourceProperties
   */
  public function setResourceProperties($resourceProperties)
  {
    $this->resourceProperties = $resourceProperties;
  }
  /**
   * @return array[]
   */
  public function getResourceProperties()
  {
    return $this->resourceProperties;
  }
  /**
   * Security Command Center managed properties. These properties are managed by
   * Security Command Center and cannot be modified by the user.
   *
   * @param SecurityCenterProperties $securityCenterProperties
   */
  public function setSecurityCenterProperties(SecurityCenterProperties $securityCenterProperties)
  {
    $this->securityCenterProperties = $securityCenterProperties;
  }
  /**
   * @return SecurityCenterProperties
   */
  public function getSecurityCenterProperties()
  {
    return $this->securityCenterProperties;
  }
  /**
   * User specified security marks. These marks are entirely managed by the user
   * and come from the SecurityMarks resource that belongs to the asset.
   *
   * @param SecurityMarks $securityMarks
   */
  public function setSecurityMarks(SecurityMarks $securityMarks)
  {
    $this->securityMarks = $securityMarks;
  }
  /**
   * @return SecurityMarks
   */
  public function getSecurityMarks()
  {
    return $this->securityMarks;
  }
  /**
   * The time at which the asset was last updated or added in Cloud SCC.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Asset::class, 'Google_Service_SecurityCommandCenter_Asset');
