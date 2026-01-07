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

class SetTagsResponse extends \Google\Model
{
  /**
   * Required. The full One Platform resource name of the service resource.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Tag keys/values directly bound to this resource. Each item in the
   * map must be expressed as " : ". For example: "123/environment" :
   * "production", "123/costCenter" : "marketing"
   *
   * @var string[]
   */
  public $tags;
  /**
   * A checksum based on the current bindings. This field is always set in
   * server responses.
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
   * Required. Tag keys/values directly bound to this resource. Each item in the
   * map must be expressed as " : ". For example: "123/environment" :
   * "production", "123/costCenter" : "marketing"
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
   * A checksum based on the current bindings. This field is always set in
   * server responses.
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
class_alias(SetTagsResponse::class, 'Google_Service_CloudMemorystoreforMemcached_SetTagsResponse');
