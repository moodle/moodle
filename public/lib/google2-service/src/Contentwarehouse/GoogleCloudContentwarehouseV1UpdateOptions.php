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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1UpdateOptions extends \Google\Model
{
  /**
   * Defaults to full replace behavior, ie. FULL_REPLACE.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_UNSPECIFIED = 'UPDATE_TYPE_UNSPECIFIED';
  /**
   * Fully replace all the fields (including previously linked raw document).
   * Any field masks will be ignored.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_REPLACE = 'UPDATE_TYPE_REPLACE';
  /**
   * Merge the fields into the existing entities.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_MERGE = 'UPDATE_TYPE_MERGE';
  /**
   * Inserts the properties by names.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_INSERT_PROPERTIES_BY_NAMES = 'UPDATE_TYPE_INSERT_PROPERTIES_BY_NAMES';
  /**
   * Replace the properties by names.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_REPLACE_PROPERTIES_BY_NAMES = 'UPDATE_TYPE_REPLACE_PROPERTIES_BY_NAMES';
  /**
   * Delete the properties by names.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_DELETE_PROPERTIES_BY_NAMES = 'UPDATE_TYPE_DELETE_PROPERTIES_BY_NAMES';
  /**
   * For each of the property, replaces the property if the it exists, otherwise
   * inserts a new property. And for the rest of the fields, merge them based on
   * update mask and merge fields options.
   */
  public const UPDATE_TYPE_UPDATE_TYPE_MERGE_AND_REPLACE_OR_INSERT_PROPERTIES_BY_NAMES = 'UPDATE_TYPE_MERGE_AND_REPLACE_OR_INSERT_PROPERTIES_BY_NAMES';
  protected $mergeFieldsOptionsType = GoogleCloudContentwarehouseV1MergeFieldsOptions::class;
  protected $mergeFieldsOptionsDataType = '';
  /**
   * Field mask for merging Document fields. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   *
   * @var string
   */
  public $updateMask;
  /**
   * Type for update.
   *
   * @var string
   */
  public $updateType;

  /**
   * Options for merging.
   *
   * @param GoogleCloudContentwarehouseV1MergeFieldsOptions $mergeFieldsOptions
   */
  public function setMergeFieldsOptions(GoogleCloudContentwarehouseV1MergeFieldsOptions $mergeFieldsOptions)
  {
    $this->mergeFieldsOptions = $mergeFieldsOptions;
  }
  /**
   * @return GoogleCloudContentwarehouseV1MergeFieldsOptions
   */
  public function getMergeFieldsOptions()
  {
    return $this->mergeFieldsOptions;
  }
  /**
   * Field mask for merging Document fields. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * Type for update.
   *
   * Accepted values: UPDATE_TYPE_UNSPECIFIED, UPDATE_TYPE_REPLACE,
   * UPDATE_TYPE_MERGE, UPDATE_TYPE_INSERT_PROPERTIES_BY_NAMES,
   * UPDATE_TYPE_REPLACE_PROPERTIES_BY_NAMES,
   * UPDATE_TYPE_DELETE_PROPERTIES_BY_NAMES,
   * UPDATE_TYPE_MERGE_AND_REPLACE_OR_INSERT_PROPERTIES_BY_NAMES
   *
   * @param self::UPDATE_TYPE_* $updateType
   */
  public function setUpdateType($updateType)
  {
    $this->updateType = $updateType;
  }
  /**
   * @return self::UPDATE_TYPE_*
   */
  public function getUpdateType()
  {
    return $this->updateType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1UpdateOptions::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1UpdateOptions');
