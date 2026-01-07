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

class ComposeRequest extends \Google\Collection
{
  protected $collection_key = 'sourceObjects';
  /**
   * If true, the source objects will be deleted.
   *
   * @var bool
   */
  public $deleteSourceObjects;
  protected $destinationType = StorageObject::class;
  protected $destinationDataType = '';
  /**
   * The kind of item this is.
   *
   * @var string
   */
  public $kind;
  protected $sourceObjectsType = ComposeRequestSourceObjects::class;
  protected $sourceObjectsDataType = 'array';

  /**
   * If true, the source objects will be deleted.
   *
   * @param bool $deleteSourceObjects
   */
  public function setDeleteSourceObjects($deleteSourceObjects)
  {
    $this->deleteSourceObjects = $deleteSourceObjects;
  }
  /**
   * @return bool
   */
  public function getDeleteSourceObjects()
  {
    return $this->deleteSourceObjects;
  }
  /**
   * Properties of the resulting object.
   *
   * @param StorageObject $destination
   */
  public function setDestination(StorageObject $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return StorageObject
   */
  public function getDestination()
  {
    return $this->destination;
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
   * The list of source objects that will be concatenated into a single object.
   *
   * @param ComposeRequestSourceObjects[] $sourceObjects
   */
  public function setSourceObjects($sourceObjects)
  {
    $this->sourceObjects = $sourceObjects;
  }
  /**
   * @return ComposeRequestSourceObjects[]
   */
  public function getSourceObjects()
  {
    return $this->sourceObjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComposeRequest::class, 'Google_Service_Storage_ComposeRequest');
