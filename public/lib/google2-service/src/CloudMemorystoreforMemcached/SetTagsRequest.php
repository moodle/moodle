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

namespace Google\Service\CloudMemorystoreforMemcached;

class SetTagsRequest extends \Google\Model
{
  /**
   * Required. The full One Platform resource name of the service resource.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. A unique identifier for this request. Must be a valid UUID. This
   * request is only idempotent if a `request_id` is provided.
   *
   * @var string
   */
  public $requestId;
  /**
   * Required. These bindings will override any bindings previously set and will
   * be effective immediately. Each item in the map must be expressed as " : ".
   * For example: "123/environment" : "production", "123/costCenter" :
   * "marketing"
   *
   * @var string[]
   */
  public $tags;
  /**
   * Optional. A checksum based on the current bindings which can be passed to
   * prevent race conditions. If not passed, etag check would be skipped.
   *
   * @var string
   */
  public $tagsEtag;

  /**
   * Required. The full One Platform resource name of the service resource.
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
   * Optional. A unique identifier for this request. Must be a valid UUID. This
   * request is only idempotent if a `request_id` is provided.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Required. These bindings will override any bindings previously set and will
   * be effective immediately. Each item in the map must be expressed as " : ".
   * For example: "123/environment" : "production", "123/costCenter" :
   * "marketing"
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
   * Optional. A checksum based on the current bindings which can be passed to
   * prevent race conditions. If not passed, etag check would be skipped.
   *
   * @param string $tagsEtag
   */
  public function setTagsEtag($tagsEtag)
  {
    $this->tagsEtag = $tagsEtag;
  }
  /**
   * @return string
   */
  public function getTagsEtag()
  {
    return $this->tagsEtag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetTagsRequest::class, 'Google_Service_CloudMemorystoreforMemcached_SetTagsRequest');
