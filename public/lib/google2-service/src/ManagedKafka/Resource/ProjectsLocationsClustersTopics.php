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

use Google\Service\ManagedKafka\ListTopicsResponse;
use Google\Service\ManagedKafka\ManagedkafkaEmpty;
use Google\Service\ManagedKafka\Topic;

/**
 * The "topics" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $topics = $managedkafkaService->projects_locations_clusters_topics;
 *  </code>
 */
class ProjectsLocationsClustersTopics extends \Google\Service\Resource
{
  /**
   * Creates a new topic in a given project and location. (topics.create)
   *
   * @param string $parent Required. The parent cluster in which to create the
   * topic. Structured like
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   * @param Topic $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string topicId Required. The ID to use for the topic, which will
   * become the final component of the topic's name. This value is structured
   * like: `my-topic-name`.
   * @return Topic
   * @throws \Google\Service\Exception
   */
  public function create($parent, Topic $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Topic::class);
  }
  /**
   * Deletes a single topic. (topics.delete)
   *
   * @param string $name Required. The name of the topic to delete.
   * `projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}`.
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
   * Returns the properties of a single topic. (topics.get)
   *
   * @param string $name Required. The name of the topic whose configuration to
   * return. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}.
   * @param array $optParams Optional parameters.
   * @return Topic
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Topic::class);
  }
  /**
   * Lists the topics in a given cluster.
   * (topics.listProjectsLocationsClustersTopics)
   *
   * @param string $parent Required. The parent cluster whose topics are to be
   * listed. Structured like
   * `projects/{project}/locations/{location}/clusters/{cluster}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of topics to return. The
   * service may return fewer than this value. If unset or zero, all topics for
   * the parent is returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListTopics` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListTopics` must match the call
   * that provided the page token.
   * @return ListTopicsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClustersTopics($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTopicsResponse::class);
  }
  /**
   * Updates the properties of a single topic. (topics.patch)
   *
   * @param string $name Identifier. The name of the topic. The `topic` segment is
   * used when connecting directly to the cluster. Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/topics/{topic}
   * @param Topic $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Topic resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The mask is
   * required and a value of * will update all fields.
   * @return Topic
   * @throws \Google\Service\Exception
   */
  public function patch($name, Topic $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Topic::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClustersTopics::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsClustersTopics');
