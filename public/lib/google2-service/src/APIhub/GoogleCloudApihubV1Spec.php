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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Spec extends \Google\Collection
{
  /**
   * Defaults to `RELAXED`.
   */
  public const PARSING_MODE_PARSING_MODE_UNSPECIFIED = 'PARSING_MODE_UNSPECIFIED';
  /**
   * Parsing of the Spec on create and update is relaxed, meaning that parsing
   * errors the spec contents will not fail the API call.
   */
  public const PARSING_MODE_RELAXED = 'RELAXED';
  /**
   * Parsing of the Spec on create and update is strict, meaning that parsing
   * errors in the spec contents will fail the API call.
   */
  public const PARSING_MODE_STRICT = 'STRICT';
  protected $collection_key = 'sourceMetadata';
  protected $additionalSpecContentsType = GoogleCloudApihubV1AdditionalSpecContent::class;
  protected $additionalSpecContentsDataType = 'array';
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  protected $contentsType = GoogleCloudApihubV1SpecContents::class;
  protected $contentsDataType = '';
  /**
   * Output only. The time at which the spec was created.
   *
   * @var string
   */
  public $createTime;
  protected $detailsType = GoogleCloudApihubV1SpecDetails::class;
  protected $detailsDataType = '';
  /**
   * Required. The display name of the spec. This can contain the file name of
   * the spec.
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  protected $lintResponseType = GoogleCloudApihubV1LintResponse::class;
  protected $lintResponseDataType = '';
  /**
   * Identifier. The name of the spec. Format: `projects/{project}/locations/{lo
   * cation}/apis/{api}/versions/{version}/specs/{spec}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Input only. Enum specifying the parsing mode for OpenAPI
   * Specification (OAS) parsing.
   *
   * @var string
   */
  public $parsingMode;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = 'array';
  /**
   * Optional. The URI of the spec source in case file is uploaded from an
   * external version control system.
   *
   * @var string
   */
  public $sourceUri;
  protected $specTypeType = GoogleCloudApihubV1AttributeValues::class;
  protected $specTypeDataType = '';
  /**
   * Output only. The time at which the spec was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The additional spec contents for the spec.
   *
   * @param GoogleCloudApihubV1AdditionalSpecContent[] $additionalSpecContents
   */
  public function setAdditionalSpecContents($additionalSpecContents)
  {
    $this->additionalSpecContents = $additionalSpecContents;
  }
  /**
   * @return GoogleCloudApihubV1AdditionalSpecContent[]
   */
  public function getAdditionalSpecContents()
  {
    return $this->additionalSpecContents;
  }
  /**
   * Optional. The list of user defined attributes associated with the spec. The
   * key is the attribute name. It will be of the format:
   * `projects/{project}/locations/{location}/attributes/{attribute}`. The value
   * is the attribute values associated with the resource.
   *
   * @param GoogleCloudApihubV1AttributeValues[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. Input only. The contents of the uploaded spec.
   *
   * @param GoogleCloudApihubV1SpecContents $contents
   */
  public function setContents(GoogleCloudApihubV1SpecContents $contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudApihubV1SpecContents
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. The time at which the spec was created.
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
   * Output only. Details parsed from the spec.
   *
   * @param GoogleCloudApihubV1SpecDetails $details
   */
  public function setDetails(GoogleCloudApihubV1SpecDetails $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleCloudApihubV1SpecDetails
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Required. The display name of the spec. This can contain the file name of
   * the spec.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. The documentation of the spec. For OpenAPI spec, this will be
   * populated from `externalDocs` in OpenAPI spec.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Optional. The lint response for the spec.
   *
   * @param GoogleCloudApihubV1LintResponse $lintResponse
   */
  public function setLintResponse(GoogleCloudApihubV1LintResponse $lintResponse)
  {
    $this->lintResponse = $lintResponse;
  }
  /**
   * @return GoogleCloudApihubV1LintResponse
   */
  public function getLintResponse()
  {
    return $this->lintResponse;
  }
  /**
   * Identifier. The name of the spec. Format: `projects/{project}/locations/{lo
   * cation}/apis/{api}/versions/{version}/specs/{spec}`
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
   * Optional. Input only. Enum specifying the parsing mode for OpenAPI
   * Specification (OAS) parsing.
   *
   * Accepted values: PARSING_MODE_UNSPECIFIED, RELAXED, STRICT
   *
   * @param self::PARSING_MODE_* $parsingMode
   */
  public function setParsingMode($parsingMode)
  {
    $this->parsingMode = $parsingMode;
  }
  /**
   * @return self::PARSING_MODE_*
   */
  public function getParsingMode()
  {
    return $this->parsingMode;
  }
  /**
   * Output only. The list of sources and metadata from the sources of the spec.
   *
   * @param GoogleCloudApihubV1SourceMetadata[] $sourceMetadata
   */
  public function setSourceMetadata($sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return GoogleCloudApihubV1SourceMetadata[]
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * Optional. The URI of the spec source in case file is uploaded from an
   * external version control system.
   *
   * @param string $sourceUri
   */
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return string
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
  /**
   * Required. The type of spec. The value should be one of the allowed values
   * defined for `projects/{project}/locations/{location}/attributes/system-
   * spec-type` attribute. The number of values for this attribute will be based
   * on the cardinality of the attribute. The same can be retrieved via
   * GetAttribute API. Note, this field is mandatory if content is provided.
   *
   * @param GoogleCloudApihubV1AttributeValues $specType
   */
  public function setSpecType(GoogleCloudApihubV1AttributeValues $specType)
  {
    $this->specType = $specType;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getSpecType()
  {
    return $this->specType;
  }
  /**
   * Output only. The time at which the spec was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Spec::class, 'Google_Service_APIhub_GoogleCloudApihubV1Spec');
