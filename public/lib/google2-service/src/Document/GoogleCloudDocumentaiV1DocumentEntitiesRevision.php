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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentEntitiesRevision extends \Google\Collection
{
  protected $collection_key = 'entities';
  protected $entitiesType = GoogleCloudDocumentaiV1DocumentEntity::class;
  protected $entitiesDataType = 'array';
  protected $entityValidationOutputType = GoogleCloudDocumentaiV1DocumentEntityValidationOutput::class;
  protected $entityValidationOutputDataType = '';
  /**
   * The revision id.
   *
   * @var string
   */
  public $revisionId;

  /**
   * The entities in this revision.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * The entity validation output for this revision.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntityValidationOutput $entityValidationOutput
   */
  public function setEntityValidationOutput(GoogleCloudDocumentaiV1DocumentEntityValidationOutput $entityValidationOutput)
  {
    $this->entityValidationOutput = $entityValidationOutput;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntityValidationOutput
   */
  public function getEntityValidationOutput()
  {
    return $this->entityValidationOutput;
  }
  /**
   * The revision id.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentEntitiesRevision::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentEntitiesRevision');
