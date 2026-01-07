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

namespace Google\Service\CloudIAP;

class PolicyName extends \Google\Model
{
  /**
   * Identifies an instance of the type. ID format varies by type. The ID format
   * is defined in the IAM .service file that defines the type, either in
   * path_mapping or in a comment.
   *
   * @var string
   */
  public $id;
  /**
   * For Cloud IAM: The location of the Policy. Must be empty or "global" for
   * Policies owned by global IAM. Must name a region from prodspec/cloud-iam-
   * cloudspec for Regional IAM Policies, see go/iam-faq#where-is-iam-currently-
   * deployed. For Local IAM: This field should be set to "local".
   *
   * @var string
   */
  public $region;
  /**
   * Resource type. Types are defined in IAM's .service files. Valid values for
   * type might be 'storage_buckets', 'compute_instances',
   * 'resourcemanager_customers', 'billing_accounts', etc.
   *
   * @var string
   */
  public $type;

  /**
   * Identifies an instance of the type. ID format varies by type. The ID format
   * is defined in the IAM .service file that defines the type, either in
   * path_mapping or in a comment.
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
   * For Cloud IAM: The location of the Policy. Must be empty or "global" for
   * Policies owned by global IAM. Must name a region from prodspec/cloud-iam-
   * cloudspec for Regional IAM Policies, see go/iam-faq#where-is-iam-currently-
   * deployed. For Local IAM: This field should be set to "local".
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Resource type. Types are defined in IAM's .service files. Valid values for
   * type might be 'storage_buckets', 'compute_instances',
   * 'resourcemanager_customers', 'billing_accounts', etc.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyName::class, 'Google_Service_CloudIAP_PolicyName');
