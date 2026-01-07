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

namespace Google\Service\Dataflow;

class TransformSummary extends \Google\Collection
{
  /**
   * Unrecognized transform type.
   */
  public const KIND_UNKNOWN_KIND = 'UNKNOWN_KIND';
  /**
   * ParDo transform.
   */
  public const KIND_PAR_DO_KIND = 'PAR_DO_KIND';
  /**
   * Group By Key transform.
   */
  public const KIND_GROUP_BY_KEY_KIND = 'GROUP_BY_KEY_KIND';
  /**
   * Flatten transform.
   */
  public const KIND_FLATTEN_KIND = 'FLATTEN_KIND';
  /**
   * Read transform.
   */
  public const KIND_READ_KIND = 'READ_KIND';
  /**
   * Write transform.
   */
  public const KIND_WRITE_KIND = 'WRITE_KIND';
  /**
   * Constructs from a constant value, such as with Create.of.
   */
  public const KIND_CONSTANT_KIND = 'CONSTANT_KIND';
  /**
   * Creates a Singleton view of a collection.
   */
  public const KIND_SINGLETON_KIND = 'SINGLETON_KIND';
  /**
   * Opening or closing a shuffle session, often as part of a GroupByKey.
   */
  public const KIND_SHUFFLE_KIND = 'SHUFFLE_KIND';
  protected $collection_key = 'outputCollectionName';
  protected $displayDataType = DisplayData::class;
  protected $displayDataDataType = 'array';
  /**
   * SDK generated id of this transform instance.
   *
   * @var string
   */
  public $id;
  /**
   * User names for all collection inputs to this transform.
   *
   * @var string[]
   */
  public $inputCollectionName;
  /**
   * Type of transform.
   *
   * @var string
   */
  public $kind;
  /**
   * User provided name for this transform instance.
   *
   * @var string
   */
  public $name;
  /**
   * User names for all collection outputs to this transform.
   *
   * @var string[]
   */
  public $outputCollectionName;

  /**
   * Transform-specific display data.
   *
   * @param DisplayData[] $displayData
   */
  public function setDisplayData($displayData)
  {
    $this->displayData = $displayData;
  }
  /**
   * @return DisplayData[]
   */
  public function getDisplayData()
  {
    return $this->displayData;
  }
  /**
   * SDK generated id of this transform instance.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * User names for all collection inputs to this transform.
   *
   * @param string[] $inputCollectionName
   */
  public function setInputCollectionName($inputCollectionName)
  {
    $this->inputCollectionName = $inputCollectionName;
  }
  /**
   * @return string[]
   */
  public function getInputCollectionName()
  {
    return $this->inputCollectionName;
  }
  /**
   * Type of transform.
   *
   * Accepted values: UNKNOWN_KIND, PAR_DO_KIND, GROUP_BY_KEY_KIND,
   * FLATTEN_KIND, READ_KIND, WRITE_KIND, CONSTANT_KIND, SINGLETON_KIND,
   * SHUFFLE_KIND
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * User provided name for this transform instance.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * User names for all collection outputs to this transform.
   *
   * @param string[] $outputCollectionName
   */
  public function setOutputCollectionName($outputCollectionName)
  {
    $this->outputCollectionName = $outputCollectionName;
  }
  /**
   * @return string[]
   */
  public function getOutputCollectionName()
  {
    return $this->outputCollectionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransformSummary::class, 'Google_Service_Dataflow_TransformSummary');
