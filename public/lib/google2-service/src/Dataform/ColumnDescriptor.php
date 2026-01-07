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

namespace Google\Service\Dataform;

class ColumnDescriptor extends \Google\Collection
{
  protected $collection_key = 'path';
  /**
   * A list of BigQuery policy tags that will be applied to the column.
   *
   * @var string[]
   */
  public $bigqueryPolicyTags;
  /**
   * A textual description of the column.
   *
   * @var string
   */
  public $description;
  /**
   * The identifier for the column. Each entry in `path` represents one level of
   * nesting.
   *
   * @var string[]
   */
  public $path;

  /**
   * A list of BigQuery policy tags that will be applied to the column.
   *
   * @param string[] $bigqueryPolicyTags
   */
  public function setBigqueryPolicyTags($bigqueryPolicyTags)
  {
    $this->bigqueryPolicyTags = $bigqueryPolicyTags;
  }
  /**
   * @return string[]
   */
  public function getBigqueryPolicyTags()
  {
    return $this->bigqueryPolicyTags;
  }
  /**
   * A textual description of the column.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The identifier for the column. Each entry in `path` represents one level of
   * nesting.
   *
   * @param string[] $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string[]
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColumnDescriptor::class, 'Google_Service_Dataform_ColumnDescriptor');
