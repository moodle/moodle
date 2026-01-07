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

class GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadata extends \Google\Model
{
  protected $entityQueryType = GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadataEntityQuery::class;
  protected $entityQueryDataType = '';
  protected $summaryOptionsType = GoogleCloudDocumentaiUiv1beta3SummaryOptions::class;
  protected $summaryOptionsDataType = '';

  /**
   * Entity query config.
   *
   * @param GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadataEntityQuery $entityQuery
   */
  public function setEntityQuery(GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadataEntityQuery $entityQuery)
  {
    $this->entityQuery = $entityQuery;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadataEntityQuery
   */
  public function getEntityQuery()
  {
    return $this->entityQuery;
  }
  /**
   * Summary options config.
   *
   * @param GoogleCloudDocumentaiUiv1beta3SummaryOptions $summaryOptions
   */
  public function setSummaryOptions(GoogleCloudDocumentaiUiv1beta3SummaryOptions $summaryOptions)
  {
    $this->summaryOptions = $summaryOptions;
  }
  /**
   * @return GoogleCloudDocumentaiUiv1beta3SummaryOptions
   */
  public function getSummaryOptions()
  {
    return $this->summaryOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3FieldExtractionMetadata');
