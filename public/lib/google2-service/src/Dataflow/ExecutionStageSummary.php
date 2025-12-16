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

class ExecutionStageSummary extends \Google\Collection
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
  protected $collection_key = 'prerequisiteStage';
  protected $componentSourceType = ComponentSource::class;
  protected $componentSourceDataType = 'array';
  protected $componentTransformType = ComponentTransform::class;
  protected $componentTransformDataType = 'array';
  /**
   * Dataflow service generated id for this stage.
   *
   * @var string
   */
  public $id;
  protected $inputSourceType = StageSource::class;
  protected $inputSourceDataType = 'array';
  /**
   * Type of transform this stage is executing.
   *
   * @var string
   */
  public $kind;
  /**
   * Dataflow service generated name for this stage.
   *
   * @var string
   */
  public $name;
  protected $outputSourceType = StageSource::class;
  protected $outputSourceDataType = 'array';
  /**
   * Other stages that must complete before this stage can run.
   *
   * @var string[]
   */
  public $prerequisiteStage;

  /**
   * Collections produced and consumed by component transforms of this stage.
   *
   * @param ComponentSource[] $componentSource
   */
  public function setComponentSource($componentSource)
  {
    $this->componentSource = $componentSource;
  }
  /**
   * @return ComponentSource[]
   */
  public function getComponentSource()
  {
    return $this->componentSource;
  }
  /**
   * Transforms that comprise this execution stage.
   *
   * @param ComponentTransform[] $componentTransform
   */
  public function setComponentTransform($componentTransform)
  {
    $this->componentTransform = $componentTransform;
  }
  /**
   * @return ComponentTransform[]
   */
  public function getComponentTransform()
  {
    return $this->componentTransform;
  }
  /**
   * Dataflow service generated id for this stage.
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
   * Input sources for this stage.
   *
   * @param StageSource[] $inputSource
   */
  public function setInputSource($inputSource)
  {
    $this->inputSource = $inputSource;
  }
  /**
   * @return StageSource[]
   */
  public function getInputSource()
  {
    return $this->inputSource;
  }
  /**
   * Type of transform this stage is executing.
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
   * Dataflow service generated name for this stage.
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
   * Output sources for this stage.
   *
   * @param StageSource[] $outputSource
   */
  public function setOutputSource($outputSource)
  {
    $this->outputSource = $outputSource;
  }
  /**
   * @return StageSource[]
   */
  public function getOutputSource()
  {
    return $this->outputSource;
  }
  /**
   * Other stages that must complete before this stage can run.
   *
   * @param string[] $prerequisiteStage
   */
  public function setPrerequisiteStage($prerequisiteStage)
  {
    $this->prerequisiteStage = $prerequisiteStage;
  }
  /**
   * @return string[]
   */
  public function getPrerequisiteStage()
  {
    return $this->prerequisiteStage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionStageSummary::class, 'Google_Service_Dataflow_ExecutionStageSummary');
