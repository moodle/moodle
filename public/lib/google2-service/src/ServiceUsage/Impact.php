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

namespace Google\Service\ServiceUsage;

class Impact extends \Google\Model
{
  /**
   * Reserved Blocks (Block n contains codes from 100n to 100(n+1) -1 Block 0 -
   * Special/Admin codes Block 1 - Impact Type of ANALYSIS_TYPE_DEPENDENCY Block
   * 2 - Impact Type of ANALYSIS_TYPE_RESOURCE_USAGE Block 3 - Impact Type of
   * ANALYSIS_TYPE_RESOURCE_EXISTENCE ...
   */
  public const IMPACT_TYPE_IMPACT_TYPE_UNSPECIFIED = 'IMPACT_TYPE_UNSPECIFIED';
  /**
   * Block 1 - Impact Type of ANALYSIS_TYPE_DEPENDENCY
   */
  public const IMPACT_TYPE_DEPENDENCY_MISSING_DEPENDENCIES = 'DEPENDENCY_MISSING_DEPENDENCIES';
  /**
   * Block 3 - Impact Type of ANALYSIS_TYPE_RESOURCE_EXISTENCE
   */
  public const IMPACT_TYPE_RESOURCE_EXISTENCE_PROJECT = 'RESOURCE_EXISTENCE_PROJECT';
  /**
   * Output only. User friendly impact detail in a free form message.
   *
   * @var string
   */
  public $detail;
  /**
   * Output only. The type of impact.
   *
   * @var string
   */
  public $impactType;
  /**
   * The parent resource that the analysis is based on and the service name that
   * the analysis is for. Example:
   * `projects/100/services/compute.googleapis.com`,
   * folders/101/services/compute.googleapis.com` and
   * `organizations/102/services/compute.googleapis.com`. Usually, the parent
   * resource here is same as the parent resource of the analyzed policy.
   * However, for some analysis types, the parent can be different. For example,
   * for resource existence analysis, if the parent resource of the analyzed
   * policy is a folder or an organization, the parent resource here can still
   * be the project that contains the resources.
   *
   * @var string
   */
  public $parent;

  /**
   * Output only. User friendly impact detail in a free form message.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Output only. The type of impact.
   *
   * Accepted values: IMPACT_TYPE_UNSPECIFIED, DEPENDENCY_MISSING_DEPENDENCIES,
   * RESOURCE_EXISTENCE_PROJECT
   *
   * @param self::IMPACT_TYPE_* $impactType
   */
  public function setImpactType($impactType)
  {
    $this->impactType = $impactType;
  }
  /**
   * @return self::IMPACT_TYPE_*
   */
  public function getImpactType()
  {
    return $this->impactType;
  }
  /**
   * The parent resource that the analysis is based on and the service name that
   * the analysis is for. Example:
   * `projects/100/services/compute.googleapis.com`,
   * folders/101/services/compute.googleapis.com` and
   * `organizations/102/services/compute.googleapis.com`. Usually, the parent
   * resource here is same as the parent resource of the analyzed policy.
   * However, for some analysis types, the parent can be different. For example,
   * for resource existence analysis, if the parent resource of the analyzed
   * policy is a folder or an organization, the parent resource here can still
   * be the project that contains the resources.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Impact::class, 'Google_Service_ServiceUsage_Impact');
