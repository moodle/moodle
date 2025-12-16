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

class GoogleCloudOrgpolicyV1BooleanPolicy extends \Google\Model
{
  /**
   * If `true`, then the `Policy` is enforced. If `false`, then any
   * configuration is acceptable. Suppose you have a `Constraint`
   * `constraints/compute.disableSerialPortAccess` with `constraint_default` set
   * to `ALLOW`. A `Policy` for that `Constraint` exhibits the following
   * behavior: - If the `Policy` at this resource has enforced set to `false`,
   * serial port connection attempts will be allowed. - If the `Policy` at this
   * resource has enforced set to `true`, serial port connection attempts will
   * be refused. - If the `Policy` at this resource is `RestoreDefault`, serial
   * port connection attempts will be allowed. - If no `Policy` is set at this
   * resource or anywhere higher in the resource hierarchy, serial port
   * connection attempts will be allowed. - If no `Policy` is set at this
   * resource, but one exists higher in the resource hierarchy, the behavior is
   * as if the`Policy` were set at this resource. The following examples
   * demonstrate the different possible layerings: Example 1 (nearest
   * `Constraint` wins): `organizations/foo` has a `Policy` with: {enforced:
   * false} `projects/bar` has no `Policy` set. The constraint at `projects/bar`
   * and `organizations/foo` will not be enforced. Example 2 (enforcement gets
   * replaced): `organizations/foo` has a `Policy` with: {enforced: false}
   * `projects/bar` has a `Policy` with: {enforced: true} The constraint at
   * `organizations/foo` is not enforced. The constraint at `projects/bar` is
   * enforced. Example 3 (RestoreDefault): `organizations/foo` has a `Policy`
   * with: {enforced: true} `projects/bar` has a `Policy` with: {RestoreDefault:
   * {}} The constraint at `organizations/foo` is enforced. The constraint at
   * `projects/bar` is not enforced, because `constraint_default` for the
   * `Constraint` is `ALLOW`.
   *
   * @var bool
   */
  public $enforced;

  /**
   * If `true`, then the `Policy` is enforced. If `false`, then any
   * configuration is acceptable. Suppose you have a `Constraint`
   * `constraints/compute.disableSerialPortAccess` with `constraint_default` set
   * to `ALLOW`. A `Policy` for that `Constraint` exhibits the following
   * behavior: - If the `Policy` at this resource has enforced set to `false`,
   * serial port connection attempts will be allowed. - If the `Policy` at this
   * resource has enforced set to `true`, serial port connection attempts will
   * be refused. - If the `Policy` at this resource is `RestoreDefault`, serial
   * port connection attempts will be allowed. - If no `Policy` is set at this
   * resource or anywhere higher in the resource hierarchy, serial port
   * connection attempts will be allowed. - If no `Policy` is set at this
   * resource, but one exists higher in the resource hierarchy, the behavior is
   * as if the`Policy` were set at this resource. The following examples
   * demonstrate the different possible layerings: Example 1 (nearest
   * `Constraint` wins): `organizations/foo` has a `Policy` with: {enforced:
   * false} `projects/bar` has no `Policy` set. The constraint at `projects/bar`
   * and `organizations/foo` will not be enforced. Example 2 (enforcement gets
   * replaced): `organizations/foo` has a `Policy` with: {enforced: false}
   * `projects/bar` has a `Policy` with: {enforced: true} The constraint at
   * `organizations/foo` is not enforced. The constraint at `projects/bar` is
   * enforced. Example 3 (RestoreDefault): `organizations/foo` has a `Policy`
   * with: {enforced: true} `projects/bar` has a `Policy` with: {RestoreDefault:
   * {}} The constraint at `organizations/foo` is enforced. The constraint at
   * `projects/bar` is not enforced, because `constraint_default` for the
   * `Constraint` is `ALLOW`.
   *
   * @param bool $enforced
   */
  public function setEnforced($enforced)
  {
    $this->enforced = $enforced;
  }
  /**
   * @return bool
   */
  public function getEnforced()
  {
    return $this->enforced;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV1BooleanPolicy::class, 'Google_Service_CloudAsset_GoogleCloudOrgpolicyV1BooleanPolicy');
