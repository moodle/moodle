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

namespace Google\Service\Contentwarehouse;

class GoogleCloudDocumentaiV1DocumentEntity extends \Google\Collection
{
  protected $collection_key = 'properties';
  /**
   * Optional. Confidence of detected Schema entity. Range `[0, 1]`.
   *
   * @var float
   */
  public $confidence;
  /**
   * Optional. Canonical id. This will be a unique value in the entity list for
   * this document.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Deprecated. Use `id` field instead.
   *
   * @var string
   */
  public $mentionId;
  /**
   * Optional. Text value of the entity e.g. `1600 Amphitheatre Pkwy`.
   *
   * @var string
   */
  public $mentionText;
  protected $normalizedValueType = GoogleCloudDocumentaiV1DocumentEntityNormalizedValue::class;
  protected $normalizedValueDataType = '';
  protected $pageAnchorType = GoogleCloudDocumentaiV1DocumentPageAnchor::class;
  protected $pageAnchorDataType = '';
  protected $propertiesType = GoogleCloudDocumentaiV1DocumentEntity::class;
  protected $propertiesDataType = 'array';
  protected $provenanceType = GoogleCloudDocumentaiV1DocumentProvenance::class;
  protected $provenanceDataType = '';
  /**
   * Optional. Whether the entity will be redacted for de-identification
   * purposes.
   *
   * @var bool
   */
  public $redacted;
  protected $textAnchorType = GoogleCloudDocumentaiV1DocumentTextAnchor::class;
  protected $textAnchorDataType = '';
  /**
   * Required. Entity type from a schema e.g. `Address`.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Confidence of detected Schema entity. Range `[0, 1]`.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Optional. Canonical id. This will be a unique value in the entity list for
   * this document.
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
   * Optional. Deprecated. Use `id` field instead.
   *
   * @param string $mentionId
   */
  public function setMentionId($mentionId)
  {
    $this->mentionId = $mentionId;
  }
  /**
   * @return string
   */
  public function getMentionId()
  {
    return $this->mentionId;
  }
  /**
   * Optional. Text value of the entity e.g. `1600 Amphitheatre Pkwy`.
   *
   * @param string $mentionText
   */
  public function setMentionText($mentionText)
  {
    $this->mentionText = $mentionText;
  }
  /**
   * @return string
   */
  public function getMentionText()
  {
    return $this->mentionText;
  }
  /**
   * Optional. Normalized entity value. Absent if the extracted value could not
   * be converted or the type (e.g. address) is not supported for certain
   * parsers. This field is also only populated for certain supported document
   * types.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntityNormalizedValue $normalizedValue
   */
  public function setNormalizedValue(GoogleCloudDocumentaiV1DocumentEntityNormalizedValue $normalizedValue)
  {
    $this->normalizedValue = $normalizedValue;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntityNormalizedValue
   */
  public function getNormalizedValue()
  {
    return $this->normalizedValue;
  }
  /**
   * Optional. Represents the provenance of this entity wrt. the location on the
   * page where it was found.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageAnchor $pageAnchor
   */
  public function setPageAnchor(GoogleCloudDocumentaiV1DocumentPageAnchor $pageAnchor)
  {
    $this->pageAnchor = $pageAnchor;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageAnchor
   */
  public function getPageAnchor()
  {
    return $this->pageAnchor;
  }
  /**
   * Optional. Entities can be nested to form a hierarchical data structure
   * representing the content in the document.
   *
   * @param GoogleCloudDocumentaiV1DocumentEntity[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentEntity[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. The history of this annotation.
   *
   * @param GoogleCloudDocumentaiV1DocumentProvenance $provenance
   */
  public function setProvenance(GoogleCloudDocumentaiV1DocumentProvenance $provenance)
  {
    $this->provenance = $provenance;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentProvenance
   */
  public function getProvenance()
  {
    return $this->provenance;
  }
  /**
   * Optional. Whether the entity will be redacted for de-identification
   * purposes.
   *
   * @param bool $redacted
   */
  public function setRedacted($redacted)
  {
    $this->redacted = $redacted;
  }
  /**
   * @return bool
   */
  public function getRedacted()
  {
    return $this->redacted;
  }
  /**
   * Optional. Provenance of the entity. Text anchor indexing into the
   * Document.text.
   *
   * @param GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor
   */
  public function setTextAnchor(GoogleCloudDocumentaiV1DocumentTextAnchor $textAnchor)
  {
    $this->textAnchor = $textAnchor;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentTextAnchor
   */
  public function getTextAnchor()
  {
    return $this->textAnchor;
  }
  /**
   * Required. Entity type from a schema e.g. `Address`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentEntity::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentEntity');
