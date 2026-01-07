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

namespace Google\Service\ArtifactRegistry;

class BatchDeleteVersionsRequest extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Required. The names of the versions to delete. The maximum number of
   * versions deleted per batch is determined by the service and is dependent on
   * the available resources in the region.
   *
   * @var string[]
   */
  public $names;
  /**
   * If true, the request is performed without deleting data, following AIP-163.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * Required. The names of the versions to delete. The maximum number of
   * versions deleted per batch is determined by the service and is dependent on
   * the available resources in the region.
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
  /**
   * If true, the request is performed without deleting data, following AIP-163.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchDeleteVersionsRequest::class, 'Google_Service_ArtifactRegistry_BatchDeleteVersionsRequest');
