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

namespace Google\Service\Datastore;

class MutationResult extends \Google\Collection
{
  protected $collection_key = 'transformResults';
  /**
   * Whether a conflict was detected for this mutation. Always false when a
   * conflict detection strategy field is not set in the mutation.
   *
   * @var bool
   */
  public $conflictDetected;
  /**
   * The create time of the entity. This field will not be set after a 'delete'.
   *
   * @var string
   */
  public $createTime;
  protected $keyType = Key::class;
  protected $keyDataType = '';
  protected $transformResultsType = Value::class;
  protected $transformResultsDataType = 'array';
  /**
   * The update time of the entity on the server after processing the mutation.
   * If the mutation doesn't change anything on the server, then the timestamp
   * will be the update timestamp of the current entity. This field will not be
   * set after a 'delete'.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The version of the entity on the server after processing the mutation. If
   * the mutation doesn't change anything on the server, then the version will
   * be the version of the current entity or, if no entity is present, a version
   * that is strictly greater than the version of any previous entity and less
   * than the version of any possible future entity.
   *
   * @var string
   */
  public $version;

  /**
   * Whether a conflict was detected for this mutation. Always false when a
   * conflict detection strategy field is not set in the mutation.
   *
   * @param bool $conflictDetected
   */
  public function setConflictDetected($conflictDetected)
  {
    $this->conflictDetected = $conflictDetected;
  }
  /**
   * @return bool
   */
  public function getConflictDetected()
  {
    return $this->conflictDetected;
  }
  /**
   * The create time of the entity. This field will not be set after a 'delete'.
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
   * The automatically allocated key. Set only when the mutation allocated a
   * key.
   *
   * @param Key $key
   */
  public function setKey(Key $key)
  {
    $this->key = $key;
  }
  /**
   * @return Key
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The results of applying each PropertyTransform, in the same order of the
   * request.
   *
   * @param Value[] $transformResults
   */
  public function setTransformResults($transformResults)
  {
    $this->transformResults = $transformResults;
  }
  /**
   * @return Value[]
   */
  public function getTransformResults()
  {
    return $this->transformResults;
  }
  /**
   * The update time of the entity on the server after processing the mutation.
   * If the mutation doesn't change anything on the server, then the timestamp
   * will be the update timestamp of the current entity. This field will not be
   * set after a 'delete'.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The version of the entity on the server after processing the mutation. If
   * the mutation doesn't change anything on the server, then the version will
   * be the version of the current entity or, if no entity is present, a version
   * that is strictly greater than the version of any previous entity and less
   * than the version of any possible future entity.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MutationResult::class, 'Google_Service_Datastore_MutationResult');
