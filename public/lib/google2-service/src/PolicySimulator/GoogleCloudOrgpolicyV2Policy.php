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

namespace Google\Service\PolicySimulator;

class GoogleCloudOrgpolicyV2Policy extends \Google\Model
{
  protected $alternateType = GoogleCloudOrgpolicyV2AlternatePolicySpec::class;
  protected $alternateDataType = '';
  protected $dryRunSpecType = GoogleCloudOrgpolicyV2PolicySpec::class;
  protected $dryRunSpecDataType = '';
  /**
   * Optional. An opaque tag indicating the current state of the policy, used
   * for concurrency control. This 'etag' is computed by the server based on the
   * value of other fields, and may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Immutable. The resource name of the policy. Must be one of the following
   * forms, where `constraint_name` is the name of the constraint which this
   * policy configures: * `projects/{project_number}/policies/{constraint_name}`
   * * `folders/{folder_id}/policies/{constraint_name}` *
   * `organizations/{organization_id}/policies/{constraint_name}` For example,
   * `projects/123/policies/compute.disableSerialPortAccess`. Note:
   * `projects/{project_id}/policies/{constraint_name}` is also an acceptable
   * name for API requests, but responses will return the name using the
   * equivalent project number.
   *
   * @var string
   */
  public $name;
  protected $specType = GoogleCloudOrgpolicyV2PolicySpec::class;
  protected $specDataType = '';

  /**
   * Deprecated.
   *
   * @deprecated
   * @param GoogleCloudOrgpolicyV2AlternatePolicySpec $alternate
   */
  public function setAlternate(GoogleCloudOrgpolicyV2AlternatePolicySpec $alternate)
  {
    $this->alternate = $alternate;
  }
  /**
   * @deprecated
   * @return GoogleCloudOrgpolicyV2AlternatePolicySpec
   */
  public function getAlternate()
  {
    return $this->alternate;
  }
  /**
   * Dry-run policy. Audit-only policy, can be used to monitor how the policy
   * would have impacted the existing and future resources if it's enforced.
   *
   * @param GoogleCloudOrgpolicyV2PolicySpec $dryRunSpec
   */
  public function setDryRunSpec(GoogleCloudOrgpolicyV2PolicySpec $dryRunSpec)
  {
    $this->dryRunSpec = $dryRunSpec;
  }
  /**
   * @return GoogleCloudOrgpolicyV2PolicySpec
   */
  public function getDryRunSpec()
  {
    return $this->dryRunSpec;
  }
  /**
   * Optional. An opaque tag indicating the current state of the policy, used
   * for concurrency control. This 'etag' is computed by the server based on the
   * value of other fields, and may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Immutable. The resource name of the policy. Must be one of the following
   * forms, where `constraint_name` is the name of the constraint which this
   * policy configures: * `projects/{project_number}/policies/{constraint_name}`
   * * `folders/{folder_id}/policies/{constraint_name}` *
   * `organizations/{organization_id}/policies/{constraint_name}` For example,
   * `projects/123/policies/compute.disableSerialPortAccess`. Note:
   * `projects/{project_id}/policies/{constraint_name}` is also an acceptable
   * name for API requests, but responses will return the name using the
   * equivalent project number.
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
   * Basic information about the organization policy.
   *
   * @param GoogleCloudOrgpolicyV2PolicySpec $spec
   */
  public function setSpec(GoogleCloudOrgpolicyV2PolicySpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return GoogleCloudOrgpolicyV2PolicySpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2Policy::class, 'Google_Service_PolicySimulator_GoogleCloudOrgpolicyV2Policy');
