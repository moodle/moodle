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

class GoogleDatastoreAdminV1EntityFilter extends \Google\Collection
{
  protected $collection_key = 'namespaceIds';
  /**
   * If empty, then this represents all kinds.
   *
   * @var string[]
   */
  public $kinds;
  /**
   * An empty list represents all namespaces. This is the preferred usage for
   * projects that don't use namespaces. An empty string element represents the
   * default namespace. This should be used if the project has data in non-
   * default namespaces, but doesn't want to include them. Each namespace in
   * this list must be unique.
   *
   * @var string[]
   */
  public $namespaceIds;

  /**
   * If empty, then this represents all kinds.
   *
   * @param string[] $kinds
   */
  public function setKinds($kinds)
  {
    $this->kinds = $kinds;
  }
  /**
   * @return string[]
   */
  public function getKinds()
  {
    return $this->kinds;
  }
  /**
   * An empty list represents all namespaces. This is the preferred usage for
   * projects that don't use namespaces. An empty string element represents the
   * default namespace. This should be used if the project has data in non-
   * default namespaces, but doesn't want to include them. Each namespace in
   * this list must be unique.
   *
   * @param string[] $namespaceIds
   */
  public function setNamespaceIds($namespaceIds)
  {
    $this->namespaceIds = $namespaceIds;
  }
  /**
   * @return string[]
   */
  public function getNamespaceIds()
  {
    return $this->namespaceIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1EntityFilter::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1EntityFilter');
