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

class RewriteResponse extends \Google\Model
{
  /**
   * true if the copy is finished; otherwise, false if the copy is in progress.
   * This property is always present in the response.
   *
   * @var bool
   */
  public $done;
  /**
   * The kind of item this is.
   *
   * @var string
   */
  public $kind;
  /**
   * The total size of the object being copied in bytes. This property is always
   * present in the response.
   *
   * @var string
   */
  public $objectSize;
  protected $resourceType = StorageObject::class;
  protected $resourceDataType = '';
  /**
   * A token to use in subsequent requests to continue copying data. This token
   * is present in the response only when there is more data to copy.
   *
   * @var string
   */
  public $rewriteToken;
  /**
   * The total bytes written so far, which can be used to provide a waiting user
   * with a progress indicator. This property is always present in the response.
   *
   * @var string
   */
  public $totalBytesRewritten;

  /**
   * true if the copy is finished; otherwise, false if the copy is in progress.
   * This property is always present in the response.
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
  }
  /**
   * The kind of item this is.
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
   * The total size of the object being copied in bytes. This property is always
   * present in the response.
   *
   * @param string $objectSize
   */
  public function setObjectSize($objectSize)
  {
    $this->objectSize = $objectSize;
  }
  /**
   * @return string
   */
  public function getObjectSize()
  {
    return $this->objectSize;
  }
  /**
   * A resource containing the metadata for the copied-to object. This property
   * is present in the response only when copying completes.
   *
   * @param StorageObject $resource
   */
  public function setResource(StorageObject $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return StorageObject
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * A token to use in subsequent requests to continue copying data. This token
   * is present in the response only when there is more data to copy.
   *
   * @param string $rewriteToken
   */
  public function setRewriteToken($rewriteToken)
  {
    $this->rewriteToken = $rewriteToken;
  }
  /**
   * @return string
   */
  public function getRewriteToken()
  {
    return $this->rewriteToken;
  }
  /**
   * The total bytes written so far, which can be used to provide a waiting user
   * with a progress indicator. This property is always present in the response.
   *
   * @param string $totalBytesRewritten
   */
  public function setTotalBytesRewritten($totalBytesRewritten)
  {
    $this->totalBytesRewritten = $totalBytesRewritten;
  }
  /**
   * @return string
   */
  public function getTotalBytesRewritten()
  {
    return $this->totalBytesRewritten;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RewriteResponse::class, 'Google_Service_Storage_RewriteResponse');
