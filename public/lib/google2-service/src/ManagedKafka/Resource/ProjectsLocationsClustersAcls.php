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

use Google\Service\ManagedKafka\Acl;
use Google\Service\ManagedKafka\AclEntry;
use Google\Service\ManagedKafka\AddAclEntryResponse;
use Google\Service\ManagedKafka\ListAclsResponse;
use Google\Service\ManagedKafka\ManagedkafkaEmpty;
use Google\Service\ManagedKafka\RemoveAclEntryResponse;

/**
 * The "acls" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $acls = $managedkafkaService->projects_locations_clusters_acls;
 *  </code>
 */
class ProjectsLocationsClustersAcls extends \Google\Service\Resource
{
  /**
   * Incremental update: Adds an acl entry to an acl. Creates the acl if it does
   * not exist yet. (acls.addAclEntry)
   *
   * @param string $acl Required. The name of the acl to add the acl entry to.
   * Structured like:
   * `projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}`.
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. See `Acl.name` for details.
   * @param AclEntry $postBody
   * @param array $optParams Optional parameters.
   * @return AddAclEntryResponse
   * @throws \Google\Service\Exception
   */
  public function addAclEntry($acl, AclEntry $postBody, $optParams = [])
  {
    $params = ['acl' => $acl, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addAclEntry', [$params], AddAclEntryResponse::class);
  }
  /**
   * Creates a new acl in the given project, location, and cluster. (acls.create)
   *
   * @param string $parent Required. The parent cluster in which to create the
   * acl. Structured like
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   * @param Acl $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string aclId Required. The ID to use for the acl, which will
   * become the final component of the acl's name. The structure of `acl_id`
   * defines the Resource Pattern (resource_type, resource_name, pattern_type) of
   * the acl. `acl_id` is structured like one of the following: For acls on the
   * cluster: `cluster` For acls on a single resource within the cluster:
   * `topic/{resource_name}` `consumerGroup/{resource_name}`
   * `transactionalId/{resource_name}` For acls on all resources that match a
   * prefix: `topicPrefixed/{resource_name}`
   * `consumerGroupPrefixed/{resource_name}`
   * `transactionalIdPrefixed/{resource_name}` For acls on all resources of a
   * given type (i.e. the wildcard literal "*"): `allTopics` (represents `topic`)
   * `allConsumerGroups` (represents `consumerGroup`) `allTransactionalIds`
   * (represents `transactionalId`)
   * @return Acl
   * @throws \Google\Service\Exception
   */
  public function create($parent, Acl $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Acl::class);
  }
  /**
   * Deletes an acl. (acls.delete)
   *
   * @param string $name Required. The name of the acl to delete. Structured like:
   * `projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}`.
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. See `Acl.name` for details.
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
   * Returns the properties of a single acl. (acls.get)
   *
   * @param string $name Required. The name of the acl to return. Structured like:
   * `projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}`.
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. See `Acl.name` for details.
   * @param array $optParams Optional parameters.
   * @return Acl
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Acl::class);
  }
  /**
   * Lists the acls in a given cluster. (acls.listProjectsLocationsClustersAcls)
   *
   * @param string $parent Required. The parent cluster whose acls are to be
   * listed. Structured like
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of acls to return. The
   * service may return fewer than this value. If unset or zero, all acls for the
   * parent is returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAcls` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAcls` must match the call
   * that provided the page token.
   * @return ListAclsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClustersAcls($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAclsResponse::class);
  }
  /**
   * Updates the properties of a single acl. (acls.patch)
   *
   * @param string $name Identifier. The name for the acl. Represents a single
   * Resource Pattern. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id} The
   * structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. `acl_id` is structured like one of
   * the following: For acls on the cluster: `cluster` For acls on a single
   * resource within the cluster: `topic/{resource_name}`
   * `consumerGroup/{resource_name}` `transactionalId/{resource_name}` For acls on
   * all resources that match a prefix: `topicPrefixed/{resource_name}`
   * `consumerGroupPrefixed/{resource_name}`
   * `transactionalIdPrefixed/{resource_name}` For acls on all resources of a
   * given type (i.e. the wildcard literal "*"): `allTopics` (represents `topic`)
   * `allConsumerGroups` (represents `consumerGroup`) `allTransactionalIds`
   * (represents `transactionalId`)
   * @param Acl $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Acl resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask.
   * @return Acl
   * @throws \Google\Service\Exception
   */
  public function patch($name, Acl $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Acl::class);
  }
  /**
   * Incremental update: Removes an acl entry from an acl. Deletes the acl if its
   * acl entries become empty (i.e. if the removed entry was the last one in the
   * acl). (acls.removeAclEntry)
   *
   * @param string $acl Required. The name of the acl to remove the acl entry
   * from. Structured like:
   * `projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}`.
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. See `Acl.name` for details.
   * @param AclEntry $postBody
   * @param array $optParams Optional parameters.
   * @return RemoveAclEntryResponse
   * @throws \Google\Service\Exception
   */
  public function removeAclEntry($acl, AclEntry $postBody, $optParams = [])
  {
    $params = ['acl' => $acl, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeAclEntry', [$params], RemoveAclEntryResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClustersAcls::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsClustersAcls');
