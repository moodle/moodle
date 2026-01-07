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

namespace Google\Service\Compute;

class OrganizationSecurityPoliciesListAssociationsResponse extends \Google\Collection
{
  protected $collection_key = 'associations';
  protected $associationsType = SecurityPolicyAssociation::class;
  protected $associationsDataType = 'array';
  /**
   * Output only. [Output Only] Type of securityPolicy associations.
   * Alwayscompute#organizationSecurityPoliciesListAssociations for lists of
   * securityPolicy associations.
   *
   * @var string
   */
  public $kind;

  /**
   * A list of associations.
   *
   * @param SecurityPolicyAssociation[] $associations
   */
  public function setAssociations($associations)
  {
    $this->associations = $associations;
  }
  /**
   * @return SecurityPolicyAssociation[]
   */
  public function getAssociations()
  {
    return $this->associations;
  }
  /**
   * Output only. [Output Only] Type of securityPolicy associations.
   * Alwayscompute#organizationSecurityPoliciesListAssociations for lists of
   * securityPolicy associations.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationSecurityPoliciesListAssociationsResponse::class, 'Google_Service_Compute_OrganizationSecurityPoliciesListAssociationsResponse');
