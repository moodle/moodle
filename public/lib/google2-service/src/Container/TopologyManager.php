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

class TopologyManager extends \Google\Model
{
  /**
   * Configures the strategy for resource alignment. Allowed values are: * none:
   * the default policy, and does not perform any topology alignment. *
   * restricted: the topology manager stores the preferred NUMA node affinity
   * for the container, and will reject the pod if the affinity if not
   * preferred. * best-effort: the topology manager stores the preferred NUMA
   * node affinity for the container. If the affinity is not preferred, the
   * topology manager will admit the pod to the node anyway. * single-numa-node:
   * the topology manager determines if the single NUMA node affinity is
   * possible. If it is, Topology Manager will store this and the Hint Providers
   * can then use this information when making the resource allocation decision.
   * If, however, this is not possible then the Topology Manager will reject the
   * pod from the node. This will result in a pod in a Terminated state with a
   * pod admission failure. The default policy value is 'none' if unspecified.
   * Details about each strategy can be found
   * [here](https://kubernetes.io/docs/tasks/administer-cluster/topology-
   * manager/#topology-manager-policies).
   *
   * @var string
   */
  public $policy;
  /**
   * The Topology Manager aligns resources in following scopes: * container *
   * pod The default scope is 'container' if unspecified. See
   * https://kubernetes.io/docs/tasks/administer-cluster/topology-
   * manager/#topology-manager-scopes
   *
   * @var string
   */
  public $scope;

  /**
   * Configures the strategy for resource alignment. Allowed values are: * none:
   * the default policy, and does not perform any topology alignment. *
   * restricted: the topology manager stores the preferred NUMA node affinity
   * for the container, and will reject the pod if the affinity if not
   * preferred. * best-effort: the topology manager stores the preferred NUMA
   * node affinity for the container. If the affinity is not preferred, the
   * topology manager will admit the pod to the node anyway. * single-numa-node:
   * the topology manager determines if the single NUMA node affinity is
   * possible. If it is, Topology Manager will store this and the Hint Providers
   * can then use this information when making the resource allocation decision.
   * If, however, this is not possible then the Topology Manager will reject the
   * pod from the node. This will result in a pod in a Terminated state with a
   * pod admission failure. The default policy value is 'none' if unspecified.
   * Details about each strategy can be found
   * [here](https://kubernetes.io/docs/tasks/administer-cluster/topology-
   * manager/#topology-manager-policies).
   *
   * @param string $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return string
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The Topology Manager aligns resources in following scopes: * container *
   * pod The default scope is 'container' if unspecified. See
   * https://kubernetes.io/docs/tasks/administer-cluster/topology-
   * manager/#topology-manager-scopes
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TopologyManager::class, 'Google_Service_Container_TopologyManager');
