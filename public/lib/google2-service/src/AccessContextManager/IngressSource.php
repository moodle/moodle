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

namespace Google\Service\AccessContextManager;

class IngressSource extends \Google\Model
{
  /**
   * An AccessLevel resource name that allow resources within the
   * ServicePerimeters to be accessed from the internet. AccessLevels listed
   * must be in the same policy as this ServicePerimeter. Referencing a
   * nonexistent AccessLevel will cause an error. If no AccessLevel names are
   * listed, resources within the perimeter can only be accessed via Google
   * Cloud calls with request origins within the perimeter. Example:
   * `accessPolicies/MY_POLICY/accessLevels/MY_LEVEL`. If a single `*` is
   * specified for `access_level`, then all IngressSources will be allowed.
   *
   * @var string
   */
  public $accessLevel;
  /**
   * A Google Cloud resource that is allowed to ingress the perimeter. Requests
   * from these resources will be allowed to access perimeter data. Currently
   * only projects and VPCs are allowed. Project format:
   * `projects/{project_number}` VPC network format:
   * `//compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NAME}`.
   * The project may be in any Google Cloud organization, not just the
   * organization that the perimeter is defined in. `*` is not allowed, the case
   * of allowing all Google Cloud resources only is not supported.
   *
   * @var string
   */
  public $resource;

  /**
   * An AccessLevel resource name that allow resources within the
   * ServicePerimeters to be accessed from the internet. AccessLevels listed
   * must be in the same policy as this ServicePerimeter. Referencing a
   * nonexistent AccessLevel will cause an error. If no AccessLevel names are
   * listed, resources within the perimeter can only be accessed via Google
   * Cloud calls with request origins within the perimeter. Example:
   * `accessPolicies/MY_POLICY/accessLevels/MY_LEVEL`. If a single `*` is
   * specified for `access_level`, then all IngressSources will be allowed.
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
   * A Google Cloud resource that is allowed to ingress the perimeter. Requests
   * from these resources will be allowed to access perimeter data. Currently
   * only projects and VPCs are allowed. Project format:
   * `projects/{project_number}` VPC network format:
   * `//compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NAME}`.
   * The project may be in any Google Cloud organization, not just the
   * organization that the perimeter is defined in. `*` is not allowed, the case
   * of allowing all Google Cloud resources only is not supported.
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
class_alias(IngressSource::class, 'Google_Service_AccessContextManager_IngressSource');
