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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1Violation extends \Google\Collection
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
   * Unspecified type.
   */
  public const VIOLATION_TYPE_VIOLATION_TYPE_UNSPECIFIED = 'VIOLATION_TYPE_UNSPECIFIED';
  /**
   * Org Policy Violation.
   */
  public const VIOLATION_TYPE_ORG_POLICY = 'ORG_POLICY';
  /**
   * Resource Violation.
   */
  public const VIOLATION_TYPE_RESOURCE = 'RESOURCE';
  protected $collection_key = 'exceptionContexts';
  /**
   * A boolean that indicates if the violation is acknowledged
   *
   * @var bool
   */
  public $acknowledged;
  /**
   * Optional. Timestamp when this violation was acknowledged first. Check
   * exception_contexts to find the last time the violation was acknowledged
   * when there are more than one violations. This field will be absent when
   * acknowledged field is marked as false.
   *
   * @var string
   */
  public $acknowledgementTime;
  /**
   * Optional. Output only. Violation Id of the org-policy violation due to
   * which the resource violation is caused. Empty for org-policy violations.
   *
   * @var string
   */
  public $associatedOrgPolicyViolationId;
  /**
   * Output only. Immutable. Audit Log Link for violated resource Format: https:
   * //console.cloud.google.com/logs/query;query={logName}{protoPayload.resource
   * Name}{timeRange}{folder}
   *
   * @var string
   */
  public $auditLogLink;
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
   * Output only. Immutable. Audit Log link to find business justification
   * provided for violation exception. Format: https://console.cloud.google.com/
   * logs/query;query={logName}{protoPayload.resourceName}{protoPayload.methodNa
   * me}{timeRange}{organization}
   *
   * @var string
   */
  public $exceptionAuditLogLink;
  protected $exceptionContextsType = GoogleCloudAssuredworkloadsV1ViolationExceptionContext::class;
  protected $exceptionContextsDataType = 'array';
  /**
   * Output only. Immutable. Name of the Violation. Format: organizations/{organ
   * ization}/locations/{location}/workloads/{workload_id}/violations/{violation
   * s_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. Name of the OrgPolicy which was modified with non-
   * compliant change and resulted this violation. Format:
   * projects/{project_number}/policies/{constraint_name}
   * folders/{folder_id}/policies/{constraint_name}
   * organizations/{organization_id}/policies/{constraint_name}
   *
   * @var string
   */
  public $nonCompliantOrgPolicy;
  /**
   * Output only. Immutable. The org-policy-constraint that was incorrectly
   * changed, which resulted in this violation.
   *
   * @deprecated
   * @var string
   */
  public $orgPolicyConstraint;
  /**
   * Optional. Output only. Parent project number where resource is present.
   * Empty for org-policy violations.
   *
   * @var string
   */
  public $parentProjectNumber;
  protected $remediationType = GoogleCloudAssuredworkloadsV1ViolationRemediation::class;
  protected $remediationDataType = '';
  /**
   * Output only. Time of the event which fixed the Violation. If the violation
   * is ACTIVE this will be empty.
   *
   * @var string
   */
  public $resolveTime;
  /**
   * Optional. Output only. Name of the resource like
   * //storage.googleapis.com/myprojectxyz-testbucket. Empty for org-policy
   * violations.
   *
   * @var string
   */
  public $resourceName;
  /**
   * Optional. Output only. Type of the resource like
   * compute.googleapis.com/Disk, etc. Empty for org-policy violations.
   *
   * @var string
   */
  public $resourceType;
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
   * Output only. Type of the violation
   *
   * @var string
   */
  public $violationType;

  /**
   * A boolean that indicates if the violation is acknowledged
   *
   * @param bool $acknowledged
   */
  public function setAcknowledged($acknowledged)
  {
    $this->acknowledged = $acknowledged;
  }
  /**
   * @return bool
   */
  public function getAcknowledged()
  {
    return $this->acknowledged;
  }
  /**
   * Optional. Timestamp when this violation was acknowledged first. Check
   * exception_contexts to find the last time the violation was acknowledged
   * when there are more than one violations. This field will be absent when
   * acknowledged field is marked as false.
   *
   * @param string $acknowledgementTime
   */
  public function setAcknowledgementTime($acknowledgementTime)
  {
    $this->acknowledgementTime = $acknowledgementTime;
  }
  /**
   * @return string
   */
  public function getAcknowledgementTime()
  {
    return $this->acknowledgementTime;
  }
  /**
   * Optional. Output only. Violation Id of the org-policy violation due to
   * which the resource violation is caused. Empty for org-policy violations.
   *
   * @param string $associatedOrgPolicyViolationId
   */
  public function setAssociatedOrgPolicyViolationId($associatedOrgPolicyViolationId)
  {
    $this->associatedOrgPolicyViolationId = $associatedOrgPolicyViolationId;
  }
  /**
   * @return string
   */
  public function getAssociatedOrgPolicyViolationId()
  {
    return $this->associatedOrgPolicyViolationId;
  }
  /**
   * Output only. Immutable. Audit Log Link for violated resource Format: https:
   * //console.cloud.google.com/logs/query;query={logName}{protoPayload.resource
   * Name}{timeRange}{folder}
   *
   * @param string $auditLogLink
   */
  public function setAuditLogLink($auditLogLink)
  {
    $this->auditLogLink = $auditLogLink;
  }
  /**
   * @return string
   */
  public function getAuditLogLink()
  {
    return $this->auditLogLink;
  }
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
   * Output only. Immutable. Audit Log link to find business justification
   * provided for violation exception. Format: https://console.cloud.google.com/
   * logs/query;query={logName}{protoPayload.resourceName}{protoPayload.methodNa
   * me}{timeRange}{organization}
   *
   * @param string $exceptionAuditLogLink
   */
  public function setExceptionAuditLogLink($exceptionAuditLogLink)
  {
    $this->exceptionAuditLogLink = $exceptionAuditLogLink;
  }
  /**
   * @return string
   */
  public function getExceptionAuditLogLink()
  {
    return $this->exceptionAuditLogLink;
  }
  /**
   * Output only. List of all the exception detail added for the violation.
   *
   * @param GoogleCloudAssuredworkloadsV1ViolationExceptionContext[] $exceptionContexts
   */
  public function setExceptionContexts($exceptionContexts)
  {
    $this->exceptionContexts = $exceptionContexts;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1ViolationExceptionContext[]
   */
  public function getExceptionContexts()
  {
    return $this->exceptionContexts;
  }
  /**
   * Output only. Immutable. Name of the Violation. Format: organizations/{organ
   * ization}/locations/{location}/workloads/{workload_id}/violations/{violation
   * s_id}
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
   * projects/{project_number}/policies/{constraint_name}
   * folders/{folder_id}/policies/{constraint_name}
   * organizations/{organization_id}/policies/{constraint_name}
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
   * Output only. Immutable. The org-policy-constraint that was incorrectly
   * changed, which resulted in this violation.
   *
   * @deprecated
   * @param string $orgPolicyConstraint
   */
  public function setOrgPolicyConstraint($orgPolicyConstraint)
  {
    $this->orgPolicyConstraint = $orgPolicyConstraint;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getOrgPolicyConstraint()
  {
    return $this->orgPolicyConstraint;
  }
  /**
   * Optional. Output only. Parent project number where resource is present.
   * Empty for org-policy violations.
   *
   * @param string $parentProjectNumber
   */
  public function setParentProjectNumber($parentProjectNumber)
  {
    $this->parentProjectNumber = $parentProjectNumber;
  }
  /**
   * @return string
   */
  public function getParentProjectNumber()
  {
    return $this->parentProjectNumber;
  }
  /**
   * Output only. Compliance violation remediation
   *
   * @param GoogleCloudAssuredworkloadsV1ViolationRemediation $remediation
   */
  public function setRemediation(GoogleCloudAssuredworkloadsV1ViolationRemediation $remediation)
  {
    $this->remediation = $remediation;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1ViolationRemediation
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
   * Optional. Output only. Name of the resource like
   * //storage.googleapis.com/myprojectxyz-testbucket. Empty for org-policy
   * violations.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Optional. Output only. Type of the resource like
   * compute.googleapis.com/Disk, etc. Empty for org-policy violations.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
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
  /**
   * Output only. Type of the violation
   *
   * Accepted values: VIOLATION_TYPE_UNSPECIFIED, ORG_POLICY, RESOURCE
   *
   * @param self::VIOLATION_TYPE_* $violationType
   */
  public function setViolationType($violationType)
  {
    $this->violationType = $violationType;
  }
  /**
   * @return self::VIOLATION_TYPE_*
   */
  public function getViolationType()
  {
    return $this->violationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1Violation::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1Violation');
