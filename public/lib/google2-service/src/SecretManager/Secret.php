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

namespace Google\Service\SecretManager;

class Secret extends \Google\Collection
{
  protected $collection_key = 'topics';
  /**
   * Optional. Custom metadata about the secret. Annotations are distinct from
   * various forms of labels. Annotations exist to allow client tools to store
   * their own state information without requiring a database. Annotation keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, begin and end with an alphanumeric character ([a-z0-9A-Z]), and
   * may have dashes (-), underscores (_), dots (.), and alphanumerics in
   * between these symbols. The total size of annotation keys and values must be
   * less than 16KiB.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The time at which the Secret was created.
   *
   * @var string
   */
  public $createTime;
  protected $customerManagedEncryptionType = CustomerManagedEncryption::class;
  protected $customerManagedEncryptionDataType = '';
  /**
   * Optional. Etag of the currently stored Secret.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Timestamp in UTC when the Secret is scheduled to expire. This is
   * always provided on output, regardless of what was sent on input.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The labels assigned to this Secret. Label keys must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression: `\p{Ll}\p{Lo}{0,62}`
   * Label values must be between 0 and 63 characters long, have a UTF-8
   * encoding of maximum 128 bytes, and must conform to the following PCRE
   * regular expression: `[\p{Ll}\p{Lo}\p{N}_-]{0,63}` No more than 64 labels
   * can be assigned to a given resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the Secret in the format
   * `projects/secrets`.
   *
   * @var string
   */
  public $name;
  protected $replicationType = Replication::class;
  protected $replicationDataType = '';
  protected $rotationType = Rotation::class;
  protected $rotationDataType = '';
  /**
   * Optional. Input only. Immutable. Mapping of Tag keys/values directly bound
   * to this resource. For example: "123/environment": "production",
   * "123/costCenter": "marketing" Tags are used to organize and group
   * resources. Tags can be used to control policy evaluation for the resource.
   *
   * @var string[]
   */
  public $tags;
  protected $topicsType = Topic::class;
  protected $topicsDataType = 'array';
  /**
   * Input only. The TTL for the Secret.
   *
   * @var string
   */
  public $ttl;
  /**
   * Optional. Mapping from version alias to version name. A version alias is a
   * string with a maximum length of 63 characters and can contain uppercase and
   * lowercase letters, numerals, and the hyphen (`-`) and underscore ('_')
   * characters. An alias string must start with a letter and cannot be the
   * string 'latest' or 'NEW'. No more than 50 aliases can be assigned to a
   * given secret. Version-Alias pairs will be viewable via GetSecret and
   * modifiable via UpdateSecret. Access by alias is only be supported on
   * GetSecretVersion and AccessSecretVersion.
   *
   * @var string[]
   */
  public $versionAliases;
  /**
   * Optional. Secret Version TTL after destruction request This is a part of
   * the Delayed secret version destroy feature. For secret with TTL>0, version
   * destruction doesn't happen immediately on calling destroy instead the
   * version goes to a disabled state and destruction happens after the TTL
   * expires.
   *
   * @var string
   */
  public $versionDestroyTtl;

