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

class GoogleIdentityAccesscontextmanagerV1EgressSource extends \Google\Model
{
  /**
   * An AccessLevel resource name that allows protected resources inside the
   * ServicePerimeters to access outside the ServicePerimeter boundaries.
   * AccessLevels listed must be in the same policy as this ServicePerimeter.
   * Referencing a nonexistent AccessLevel will cause an error. If an
   * AccessLevel name is not specified, only resources within the perimeter can
   * be accessed through Google Cloud calls with request origins within the
   * perimeter. Example: `accessPolicies/MY_POLICY/accessLevels/MY_LEVEL`. If a
   * single `*` is specified for `access_level`, then all EgressSources will be
   * allowed.
   *
   * @var string
   */
  public $accessLevel;
  /**
   * A Google Cloud resource from the service perimeter that you want to allow
   * to access data outside the perimeter. This field supports only projects.
   * The project format is `projects/{project_number}`. You can't use `*` in
   * this field to allow all Google Cloud resources.
   *
   * @var string
   */
  public $resource;

  /**
   * An AccessLevel resource name that allows protected resources inside the
   * ServicePerimeters to access outside the ServicePerimeter boundaries.
   * AccessLevels listed must be in the same policy as this ServicePerimeter.
   * Referencing a nonexistent AccessLevel will cause an error. If an
   * AccessLevel name is not specified, only resources within the perimeter can
   * be accessed through Google Cloud calls with request origins within the
   * perimeter. Example: `accessPolicies/MY_POLICY/accessLevels/MY_LEVEL`. If a
   * single `*` is specified for `access_level`, then all EgressSources will be
   * allowed.
   *
   * @param string $accessLevel
   */
  public function setAccessLevel($accessLevel)
  {
    $this->accessLevel = $accessLevel;
  }
  /**
   * @return string
   */
  public function getAccessLevel()
  {
    return $this->accessLevel;
  }
  /**
   * A Google Cloud resource from the service perimeter that you want to allow
   * to access data outside the perimeter. This field supports only projects.
   * The project format is `projects/{project_number}`. You can't use `*` in
   * this field to allow all Google Cloud resources.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1EgressSource::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1EgressSource');
