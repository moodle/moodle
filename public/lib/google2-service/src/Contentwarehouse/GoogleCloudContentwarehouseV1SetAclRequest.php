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

class GoogleCloudContentwarehouseV1SetAclRequest extends \Google\Model
{
  protected $policyType = GoogleIamV1Policy::class;
  protected $policyDataType = '';
  /**
   * For Set Project ACL only. Authorization check for end user will be ignored
   * when project_owner=true.
   *
   * @var bool
   */
  public $projectOwner;
  protected $requestMetadataType = GoogleCloudContentwarehouseV1RequestMetadata::class;
  protected $requestMetadataDataType = '';

  /**
   * Required. REQUIRED: The complete policy to be applied to the `resource`.
   * The size of the policy is limited to a few 10s of KB. This refers to an
   * Identity and Access (IAM) policy, which specifies access controls for the
   * Document. You can set ACL with condition for projects only. Supported
   * operators are: `=`, `!=`, `<`, `<=`, `>`, and `>=` where the left of the
   * operator is `DocumentSchemaId` or property name and the right of the
   * operator is a number or a quoted string. You must escape backslash (\\) and
   * quote (\") characters. Boolean expressions (AND/OR) are supported up to 3
   * levels of nesting (for example, "((A AND B AND C) OR D) AND E"), a maximum
   * of 10 comparisons are allowed in the expression. The expression must be <
   * 6000 bytes in length. Sample condition: `"DocumentSchemaId = \"some schema
   * id\" OR SchemaId.floatPropertyName >= 10"`
   *
   * @param GoogleIamV1Policy $policy
   */
  public function setPolicy(GoogleIamV1Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return GoogleIamV1Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * For Set Project ACL only. Authorization check for end user will be ignored
   * when project_owner=true.
   *
   * @param bool $projectOwner
   */
  public function setProjectOwner($projectOwner)
  {
    $this->projectOwner = $projectOwner;
  }
  /**
   * @return bool
   */
  public function getProjectOwner()
  {
    return $this->projectOwner;
  }
  /**
   * The meta information collected about the end user, used to enforce access
   * control for the service.
   *
   * @param GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(GoogleCloudContentwarehouseV1RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return GoogleCloudContentwarehouseV1RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1SetAclRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1SetAclRequest');
