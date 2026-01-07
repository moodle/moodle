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

namespace Google\Service\Firestore;

class CollectionSelector extends \Google\Model
{
  /**
   * When false, selects only collections that are immediate children of the
   * `parent` specified in the containing `RunQueryRequest`. When true, selects
   * all descendant collections.
   *
   * @var bool
   */
  public $allDescendants;
  /**
   * The collection ID. When set, selects only collections with this ID.
   *
   * @var string
   */
  public $collectionId;

  /**
   * When false, selects only collections that are immediate children of the
   * `parent` specified in the containing `RunQueryRequest`. When true, selects
   * all descendant collections.
   *
   * @param bool $allDescendants
   */
  public function setAllDescendants($allDescendants)
  {
    $this->allDescendants = $allDescendants;
  }
  /**
   * @return bool
   */
  public function getAllDescendants()
  {
    return $this->allDescendants;
  }
  /**
   * The collection ID. When set, selects only collections with this ID.
   *
   * @param string $collectionId
   */
  public function setCollectionId($collectionId)
  {
    $this->collectionId = $collectionId;
  }
  /**
   * @return string
   */
  public function getCollectionId()
  {
    return $this->collectionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CollectionSelector::class, 'Google_Service_Firestore_CollectionSelector');
