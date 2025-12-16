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

class GoogleCloudDocumentaiV1DocumentPageFormField extends \Google\Collection
{
  protected $collection_key = 'valueDetectedLanguages';
  /**
   * Created for Labeling UI to export key text. If corrections were made to the
   * text identified by the `field_name.text_anchor`, this field will contain
   * the correction.
   *
   * @var string
   */
  public $correctedKeyText;
  /**
   * Created for Labeling UI to export value text. If corrections were made to
   * the text identified by the `field_value.text_anchor`, this field will
   * contain the correction.
   *
   * @var string
   */
  public $correctedValueText;
  protected $fieldNameType = GoogleCloudDocumentaiV1DocumentPageLayout::class;
  protected $fieldNameDataType = '';
  protected $fieldValueType = GoogleCloudDocumentaiV1DocumentPageLayout::class;
  protected $fieldValueDataType = '';
  protected $nameDetectedLanguagesType = GoogleCloudDocumentaiV1DocumentPageDetectedLanguage::class;
  protected $nameDetectedLanguagesDataType = 'array';
  protected $provenanceType = GoogleCloudDocumentaiV1DocumentProvenance::class;
  protected $provenanceDataType = '';
  protected $valueDetectedLanguagesType = GoogleCloudDocumentaiV1DocumentPageDetectedLanguage::class;
  protected $valueDetectedLanguagesDataType = 'array';
  /**
   * If the value is non-textual, this field represents the type. Current valid
   * values are: - blank (this indicates the `field_value` is normal text) -
   * `unfilled_checkbox` - `filled_checkbox`
   *
   * @var string
   */
  public $valueType;

  /**
   * Created for Labeling UI to export key text. If corrections were made to the
   * text identified by the `field_name.text_anchor`, this field will contain
   * the correction.
   *
   * @param string $correctedKeyText
   */
  public function setCorrectedKeyText($correctedKeyText)
  {
    $this->correctedKeyText = $correctedKeyText;
  }
  /**
   * @return string
   */
  public function getCorrectedKeyText()
  {
    return $this->correctedKeyText;
  }
  /**
   * Created for Labeling UI to export value text. If corrections were made to
   * the text identified by the `field_value.text_anchor`, this field will
   * contain the correction.
   *
   * @param string $correctedValueText
   */
  public function setCorrectedValueText($correctedValueText)
  {
    $this->correctedValueText = $correctedValueText;
  }
  /**
   * @return string
   */
  public function getCorrectedValueText()
  {
    return $this->correctedValueText;
  }
  /**
   * Layout for the FormField name. e.g. `Address`, `Email`, `Grand total`,
   * `Phone number`, etc.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageLayout $fieldName
   */
  public function setFieldName(GoogleCloudDocumentaiV1DocumentPageLayout $fieldName)
  {
    $this->fieldName = $fieldName;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageLayout
   */
  public function getFieldName()
  {
    return $this->fieldName;
  }
  /**
   * Layout for the FormField value.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageLayout $fieldValue
   */
  public function setFieldValue(GoogleCloudDocumentaiV1DocumentPageLayout $fieldValue)
  {
    $this->fieldValue = $fieldValue;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageLayout
   */
  public function getFieldValue()
  {
    return $this->fieldValue;
  }
  /**
   * A list of detected languages for name together with confidence.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[] $nameDetectedLanguages
   */
  public function setNameDetectedLanguages($nameDetectedLanguages)
  {
    $this->nameDetectedLanguages = $nameDetectedLanguages;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[]
   */
  public function getNameDetectedLanguages()
  {
    return $this->nameDetectedLanguages;
  }
  /**
   * The history of this annotation.
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
   * A list of detected languages for value together with confidence.
   *
   * @param GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[] $valueDetectedLanguages
   */
  public function setValueDetectedLanguages($valueDetectedLanguages)
  {
    $this->valueDetectedLanguages = $valueDetectedLanguages;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentPageDetectedLanguage[]
   */
  public function getValueDetectedLanguages()
  {
    return $this->valueDetectedLanguages;
  }
  /**
   * If the value is non-textual, this field represents the type. Current valid
   * values are: - blank (this indicates the `field_value` is normal text) -
   * `unfilled_checkbox` - `filled_checkbox`
   *
   * @param string $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return string
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageFormField::class, 'Google_Service_Contentwarehouse_GoogleCloudDocumentaiV1DocumentPageFormField');
