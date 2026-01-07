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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2RuleDoNotAssociateAction extends \Google\Collection
{
  protected $collection_key = 'terms';
  /**
   * Cannot contain duplicates or the query term. Can specify up to 100 terms.
   *
   * @var string[]
   */
  public $doNotAssociateTerms;
  /**
   * Terms from the search query. Will not consider do_not_associate_terms for
   * search if in search query. Can specify up to 100 terms.
   *
   * @var string[]
   */
  public $queryTerms;
  /**
   * Will be [deprecated = true] post migration;
   *
   * @var string[]
   */
  public $terms;

  /**
   * Cannot contain duplicates or the query term. Can specify up to 100 terms.
   *
   * @param string[] $doNotAssociateTerms
   */
  public function setDoNotAssociateTerms($doNotAssociateTerms)
  {
    $this->doNotAssociateTerms = $doNotAssociateTerms;
  }
  /**
   * @return string[]
   */
  public function getDoNotAssociateTerms()
  {
    return $this->doNotAssociateTerms;
  }
  /**
   * Terms from the search query. Will not consider do_not_associate_terms for
   * search if in search query. Can specify up to 100 terms.
   *
   * @param string[] $queryTerms
   */
  public function setQueryTerms($queryTerms)
  {
    $this->queryTerms = $queryTerms;
  }
  /**
   * @return string[]
   */
  public function getQueryTerms()
  {
    return $this->queryTerms;
  }
  /**
   * Will be [deprecated = true] post migration;
   *
   * @param string[] $terms
   */
  public function setTerms($terms)
  {
    $this->terms = $terms;
  }
  /**
   * @return string[]
   */
  public function getTerms()
  {
    return $this->terms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RuleDoNotAssociateAction::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RuleDoNotAssociateAction');
