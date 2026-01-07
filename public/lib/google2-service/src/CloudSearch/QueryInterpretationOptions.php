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

namespace Google\Service\CloudSearch;

class QueryInterpretationOptions extends \Google\Model
{
  /**
   * Flag to disable natural language (NL) interpretation of queries. Default is
   * false, Set to true to disable natural language interpretation. NL
   * interpretation only applies to predefined datasources.
   *
   * @var bool
   */
  public $disableNlInterpretation;
  /**
   * Use this flag to disable supplemental results for a query. Supplemental
   * results setting chosen at SearchApplication level will take precedence if
   * set to True.
   *
   * @var bool
   */
  public $disableSupplementalResults;
  /**
   * Enable this flag to turn off all internal optimizations like natural
   * language (NL) interpretation of queries, supplemental result retrieval, and
   * usage of synonyms including custom ones. Nl interpretation will be disabled
   * if either one of the two flags is true.
   *
   * @var bool
   */
  public $enableVerbatimMode;

  /**
   * Flag to disable natural language (NL) interpretation of queries. Default is
   * false, Set to true to disable natural language interpretation. NL
   * interpretation only applies to predefined datasources.
   *
   * @param bool $disableNlInterpretation
   */
  public function setDisableNlInterpretation($disableNlInterpretation)
  {
    $this->disableNlInterpretation = $disableNlInterpretation;
  }
  /**
   * @return bool
   */
  public function getDisableNlInterpretation()
  {
    return $this->disableNlInterpretation;
  }
  /**
   * Use this flag to disable supplemental results for a query. Supplemental
   * results setting chosen at SearchApplication level will take precedence if
   * set to True.
   *
   * @param bool $disableSupplementalResults
   */
  public function setDisableSupplementalResults($disableSupplementalResults)
  {
    $this->disableSupplementalResults = $disableSupplementalResults;
  }
  /**
   * @return bool
   */
  public function getDisableSupplementalResults()
  {
    return $this->disableSupplementalResults;
  }
  /**
   * Enable this flag to turn off all internal optimizations like natural
   * language (NL) interpretation of queries, supplemental result retrieval, and
   * usage of synonyms including custom ones. Nl interpretation will be disabled
   * if either one of the two flags is true.
   *
   * @param bool $enableVerbatimMode
   */
  public function setEnableVerbatimMode($enableVerbatimMode)
  {
    $this->enableVerbatimMode = $enableVerbatimMode;
  }
  /**
   * @return bool
   */
  public function getEnableVerbatimMode()
  {
    return $this->enableVerbatimMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryInterpretationOptions::class, 'Google_Service_CloudSearch_QueryInterpretationOptions');
