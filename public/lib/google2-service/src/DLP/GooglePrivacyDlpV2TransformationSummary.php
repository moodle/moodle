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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TransformationSummary extends \Google\Collection
{
  protected $collection_key = 'results';
  protected $fieldType = GooglePrivacyDlpV2FieldId::class;
  protected $fieldDataType = '';
  protected $fieldTransformationsType = GooglePrivacyDlpV2FieldTransformation::class;
  protected $fieldTransformationsDataType = 'array';
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';
  protected $recordSuppressType = GooglePrivacyDlpV2RecordSuppression::class;
  protected $recordSuppressDataType = '';
  protected $resultsType = GooglePrivacyDlpV2SummaryResult::class;
  protected $resultsDataType = 'array';
  protected $transformationType = GooglePrivacyDlpV2PrimitiveTransformation::class;
  protected $transformationDataType = '';
  /**
   * Total size in bytes that were transformed in some way.
   *
   * @var string
   */
  public $transformedBytes;

  /**
   * Set if the transformation was limited to a specific FieldId.
   *
   * @param GooglePrivacyDlpV2FieldId $field
   */
  public function setField(GooglePrivacyDlpV2FieldId $field)
  {
    $this->field = $field;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * The field transformation that was applied. If multiple field
   * transformations are requested for a single field, this list will contain
   * all of them; otherwise, only one is supplied.
   *
   * @param GooglePrivacyDlpV2FieldTransformation[] $fieldTransformations
   */
  public function setFieldTransformations($fieldTransformations)
  {
    $this->fieldTransformations = $fieldTransformations;
  }
  /**
   * @return GooglePrivacyDlpV2FieldTransformation[]
   */
  public function getFieldTransformations()
  {
    return $this->fieldTransformations;
  }
  /**
   * Set if the transformation was limited to a specific InfoType.
   *
   * @param GooglePrivacyDlpV2InfoType $infoType
   */
  public function setInfoType(GooglePrivacyDlpV2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * The specific suppression option these stats apply to.
   *
   * @param GooglePrivacyDlpV2RecordSuppression $recordSuppress
   */
  public function setRecordSuppress(GooglePrivacyDlpV2RecordSuppression $recordSuppress)
  {
    $this->recordSuppress = $recordSuppress;
  }
  /**
   * @return GooglePrivacyDlpV2RecordSuppression
   */
  public function getRecordSuppress()
  {
    return $this->recordSuppress;
  }
  /**
   * Collection of all transformations that took place or had an error.
   *
   * @param GooglePrivacyDlpV2SummaryResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GooglePrivacyDlpV2SummaryResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * The specific transformation these stats apply to.
   *
   * @param GooglePrivacyDlpV2PrimitiveTransformation $transformation
   */
  public function setTransformation(GooglePrivacyDlpV2PrimitiveTransformation $transformation)
  {
    $this->transformation = $transformation;
  }
  /**
   * @return GooglePrivacyDlpV2PrimitiveTransformation
   */
  public function getTransformation()
  {
    return $this->transformation;
  }
  /**
   * Total size in bytes that were transformed in some way.
   *
   * @param string $transformedBytes
   */
  public function setTransformedBytes($transformedBytes)
  {
    $this->transformedBytes = $transformedBytes;
  }
  /**
   * @return string
   */
  public function getTransformedBytes()
  {
    return $this->transformedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationSummary::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationSummary');
