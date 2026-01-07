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

namespace Google\Service\NetAppFiles;

class CacheConfig extends \Google\Model
{
  /**
   * Default unspecified state.
   */
  public const CACHE_PRE_POPULATE_STATE_CACHE_PRE_POPULATE_STATE_UNSPECIFIED = 'CACHE_PRE_POPULATE_STATE_UNSPECIFIED';
  /**
   * State representing when the most recent create or update request did not
   * require a prepopulation job.
   */
  public const CACHE_PRE_POPULATE_STATE_NOT_NEEDED = 'NOT_NEEDED';
  /**
   * State representing when the most recent update request requested a
   * prepopulation job but it has not yet completed.
   */
  public const CACHE_PRE_POPULATE_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * State representing when the most recent update request requested a
   * prepopulation job and it has completed successfully.
   */
  public const CACHE_PRE_POPULATE_STATE_COMPLETE = 'COMPLETE';
  /**
   * State representing when the most recent update request requested a
   * prepopulation job but the prepopulate job failed.
   */
  public const CACHE_PRE_POPULATE_STATE_ERROR = 'ERROR';
  protected $cachePrePopulateType = CachePrePopulate::class;
  protected $cachePrePopulateDataType = '';
  /**
   * Output only. State of the prepopulation job indicating how the
   * prepopulation is progressing.
   *
   * @var string
   */
  public $cachePrePopulateState;
  /**
   * Optional. Flag indicating whether a CIFS change notification is enabled for
   * the FlexCache volume.
   *
   * @var bool
   */
  public $cifsChangeNotifyEnabled;
  /**
   * Optional. Flag indicating whether writeback is enabled for the FlexCache
   * volume.
   *
   * @var bool
   */
  public $writebackEnabled;

  /**
   * Optional. Pre-populate cache volume with data from the origin volume.
   *
   * @param CachePrePopulate $cachePrePopulate
   */
  public function setCachePrePopulate(CachePrePopulate $cachePrePopulate)
  {
    $this->cachePrePopulate = $cachePrePopulate;
  }
  /**
   * @return CachePrePopulate
   */
  public function getCachePrePopulate()
  {
    return $this->cachePrePopulate;
  }
  /**
   * Output only. State of the prepopulation job indicating how the
   * prepopulation is progressing.
   *
   * Accepted values: CACHE_PRE_POPULATE_STATE_UNSPECIFIED, NOT_NEEDED,
   * IN_PROGRESS, COMPLETE, ERROR
   *
   * @param self::CACHE_PRE_POPULATE_STATE_* $cachePrePopulateState
   */
  public function setCachePrePopulateState($cachePrePopulateState)
  {
    $this->cachePrePopulateState = $cachePrePopulateState;
  }
  /**
   * @return self::CACHE_PRE_POPULATE_STATE_*
   */
  public function getCachePrePopulateState()
  {
    return $this->cachePrePopulateState;
  }
  /**
   * Optional. Flag indicating whether a CIFS change notification is enabled for
   * the FlexCache volume.
   *
   * @param bool $cifsChangeNotifyEnabled
   */
  public function setCifsChangeNotifyEnabled($cifsChangeNotifyEnabled)
  {
    $this->cifsChangeNotifyEnabled = $cifsChangeNotifyEnabled;
  }
  /**
   * @return bool
   */
  public function getCifsChangeNotifyEnabled()
  {
    return $this->cifsChangeNotifyEnabled;
  }
  /**
   * Optional. Flag indicating whether writeback is enabled for the FlexCache
   * volume.
   *
   * @param bool $writebackEnabled
   */
  public function setWritebackEnabled($writebackEnabled)
  {
    $this->writebackEnabled = $writebackEnabled;
  }
  /**
   * @return bool
   */
  public function getWritebackEnabled()
  {
    return $this->writebackEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CacheConfig::class, 'Google_Service_NetAppFiles_CacheConfig');
