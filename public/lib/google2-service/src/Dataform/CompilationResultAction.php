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

class CompilationResultAction extends \Google\Model
{
  protected $assertionType = Assertion::class;
  protected $assertionDataType = '';
  protected $canonicalTargetType = Target::class;
  protected $canonicalTargetDataType = '';
  protected $dataPreparationType = DataPreparation::class;
  protected $dataPreparationDataType = '';
  protected $declarationType = Declaration::class;
  protected $declarationDataType = '';
  /**
   * The full path including filename in which this action is located, relative
   * to the workspace root.
   *
   * @var string
   */
  public $filePath;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  protected $notebookType = Notebook::class;
  protected $notebookDataType = '';
  protected $operationsType = Operations::class;
  protected $operationsDataType = '';
  protected $relationType = Relation::class;
  protected $relationDataType = '';
  protected $targetType = Target::class;
  protected $targetDataType = '';

  /**
   * The assertion executed by this action.
   *
   * @param Assertion $assertion
   */
  public function setAssertion(Assertion $assertion)
  {
    $this->assertion = $assertion;
  }
  /**
   * @return Assertion
   */
  public function getAssertion()
  {
    return $this->assertion;
  }
  /**
   * The action's identifier if the project had been compiled without any
   * overrides configured. Unique within the compilation result.
   *
   * @param Target $canonicalTarget
   */
  public function setCanonicalTarget(Target $canonicalTarget)
  {
    $this->canonicalTarget = $canonicalTarget;
  }
  /**
   * @return Target
   */
  public function getCanonicalTarget()
  {
    return $this->canonicalTarget;
  }
  /**
   * The data preparation executed by this action.
   *
   * @param DataPreparation $dataPreparation
   */
  public function setDataPreparation(DataPreparation $dataPreparation)
  {
    $this->dataPreparation = $dataPreparation;
  }
  /**
   * @return DataPreparation
   */
  public function getDataPreparation()
  {
    return $this->dataPreparation;
  }
  /**
   * The declaration declared by this action.
   *
   * @param Declaration $declaration
   */
  public function setDeclaration(Declaration $declaration)
  {
    $this->declaration = $declaration;
  }
  /**
   * @return Declaration
   */
  public function getDeclaration()
  {
    return $this->declaration;
  }
  /**
   * The full path including filename in which this action is located, relative
   * to the workspace root.
   *
   * @param string $filePath
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;
  }
  /**
   * @return string
   */
  public function getFilePath()
  {
    return $this->filePath;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * The notebook executed by this action.
   *
   * @param Notebook $notebook
   */
  public function setNotebook(Notebook $notebook)
  {
    $this->notebook = $notebook;
  }
  /**
   * @return Notebook
   */
  public function getNotebook()
  {
    return $this->notebook;
  }
  /**
   * The database operations executed by this action.
   *
   * @param Operations $operations
   */
  public function setOperations(Operations $operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return Operations
   */
  public function getOperations()
  {
    return $this->operations;
  }
  /**
   * The database relation created/updated by this action.
   *
   * @param Relation $relation
   */
  public function setRelation(Relation $relation)
  {
    $this->relation = $relation;
  }
  /**
   * @return Relation
   */
  public function getRelation()
  {
    return $this->relation;
  }
  /**
   * This action's identifier. Unique within the compilation result.
   *
   * @param Target $target
   */
  public function setTarget(Target $target)
  {
    $this->target = $target;
  }
  /**
   * @return Target
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompilationResultAction::class, 'Google_Service_Dataform_CompilationResultAction');
