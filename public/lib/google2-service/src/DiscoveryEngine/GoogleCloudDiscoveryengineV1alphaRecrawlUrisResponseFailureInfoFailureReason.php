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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaRecrawlUrisResponseFailureInfoFailureReason extends \Google\Model
{
  /**
   * Default value.
   */
  public const CORPUS_TYPE_CORPUS_TYPE_UNSPECIFIED = 'CORPUS_TYPE_UNSPECIFIED';
  /**
   * Denotes a crawling attempt for the desktop version of a page.
   */
  public const CORPUS_TYPE_DESKTOP = 'DESKTOP';
  /**
   * Denotes a crawling attempt for the mobile version of a page.
   */
  public const CORPUS_TYPE_MOBILE = 'MOBILE';
  /**
   * DESKTOP, MOBILE, or CORPUS_TYPE_UNSPECIFIED.
   *
   * @var string
   */
  public $corpusType;
  /**
   * Reason why the URI was not crawled.
   *
   * @var string
   */
  public $errorMessage;

  /**
   * DESKTOP, MOBILE, or CORPUS_TYPE_UNSPECIFIED.
   *
   * Accepted values: CORPUS_TYPE_UNSPECIFIED, DESKTOP, MOBILE
   *
   * @param self::CORPUS_TYPE_* $corpusType
   */
  public function setCorpusType($corpusType)
  {
    $this->corpusType = $corpusType;
  }
  /**
   * @return self::CORPUS_TYPE_*
   */
  public function getCorpusType()
  {
    return $this->corpusType;
  }
  /**
   * Reason why the URI was not crawled.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaRecrawlUrisResponseFailureInfoFailureReason::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaRecrawlUrisResponseFailureInfoFailureReason');
