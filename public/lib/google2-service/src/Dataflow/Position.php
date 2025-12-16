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

namespace Google\Service\Dataflow;

class Position extends \Google\Model
{
  /**
   * Position is a byte offset.
   *
   * @var string
   */
  public $byteOffset;
  protected $concatPositionType = ConcatPosition::class;
  protected $concatPositionDataType = '';
  /**
   * Position is past all other positions. Also useful for the end position of
   * an unbounded range.
   *
   * @var bool
   */
  public $end;
  /**
   * Position is a string key, ordered lexicographically.
   *
   * @var string
   */
  public $key;
  /**
   * Position is a record index.
   *
   * @var string
   */
  public $recordIndex;
  /**
   * CloudPosition is a base64 encoded BatchShufflePosition (with FIXED
   * sharding).
   *
   * @var string
   */
  public $shufflePosition;

  /**
   * Position is a byte offset.
   *
   * @param string $byteOffset
   */
  public function setByteOffset($byteOffset)
  {
    $this->byteOffset = $byteOffset;
  }
  /**
   * @return string
   */
  public function getByteOffset()
  {
    return $this->byteOffset;
  }
  /**
   * CloudPosition is a concat position.
   *
   * @param ConcatPosition $concatPosition
   */
  public function setConcatPosition(ConcatPosition $concatPosition)
  {
    $this->concatPosition = $concatPosition;
  }
  /**
   * @return ConcatPosition
   */
  public function getConcatPosition()
  {
    return $this->concatPosition;
  }
  /**
   * Position is past all other positions. Also useful for the end position of
   * an unbounded range.
   *
   * @param bool $end
   */
  public function setEnd($end)
  {
    $this->end = $end;
  }
  /**
   * @return bool
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Position is a string key, ordered lexicographically.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Position is a record index.
   *
   * @param string $recordIndex
   */
  public function setRecordIndex($recordIndex)
  {
    $this->recordIndex = $recordIndex;
  }
  /**
   * @return string
   */
  public function getRecordIndex()
  {
    return $this->recordIndex;
  }
  /**
   * CloudPosition is a base64 encoded BatchShufflePosition (with FIXED
   * sharding).
   *
   * @param string $shufflePosition
   */
  public function setShufflePosition($shufflePosition)
  {
    $this->shufflePosition = $shufflePosition;
  }
  /**
   * @return string
   */
  public function getShufflePosition()
  {
    return $this->shufflePosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Position::class, 'Google_Service_Dataflow_Position');
