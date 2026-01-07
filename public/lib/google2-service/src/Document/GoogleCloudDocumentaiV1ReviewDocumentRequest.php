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

class GoogleCloudDocumentaiV1ReviewDocumentRequest extends \Google\Model
{
  /**
   * The default priority level.
   */
  public const PRIORITY_DEFAULT = 'DEFAULT';
  /**
   * The urgent priority level. The labeling manager should allocate labeler
   * resource to the urgent task queue to respect this priority level.
   */
  public const PRIORITY_URGENT = 'URGENT';
  protected $documentSchemaType = GoogleCloudDocumentaiV1DocumentSchema::class;
  protected $documentSchemaDataType = '';
  /**
   * Whether the validation should be performed on the ad-hoc review request.
   *
   * @var bool
   */
  public $enableSchemaValidation;
  protected $inlineDocumentType = GoogleCloudDocumentaiV1Document::class;
  protected $inlineDocumentDataType = '';
  /**
   * The priority of the human review task.
   *
   * @var string
   */
  public $priority;

  /**
   * The document schema of the human review task.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchema $documentSchema
   */
  public function setDocumentSchema(GoogleCloudDocumentaiV1DocumentSchema $documentSchema)
  {
    $this->documentSchema = $documentSchema;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchema
   */
  public function getDocumentSchema()
  {
    return $this->documentSchema;
  }
  /**
   * Whether the validation should be performed on the ad-hoc review request.
   *
   * @param bool $enableSchemaValidation
   */
  public function setEnableSchemaValidation($enableSchemaValidation)
  {
    $this->enableSchemaValidation = $enableSchemaValidation;
  }
  /**
   * @return bool
   */
  public function getEnableSchemaValidation()
  {
    return $this->enableSchemaValidation;
  }
  /**
   * An inline document proto.
   *
   * @param GoogleCloudDocumentaiV1Document $inlineDocument
   */
  public function setInlineDocument(GoogleCloudDocumentaiV1Document $inlineDocument)
  {
    $this->inlineDocument = $inlineDocument;
  }
  /**
   * @return GoogleCloudDocumentaiV1Document
   */
  public function getInlineDocument()
  {
    return $this->inlineDocument;
  }
  /**
   * The priority of the human review task.
   *
   * Accepted values: DEFAULT, URGENT
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ReviewDocumentRequest::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ReviewDocumentRequest');