  /**
   * Optional. Custom metadata about the secret. Annotations are distinct from
   * various forms of labels. Annotations exist to allow client tools to store
   * their own state information without requiring a database. Annotation keys
   * must be between 1 and 63 characters long, have a UTF-8 encoding of maximum
   * 128 bytes, begin and end with an alphanumeric character ([a-z0-9A-Z]), and
   * may have dashes (-), underscores (_), dots (.), and alphanumerics in
   * between these symbols. The total size of annotation keys and values must be
   * less than 16KiB.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The time at which the Secret was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The customer-managed encryption configuration of the regionalized
   * secrets. If no configuration is provided, Google-managed default encryption
   * is used. Updates to the Secret encryption configuration only apply to
   * SecretVersions added afterwards. They do not apply retroactively to
   * existing SecretVersions.
   *
   * @param CustomerManagedEncryption $customerManagedEncryption
   */
  public function setCustomerManagedEncryption(CustomerManagedEncryption $customerManagedEncryption)
  {
    $this->customerManagedEncryption = $customerManagedEncryption;
  }
  /**
   * @return CustomerManagedEncryption
   */
  public function getCustomerManagedEncryption()
  {
    return $this->customerManagedEncryption;
  }
  /**
   * Optional. Etag of the currently stored Secret.
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
   * Optional. Timestamp in UTC when the Secret is scheduled to expire. This is
   * always provided on output, regardless of what was sent on input.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The labels assigned to this Secret. Label keys must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression: `\p{Ll}\p{Lo}{0,62}`
   * Label values must be between 0 and 63 characters long, have a UTF-8
   * encoding of maximum 128 bytes, and must conform to the following PCRE
   * regular expression: `[\p{Ll}\p{Lo}\p{N}_-]{0,63}` No more than 64 labels
   * can be assigned to a given resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The resource name of the Secret in the format
   * `projects/secrets`.
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
   * Optional. Immutable. The replication policy of the secret data attached to
   * the Secret. The replication policy cannot be changed after the Secret has
   * been created.
   *
   * @param Replication $replication
   */
  public function setReplication(Replication $replication)
  {
    $this->replication = $replication;
  }
  /**
   * @return Replication
   */
  public function getReplication()
  {
    return $this->replication;
  }
  /**
   * Optional. Rotation policy attached to the Secret. May be excluded if there
   * is no rotation policy.
   *
   * @param Rotation $rotation
   */
  public function setRotation(Rotation $rotation)
  {
    $this->rotation = $rotation;
  }
  /**
   * @return Rotation
   */
  public function getRotation()
  {
    return $this->rotation;
  }
  /**
   * Optional. Input only. Immutable. Mapping of Tag keys/values directly bound
   * to this resource. For example: "123/environment": "production",
   * "123/costCenter": "marketing" Tags are used to organize and group
   * resources. Tags can be used to control policy evaluation for the resource.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Optional. A list of up to 10 Pub/Sub topics to which messages are published
   * when control plane operations are called on the secret or its versions.
   *
   * @param Topic[] $topics
   */
  public function setTopics($topics)
  {
    $this->topics = $topics;
  }
  /**
   * @return Topic[]
   */
  public function getTopics()
  {
    return $this->topics;
  }
  /**
   * Input only. The TTL for the Secret.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
  /**
   * Optional. Mapping from version alias to version name. A version alias is a
   * string with a maximum length of 63 characters and can contain uppercase and
   * lowercase letters, numerals, and the hyphen (`-`) and underscore ('_')
   * characters. An alias string must start with a letter and cannot be the
   * string 'latest' or 'NEW'. No more than 50 aliases can be assigned to a
   * given secret. Version-Alias pairs will be viewable via GetSecret and
   * modifiable via UpdateSecret. Access by alias is only be supported on
   * GetSecretVersion and AccessSecretVersion.
   *
   * @param string[] $versionAliases
   */
  public function setVersionAliases($versionAliases)
  {
    $this->versionAliases = $versionAliases;
  }
  /**
   * @return string[]
   */
  public function getVersionAliases()
  {
    return $this->versionAliases;
  }
  /**
   * Optional. Secret Version TTL after destruction request This is a part of
   * the Delayed secret version destroy feature. For secret with TTL>0, version
   * destruction doesn't happen immediately on calling destroy instead the
   * version goes to a disabled state and destruction happens after the TTL
   * expires.
   *
   * @param string $versionDestroyTtl
   */
  public function setVersionDestroyTtl($versionDestroyTtl)
  {
    $this->versionDestroyTtl = $versionDestroyTtl;
  }
  /**
   * @return string
   */
  public function getVersionDestroyTtl()
  {
    return $this->versionDestroyTtl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Secret::class, 'Google_Service_SecretManager_Secret');
