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

class QueryInterpretation extends \Google\Model
{
  /**
   * Neither the natural language interpretation, nor a broader version of the
   * query is used to fetch the search results.
   */
  public const INTERPRETATION_TYPE_NONE = 'NONE';
  /**
   * The results from original query are blended with other results. The reason
   * for blending these other results with the results from original query is
   * populated in the 'Reason' field below.
   */
  public const INTERPRETATION_TYPE_BLEND = 'BLEND';
  /**
   * The results from original query are replaced. The reason for replacing the
   * results from original query is populated in the 'Reason' field below.
   */
  public const INTERPRETATION_TYPE_REPLACE = 'REPLACE';
  public const REASON_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Natural language interpretation of the query is used to fetch the search
   * results.
   */
  public const REASON_QUERY_HAS_NATURAL_LANGUAGE_INTENT = 'QUERY_HAS_NATURAL_LANGUAGE_INTENT';
  /**
   * Query and document terms similarity is used to selectively broaden the
   * query to retrieve additional search results since enough results were not
   * found for the user query. Interpreted query will be empty for this case.
   */
  public const REASON_NOT_ENOUGH_RESULTS_FOUND_FOR_USER_QUERY = 'NOT_ENOUGH_RESULTS_FOUND_FOR_USER_QUERY';
  /**
   * @var string
   */
  public $interpretationType;
  /**
   * The interpretation of the query used in search. For example, queries with
   * natural language intent like "email from john" will be interpreted as
   * "from:john source:mail". This field will not be filled when the reason is
   * NOT_ENOUGH_RESULTS_FOUND_FOR_USER_QUERY.
   *
   * @var string
   */
  public $interpretedQuery;
  /**
   * The reason for interpretation of the query. This field will not be
   * UNSPECIFIED if the interpretation type is not NONE.
   *
   * @var string
   */
  public $reason;

  /**
   * @param self::INTERPRETATION_TYPE_* $interpretationType
   */
  public function setInterpretationType($interpretationType)
  {
    $this->interpretationType = $interpretationType;
  }
  /**
   * @return self::INTERPRETATION_TYPE_*
   */
  public function getInterpretationType()
  {
    return $this->interpretationType;
  }
  /**
   * The interpretation of the query used in search. For example, queries with
   * natural language intent like "email from john" will be interpreted as
   * "from:john source:mail". This field will not be filled when the reason is
   * NOT_ENOUGH_RESULTS_FOUND_FOR_USER_QUERY.
   *
   * @param string $interpretedQuery
   */
  public function setInterpretedQuery($interpretedQuery)
  {
    $this->interpretedQuery = $interpretedQuery;
  }
  /**
   * @return string
   */
  public function getInterpretedQuery()
  {
    return $this->interpretedQuery;
  }
  /**
   * The reason for interpretation of the query. This field will not be
   * UNSPECIFIED if the interpretation type is not NONE.
   *
   * Accepted values: UNSPECIFIED, QUERY_HAS_NATURAL_LANGUAGE_INTENT,
   * NOT_ENOUGH_RESULTS_FOUND_FOR_USER_QUERY
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryInterpretation::class, 'Google_Service_CloudSearch_QueryInterpretation');
