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

class GoogleCloudDialogflowCxV3ExportEntityTypesRequest extends \Google\Collection
{
  /**
   * Unspecified format. Treated as `BLOB`.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * EntityTypes will be exported as raw bytes.
   */
  public const DATA_FORMAT_BLOB = 'BLOB';
  /**
   * EntityTypes will be exported in JSON Package format.
   */
  public const DATA_FORMAT_JSON_PACKAGE = 'JSON_PACKAGE';
  protected $collection_key = 'entityTypes';
  /**
   * Optional. The data format of the exported entity types. If not specified,
   * `BLOB` is assumed.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * Required. The name of the entity types to export. Format:
   * `projects//locations//agents//entityTypes/`.
   *
   * @var string[]
   */
  public $entityTypes;
  /**
   * Optional. The option to return the serialized entity types inline.
   *
   * @var bool
   */
  public $entityTypesContentInline;
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the entity
   * types to. The format of this URI must be `gs:`. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
   * control](https://cloud.google.com/dialogflow/cx/docs/concept/access-
   * control#storage).
   *
   * @var string
   */
  public $entityTypesUri;
  /**
   * Optional. The language to retrieve the entity type for. The following
   * fields are language dependent: * `EntityType.entities.value` *
   * `EntityType.entities.synonyms` * `EntityType.excluded_phrases.value` If not
   * specified, all language dependent fields will be retrieved. [Many
   * languages](https://cloud.google.com/dialogflow/docs/reference/language) are
   * supported. Note: languages must be enabled in the agent before they can be
   * used.
   *
   * @var string
   */
  public $languageCode;

  /**
   * Optional. The data format of the exported entity types. If not specified,
   * `BLOB` is assumed.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, BLOB, JSON_PACKAGE
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * Required. The name of the entity types to export. Format:
   * `projects//locations//agents//entityTypes/`.
   *
   * @param string[] $entityTypes
   */
  public function setEntityTypes($entityTypes)
  {
    $this->entityTypes = $entityTypes;
  }
  /**
   * @return string[]
   */
  public function getEntityTypes()
  {
    return $this->entityTypes;
  }
  /**
   * Optional. The option to return the serialized entity types inline.
   *
   * @param bool $entityTypesContentInline
   */
  public function setEntityTypesContentInline($entityTypesContentInline)
  {
    $this->entityTypesContentInline = $entityTypesContentInline;
  }
  /**
   * @return bool
   */
  public function getEntityTypesContentInline()
  {
    return $this->entityTypesContentInline;
  }
  /**
   * Optional. The [Google Cloud
   * Storage](https://cloud.google.com/storage/docs/) URI to export the entity
   * types to. The format of this URI must be `gs:`. Dialogflow performs a write
   * operation for the Cloud Storage object on the caller's behalf, so your
   * request authentication must have write permissions for the object. For more
   * information, see [Dialogflow access
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
   * Optional. The language to retrieve the entity type for. The following
   * fields are language dependent: * `EntityType.entities.value` *
   * `EntityType.entities.synonyms` * `EntityType.excluded_phrases.value` If not
   * specified, all language dependent fields will be retrieved. [Many
   * languages](https://cloud.google.com/dialogflow/docs/reference/language) are
   * supported. Note: languages must be enabled in the agent before they can be
   * used.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExportEntityTypesRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExportEntityTypesRequest');
