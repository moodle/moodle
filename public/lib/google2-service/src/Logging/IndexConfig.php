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

namespace Google\Service\Logging;

class IndexConfig extends \Google\Model
{
  /**
   * The index's type is unspecified.
   */
  public const TYPE_INDEX_TYPE_UNSPECIFIED = 'INDEX_TYPE_UNSPECIFIED';
  /**
   * The index is a string-type index.
   */
  public const TYPE_INDEX_TYPE_STRING = 'INDEX_TYPE_STRING';
  /**
   * The index is a integer-type index.
   */
  public const TYPE_INDEX_TYPE_INTEGER = 'INDEX_TYPE_INTEGER';
  /**
   * Output only. The timestamp when the index was last modified.This is used to
   * return the timestamp, and will be ignored if supplied during update.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The LogEntry field path to index.Note that some paths are
   * automatically indexed, and other paths are not eligible for indexing. See
   * indexing documentation(
   * https://cloud.google.com/logging/docs/analyze/custom-index) for details.For
   * example: jsonPayload.request.status
   *
   * @var string
   */
  public $fieldPath;
  /**
   * Required. The type of data in this index.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The timestamp when the index was last modified.This is used to
   * return the timestamp, and will be ignored if supplied during update.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The LogEntry field path to index.Note that some paths are
   * automatically indexed, and other paths are not eligible for indexing. See
   * indexing documentation(
   * https://cloud.google.com/logging/docs/analyze/custom-index) for details.For
   * example: jsonPayload.request.status
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
   * Required. The type of data in this index.
   *
   * Accepted values: INDEX_TYPE_UNSPECIFIED, INDEX_TYPE_STRING,
   * INDEX_TYPE_INTEGER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexConfig::class, 'Google_Service_Logging_IndexConfig');
