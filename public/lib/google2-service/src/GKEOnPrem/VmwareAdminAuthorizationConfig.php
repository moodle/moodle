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

namespace Google\Service\GKEOnPrem;

class VmwareAdminAuthorizationConfig extends \Google\Collection
{
  protected $collection_key = 'viewerUsers';
  protected $viewerUsersType = ClusterUser::class;
  protected $viewerUsersDataType = 'array';

  /**
   * For VMware admin clusters, users will be granted the cluster-viewer role on
   * the cluster.
   *
   * @param ClusterUser[] $viewerUsers
   */
  public function setViewerUsers($viewerUsers)
  {
    $this->viewerUsers = $viewerUsers;
  }
  /**
   * @return ClusterUser[]
   */
  public function getViewerUsers()
  {
    return $this->viewerUsers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminAuthorizationConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminAuthorizationConfig');
