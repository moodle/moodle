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

class GoogleCloudAssuredworkloadsV1AcknowledgeViolationRequest extends \Google\Model
{
  /**
   * Acknowledge type unspecified.
   */
  public const ACKNOWLEDGE_TYPE_ACKNOWLEDGE_TYPE_UNSPECIFIED = 'ACKNOWLEDGE_TYPE_UNSPECIFIED';
  /**
   * Acknowledge only the specific violation.
   */
  public const ACKNOWLEDGE_TYPE_SINGLE_VIOLATION = 'SINGLE_VIOLATION';
  /**
   * Acknowledge specified orgPolicy violation and also associated resource
   * violations.
   */
  public const ACKNOWLEDGE_TYPE_EXISTING_CHILD_RESOURCE_VIOLATIONS = 'EXISTING_CHILD_RESOURCE_VIOLATIONS';
  /**
   * Optional. Acknowledge type of specified violation.
   *
   * @var string
   */
  public $acknowledgeType;
  /**
   * Required. Business justification explaining the need for violation
   * acknowledgement
   *
   * @var string
   */
  public $comment;
  /**
   * Optional. This field is deprecated and will be removed in future version of
   * the API. Name of the OrgPolicy which was modified with non-compliant change
   * and resulted in this violation. Format:
   * projects/{project_number}/policies/{constraint_name}
   * folders/{folder_id}/policies/{constraint_name}
   * organizations/{organization_id}/policies/{constraint_name}
   *
   * @deprecated
   * @var string
   */
  public $nonCompliantOrgPolicy;

  /**
   * Optional. Acknowledge type of specified violation.
   *
   * Accepted values: ACKNOWLEDGE_TYPE_UNSPECIFIED, SINGLE_VIOLATION,
   * EXISTING_CHILD_RESOURCE_VIOLATIONS
   *
   * @param self::ACKNOWLEDGE_TYPE_* $acknowledgeType
   */
  public function setAcknowledgeType($acknowledgeType)
  {
    $this->acknowledgeType = $acknowledgeType;
  }
  /**
   * @return self::ACKNOWLEDGE_TYPE_*
   */
  public function getAcknowledgeType()
  {
    return $this->acknowledgeType;
  }
  /**
   * Required. Business justification explaining the need for violation
   * acknowledgement
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * Optional. This field is deprecated and will be removed in future version of
   * the API. Name of the OrgPolicy which was modified with non-compliant change
   * and resulted in this violation. Format:
   * projects/{project_number}/policies/{constraint_name}
   * folders/{folder_id}/policies/{constraint_name}
   * organizations/{organization_id}/policies/{constraint_name}
   *
   * @deprecated
   * @param string $nonCompliantOrgPolicy
   */
  public function setNonCompliantOrgPolicy($nonCompliantOrgPolicy)
  {
    $this->nonCompliantOrgPolicy = $nonCompliantOrgPolicy;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNonCompliantOrgPolicy()
  {
    return $this->nonCompliantOrgPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1AcknowledgeViolationRequest::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1AcknowledgeViolationRequest');
