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

class GooglePrivacyDlpV2Domain extends \Google\Collection
{
  /**
   * Category unspecified.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * Indicates that the data profile is related to artificial intelligence. When
   * set, all findings stored to Security Command Center will set the
   * corresponding AI domain field of `Finding` objects.
   */
  public const CATEGORY_AI = 'AI';
  /**
   * Indicates that the data profile is related to code.
   */
  public const CATEGORY_CODE = 'CODE';
  protected $collection_key = 'signals';
  /**
   * A domain category that this profile is related to.
   *
   * @var string
   */
  public $category;
  /**
   * The collection of signals that influenced selection of the category.
   *
   * @var string[]
   */
  public $signals;

  /**
   * A domain category that this profile is related to.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, AI, CODE
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The collection of signals that influenced selection of the category.
   *
   * @param string[] $signals
   */
  public function setSignals($signals)
  {
    $this->signals = $signals;
  }
  /**
   * @return string[]
   */
  public function getSignals()
  {
    return $this->signals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Domain::class, 'Google_Service_DLP_GooglePrivacyDlpV2Domain');
