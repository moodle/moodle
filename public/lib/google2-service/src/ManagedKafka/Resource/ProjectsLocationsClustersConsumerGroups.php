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

namespace Google\Service\ManagedKafka\Resource;

use Google\Service\ManagedKafka\ConsumerGroup;
use Google\Service\ManagedKafka\ListConsumerGroupsResponse;
use Google\Service\ManagedKafka\ManagedkafkaEmpty;

/**
 * The "consumerGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $consumerGroups = $managedkafkaService->projects_locations_clusters_consumerGroups;
 *  </code>
 */
class ProjectsLocationsClustersConsumerGroups extends \Google\Service\Resource
{
  /**
   * Deletes a single consumer group. (consumerGroups.delete)
   *
   * @param string $name Required. The name of the consumer group to delete. `proj
   * ects/{project}/locations/{location}/clusters/{cluster}/consumerGroups/{consum
   * erGroup}`.
   * @param array $optParams Optional parameters.
   * @return ManagedkafkaEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ManagedkafkaEmpty::class);
  }
  /**
   * Returns the properties of a single consumer group. (consumerGroups.get)
   *
   * @param string $name Required. The name of the consumer group whose
   * configuration to return. `projects/{project}/locations/{location}/clusters/{c
   * luster}/consumerGroups/{consumerGroup}`.
   * @param array $optParams Optional parameters.
   * @return ConsumerGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ConsumerGroup::class);
  }
  /**
   * Lists the consumer groups in a given cluster.
   * (consumerGroups.listProjectsLocationsClustersConsumerGroups)
   *
   * @param string $parent Required. The parent cluster whose consumer groups are
   * to be listed. Structured like
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of consumer groups to
   * return. The service may return fewer than this value. If unset or zero, all
   * consumer groups for the parent is returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListConsumerGroups` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListConsumerGroups` must match
   * the call that provided the page token.
   * @opt_param string view Optional. Specifies the view (BASIC or FULL) of the
   * ConsumerGroup resource to be returned in the response. Defaults to FULL view.
   * @return ListConsumerGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClustersConsumerGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConsumerGroupsResponse::class);
  }
  /**
   * Updates the properties of a single consumer group. (consumerGroups.patch)
   *
   * @param string $name Identifier. The name of the consumer group. The
   * `consumer_group` segment is used when connecting directly to the cluster.
   * Structured like: projects/{project}/locations/{location}/clusters/{cluster}/c
   * onsumerGroups/{consumer_group}
   * @param ConsumerGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the ConsumerGroup resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. The mask is
   * required and a value of * will update all fields.
   * @return ConsumerGroup
   * @throws \Google\Service\Exception
   */
  public function patch($name, ConsumerGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], ConsumerGroup::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClustersConsumerGroups::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsClustersConsumerGroups');
