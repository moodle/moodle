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

namespace Google\Service\Logging;

class DefaultSinkConfig extends \Google\Collection
{
  /**
   * The filter's write mode is unspecified. This mode must not be used.
   */
  public const MODE_FILTER_WRITE_MODE_UNSPECIFIED = 'FILTER_WRITE_MODE_UNSPECIFIED';
  /**
   * The contents of filter will be appended to the built-in _Default sink
   * filter. Using the append mode with an empty filter will keep the sink
   * inclusion filter unchanged.
   */
  public const MODE_APPEND = 'APPEND';
  /**
   * The contents of filter will overwrite the built-in _Default sink filter.
   */
  public const MODE_OVERWRITE = 'OVERWRITE';
  protected $collection_key = 'exclusions';
  protected $exclusionsType = LogExclusion::class;
  protected $exclusionsDataType = 'array';
  /**
   * Optional. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries). The only
   * exported log entries are those that are in the resource owning the sink and
   * that match the filter.For
   * example:logName="projects/[PROJECT_ID]/logs/[LOG_ID]" AND severity>=ERRORTo
   * match all logs, don't add exclusions and use the following line as the
   * value of filter:logName:*Cannot be empty or unset when the value of mode is
   * OVERWRITE.
   *
   * @var string
   */
  public $filter;
  /**
   * Required. Determines the behavior to apply to the built-in _Default sink
   * inclusion filter.Exclusions are always appended, as built-in _Default sinks
   * have no exclusions.
   *
   * @var string
   */
  public $mode;

  /**
   * Optional. Specifies the set of exclusions to be added to the _Default sink
   * in newly created resource containers.
   *
   * @param LogExclusion[] $exclusions
   */
  public function setExclusions($exclusions)
  {
    $this->exclusions = $exclusions;
  }
  /**
   * @return LogExclusion[]
   */
  public function getExclusions()
  {
    return $this->exclusions;
  }
  /**
   * Optional. An advanced logs filter
   * (https://cloud.google.com/logging/docs/view/advanced-queries). The only
   * exported log entries are those that are in the resource owning the sink and
   * that match the filter.For
   * example:logName="projects/[PROJECT_ID]/logs/[LOG_ID]" AND severity>=ERRORTo
   * match all logs, don't add exclusions and use the following line as the
   * value of filter:logName:*Cannot be empty or unset when the value of mode is
   * OVERWRITE.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. Determines the behavior to apply to the built-in _Default sink
   * inclusion filter.Exclusions are always appended, as built-in _Default sinks
   * have no exclusions.
   *
   * Accepted values: FILTER_WRITE_MODE_UNSPECIFIED, APPEND, OVERWRITE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultSinkConfig::class, 'Google_Service_Logging_DefaultSinkConfig');
