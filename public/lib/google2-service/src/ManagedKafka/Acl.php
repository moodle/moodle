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

namespace Google\Service\ManagedKafka;

class Acl extends \Google\Collection
{
  protected $collection_key = 'aclEntries';
  protected $aclEntriesType = AclEntry::class;
  protected $aclEntriesDataType = 'array';
  /**
   * Optional. `etag` is used for concurrency control. An `etag` is returned in
   * the response to `GetAcl` and `CreateAcl`. Callers are required to put that
   * etag in the request to `UpdateAcl` to ensure that their change will be
   * applied to the same version of the acl that exists in the Kafka Cluster. A
   * terminal 'T' character in the etag indicates that the AclEntries were
   * truncated; more entries for the Acl exist on the Kafka Cluster, but can't
   * be returned in the Acl due to repeated field limits.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The name for the acl. Represents a single Resource Pattern.
   * Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. `acl_id` is structured like one of
   * the following: For acls on the cluster: `cluster` For acls on a single
   * resource within the cluster: `topic/{resource_name}`
   * `consumerGroup/{resource_name}` `transactionalId/{resource_name}` For acls
   * on all resources that match a prefix: `topicPrefixed/{resource_name}`
   * `consumerGroupPrefixed/{resource_name}`
   * `transactionalIdPrefixed/{resource_name}` For acls on all resources of a
   * given type (i.e. the wildcard literal "*"): `allTopics` (represents
   * `topic`) `allConsumerGroups` (represents `consumerGroup`)
   * `allTransactionalIds` (represents `transactionalId`)
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The ACL pattern type derived from the name. One of: LITERAL,
   * PREFIXED.
   *
   * @var string
   */
  public $patternType;
  /**
   * Output only. The ACL resource name derived from the name. For cluster
   * resource_type, this is always "kafka-cluster". Can be the wildcard literal
   * "*".
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The ACL resource type derived from the name. One of: CLUSTER,
   * TOPIC, GROUP, TRANSACTIONAL_ID.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Required. The ACL entries that apply to the resource pattern. The maximum
   * number of allowed entries 100.
   *
   * @param AclEntry[] $aclEntries
   */
  public function setAclEntries($aclEntries)
  {
    $this->aclEntries = $aclEntries;
  }
  /**
   * @return AclEntry[]
   */
  public function getAclEntries()
  {
    return $this->aclEntries;
  }
  /**
   * Optional. `etag` is used for concurrency control. An `etag` is returned in
   * the response to `GetAcl` and `CreateAcl`. Callers are required to put that
   * etag in the request to `UpdateAcl` to ensure that their change will be
   * applied to the same version of the acl that exists in the Kafka Cluster. A
   * terminal 'T' character in the etag indicates that the AclEntries were
   * truncated; more entries for the Acl exist on the Kafka Cluster, but can't
   * be returned in the Acl due to repeated field limits.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifier. The name for the acl. Represents a single Resource Pattern.
   * Structured like:
   * projects/{project}/locations/{location}/clusters/{cluster}/acls/{acl_id}
   * The structure of `acl_id` defines the Resource Pattern (resource_type,
   * resource_name, pattern_type) of the acl. `acl_id` is structured like one of
   * the following: For acls on the cluster: `cluster` For acls on a single
   * resource within the cluster: `topic/{resource_name}`
   * `consumerGroup/{resource_name}` `transactionalId/{resource_name}` For acls
   * on all resources that match a prefix: `topicPrefixed/{resource_name}`
   * `consumerGroupPrefixed/{resource_name}`
   * `transactionalIdPrefixed/{resource_name}` For acls on all resources of a
   * given type (i.e. the wildcard literal "*"): `allTopics` (represents
   * `topic`) `allConsumerGroups` (represents `consumerGroup`)
   * `allTransactionalIds` (represents `transactionalId`)
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The ACL pattern type derived from the name. One of: LITERAL,
   * PREFIXED.
   *
   * @param string $patternType
   */
  public function setPatternType($patternType)
  {
    $this->patternType = $patternType;
  }
  /**
   * @return string
   */
  public function getPatternType()
  {
    return $this->patternType;
  }
  /**
   * Output only. The ACL resource name derived from the name. For cluster
   * resource_type, this is always "kafka-cluster". Can be the wildcard literal
   * "*".
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The ACL resource type derived from the name. One of: CLUSTER,
   * TOPIC, GROUP, TRANSACTIONAL_ID.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Acl::class, 'Google_Service_ManagedKafka_Acl');
