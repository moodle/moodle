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

namespace Google\Service\ServiceNetworking;

class CommonLanguageSettings extends \Google\Collection
{
  protected $collection_key = 'destinations';
  /**
   * The destination where API teams want this client library to be published.
   *
   * @var string[]
   */
  public $destinations;
  /**
   * Link to automatically generated reference documentation. Example:
   * https://cloud.google.com/nodejs/docs/reference/asset/latest
   *
   * @deprecated
   * @var string
   */
  public $referenceDocsUri;
  protected $selectiveGapicGenerationType = SelectiveGapicGeneration::class;
  protected $selectiveGapicGenerationDataType = '';

  /**
   * The destination where API teams want this client library to be published.
   *
   * @param string[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return string[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Link to automatically generated reference documentation. Example:
   * https://cloud.google.com/nodejs/docs/reference/asset/latest
   *
   * @deprecated
   * @param string $referenceDocsUri
   */
  public function setReferenceDocsUri($referenceDocsUri)
  {
    $this->referenceDocsUri = $referenceDocsUri;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getReferenceDocsUri()
  {
    return $this->referenceDocsUri;
  }
  /**
   * Configuration for which RPCs should be generated in the GAPIC client.
   *
   * @param SelectiveGapicGeneration $selectiveGapicGeneration
   */
  public function setSelectiveGapicGeneration(SelectiveGapicGeneration $selectiveGapicGeneration)
  {
    $this->selectiveGapicGeneration = $selectiveGapicGeneration;
  }
  /**
   * @return SelectiveGapicGeneration
   */
  public function getSelectiveGapicGeneration()
  {
    return $this->selectiveGapicGeneration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommonLanguageSettings::class, 'Google_Service_ServiceNetworking_CommonLanguageSettings');
