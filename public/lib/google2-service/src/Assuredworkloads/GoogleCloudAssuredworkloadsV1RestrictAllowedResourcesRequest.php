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

class GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest extends \Google\Model
{
  /**
   * Unknown restriction type.
   */
  public const RESTRICTION_TYPE_RESTRICTION_TYPE_UNSPECIFIED = 'RESTRICTION_TYPE_UNSPECIFIED';
  /**
   * Allow the use all of all gcp products, irrespective of the compliance
   * posture. This effectively removes gcp.restrictServiceUsage OrgPolicy on the
   * AssuredWorkloads Folder.
   */
  public const RESTRICTION_TYPE_ALLOW_ALL_GCP_RESOURCES = 'ALLOW_ALL_GCP_RESOURCES';
  /**
   * Based on Workload's compliance regime, allowed list changes. See -
   * https://cloud.google.com/assured-workloads/docs/supported-products for the
   * list of supported resources.
   */
  public const RESTRICTION_TYPE_ALLOW_COMPLIANT_RESOURCES = 'ALLOW_COMPLIANT_RESOURCES';
  /**
   * Similar to ALLOW_COMPLIANT_RESOURCES but adds the list of compliant
   * resources to the existing list of compliant resources. Effective org-policy
   * of the Folder is considered to ensure there is no disruption to the
   * existing customer workflows.
   */
  public const RESTRICTION_TYPE_APPEND_COMPLIANT_RESOURCES = 'APPEND_COMPLIANT_RESOURCES';
  /**
   * Required. The type of restriction for using gcp products in the Workload
   * environment.
   *
   * @var string
   */
  public $restrictionType;

  /**
   * Required. The type of restriction for using gcp products in the Workload
   * environment.
   *
   * Accepted values: RESTRICTION_TYPE_UNSPECIFIED, ALLOW_ALL_GCP_RESOURCES,
   * ALLOW_COMPLIANT_RESOURCES, APPEND_COMPLIANT_RESOURCES
   *
   * @param self::RESTRICTION_TYPE_* $restrictionType
   */
  public function setRestrictionType($restrictionType)
  {
    $this->restrictionType = $restrictionType;
  }
  /**
   * @return self::RESTRICTION_TYPE_*
   */
  public function getRestrictionType()
  {
    return $this->restrictionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1RestrictAllowedResourcesRequest');
