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

class GooglePrivacyDlpV2TaggedField extends \Google\Model
{
  /**
   * A column can be tagged with a custom tag. In this case, the user must
   * indicate an auxiliary table that contains statistical information on the
   * possible values of this column.
   *
   * @var string
   */
  public $customTag;
  protected $fieldType = GooglePrivacyDlpV2FieldId::class;
  protected $fieldDataType = '';
  protected $inferredType = GoogleProtobufEmpty::class;
  protected $inferredDataType = '';
  protected $infoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $infoTypeDataType = '';

  /**
   * A column can be tagged with a custom tag. In this case, the user must
   * indicate an auxiliary table that contains statistical information on the
   * possible values of this column.
   *
   * @param string $customTag
   */
  public function setCustomTag($customTag)
  {
    $this->customTag = $customTag;
  }
  /**
   * @return string
   */
  public function getCustomTag()
  {
    return $this->customTag;
  }
  /**
   * Required. Identifies the column.
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
   * If no semantic tag is indicated, we infer the statistical model from the
   * distribution of values in the input data
   *
   * @param GoogleProtobufEmpty $inferred
   */
  public function setInferred(GoogleProtobufEmpty $inferred)
  {
    $this->inferred = $inferred;
  }
  /**
   * @return GoogleProtobufEmpty
   */
  public function getInferred()
  {
    return $this->inferred;
  }
  /**
   * A column can be tagged with a InfoType to use the relevant public dataset
   * as a statistical model of population, if available. We currently support US
   * ZIP codes, region codes, ages and genders. To programmatically obtain the
   * list of supported InfoTypes, use ListInfoTypes with the
   * supported_by=RISK_ANALYSIS filter.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TaggedField::class, 'Google_Service_DLP_GooglePrivacyDlpV2TaggedField');
