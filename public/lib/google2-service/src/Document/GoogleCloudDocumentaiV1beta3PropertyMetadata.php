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

class GoogleCloudDocumentaiV1beta3PropertyMetadata extends \Google\Model
{
  protected $fieldExtractionMetadataType = GoogleCloudDocumentaiV1beta3FieldExtractionMetadata::class;
  protected $fieldExtractionMetadataDataType = '';
  /**
   * Whether the property should be considered as "inactive".
   *
   * @var bool
   */
  public $inactive;

  /**
   * Field extraction metadata on the property.
   *
   * @param GoogleCloudDocumentaiV1beta3FieldExtractionMetadata $fieldExtractionMetadata
   */
  public function setFieldExtractionMetadata(GoogleCloudDocumentaiV1beta3FieldExtractionMetadata $fieldExtractionMetadata)
  {
    $this->fieldExtractionMetadata = $fieldExtractionMetadata;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3FieldExtractionMetadata
   */
  public function getFieldExtractionMetadata()
  {
    return $this->fieldExtractionMetadata;
  }
  /**
   * Whether the property should be considered as "inactive".
   *
   * @param bool $inactive
   */
  public function setInactive($inactive)
  {
    $this->inactive = $inactive;
  }
  /**
   * @return bool
   */
  public function getInactive()
  {
    return $this->inactive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3PropertyMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3PropertyMetadata');
