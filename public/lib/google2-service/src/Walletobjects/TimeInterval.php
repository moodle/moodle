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

namespace Google\Service\Walletobjects;

class TimeInterval extends \Google\Model
{
  protected $endType = DateTime::class;
  protected $endDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#timeInterval"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $startType = DateTime::class;
  protected $startDataType = '';

  /**
   * End time of the interval. Offset is not required. If an offset is provided
   * and `start` time is set, `start` must also include an offset.
   *
   * @param DateTime $end
   */
  public function setEnd(DateTime $end)
  {
    $this->end = $end;
  }
  /**
   * @return DateTime
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#timeInterval"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Start time of the interval. Offset is not required. If an offset is
   * provided and `end` time is set, `end` must also include an offset.
   *
   * @param DateTime $start
   */
  public function setStart(DateTime $start)
  {
    $this->start = $start;
  }
  /**
   * @return DateTime
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeInterval::class, 'Google_Service_Walletobjects_TimeInterval');
