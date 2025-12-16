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

namespace Google\Service\CloudAsset;

class GoogleCloudOrgpolicyV1Policy extends \Google\Model
{
  protected $booleanPolicyType = GoogleCloudOrgpolicyV1BooleanPolicy::class;
  protected $booleanPolicyDataType = '';
  /**
   * The name of the `Constraint` the `Policy` is configuring, for example,
   * `constraints/serviceuser.services`. A [list of available
   * constraints](/resource-manager/docs/organization-policy/org-policy-
   * constraints) is available. Immutable after creation.
   *
   * @var string
   */
  public $constraint;
  /**
   * An opaque tag indicating the current version of the `Policy`, used for
   * concurrency control. When the `Policy` is returned from either a
   * `GetPolicy` or a `ListOrgPolicy` request, this `etag` indicates the version
   * of the current `Policy` to use when executing a read-modify-write loop.
   * When the `Policy` is returned from a `GetEffectivePolicy` request, the
   * `etag` will be unset. When the `Policy` is used in a `SetOrgPolicy` method,
   * use the `etag` value that was returned from a `GetOrgPolicy` request as
   * part of a read-modify-write loop for concurrency control. Not setting the
   * `etag`in a `SetOrgPolicy` request will result in an unconditional write of
   * the `Policy`.
   *
   * @var string
   */
  public $etag;
  protected $listPolicyType = GoogleCloudOrgpolicyV1ListPolicy::class;
  protected $listPolicyDataType = '';
  protected $restoreDefaultType = GoogleCloudOrgpolicyV1RestoreDefault::class;
  protected $restoreDefaultDataType = '';
  /**
   * The time stamp the `Policy` was previously updated. This is set by the
   * server, not specified by the caller, and represents the last time a call to
   * `SetOrgPolicy` was made for that `Policy`. Any value set by the client will
   * be ignored.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Version of the `Policy`. Default version is 0;
   *
   * @var int
   */
  public $version;

  /**
   * For boolean `Constraints`, whether to enforce the `Constraint` or not.
   *
   * @param GoogleCloudOrgpolicyV1BooleanPolicy $booleanPolicy
   */
  public function setBooleanPolicy(GoogleCloudOrgpolicyV1BooleanPolicy $booleanPolicy)
  {
    $this->booleanPolicy = $booleanPolicy;
  }
  /**
   * @return GoogleCloudOrgpolicyV1BooleanPolicy
   */
  public function getBooleanPolicy()
  {
    return $this->booleanPolicy;
  }
  /**
   * The name of the `Constraint` the `Policy` is configuring, for example,
   * `constraints/serviceuser.services`. A [list of available
   * constraints](/resource-manager/docs/organization-policy/org-policy-
   * constraints) is available. Immutable after creation.
   *
   * @param string $constraint
   */
  public function setConstraint($constraint)
  {
    $this->constraint = $constraint;
  }
  /**
   * @return string
   */
  public function getConstraint()
  {
    return $this->constraint;
  }
  /**
   * An opaque tag indicating the current version of the `Policy`, used for
   * concurrency control. When the `Policy` is returned from either a
   * `GetPolicy` or a `ListOrgPolicy` request, this `etag` indicates the version
   * of the current `Policy` to use when executing a read-modify-write loop.
   * When the `Policy` is returned from a `GetEffectivePolicy` request, the
   * `etag` will be unset. When the `Policy` is used in a `SetOrgPolicy` method,
   * use the `etag` value that was returned from a `GetOrgPolicy` request as
   * part of a read-modify-write loop for concurrency control. Not setting the
   * `etag`in a `SetOrgPolicy` request will result in an unconditional write of
   * the `Policy`.
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
   * List of values either allowed or disallowed.
   *
   * @param GoogleCloudOrgpolicyV1ListPolicy $listPolicy
   */
  public function setListPolicy(GoogleCloudOrgpolicyV1ListPolicy $listPolicy)
  {
    $this->listPolicy = $listPolicy;
  }
  /**
   * @return GoogleCloudOrgpolicyV1ListPolicy
   */
  public function getListPolicy()
  {
    return $this->listPolicy;
  }
  /**
   * Restores the default behavior of the constraint; independent of
   * `Constraint` type.
   *
   * @param GoogleCloudOrgpolicyV1RestoreDefault $restoreDefault
   */
  public function setRestoreDefault(GoogleCloudOrgpolicyV1RestoreDefault $restoreDefault)
  {
    $this->restoreDefault = $restoreDefault;
  }
  /**
   * @return GoogleCloudOrgpolicyV1RestoreDefault
   */
  public function getRestoreDefault()
  {
    return $this->restoreDefault;
  }
  /**
   * The time stamp the `Policy` was previously updated. This is set by the
   * server, not specified by the caller, and represents the last time a call to
   * `SetOrgPolicy` was made for that `Policy`. Any value set by the client will
   * be ignored.
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
  /**
   * Version of the `Policy`. Default version is 0;
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV1Policy::class, 'Google_Service_CloudAsset_GoogleCloudOrgpolicyV1Policy');
