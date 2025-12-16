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

class GoogleFirestoreAdminV1VectorConfig extends \Google\Model
{
  /**
   * Required. The vector dimension this configuration applies to. The resulting
   * index will only include vectors of this dimension, and can be used for
   * vector search with the same dimension.
   *
   * @var int
   */
  public $dimension;
  protected $flatType = GoogleFirestoreAdminV1FlatIndex::class;
  protected $flatDataType = '';

  /**
   * Required. The vector dimension this configuration applies to. The resulting
   * index will only include vectors of this dimension, and can be used for
   * vector search with the same dimension.
   *
   * @param int $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return int
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Indicates the vector index is a flat index.
   *
   * @param GoogleFirestoreAdminV1FlatIndex $flat
   */
  public function setFlat(GoogleFirestoreAdminV1FlatIndex $flat)
  {
    $this->flat = $flat;
  }
  /**
   * @return GoogleFirestoreAdminV1FlatIndex
   */
  public function getFlat()
  {
    return $this->flat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1VectorConfig::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1VectorConfig');
