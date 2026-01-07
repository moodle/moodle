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

namespace Google\Service\SecurityCommandCenter;

class ServiceAccountDelegationInfo extends \Google\Model
{
  /**
   * The email address of a Google account.
   *
   * @var string
   */
  public $principalEmail;
  /**
   * A string representing the principal_subject associated with the identity.
   * As compared to `principal_email`, supports principals that aren't
   * associated with email addresses, such as third party principals. For most
   * identities, the format will be `principal://iam.googleapis.com/{identity
   * pool name}/subjects/{subject}` except for some GKE identities
   * (GKE_WORKLOAD, FREEFORM, GKE_HUB_WORKLOAD) that are still in the legacy
   * format `serviceAccount:{identity pool name}[{subject}]`
   *
   * @var string
   */
  public $principalSubject;

  /**
   * The email address of a Google account.
   *
   * @param string $principalEmail
   */
  public function setPrincipalEmail($principalEmail)
  {
    $this->principalEmail = $principalEmail;
  }
  /**
   * @return string
   */
  public function getPrincipalEmail()
  {
    return $this->principalEmail;
  }
  /**
   * A string representing the principal_subject associated with the identity.
   * As compared to `principal_email`, supports principals that aren't
   * associated with email addresses, such as third party principals. For most
   * identities, the format will be `principal://iam.googleapis.com/{identity
   * pool name}/subjects/{subject}` except for some GKE identities
   * (GKE_WORKLOAD, FREEFORM, GKE_HUB_WORKLOAD) that are still in the legacy
   * format `serviceAccount:{identity pool name}[{subject}]`
   *
   * @param string $principalSubject
   */
  public function setPrincipalSubject($principalSubject)
  {
    $this->principalSubject = $principalSubject;
  }
  /**
   * @return string
   */
  public function getPrincipalSubject()
  {
    return $this->principalSubject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAccountDelegationInfo::class, 'Google_Service_SecurityCommandCenter_ServiceAccountDelegationInfo');
