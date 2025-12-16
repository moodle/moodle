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

namespace Google\Service\Storage;

class Policy extends \Google\Collection
{
  protected $collection_key = 'bindings';
  protected $bindingsType = PolicyBindings::class;
  protected $bindingsDataType = 'array';
  /**
   * HTTP 1.1  Entity tag for the policy.
   *
   * @var string
   */
  public $etag;
  /**
   * The kind of item this is. For policies, this is always storage#policy. This
   * field is ignored on input.
   *
   * @var string
   */
  public $kind;
  /**
   * The ID of the resource to which this policy belongs. Will be of the form
   * projects/_/buckets/bucket for buckets,
   * projects/_/buckets/bucket/objects/object for objects, and
   * projects/_/buckets/bucket/managedFolders/managedFolder. A specific
   * generation may be specified by appending #generationNumber to the end of
   * the object name, e.g. projects/_/buckets/my-bucket/objects/data.txt#17. The
   * current generation can be denoted with #0. This field is ignored on input.
   *
   * @var string
   */
  public $resourceId;
  /**
   * The IAM policy format version.
   *
   * @var int
   */
  public $version;

  /**
   * An association between a role, which comes with a set of permissions, and
   * members who may assume that role.
   *
   * @param PolicyBindings[] $bindings
   */
  public function setBindings($bindings)
  {
    $this->bindings = $bindings;
  }
  /**
   * @return PolicyBindings[]
   */
  public function getBindings()
  {
    return $this->bindings;
  }
  /**
   * HTTP 1.1  Entity tag for the policy.
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
   * The kind of item this is. For policies, this is always storage#policy. This
   * field is ignored on input.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The ID of the resource to which this policy belongs. Will be of the form
   * projects/_/buckets/bucket for buckets,
   * projects/_/buckets/bucket/objects/object for objects, and
   * projects/_/buckets/bucket/managedFolders/managedFolder. A specific
   * generation may be specified by appending #generationNumber to the end of
   * the object name, e.g. projects/_/buckets/my-bucket/objects/data.txt#17. The
   * current generation can be denoted with #0. This field is ignored on input.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The IAM policy format version.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_Storage_Policy');
