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

class GoogleFirestoreAdminV1IndexField extends \Google\Model
{
  /**
   * The index does not support additional array queries.
   */
  public const ARRAY_CONFIG_ARRAY_CONFIG_UNSPECIFIED = 'ARRAY_CONFIG_UNSPECIFIED';
  /**
   * The index supports array containment queries.
   */
  public const ARRAY_CONFIG_CONTAINS = 'CONTAINS';
  /**
   * The ordering is unspecified. Not a valid option.
   */
  public const ORDER_ORDER_UNSPECIFIED = 'ORDER_UNSPECIFIED';
  /**
   * The field is ordered by ascending field value.
   */
  public const ORDER_ASCENDING = 'ASCENDING';
  /**
   * The field is ordered by descending field value.
   */
  public const ORDER_DESCENDING = 'DESCENDING';
  /**
   * Indicates that this field supports operations on `array_value`s.
   *
   * @var string
   */
  public $arrayConfig;
  /**
   * Can be __name__. For single field indexes, this must match the name of the
   * field or may be omitted.
   *
   * @var string
   */
  public $fieldPath;
  /**
   * Indicates that this field supports ordering by the specified order or
   * comparing using =, !=, <, <=, >, >=.
   *
   * @var string
   */
  public $order;
  protected $vectorConfigType = GoogleFirestoreAdminV1VectorConfig::class;
  protected $vectorConfigDataType = '';

  /**
   * Indicates that this field supports operations on `array_value`s.
   *
   * Accepted values: ARRAY_CONFIG_UNSPECIFIED, CONTAINS
   *
   * @param self::ARRAY_CONFIG_* $arrayConfig
   */
  public function setArrayConfig($arrayConfig)
  {
    $this->arrayConfig = $arrayConfig;
  }
  /**
   * @return self::ARRAY_CONFIG_*
   */
  public function getArrayConfig()
  {
    return $this->arrayConfig;
  }
  /**
   * Can be __name__. For single field indexes, this must match the name of the
   * field or may be omitted.
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * Indicates that this field supports ordering by the specified order or
   * comparing using =, !=, <, <=, >, >=.
   *
   * Accepted values: ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::ORDER_* $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return self::ORDER_*
   */
  public function getOrder()
  {
    return $this->order;
  }
  /**
   * Indicates that this field supports nearest neighbor and distance operations
   * on vector.
   *
   * @param GoogleFirestoreAdminV1VectorConfig $vectorConfig
   */
  public function setVectorConfig(GoogleFirestoreAdminV1VectorConfig $vectorConfig)
  {
    $this->vectorConfig = $vectorConfig;
  }
  /**
   * @return GoogleFirestoreAdminV1VectorConfig
   */
  public function getVectorConfig()
  {
    return $this->vectorConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1IndexField::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1IndexField');
