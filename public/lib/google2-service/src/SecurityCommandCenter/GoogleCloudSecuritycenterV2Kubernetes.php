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

class GoogleCloudSecuritycenterV2Kubernetes extends \Google\Collection
{
  protected $collection_key = 'roles';
  protected $accessReviewsType = GoogleCloudSecuritycenterV2AccessReview::class;
  protected $accessReviewsDataType = 'array';
  protected $bindingsType = GoogleCloudSecuritycenterV2Binding::class;
  protected $bindingsDataType = 'array';
  protected $nodePoolsType = GoogleCloudSecuritycenterV2NodePool::class;
  protected $nodePoolsDataType = 'array';
  protected $nodesType = GoogleCloudSecuritycenterV2Node::class;
  protected $nodesDataType = 'array';
  protected $objectsType = GoogleCloudSecuritycenterV2Object::class;
  protected $objectsDataType = 'array';
  protected $podsType = GoogleCloudSecuritycenterV2Pod::class;
  protected $podsDataType = 'array';
  protected $rolesType = GoogleCloudSecuritycenterV2Role::class;
  protected $rolesDataType = 'array';

  /**
   * Provides information on any Kubernetes access reviews (privilege checks)
   * relevant to the finding.
   *
   * @param GoogleCloudSecuritycenterV2AccessReview[] $accessReviews
   */
  public function setAccessReviews($accessReviews)
  {
    $this->accessReviews = $accessReviews;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AccessReview[]
   */
  public function getAccessReviews()
  {
    return $this->accessReviews;
  }
  /**
   * Provides Kubernetes role binding information for findings that involve
   * [RoleBindings or ClusterRoleBindings](https://cloud.google.com/kubernetes-
   * engine/docs/how-to/role-based-access-control).
   *
   * @param GoogleCloudSecuritycenterV2Binding[] $bindings
   */
  public function setBindings($bindings)
  {
    $this->bindings = $bindings;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Binding[]
   */
  public function getBindings()
  {
    return $this->bindings;
  }
  /**
   * GKE [node pools](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/node-pools) associated with the finding. This field
   * contains node pool information for each node, when it is available.
   *
   * @param GoogleCloudSecuritycenterV2NodePool[] $nodePools
   */
  public function setNodePools($nodePools)
  {
    $this->nodePools = $nodePools;
  }
  /**
   * @return GoogleCloudSecuritycenterV2NodePool[]
   */
  public function getNodePools()
  {
    return $this->nodePools;
  }
  /**
   * Provides Kubernetes [node](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/cluster-architecture#nodes) information.
   *
   * @param GoogleCloudSecuritycenterV2Node[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Node[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Kubernetes objects related to the finding.
   *
   * @param GoogleCloudSecuritycenterV2Object[] $objects
   */
  public function setObjects($objects)
  {
    $this->objects = $objects;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Object[]
   */
  public function getObjects()
  {
    return $this->objects;
  }
  /**
   * Kubernetes [Pods](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/pod) associated with the finding. This field contains
   * Pod records for each container that is owned by a Pod.
   *
   * @param GoogleCloudSecuritycenterV2Pod[] $pods
   */
  public function setPods($pods)
  {
    $this->pods = $pods;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Pod[]
   */
  public function getPods()
  {
    return $this->pods;
  }
  /**
   * Provides Kubernetes role information for findings that involve [Roles or
   * ClusterRoles](https://cloud.google.com/kubernetes-engine/docs/how-to/role-
   * based-access-control).
   *
   * @param GoogleCloudSecuritycenterV2Role[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return GoogleCloudSecuritycenterV2Role[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2Kubernetes::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Kubernetes');
