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

namespace Google\Service\CloudResourceManager;

class TagBindingCollection extends \Google\Model
{
  /**
   * Optional. A checksum based on the current bindings which can be passed to
   * prevent race conditions. This field is always set in server responses.
   *
   * @var string
   */
  public $etag;
  /**
   * The full resource name of the resource the TagBindings are bound to. E.g.
   * `//cloudresourcemanager.googleapis.com/projects/123`
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Identifier. The name of the TagBindingCollection, following the convention:
   * `locations/{location}/tagBindingCollections/{encoded-full-resource-name}`
   * where the encoded-full-resource-name is the UTF-8 encoded name of the GCP
   * resource the TagBindings are bound to. "locations/global/tagBindingCollecti
   * ons/%2f%2fcloudresourcemanager.googleapis.com%2fprojects%2f123"
   *
   * @var string
   */
  public $name;
  /**
   * Tag keys/values directly bound to this resource, specified in namespaced
   * format. For example: "123/environment": "production"
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. A checksum based on the current bindings which can be passed to
   * prevent race conditions. This field is always set in server responses.
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
   * The full resource name of the resource the TagBindings are bound to. E.g.
   * `//cloudresourcemanager.googleapis.com/projects/123`
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * Identifier. The name of the TagBindingCollection, following the convention:
   * `locations/{location}/tagBindingCollections/{encoded-full-resource-name}`
   * where the encoded-full-resource-name is the UTF-8 encoded name of the GCP
   * resource the TagBindings are bound to. "locations/global/tagBindingCollecti
   * ons/%2f%2fcloudresourcemanager.googleapis.com%2fprojects%2f123"
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
   * Tag keys/values directly bound to this resource, specified in namespaced
   * format. For example: "123/environment": "production"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagBindingCollection::class, 'Google_Service_CloudResourceManager_TagBindingCollection');
