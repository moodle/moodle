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

namespace Google\Service\Spanner;

class PartialResultSet extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * If true, then the final value in values is chunked, and must be combined
   * with more values from subsequent `PartialResultSet`s to obtain a complete
   * field value.
   *
   * @var bool
   */
  public $chunkedValue;
  /**
   * Optional. Indicates whether this is the last `PartialResultSet` in the
   * stream. The server might optionally set this field. Clients shouldn't rely
   * on this field being set in all cases.
   *
   * @var bool
   */
  public $last;
  protected $metadataType = ResultSetMetadata::class;
  protected $metadataDataType = '';
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  /**
   * Streaming calls might be interrupted for a variety of reasons, such as TCP
   * connection loss. If this occurs, the stream of results can be resumed by
   * re-sending the original request and including `resume_token`. Note that
   * executing any other transaction in the same session invalidates the token.
   *
   * @var string
   */
  public $resumeToken;
  protected $statsType = ResultSetStats::class;
  protected $statsDataType = '';
  /**
   * A streamed result set consists of a stream of values, which might be split
   * into many `PartialResultSet` messages to accommodate large rows and/or
   * large values. Every N complete values defines a row, where N is equal to
   * the number of entries in metadata.row_type.fields. Most values are encoded
   * based on type as described here. It's possible that the last value in
   * values is "chunked", meaning that the rest of the value is sent in
   * subsequent `PartialResultSet`(s). This is denoted by the chunked_value
   * field. Two or more chunked values can be merged to form a complete value as
   * follows: * `bool/number/null`: can't be chunked * `string`: concatenate the
   * strings * `list`: concatenate the lists. If the last element in a list is a
   * `string`, `list`, or `object`, merge it with the first element in the next
   * list by applying these rules recursively. * `object`: concatenate the
   * (field name, field value) pairs. If a field name is duplicated, then apply
   * these rules recursively to merge the field values. Some examples of
   * merging: Strings are concatenated. "foo", "bar" => "foobar" Lists of non-
   * strings are concatenated. [2, 3], [4] => [2, 3, 4] Lists are concatenated,
   * but the last and first elements are merged because they are strings. ["a",
   * "b"], ["c", "d"] => ["a", "bc", "d"] Lists are concatenated, but the last
   * and first elements are merged because they are lists. Recursively, the last
   * and first elements of the inner lists are merged because they are strings.
   * ["a", ["b", "c"]], [["d"], "e"] => ["a", ["b", "cd"], "e"] Non-overlapping
   * object fields are combined. {"a": "1"}, {"b": "2"} => {"a": "1", "b": 2"}
   * Overlapping object fields are merged. {"a": "1"}, {"a": "2"} => {"a": "12"}
   * Examples of merging objects containing lists of strings. {"a": ["1"]},
   * {"a": ["2"]} => {"a": ["12"]} For a more complete example, suppose a
   * streaming SQL query is yielding a result set whose rows contain a single
   * string field. The following `PartialResultSet`s might be yielded: {
   * "metadata": { ... } "values": ["Hello", "W"] "chunked_value": true
   * "resume_token": "Af65..." } { "values": ["orl"] "chunked_value": true } {
   * "values": ["d"] "resume_token": "Zx1B..." } This sequence of
   * `PartialResultSet`s encodes two rows, one containing the field value
   * `"Hello"`, and a second containing the field value `"World" = "W" + "orl" +
   * "d"`. Not all `PartialResultSet`s contain a `resume_token`. Execution can
   * only be resumed from a previously yielded `resume_token`. For the above
   * sequence of `PartialResultSet`s, resuming the query with `"resume_token":
   * "Af65..."` yields results from the `PartialResultSet` with value "orl".
   *
   * @var array[]
   */
  public $values;

  /**
   * If true, then the final value in values is chunked, and must be combined
   * with more values from subsequent `PartialResultSet`s to obtain a complete
   * field value.
   *
   * @param bool $chunkedValue
   */
  public function setChunkedValue($chunkedValue)
  {
    $this->chunkedValue = $chunkedValue;
  }
  /**
   * @return bool
   */
  public function getChunkedValue()
  {
    return $this->chunkedValue;
  }
  /**
   * Optional. Indicates whether this is the last `PartialResultSet` in the
   * stream. The server might optionally set this field. Clients shouldn't rely
   * on this field being set in all cases.
   *
   * @param bool $last
   */
  public function setLast($last)
  {
    $this->last = $last;
  }
  /**
   * @return bool
   */
  public function getLast()
  {
    return $this->last;
  }
  /**
   * Metadata about the result set, such as row type information. Only present
   * in the first response.
   *
   * @param ResultSetMetadata $metadata
   */
  public function setMetadata(ResultSetMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ResultSetMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. A precommit token is included if the read-write transaction has
   * multiplexed sessions enabled. Pass the precommit token with the highest
   * sequence number from this transaction attempt to the Commit request for
   * this transaction.
   *
   * @param MultiplexedSessionPrecommitToken $precommitToken
   */
  public function setPrecommitToken(MultiplexedSessionPrecommitToken $precommitToken)
  {
    $this->precommitToken = $precommitToken;
  }
  /**
   * @return MultiplexedSessionPrecommitToken
   */
  public function getPrecommitToken()
  {
    return $this->precommitToken;
  }
  /**
   * Streaming calls might be interrupted for a variety of reasons, such as TCP
   * connection loss. If this occurs, the stream of results can be resumed by
   * re-sending the original request and including `resume_token`. Note that
   * executing any other transaction in the same session invalidates the token.
   *
   * @param string $resumeToken
   */
  public function setResumeToken($resumeToken)
  {
    $this->resumeToken = $resumeToken;
  }
  /**
   * @return string
   */
  public function getResumeToken()
  {
    return $this->resumeToken;
  }
  /**
   * Query plan and execution statistics for the statement that produced this
   * streaming result set. These can be requested by setting
   * ExecuteSqlRequest.query_mode and are sent only once with the last response
   * in the stream. This field is also present in the last response for DML
   * statements.
   *
   * @param ResultSetStats $stats
   */
  public function setStats(ResultSetStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return ResultSetStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * A streamed result set consists of a stream of values, which might be split
   * into many `PartialResultSet` messages to accommodate large rows and/or
   * large values. Every N complete values defines a row, where N is equal to
   * the number of entries in metadata.row_type.fields. Most values are encoded
   * based on type as described here. It's possible that the last value in
   * values is "chunked", meaning that the rest of the value is sent in
   * subsequent `PartialResultSet`(s). This is denoted by the chunked_value
   * field. Two or more chunked values can be merged to form a complete value as
   * follows: * `bool/number/null`: can't be chunked * `string`: concatenate the
   * strings * `list`: concatenate the lists. If the last element in a list is a
   * `string`, `list`, or `object`, merge it with the first element in the next
   * list by applying these rules recursively. * `object`: concatenate the
   * (field name, field value) pairs. If a field name is duplicated, then apply
   * these rules recursively to merge the field values. Some examples of
   * merging: Strings are concatenated. "foo", "bar" => "foobar" Lists of non-
   * strings are concatenated. [2, 3], [4] => [2, 3, 4] Lists are concatenated,
   * but the last and first elements are merged because they are strings. ["a",
   * "b"], ["c", "d"] => ["a", "bc", "d"] Lists are concatenated, but the last
   * and first elements are merged because they are lists. Recursively, the last
   * and first elements of the inner lists are merged because they are strings.
   * ["a", ["b", "c"]], [["d"], "e"] => ["a", ["b", "cd"], "e"] Non-overlapping
   * object fields are combined. {"a": "1"}, {"b": "2"} => {"a": "1", "b": 2"}
   * Overlapping object fields are merged. {"a": "1"}, {"a": "2"} => {"a": "12"}
   * Examples of merging objects containing lists of strings. {"a": ["1"]},
   * {"a": ["2"]} => {"a": ["12"]} For a more complete example, suppose a
   * streaming SQL query is yielding a result set whose rows contain a single
   * string field. The following `PartialResultSet`s might be yielded: {
   * "metadata": { ... } "values": ["Hello", "W"] "chunked_value": true
   * "resume_token": "Af65..." } { "values": ["orl"] "chunked_value": true } {
   * "values": ["d"] "resume_token": "Zx1B..." } This sequence of
   * `PartialResultSet`s encodes two rows, one containing the field value
   * `"Hello"`, and a second containing the field value `"World" = "W" + "orl" +
   * "d"`. Not all `PartialResultSet`s contain a `resume_token`. Execution can
   * only be resumed from a previously yielded `resume_token`. For the above
   * sequence of `PartialResultSet`s, resuming the query with `"resume_token":
   * "Af65..."` yields results from the `PartialResultSet` with value "orl".
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartialResultSet::class, 'Google_Service_Spanner_PartialResultSet');
