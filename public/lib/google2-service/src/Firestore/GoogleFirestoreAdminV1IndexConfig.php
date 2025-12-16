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

class GoogleFirestoreAdminV1IndexConfig extends \Google\Collection
{
  protected $collection_key = 'indexes';
  /**
   * Output only. Specifies the resource name of the `Field` from which this
   * field's index configuration is set (when `uses_ancestor_config` is true),
   * or from which it *would* be set if this field had no index configuration
   * (when `uses_ancestor_config` is false).
   *
   * @var string
   */
  public $ancestorField;
  protected $indexesType = GoogleFirestoreAdminV1Index::class;
  protected $indexesDataType = 'array';
  /**
   * Output only When true, the `Field`'s index configuration is in the process
   * of being reverted. Once complete, the index config will transition to the
   * same state as the field specified by `ancestor_field`, at which point
   * `uses_ancestor_config` will be `true` and `reverting` will be `false`.
   *
   * @var bool
   */
  public $reverting;
  /**
   * Output only. When true, the `Field`'s index configuration is set from the
   * configuration specified by the `ancestor_field`. When false, the `Field`'s
   * index configuration is defined explicitly.
   *
   * @var bool
   */
  public $usesAncestorConfig;

  /**
   * Output only. Specifies the resource name of the `Field` from which this
   * field's index configuration is set (when `uses_ancestor_config` is true),
   * or from which it *would* be set if this field had no index configuration
   * (when `uses_ancestor_config` is false).
   *
   * @param string $ancestorField
   */
  public function setAncestorField($ancestorField)
  {
    $this->ancestorField = $ancestorField;
  }
  /**
   * @return string
   */
  public function getAncestorField()
  {
    return $this->ancestorField;
  }
  /**
   * The indexes supported for this field.
   *
   * @param GoogleFirestoreAdminV1Index[] $indexes
   */
  public function setIndexes($indexes)
  {
    $this->indexes = $indexes;
  }
  /**
   * @return GoogleFirestoreAdminV1Index[]
   */
  public function getIndexes()
  {
    return $this->indexes;
  }
  /**
   * Output only When true, the `Field`'s index configuration is in the process
   * of being reverted. Once complete, the index config will transition to the
   * same state as the field specified by `ancestor_field`, at which point
   * `uses_ancestor_config` will be `true` and `reverting` will be `false`.
   *
   * @param bool $reverting
   */
  public function setReverting($reverting)
  {
    $this->reverting = $reverting;
  }
  /**
   * @return bool
   */
  public function getReverting()
  {
    return $this->reverting;
  }
  /**
   * Output only. When true, the `Field`'s index configuration is set from the
   * configuration specified by the `ancestor_field`. When false, the `Field`'s
   * index configuration is defined explicitly.
   *
   * @param bool $usesAncestorConfig
   */
  public function setUsesAncestorConfig($usesAncestorConfig)
  {
    $this->usesAncestorConfig = $usesAncestorConfig;
  }
  /**
   * @return bool
   */
  public function getUsesAncestorConfig()
  {
    return $this->usesAncestorConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1IndexConfig::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1IndexConfig');
