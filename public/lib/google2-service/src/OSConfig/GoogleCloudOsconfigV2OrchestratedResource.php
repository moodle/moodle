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

namespace Google\Service\OSConfig;

class GoogleCloudOsconfigV2OrchestratedResource extends \Google\Model
{
  /**
   * Optional. ID of the resource to be used while generating set of affected
   * resources. For UPSERT action the value is auto-generated during
   * PolicyOrchestrator creation when not set. When the value is set it should
   * following next restrictions: * Must contain only lowercase letters,
   * numbers, and hyphens. * Must start with a letter. * Must be between 1-63
   * characters. * Must end with a number or a letter. * Must be unique within
   * the project. For DELETE action, ID must be specified explicitly during
   * PolicyOrchestrator creation.
   *
   * @var string
   */
  public $id;
  protected $osPolicyAssignmentV1PayloadType = OSPolicyAssignment::class;
  protected $osPolicyAssignmentV1PayloadDataType = '';

  /**
   * Optional. ID of the resource to be used while generating set of affected
   * resources. For UPSERT action the value is auto-generated during
   * PolicyOrchestrator creation when not set. When the value is set it should
   * following next restrictions: * Must contain only lowercase letters,
   * numbers, and hyphens. * Must start with a letter. * Must be between 1-63
   * characters. * Must end with a number or a letter. * Must be unique within
   * the project. For DELETE action, ID must be specified explicitly during
   * PolicyOrchestrator creation.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. OSPolicyAssignment resource to be created, updated or deleted.
   * Name field is ignored and replace with a generated value. With this field
   * set, orchestrator will perform actions on
   * `project/{project}/locations/{zone}/osPolicyAssignments/{resource_id}`
   * resources, where `project` and `zone` pairs come from the expanded scope,
   * and `resource_id` comes from the `resource_id` field of orchestrator
   * resource.
   *
   * @param OSPolicyAssignment $osPolicyAssignmentV1Payload
   */
  public function setOsPolicyAssignmentV1Payload(OSPolicyAssignment $osPolicyAssignmentV1Payload)
  {
    $this->osPolicyAssignmentV1Payload = $osPolicyAssignmentV1Payload;
  }
  /**
   * @return OSPolicyAssignment
   */
  public function getOsPolicyAssignmentV1Payload()
  {
    return $this->osPolicyAssignmentV1Payload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOsconfigV2OrchestratedResource::class, 'Google_Service_OSConfig_GoogleCloudOsconfigV2OrchestratedResource');
