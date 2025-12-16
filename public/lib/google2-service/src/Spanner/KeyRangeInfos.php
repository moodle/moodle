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

class KeyRangeInfos extends \Google\Collection
{
  protected $collection_key = 'infos';
  protected $infosType = KeyRangeInfo::class;
  protected $infosDataType = 'array';
  /**
   * The total size of the list of all KeyRangeInfos. This may be larger than
   * the number of repeated messages above. If that is the case, this number may
   * be used to determine how many are not being shown.
   *
   * @var int
   */
  public $totalSize;

  /**
   * The list individual KeyRangeInfos.
   *
   * @param KeyRangeInfo[] $infos
   */
  public function setInfos($infos)
  {
    $this->infos = $infos;
  }
  /**
   * @return KeyRangeInfo[]
   */
  public function getInfos()
  {
    return $this->infos;
  }
  /**
   * The total size of the list of all KeyRangeInfos. This may be larger than
   * the number of repeated messages above. If that is the case, this number may
   * be used to determine how many are not being shown.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyRangeInfos::class, 'Google_Service_Spanner_KeyRangeInfos');
