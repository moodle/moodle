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

class VisualizationData extends \Google\Collection
{
  /**
   * Required default value
   */
  public const KEY_UNIT_KEY_UNIT_UNSPECIFIED = 'KEY_UNIT_UNSPECIFIED';
  /**
   * Each entry corresponds to one key
   */
  public const KEY_UNIT_KEY = 'KEY';
  /**
   * Each entry corresponds to a chunk of keys
   */
  public const KEY_UNIT_CHUNK = 'CHUNK';
  protected $collection_key = 'prefixNodes';
  /**
   * The token signifying the end of a data_source.
   *
   * @var string
   */
  public $dataSourceEndToken;
  /**
   * The token delimiting a datasource name from the rest of a key in a
   * data_source.
   *
   * @var string
   */
  public $dataSourceSeparatorToken;
  protected $diagnosticMessagesType = DiagnosticMessage::class;
  protected $diagnosticMessagesDataType = 'array';
  /**
   * We discretize the entire keyspace into buckets. Assuming each bucket has an
   * inclusive keyrange and covers keys from k(i) ... k(n). In this case k(n)
   * would be an end key for a given range. end_key_string is the collection of
   * all such end keys
   *
   * @var string[]
   */
  public $endKeyStrings;
  /**
   * Whether this scan contains PII.
   *
   * @var bool
   */
  public $hasPii;
  /**
   * Keys of key ranges that contribute significantly to a given metric Can be
   * thought of as heavy hitters.
   *
   * @var string[]
   */
  public $indexedKeys;
  /**
   * The token delimiting the key prefixes.
   *
   * @var string
   */
  public $keySeparator;
  /**
   * The unit for the key: e.g. 'key' or 'chunk'.
   *
   * @var string
   */
  public $keyUnit;
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  protected $prefixNodesType = PrefixNode::class;
  protected $prefixNodesDataType = 'array';

  /**
   * The token signifying the end of a data_source.
   *
   * @param string $dataSourceEndToken
   */
  public function setDataSourceEndToken($dataSourceEndToken)
  {
    $this->dataSourceEndToken = $dataSourceEndToken;
  }
  /**
   * @return string
   */
  public function getDataSourceEndToken()
  {
    return $this->dataSourceEndToken;
  }
  /**
   * The token delimiting a datasource name from the rest of a key in a
   * data_source.
   *
   * @param string $dataSourceSeparatorToken
   */
  public function setDataSourceSeparatorToken($dataSourceSeparatorToken)
  {
    $this->dataSourceSeparatorToken = $dataSourceSeparatorToken;
  }
  /**
   * @return string
   */
  public function getDataSourceSeparatorToken()
  {
    return $this->dataSourceSeparatorToken;
  }
  /**
   * The list of messages (info, alerts, ...)
   *
   * @param DiagnosticMessage[] $diagnosticMessages
   */
  public function setDiagnosticMessages($diagnosticMessages)
  {
    $this->diagnosticMessages = $diagnosticMessages;
  }
  /**
   * @return DiagnosticMessage[]
   */
  public function getDiagnosticMessages()
  {
    return $this->diagnosticMessages;
  }
  /**
   * We discretize the entire keyspace into buckets. Assuming each bucket has an
   * inclusive keyrange and covers keys from k(i) ... k(n). In this case k(n)
   * would be an end key for a given range. end_key_string is the collection of
   * all such end keys
   *
   * @param string[] $endKeyStrings
   */
  public function setEndKeyStrings($endKeyStrings)
  {
    $this->endKeyStrings = $endKeyStrings;
  }
  /**
   * @return string[]
   */
  public function getEndKeyStrings()
  {
    return $this->endKeyStrings;
  }
  /**
   * Whether this scan contains PII.
   *
   * @param bool $hasPii
   */
  public function setHasPii($hasPii)
  {
    $this->hasPii = $hasPii;
  }
  /**
   * @return bool
   */
  public function getHasPii()
  {
    return $this->hasPii;
  }
  /**
   * Keys of key ranges that contribute significantly to a given metric Can be
   * thought of as heavy hitters.
   *
   * @param string[] $indexedKeys
   */
  public function setIndexedKeys($indexedKeys)
  {
    $this->indexedKeys = $indexedKeys;
  }
  /**
   * @return string[]
   */
  public function getIndexedKeys()
  {
    return $this->indexedKeys;
  }
  /**
   * The token delimiting the key prefixes.
   *
   * @param string $keySeparator
   */
  public function setKeySeparator($keySeparator)
  {
    $this->keySeparator = $keySeparator;
  }
  /**
   * @return string
   */
  public function getKeySeparator()
  {
    return $this->keySeparator;
  }
  /**
   * The unit for the key: e.g. 'key' or 'chunk'.
   *
   * Accepted values: KEY_UNIT_UNSPECIFIED, KEY, CHUNK
   *
   * @param self::KEY_UNIT_* $keyUnit
   */
  public function setKeyUnit($keyUnit)
  {
    $this->keyUnit = $keyUnit;
  }
  /**
   * @return self::KEY_UNIT_*
   */
  public function getKeyUnit()
  {
    return $this->keyUnit;
  }
  /**
   * The list of data objects for each metric.
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The list of extracted key prefix nodes used in the key prefix hierarchy.
   *
   * @param PrefixNode[] $prefixNodes
   */
  public function setPrefixNodes($prefixNodes)
  {
    $this->prefixNodes = $prefixNodes;
  }
  /**
   * @return PrefixNode[]
   */
  public function getPrefixNodes()
  {
    return $this->prefixNodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VisualizationData::class, 'Google_Service_Spanner_VisualizationData');
