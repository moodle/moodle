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

namespace Google\Service\DataManager;

class PairData extends \Google\Collection
{
  protected $collection_key = 'pairIds';
  /**
   * Required. Cleanroom-provided PII data, hashed with SHA256, and encrypted
   * with an EC commutative cipher using publisher key for the
   * [PAIR]((//support.google.com/admanager/answer/15067908)) user list. At most
   * 10 `pairIds` can be provided in a single AudienceMember.
   *
   * @var string[]
   */
  public $pairIds;

  /**
   * Required. Cleanroom-provided PII data, hashed with SHA256, and encrypted
   * with an EC commutative cipher using publisher key for the
   * [PAIR]((//support.google.com/admanager/answer/15067908)) user list. At most
   * 10 `pairIds` can be provided in a single AudienceMember.
   *
   * @param string[] $pairIds
   */
  public function setPairIds($pairIds)
  {
    $this->pairIds = $pairIds;
  }
  /**
   * @return string[]
   */
  public function getPairIds()
  {
    return $this->pairIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PairData::class, 'Google_Service_DataManager_PairData');
