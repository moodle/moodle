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

class Kubernetes extends \Google\Collection
{
  protected $collection_key = 'roles';
  protected $accessReviewsType = AccessReview::class;
  protected $accessReviewsDataType = 'array';
  protected $bindingsType = GoogleCloudSecuritycenterV1Binding::class;
  protected $bindingsDataType = 'array';
  protected $nodePoolsType = NodePool::class;
  protected $nodePoolsDataType = 'array';
  protected $nodesType = Node::class;
  protected $nodesDataType = 'array';
  protected $objectsType = SecuritycenterObject::class;
  protected $objectsDataType = 'array';
  protected $podsType = Pod::class;
  protected $podsDataType = 'array';
  protected $rolesType = Role::class;
  protected $rolesDataType = 'array';

  /**
   * Provides information on any Kubernetes access reviews (privilege checks)
   * relevant to the finding.
   *
   * @param AccessReview[] $accessReviews
   */
  public function setAccessReviews($accessReviews)
  {
    $this->accessReviews = $accessReviews;
  }
  /**
   * @return AccessReview[]
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
   * @param GoogleCloudSecuritycenterV1Binding[] $bindings
   */
  public function setBindings($bindings)
  {
    $this->bindings = $bindings;
  }
  /**
   * @return GoogleCloudSecuritycenterV1Binding[]
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
   * @param NodePool[] $nodePools
   */
  public function setNodePools($nodePools)
  {
    $this->nodePools = $nodePools;
  }
  /**
   * @return NodePool[]
   */
  public function getNodePools()
  {
    return $this->nodePools;
  }
  /**
   * Provides Kubernetes [node](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/cluster-architecture#nodes) information.
   *
   * @param Node[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return Node[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Kubernetes objects related to the finding.
   *
   * @param SecuritycenterObject[] $objects
   */
  public function setObjects($objects)
  {
    $this->objects = $objects;
  }
  /**
   * @return SecuritycenterObject[]
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
   * @param Pod[] $pods
   */
  public function setPods($pods)
  {
    $this->pods = $pods;
  }
  /**
   * @return Pod[]
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
   * @param Role[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return Role[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Kubernetes::class, 'Google_Service_SecurityCommandCenter_Kubernetes');
