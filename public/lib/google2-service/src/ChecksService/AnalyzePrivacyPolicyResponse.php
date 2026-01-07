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

namespace Google\Service\ChecksService;

class AnalyzePrivacyPolicyResponse extends \Google\Collection
{
  protected $collection_key = 'sectionAnnotations';
  protected $dataPurposeAnnotationsType = PolicyPurposeOfUseAnnotation::class;
  protected $dataPurposeAnnotationsDataType = 'array';
  protected $dataTypeAnnotationsType = PolicyDataTypeAnnotation::class;
  protected $dataTypeAnnotationsDataType = 'array';
  /**
   * @var string
   */
  public $htmlContent;
  protected $lastUpdatedDateInfoType = LastUpdatedDate::class;
  protected $lastUpdatedDateInfoDataType = '';
  protected $sectionAnnotationsType = PolicySectionAnnotation::class;
  protected $sectionAnnotationsDataType = 'array';

  /**
   * @param PolicyPurposeOfUseAnnotation[]
   */
  public function setDataPurposeAnnotations($dataPurposeAnnotations)
  {
    $this->dataPurposeAnnotations = $dataPurposeAnnotations;
  }
  /**
   * @return PolicyPurposeOfUseAnnotation[]
   */
  public function getDataPurposeAnnotations()
  {
    return $this->dataPurposeAnnotations;
  }
  /**
   * @param PolicyDataTypeAnnotation[]
   */
  public function setDataTypeAnnotations($dataTypeAnnotations)
  {
    $this->dataTypeAnnotations = $dataTypeAnnotations;
  }
  /**
   * @return PolicyDataTypeAnnotation[]
   */
  public function getDataTypeAnnotations()
  {
    return $this->dataTypeAnnotations;
  }
  /**
   * @param string
   */
  public function setHtmlContent($htmlContent)
  {
    $this->htmlContent = $htmlContent;
  }
  /**
   * @return string
   */
  public function getHtmlContent()
  {
    return $this->htmlContent;
  }
  /**
   * @param LastUpdatedDate
   */
  public function setLastUpdatedDateInfo(LastUpdatedDate $lastUpdatedDateInfo)
  {
    $this->lastUpdatedDateInfo = $lastUpdatedDateInfo;
  }
  /**
   * @return LastUpdatedDate
   */
  public function getLastUpdatedDateInfo()
  {
    return $this->lastUpdatedDateInfo;
  }
  /**
   * @param PolicySectionAnnotation[]
   */
  public function setSectionAnnotations($sectionAnnotations)
  {
    $this->sectionAnnotations = $sectionAnnotations;
  }
  /**
   * @return PolicySectionAnnotation[]
   */
  public function getSectionAnnotations()
  {
    return $this->sectionAnnotations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzePrivacyPolicyResponse::class, 'Google_Service_ChecksService_AnalyzePrivacyPolicyResponse');
