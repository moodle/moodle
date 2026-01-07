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

namespace Google\Service\CloudHealthcare;

class AnalyzeEntitiesRequest extends \Google\Collection
{
  /**
   * No alternative output format is specified.
   */
  public const ALTERNATIVE_OUTPUT_FORMAT_ALTERNATIVE_OUTPUT_FORMAT_UNSPECIFIED = 'ALTERNATIVE_OUTPUT_FORMAT_UNSPECIFIED';
  /**
   * FHIR bundle output.
   */
  public const ALTERNATIVE_OUTPUT_FORMAT_FHIR_BUNDLE = 'FHIR_BUNDLE';
  protected $collection_key = 'licensedVocabularies';
  /**
   * Optional. Alternative output format to be generated based on the results of
   * analysis.
   *
   * @var string
   */
  public $alternativeOutputFormat;
  /**
   * document_content is a document to be annotated.
   *
   * @var string
   */
  public $documentContent;
  /**
   * A list of licensed vocabularies to use in the request, in addition to the
   * default unlicensed vocabularies.
   *
   * @var string[]
   */
  public $licensedVocabularies;

  /**
   * Optional. Alternative output format to be generated based on the results of
   * analysis.
   *
   * Accepted values: ALTERNATIVE_OUTPUT_FORMAT_UNSPECIFIED, FHIR_BUNDLE
   *
   * @param self::ALTERNATIVE_OUTPUT_FORMAT_* $alternativeOutputFormat
   */
  public function setAlternativeOutputFormat($alternativeOutputFormat)
  {
    $this->alternativeOutputFormat = $alternativeOutputFormat;
  }
  /**
   * @return self::ALTERNATIVE_OUTPUT_FORMAT_*
   */
  public function getAlternativeOutputFormat()
  {
    return $this->alternativeOutputFormat;
  }
  /**
   * document_content is a document to be annotated.
   *
   * @param string $documentContent
   */
  public function setDocumentContent($documentContent)
  {
    $this->documentContent = $documentContent;
  }
  /**
   * @return string
   */
  public function getDocumentContent()
  {
    return $this->documentContent;
  }
  /**
   * A list of licensed vocabularies to use in the request, in addition to the
   * default unlicensed vocabularies.
   *
   * @param string[] $licensedVocabularies
   */
  public function setLicensedVocabularies($licensedVocabularies)
  {
    $this->licensedVocabularies = $licensedVocabularies;
  }
  /**
   * @return string[]
   */
  public function getLicensedVocabularies()
  {
    return $this->licensedVocabularies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyzeEntitiesRequest::class, 'Google_Service_CloudHealthcare_AnalyzeEntitiesRequest');
