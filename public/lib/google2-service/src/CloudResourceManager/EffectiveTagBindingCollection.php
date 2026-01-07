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

class EffectiveTagBindingCollection extends \Google\Model
{
  /**
   * Tag keys/values effectively bound to this resource, specified in namespaced
   * format. For example: "123/environment": "production"
   *
   * @var string[]
   */
  public $effectiveTags;
  /**
   * The full resource name of the resource the TagBindings are bound to. E.g.
   * `//cloudresourcemanager.googleapis.com/projects/123`
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Identifier. The name of the EffectiveTagBindingCollection, following the
   * convention: `locations/{location}/effectiveTagBindingCollections/{encoded-
   * full-resource-name}` where the encoded-full-resource-name is the UTF-8
   * encoded name of the GCP resource the TagBindings are bound to. E.g. "locati
   * ons/global/effectiveTagBindingCollections/%2f%2fcloudresourcemanager.google
   * apis.com%2fprojects%2f123"
   *
   * @var string
   */
  public $name;

  /**
   * Tag keys/values effectively bound to this resource, specified in namespaced
   * format. For example: "123/environment": "production"
   *
   * @param string[] $effectiveTags
   */
  public function setEffectiveTags($effectiveTags)
  {
    $this->effectiveTags = $effectiveTags;
  }
  /**
   * @return string[]
   */
  public function getEffectiveTags()
  {
    return $this->effectiveTags;
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
   * Identifier. The name of the EffectiveTagBindingCollection, following the
   * convention: `locations/{location}/effectiveTagBindingCollections/{encoded-
   * full-resource-name}` where the encoded-full-resource-name is the UTF-8
   * encoded name of the GCP resource the TagBindings are bound to. E.g. "locati
   * ons/global/effectiveTagBindingCollections/%2f%2fcloudresourcemanager.google
   * apis.com%2fprojects%2f123"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EffectiveTagBindingCollection::class, 'Google_Service_CloudResourceManager_EffectiveTagBindingCollection');
