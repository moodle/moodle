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

namespace Google\Service\ServiceNetworking;

class PolicyBinding extends \Google\Model
{
  /**
   * Required. Member to bind the role with. See
   * /iam/docs/reference/rest/v1/Policy#Binding for how to format each member.
   * Eg. - user:myuser@mydomain.com - serviceAccount:my-service-
   * account@app.gserviceaccount.com
   *
   * @var string
   */
  public $member;
  /**
   * Required. Role to apply. Only allowlisted roles can be used at the
   * specified granularity. The role must be one of the following: -
   * 'roles/container.hostServiceAgentUser' applied on the shared VPC host
   * project - 'roles/compute.securityAdmin' applied on the shared VPC host
   * project - 'roles/compute.networkAdmin' applied on the shared VPC host
   * project - 'roles/tpu.xpnAgent' applied on the shared VPC host project -
   * 'roles/dns.admin' applied on the shared VPC host project -
   * 'roles/logging.admin' applied on the shared VPC host project -
   * 'roles/monitoring.viewer' applied on the shared VPC host project -
   * 'roles/servicemanagement.quotaViewer' applied on the shared VPC host
   * project
   *
   * @var string
   */
  public $role;

  /**
   * Required. Member to bind the role with. See
   * /iam/docs/reference/rest/v1/Policy#Binding for how to format each member.
   * Eg. - user:myuser@mydomain.com - serviceAccount:my-service-
   * account@app.gserviceaccount.com
   *
   * @param string $member
   */
  public function setMember($member)
  {
    $this->member = $member;
  }
  /**
   * @return string
   */
  public function getMember()
  {
    return $this->member;
  }
  /**
   * Required. Role to apply. Only allowlisted roles can be used at the
   * specified granularity. The role must be one of the following: -
   * 'roles/container.hostServiceAgentUser' applied on the shared VPC host
   * project - 'roles/compute.securityAdmin' applied on the shared VPC host
   * project - 'roles/compute.networkAdmin' applied on the shared VPC host
   * project - 'roles/tpu.xpnAgent' applied on the shared VPC host project -
   * 'roles/dns.admin' applied on the shared VPC host project -
   * 'roles/logging.admin' applied on the shared VPC host project -
   * 'roles/monitoring.viewer' applied on the shared VPC host project -
   * 'roles/servicemanagement.quotaViewer' applied on the shared VPC host
   * project
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyBinding::class, 'Google_Service_ServiceNetworking_PolicyBinding');
