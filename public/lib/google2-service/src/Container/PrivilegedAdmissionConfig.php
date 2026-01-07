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

namespace Google\Service\Container;

class PrivilegedAdmissionConfig extends \Google\Collection
{
  protected $collection_key = 'allowlistPaths';
  /**
   * The customer allowlist Cloud Storage paths for the cluster. These paths are
   * used with the `--autopilot-privileged-admission` flag to authorize
   * privileged workloads in Autopilot clusters. Paths can be GKE-owned, in the
   * format `gke:/`, or customer-owned, in the format `gs:`. Wildcards (`*`) are
   * supported to authorize all allowlists under specific paths or directories.
   * Example: `gs://my-bucket` will authorize all allowlists under the `my-
   * bucket` bucket.
   *
   * @var string[]
   */
  public $allowlistPaths;

  /**
   * The customer allowlist Cloud Storage paths for the cluster. These paths are
   * used with the `--autopilot-privileged-admission` flag to authorize
   * privileged workloads in Autopilot clusters. Paths can be GKE-owned, in the
   * format `gke:/`, or customer-owned, in the format `gs:`. Wildcards (`*`) are
   * supported to authorize all allowlists under specific paths or directories.
   * Example: `gs://my-bucket` will authorize all allowlists under the `my-
   * bucket` bucket.
   *
   * @param string[] $allowlistPaths
   */
  public function setAllowlistPaths($allowlistPaths)
  {
    $this->allowlistPaths = $allowlistPaths;
  }
  /**
   * @return string[]
   */
  public function getAllowlistPaths()
  {
    return $this->allowlistPaths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivilegedAdmissionConfig::class, 'Google_Service_Container_PrivilegedAdmissionConfig');
