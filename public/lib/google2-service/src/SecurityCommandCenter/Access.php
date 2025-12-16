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

class Access extends \Google\Collection
{
  protected $collection_key = 'serviceAccountDelegationInfo';
  /**
   * Caller's IP address, such as "1.1.1.1".
   *
   * @var string
   */
  public $callerIp;
  protected $callerIpGeoType = Geolocation::class;
  protected $callerIpGeoDataType = '';
  /**
   * The method that the service account called, e.g. "SetIamPolicy".
   *
   * @var string
   */
  public $methodName;
  /**
   * Associated email, such as "foo@google.com". The email address of the
   * authenticated user or a service account acting on behalf of a third party
   * principal making the request. For third party identity callers, the
   * `principal_subject` field is populated instead of this field. For privacy
   * reasons, the principal email address is sometimes redacted. For more
   * information, see [Caller identities in audit
   * logs](https://cloud.google.com/logging/docs/audit#user-id).
   *
   * @var string
   */
  public $principalEmail;
  /**
   * A string that represents the principal_subject that is associated with the
   * identity. Unlike `principal_email`, `principal_subject` supports principals
   * that aren't associated with email addresses, such as third party
   * principals. For most identities, the format is
   * `principal://iam.googleapis.com/{identity pool name}/subject/{subject}`.
   * Some GKE identities, such as GKE_WORKLOAD, FREEFORM, and GKE_HUB_WORKLOAD,
   * still use the legacy format `serviceAccount:{identity pool
   * name}[{subject}]`.
   *
   * @var string
   */
  public $principalSubject;
  protected $serviceAccountDelegationInfoType = ServiceAccountDelegationInfo::class;
  protected $serviceAccountDelegationInfoDataType = 'array';
  /**
   * The name of the service account key that was used to create or exchange
   * credentials when authenticating the service account that made the request.
   * This is a scheme-less URI full resource name. For example: "//iam.googleapi
   * s.com/projects/{PROJECT_ID}/serviceAccounts/{ACCOUNT}/keys/{key}".
   *
   * @var string
   */
  public $serviceAccountKeyName;
  /**
   * This is the API service that the service account made a call to, e.g.
   * "iam.googleapis.com"
   *
   * @var string
   */
  public $serviceName;
  /**
   * The caller's user agent string associated with the finding.
   *
   * @var string
   */
  public $userAgent;
  /**
   * Type of user agent associated with the finding. For example, an operating
   * system shell or an embedded or standalone application.
   *
   * @var string
   */
  public $userAgentFamily;
  /**
   * A string that represents a username. The username provided depends on the
   * type of the finding and is likely not an IAM principal. For example, this
   * can be a system username if the finding is related to a virtual machine, or
   * it can be an application login username.
   *
   * @var string
   */
  public $userName;

  /**
   * Caller's IP address, such as "1.1.1.1".
   *
   * @param string $callerIp
   */
  public function setCallerIp($callerIp)
  {
    $this->callerIp = $callerIp;
  }
  /**
   * @return string
   */
  public function getCallerIp()
  {
    return $this->callerIp;
  }
  /**
   * The caller IP's geolocation, which identifies where the call came from.
   *
   * @param Geolocation $callerIpGeo
   */
  public function setCallerIpGeo(Geolocation $callerIpGeo)
  {
    $this->callerIpGeo = $callerIpGeo;
  }
  /**
   * @return Geolocation
   */
  public function getCallerIpGeo()
  {
    return $this->callerIpGeo;
  }
  /**
   * The method that the service account called, e.g. "SetIamPolicy".
   *
   * @param string $methodName
   */
  public function setMethodName($methodName)
  {
    $this->methodName = $methodName;
  }
  /**
   * @return string
   */
  public function getMethodName()
  {
    return $this->methodName;
  }
  /**
   * Associated email, such as "foo@google.com". The email address of the
   * authenticated user or a service account acting on behalf of a third party
   * principal making the request. For third party identity callers, the
   * `principal_subject` field is populated instead of this field. For privacy
   * reasons, the principal email address is sometimes redacted. For more
   * information, see [Caller identities in audit
   * logs](https://cloud.google.com/logging/docs/audit#user-id).
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
   * A string that represents the principal_subject that is associated with the
   * identity. Unlike `principal_email`, `principal_subject` supports principals
   * that aren't associated with email addresses, such as third party
   * principals. For most identities, the format is
   * `principal://iam.googleapis.com/{identity pool name}/subject/{subject}`.
   * Some GKE identities, such as GKE_WORKLOAD, FREEFORM, and GKE_HUB_WORKLOAD,
   * still use the legacy format `serviceAccount:{identity pool
   * name}[{subject}]`.
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
   * The identity delegation history of an authenticated service account that
   * made the request. The `serviceAccountDelegationInfo[]` object contains
   * information about the real authorities that try to access Google Cloud
   * resources by delegating on a service account. When multiple authorities are
   * present, they are guaranteed to be sorted based on the original ordering of
   * the identity delegation events.
   *
   * @param ServiceAccountDelegationInfo[] $serviceAccountDelegationInfo
   */
  public function setServiceAccountDelegationInfo($serviceAccountDelegationInfo)
  {
    $this->serviceAccountDelegationInfo = $serviceAccountDelegationInfo;
  }
  /**
   * @return ServiceAccountDelegationInfo[]
   */
  public function getServiceAccountDelegationInfo()
  {
    return $this->serviceAccountDelegationInfo;
  }
  /**
   * The name of the service account key that was used to create or exchange
   * credentials when authenticating the service account that made the request.
   * This is a scheme-less URI full resource name. For example: "//iam.googleapi
   * s.com/projects/{PROJECT_ID}/serviceAccounts/{ACCOUNT}/keys/{key}".
   *
   * @param string $serviceAccountKeyName
   */
  public function setServiceAccountKeyName($serviceAccountKeyName)
  {
    $this->serviceAccountKeyName = $serviceAccountKeyName;
  }
  /**
   * @return string
   */
  public function getServiceAccountKeyName()
  {
    return $this->serviceAccountKeyName;
  }
  /**
   * This is the API service that the service account made a call to, e.g.
   * "iam.googleapis.com"
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * The caller's user agent string associated with the finding.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * Type of user agent associated with the finding. For example, an operating
   * system shell or an embedded or standalone application.
   *
   * @param string $userAgentFamily
   */
  public function setUserAgentFamily($userAgentFamily)
  {
    $this->userAgentFamily = $userAgentFamily;
  }
  /**
   * @return string
   */
  public function getUserAgentFamily()
  {
    return $this->userAgentFamily;
  }
  /**
   * A string that represents a username. The username provided depends on the
   * type of the finding and is likely not an IAM principal. For example, this
   * can be a system username if the finding is related to a virtual machine, or
   * it can be an application login username.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Access::class, 'Google_Service_SecurityCommandCenter_Access');
