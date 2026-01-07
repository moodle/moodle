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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ImportEntityTypesRequest extends \Google\Model
{
  /**
   * Unspecified. If used, system uses REPORT_CONFLICT as default.
   */
  public const MERGE_OPTION_MERGE_OPTION_UNSPECIFIED = 'MERGE_OPTION_UNSPECIFIED';
  /**
   * Replace the original entity type in the agent with the new entity type when
   * display name conflicts exist.
   */
  public const MERGE_OPTION_REPLACE = 'REPLACE';
  /**
   * Merge the original entity type with the new entity type when display name
   * conflicts exist.
   */
  public const MERGE_OPTION_MERGE = 'MERGE';
  /**
   * Create new entity types with new display names to differentiate them from
   * the existing entity types when display name conflicts exist.
   */
  public const MERGE_OPTION_RENAME = 'RENAME';
  /**
   * Report conflict information if display names conflict is detected.
   * Otherwise, import entity types.
   */
  public const MERGE_OPTION_REPORT_CONFLICT = 'REPORT_CONFLICT';
  /**
   * Keep the original entity type and discard the conflicting new entity type
   * when display name conflicts exist.
   */
  public const MERGE_OPTION_KEEP = 'KEEP';
  protected $entityTypesContentType = GoogleCloudDialogflowCxV3InlineSource::class;
  protected $entityTypesContentDataType = '';
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import entity types from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $entityTypesUri;
  /**
   * Required. Merge option for importing entity types.
   *
   * @var string
   */
  public $mergeOption;
  /**
   * Optional. The target entity type to import into. Format:
   * `projects//locations//agents//entity_types/`. If set, there should be only
   * one entity type included in entity_types, of which the type should match
   * the type of the target entity type. All entities in the imported entity
   * type will be added to the target entity type.
   *
   * @var string
   */
  public $targetEntityType;

  /**
   * Uncompressed byte content of entity types.
   *
   * @param GoogleCloudDialogflowCxV3InlineSource $entityTypesContent
   */
  public function setEntityTypesContent(GoogleCloudDialogflowCxV3InlineSource $entityTypesContent)
  {
    $this->entityTypesContent = $entityTypesContent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3InlineSource
   */
  public function getEntityTypesContent()
  {
    return $this->entityTypesContent;
  }
  /**
   * The [Google Cloud Storage](https://cloud.google.com/storage/docs/) URI to
   * import entity types from. The format of this URI must be `gs:`. Dialogflow
   * performs a read operation for the Cloud Storage object on the caller's
   * behalf, so your request authentication must have read permissions for the
   * object. For more information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @param string $entityTypesUri
   */
  public function setEntityTypesUri($entityTypesUri)
  {
    $this->entityTypesUri = $entityTypesUri;
  }
  /**
   * @return string
   */
  public function getEntityTypesUri()
  {
    return $this->entityTypesUri;
  }
  /**
   * Required. Merge option for importing entity types.
   *
   * Accepted values: MERGE_OPTION_UNSPECIFIED, REPLACE, MERGE, RENAME,
   * REPORT_CONFLICT, KEEP
   *
   * @param self::MERGE_OPTION_* $mergeOption
   */
  public function setMergeOption($mergeOption)
  {
    $this->mergeOption = $mergeOption;
  }
  /**
   * @return self::MERGE_OPTION_*
   */
  public function getMergeOption()
  {
    return $this->mergeOption;
  }
  /**
   * Optional. The target entity type to import into. Format:
   * `projects//locations//agents//entity_types/`. If set, there should be only
   * one entity type included in entity_types, of which the type should match
   * the type of the target entity type. All entities in the imported entity
   * type will be added to the target entity type.
   *
   * @param string $targetEntityType
   */
  public function setTargetEntityType($targetEntityType)
  {
    $this->targetEntityType = $targetEntityType;
  }
  /**
   * @return string
   */
  public function getTargetEntityType()
  {
    return $this->targetEntityType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ImportEntityTypesRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ImportEntityTypesRequest');
