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

namespace Google\Service\ToolResults;

class NonSdkApi extends \Google\Collection
{
  public const LIST_NONE = 'NONE';
  public const LIST_WHITE = 'WHITE';
  public const LIST_BLACK = 'BLACK';
  public const LIST_GREY = 'GREY';
  public const LIST_GREY_MAX_O = 'GREY_MAX_O';
  public const LIST_GREY_MAX_P = 'GREY_MAX_P';
  public const LIST_GREY_MAX_Q = 'GREY_MAX_Q';
  public const LIST_GREY_MAX_R = 'GREY_MAX_R';
  public const LIST_GREY_MAX_S = 'GREY_MAX_S';
  protected $collection_key = 'insights';
  /**
   * The signature of the Non-SDK API
   *
   * @var string
   */
  public $apiSignature;
  /**
   * Example stack traces of this API being called.
   *
   * @var string[]
   */
  public $exampleStackTraces;
  protected $insightsType = NonSdkApiInsight::class;
  protected $insightsDataType = 'array';
  /**
   * The total number of times this API was observed to have been called.
   *
   * @var int
   */
  public $invocationCount;
  /**
   * Which list this API appears on
   *
   * @deprecated
   * @var string
   */
  public $list;

  /**
   * The signature of the Non-SDK API
   *
   * @param string $apiSignature
   */
  public function setApiSignature($apiSignature)
  {
    $this->apiSignature = $apiSignature;
  }
  /**
   * @return string
   */
  public function getApiSignature()
  {
    return $this->apiSignature;
  }
  /**
   * Example stack traces of this API being called.
   *
   * @param string[] $exampleStackTraces
   */
  public function setExampleStackTraces($exampleStackTraces)
  {
    $this->exampleStackTraces = $exampleStackTraces;
  }
  /**
   * @return string[]
   */
  public function getExampleStackTraces()
  {
    return $this->exampleStackTraces;
  }
  /**
   * Optional debugging insights for non-SDK API violations.
   *
   * @param NonSdkApiInsight[] $insights
   */
  public function setInsights($insights)
  {
    $this->insights = $insights;
  }
  /**
   * @return NonSdkApiInsight[]
   */
  public function getInsights()
  {
    return $this->insights;
  }
  /**
   * The total number of times this API was observed to have been called.
   *
   * @param int $invocationCount
   */
  public function setInvocationCount($invocationCount)
  {
    $this->invocationCount = $invocationCount;
  }
  /**
   * @return int
   */
  public function getInvocationCount()
  {
    return $this->invocationCount;
  }
  /**
   * Which list this API appears on
   *
   * Accepted values: NONE, WHITE, BLACK, GREY, GREY_MAX_O, GREY_MAX_P,
   * GREY_MAX_Q, GREY_MAX_R, GREY_MAX_S
   *
   * @deprecated
   * @param self::LIST_* $list
   */
  public function setList($list)
  {
    $this->list = $list;
  }
  /**
   * @deprecated
   * @return self::LIST_*
   */
  public function getList()
  {
    return $this->list;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NonSdkApi::class, 'Google_Service_ToolResults_NonSdkApi');
