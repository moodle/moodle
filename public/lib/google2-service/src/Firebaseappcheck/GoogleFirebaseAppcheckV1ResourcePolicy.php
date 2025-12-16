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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1ResourcePolicy extends \Google\Model
{
  /**
   * Firebase App Check is not enforced for the service, nor are App Check
   * metrics collected. Though the service is not protected by App Check in this
   * mode, other applicable protections, such as user authorization, are still
   * enforced. An unconfigured service is in this mode by default.
   */
  public const ENFORCEMENT_MODE_OFF = 'OFF';
  /**
   * Firebase App Check is not enforced for the service. App Check metrics are
   * collected to help you decide when to turn on enforcement for the service.
   * Though the service is not protected by App Check in this mode, other
   * applicable protections, such as user authorization, are still enforced.
   * Some services require certain conditions to be met before they will work
   * with App Check, such as requiring you to upgrade to a specific service
   * tier. Until those requirements are met for a service, this `UNENFORCED`
   * setting will have no effect and App Check will not work with that service.
   */
  public const ENFORCEMENT_MODE_UNENFORCED = 'UNENFORCED';
  /**
   * Firebase App Check is enforced for the service. The service will reject any
   * request that attempts to access your project's resources if it does not
   * have valid App Check token attached, with some exceptions depending on the
   * service; for example, some services will still allow requests bearing the
   * developer's privileged service account credentials without an App Check
   * token. App Check metrics continue to be collected to help you detect issues
   * with your App Check integration and monitor the composition of your
   * callers. While the service is protected by App Check, other applicable
   * protections, such as user authorization, continue to be enforced at the
   * same time. Use caution when choosing to enforce App Check on a Firebase
   * service. If your users have not updated to an App Check capable version of
   * your app, their apps will no longer be able to use your Firebase services
   * that are enforcing App Check. App Check metrics can help you decide whether
   * to enforce App Check on your Firebase services. If your app has not
   * launched yet, you should enable enforcement immediately, since there are no
   * outdated clients in use. Some services require certain conditions to be met
   * before they will work with App Check, such as requiring you to upgrade to a
   * specific service tier. Until those requirements are met for a service, this
   * `ENFORCED` setting will have no effect and App Check will not work with
   * that service.
   */
  public const ENFORCEMENT_MODE_ENFORCED = 'ENFORCED';
  /**
   * Required. The App Check enforcement mode for this resource. This will
   * override the EnforcementMode setting on the service.
   *
   * @var string
   */
  public $enforcementMode;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. This etag is strongly validated as
   * defined by RFC 7232.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Identifier. The relative name of the resource policy object, in
   * the format: ``` projects/{project_number}/services/{service_id}/resourcePol
   * icies/{resource_policy_id} ``` Note that the `service_id` element must be a
   * supported service ID. Currently, the following service IDs are supported: *
   * `oauth2.googleapis.com` (Google Identity for iOS) `resource_policy_id` is a
   * system-generated UID.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Service specific name of the resource object to which this policy
   * applies, in the format: * **iOS OAuth clients** (Google Identity for iOS):
   * `//oauth2.googleapis.com/projects/{project_number}/oauthClients/{oauth_clie
   * nt_id}` Note that the resource must belong to the service specified in the
   * `name` and be from the same project as this policy, but the resource is
   * allowed to be missing at the time of creation of this policy; in that case,
   * we make a best-effort attempt at respecting this policy, but it may not
   * have any effect until the resource is fully created.
   *
   * @var string
   */
  public $targetResource;
  /**
   * Output only. Timestamp when this resource policy configuration object was
   * most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The App Check enforcement mode for this resource. This will
   * override the EnforcementMode setting on the service.
   *
   * Accepted values: OFF, UNENFORCED, ENFORCED
   *
   * @param self::ENFORCEMENT_MODE_* $enforcementMode
   */
  public function setEnforcementMode($enforcementMode)
  {
    $this->enforcementMode = $enforcementMode;
  }
  /**
   * @return self::ENFORCEMENT_MODE_*
   */
  public function getEnforcementMode()
  {
    return $this->enforcementMode;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. This etag is strongly validated as
   * defined by RFC 7232.
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
   * Required. Identifier. The relative name of the resource policy object, in
   * the format: ``` projects/{project_number}/services/{service_id}/resourcePol
   * icies/{resource_policy_id} ``` Note that the `service_id` element must be a
   * supported service ID. Currently, the following service IDs are supported: *
   * `oauth2.googleapis.com` (Google Identity for iOS) `resource_policy_id` is a
   * system-generated UID.
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
   * Required. Service specific name of the resource object to which this policy
   * applies, in the format: * **iOS OAuth clients** (Google Identity for iOS):
   * `//oauth2.googleapis.com/projects/{project_number}/oauthClients/{oauth_clie
   * nt_id}` Note that the resource must belong to the service specified in the
   * `name` and be from the same project as this policy, but the resource is
   * allowed to be missing at the time of creation of this policy; in that case,
   * we make a best-effort attempt at respecting this policy, but it may not
   * have any effect until the resource is fully created.
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
  /**
   * Output only. Timestamp when this resource policy configuration object was
   * most recently updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1ResourcePolicy::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1ResourcePolicy');
