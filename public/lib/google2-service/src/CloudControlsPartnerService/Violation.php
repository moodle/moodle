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

namespace Google\Service\CloudControlsPartnerService;

class Violation extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Violation is resolved.
   */
  public const STATE_RESOLVED = 'RESOLVED';
  /**
   * Violation is Unresolved
   */
  public const STATE_UNRESOLVED = 'UNRESOLVED';
  /**
   * Violation is Exception
   */
  public const STATE_EXCEPTION = 'EXCEPTION';
  /**
   * Output only. Time of the event which triggered the Violation.
   *
   * @var string
   */
  public $beginTime;
  /**
   * Output only. Category under which this violation is mapped. e.g. Location,
   * Service Usage, Access, Encryption, etc.
   *
   * @var string
   */
  public $category;
  /**
   * Output only. Description for the Violation. e.g. OrgPolicy
   * gcp.resourceLocations has non compliant value.
   *
   * @var string
   */
  public $description;
  /**
   * The folder_id of the violation
   *
   * @var string
   */
  public $folderId;
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/violations/{violation}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. Name of the OrgPolicy which was modified with non-
   * compliant change and resulted this violation. Format:
   * `projects/{project_number}/policies/{constraint_name}`
   * `folders/{folder_id}/policies/{constraint_name}`
   * `organizations/{organization_id}/policies/{constraint_name}`
   *
   * @var string
   */
  public $nonCompliantOrgPolicy;
  protected $remediationType = Remediation::class;
  protected $remediationDataType = '';
  /**
   * Output only. Time of the event which fixed the Violation. If the violation
   * is ACTIVE this will be empty.
   *
   * @var string
   */
  public $resolveTime;
  /**
   * Output only. State of the violation
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last time when the Violation record was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time of the event which triggered the Violation.
   *
   * @param string $beginTime
   */
  public function setBeginTime($beginTime)
  {
    $this->beginTime = $beginTime;
  }
  /**
   * @return string
   */
  public function getBeginTime()
  {
    return $this->beginTime;
  }
  /**
   * Output only. Category under which this violation is mapped. e.g. Location,
   * Service Usage, Access, Encryption, etc.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Output only. Description for the Violation. e.g. OrgPolicy
   * gcp.resourceLocations has non compliant value.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The folder_id of the violation
   *
   * @param string $folderId
   */
  public function setFolderId($folderId)
  {
    $this->folderId = $folderId;
  }
  /**
   * @return string
   */
  public function getFolderId()
  {
    return $this->folderId;
  }
  /**
   * Identifier. Format: `organizations/{organization}/locations/{location}/cust
   * omers/{customer}/workloads/{workload}/violations/{violation}`
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
   * Output only. Immutable. Name of the OrgPolicy which was modified with non-
   * compliant change and resulted this violation. Format:
   * `projects/{project_number}/policies/{constraint_name}`
   * `folders/{folder_id}/policies/{constraint_name}`
   * `organizations/{organization_id}/policies/{constraint_name}`
   *
   * @param string $nonCompliantOrgPolicy
   */
  public function setNonCompliantOrgPolicy($nonCompliantOrgPolicy)
  {
    $this->nonCompliantOrgPolicy = $nonCompliantOrgPolicy;
  }
  /**
   * @return string
   */
  public function getNonCompliantOrgPolicy()
  {
    return $this->nonCompliantOrgPolicy;
  }
  /**
   * Output only. Compliance violation remediation
   *
   * @param Remediation $remediation
   */
  public function setRemediation(Remediation $remediation)
  {
    $this->remediation = $remediation;
  }
  /**
   * @return Remediation
   */
  public function getRemediation()
  {
    return $this->remediation;
  }
  /**
   * Output only. Time of the event which fixed the Violation. If the violation
   * is ACTIVE this will be empty.
   *
   * @param string $resolveTime
   */
  public function setResolveTime($resolveTime)
  {
    $this->resolveTime = $resolveTime;
  }
  /**
   * @return string
   */
  public function getResolveTime()
  {
    return $this->resolveTime;
  }
  /**
   * Output only. State of the violation
   *
   * Accepted values: STATE_UNSPECIFIED, RESOLVED, UNRESOLVED, EXCEPTION
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The last time when the Violation record was updated.
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
class_alias(Violation::class, 'Google_Service_CloudControlsPartnerService_Violation');
