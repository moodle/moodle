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

namespace Google\Service\PolicyTroubleshooter;

class GoogleCloudPolicytroubleshooterV1AccessTuple extends \Google\Model
{
  /**
   * Required. The full resource name that identifies the resource. For example,
   * `//compute.googleapis.com/projects/my-project/zones/us-
   * central1-a/instances/my-instance`. For examples of full resource names for
   * Google Cloud services, see
   * https://cloud.google.com/iam/help/troubleshooter/full-resource-names.
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Required. The IAM permission to check for the specified principal and
   * resource. For a complete list of IAM permissions, see
   * https://cloud.google.com/iam/help/permissions/reference. For a complete
   * list of predefined IAM roles and the permissions in each role, see
   * https://cloud.google.com/iam/help/roles/reference.
   *
   * @var string
   */
  public $permission;
  /**
   * Required. The principal whose access you want to check, in the form of the
   * email address that represents that principal. For example,
   * `alice@example.com` or `my-service-account@my-
   * project.iam.gserviceaccount.com`. The principal must be a Google Account or
   * a service account. Other types of principals are not supported.
   *
   * @var string
   */
  public $principal;

  /**
   * Required. The full resource name that identifies the resource. For example,
   * `//compute.googleapis.com/projects/my-project/zones/us-
   * central1-a/instances/my-instance`. For examples of full resource names for
   * Google Cloud services, see
   * https://cloud.google.com/iam/help/troubleshooter/full-resource-names.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * Required. The IAM permission to check for the specified principal and
   * resource. For a complete list of IAM permissions, see
   * https://cloud.google.com/iam/help/permissions/reference. For a complete
   * list of predefined IAM roles and the permissions in each role, see
   * https://cloud.google.com/iam/help/roles/reference.
   *
   * @param string $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * Required. The principal whose access you want to check, in the form of the
   * email address that represents that principal. For example,
   * `alice@example.com` or `my-service-account@my-
   * project.iam.gserviceaccount.com`. The principal must be a Google Account or
   * a service account. Other types of principals are not supported.
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicytroubleshooterV1AccessTuple::class, 'Google_Service_PolicyTroubleshooter_GoogleCloudPolicytroubleshooterV1AccessTuple');
