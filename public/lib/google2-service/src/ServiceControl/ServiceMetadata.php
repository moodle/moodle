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

namespace Google\Service\ServiceControl;

class ServiceMetadata extends \Google\Model
{
  /**
   * Additional metadata provided by service teams to describe service specific
   * job information that was triggered by the original principal.
   *
   * @var array[]
   */
  public $jobMetadata;
  /**
   * A string representing the principal_subject associated with the identity.
   * For most identities, the format will be
   * `principal://iam.googleapis.com/{identity pool name}/subject/{subject)`
   * except for some GKE identities (GKE_WORKLOAD, FREEFORM, GKE_HUB_WORKLOAD)
   * that are still in the legacy format `serviceAccount:{identity pool
   * name}[{subject}]` If the identity is a Google account (e.g. workspace user
   * account or service account), this will be the email of the prefixed by
   * `serviceAccount:`. For example: `serviceAccount:my-service-
   * account@project-1.iam.gserviceaccount.com`. If the identity is an
   * individual user, the identity will be formatted as:
   * `user:user_ABC@email.com`.
   *
   * @var string
   */
  public $principalSubject;
  /**
   * The service's fully qualified domain name, e.g. "dataproc.googleapis.com".
   *
   * @var string
   */
  public $serviceDomain;

  /**
   * Additional metadata provided by service teams to describe service specific
   * job information that was triggered by the original principal.
   *
   * @param array[] $jobMetadata
   */
  public function setJobMetadata($jobMetadata)
  {
    $this->jobMetadata = $jobMetadata;
  }
  /**
   * @return array[]
   */
  public function getJobMetadata()
  {
    return $this->jobMetadata;
  }
  /**
   * A string representing the principal_subject associated with the identity.
   * For most identities, the format will be
   * `principal://iam.googleapis.com/{identity pool name}/subject/{subject)`
   * except for some GKE identities (GKE_WORKLOAD, FREEFORM, GKE_HUB_WORKLOAD)
   * that are still in the legacy format `serviceAccount:{identity pool
   * name}[{subject}]` If the identity is a Google account (e.g. workspace user
   * account or service account), this will be the email of the prefixed by
   * `serviceAccount:`. For example: `serviceAccount:my-service-
   * account@project-1.iam.gserviceaccount.com`. If the identity is an
   * individual user, the identity will be formatted as:
   * `user:user_ABC@email.com`.
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
  /**
   * The service's fully qualified domain name, e.g. "dataproc.googleapis.com".
   *
   * @param string $serviceDomain
   */
  public function setServiceDomain($serviceDomain)
  {
    $this->serviceDomain = $serviceDomain;
  }
  /**
   * @return string
   */
  public function getServiceDomain()
  {
    return $this->serviceDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMetadata::class, 'Google_Service_ServiceControl_ServiceMetadata');
