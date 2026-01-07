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

namespace Google\Service\CloudHealthcare;

class RollbackFhirResourceFilteringFields extends \Google\Collection
{
  protected $collection_key = 'operationIds';
  /**
   * Optional. A filter expression that matches data in the `Resource.meta`
   * element. Supports all filters in [AIP-160](https://google.aip.dev/160)
   * except the "has" (`:`) operator. Supports the following custom functions: *
   * `tag("") = ""` for tag filtering. * `extension_value_ts("") = ` for
   * filtering extensions with a timestamp, where `` is a Unix timestamp.
   * Supports the `>`, `<`, `<=`, `>=`, and `!=` comparison operators.
   *
   * @var string
   */
  public $metadataFilter;
  /**
   * Optional. A list of operation IDs to roll back.
   *
   * @var string[]
   */
  public $operationIds;

  /**
   * Optional. A filter expression that matches data in the `Resource.meta`
   * element. Supports all filters in [AIP-160](https://google.aip.dev/160)
   * except the "has" (`:`) operator. Supports the following custom functions: *
   * `tag("") = ""` for tag filtering. * `extension_value_ts("") = ` for
   * filtering extensions with a timestamp, where `` is a Unix timestamp.
   * Supports the `>`, `<`, `<=`, `>=`, and `!=` comparison operators.
   *
   * @param string $metadataFilter
   */
  public function setMetadataFilter($metadataFilter)
  {
    $this->metadataFilter = $metadataFilter;
  }
  /**
   * @return string
   */
  public function getMetadataFilter()
  {
    return $this->metadataFilter;
  }
  /**
   * Optional. A list of operation IDs to roll back.
   *
   * @param string[] $operationIds
   */
  public function setOperationIds($operationIds)
  {
    $this->operationIds = $operationIds;
  }
  /**
   * @return string[]
   */
  public function getOperationIds()
  {
    return $this->operationIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackFhirResourceFilteringFields::class, 'Google_Service_CloudHealthcare_RollbackFhirResourceFilteringFields');
