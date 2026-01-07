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

namespace Google\Service\Dataproc;

class NodeGroupAffinity extends \Google\Model
{
  /**
   * Required. The URI of a sole-tenant node group resource
   * (https://cloud.google.com/compute/docs/reference/rest/v1/nodeGroups) that
   * the cluster will be created on.A full URL, partial URI, or node group name
   * are valid. Examples: https://www.googleapis.com/compute/v1/projects/[projec
   * t_id]/zones/[zone]/nodeGroups/node-group-1
   * projects/[project_id]/zones/[zone]/nodeGroups/node-group-1 node-group-1
   *
   * @var string
   */
  public $nodeGroupUri;

  /**
   * Required. The URI of a sole-tenant node group resource
   * (https://cloud.google.com/compute/docs/reference/rest/v1/nodeGroups) that
   * the cluster will be created on.A full URL, partial URI, or node group name
   * are valid. Examples: https://www.googleapis.com/compute/v1/projects/[projec
   * t_id]/zones/[zone]/nodeGroups/node-group-1
   * projects/[project_id]/zones/[zone]/nodeGroups/node-group-1 node-group-1
   *
   * @param string $nodeGroupUri
   */
  public function setNodeGroupUri($nodeGroupUri)
  {
    $this->nodeGroupUri = $nodeGroupUri;
  }
  /**
   * @return string
   */
  public function getNodeGroupUri()
  {
    return $this->nodeGroupUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroupAffinity::class, 'Google_Service_Dataproc_NodeGroupAffinity');
