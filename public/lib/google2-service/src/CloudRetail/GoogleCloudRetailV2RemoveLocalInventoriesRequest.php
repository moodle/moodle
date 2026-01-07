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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2RemoveLocalInventoriesRequest extends \Google\Collection
{
  protected $collection_key = 'placeIds';
  /**
   * If set to true, and the Product is not found, the local inventory removal
   * request will still be processed and retained for at most 1 day and
   * processed once the Product is created. If set to false, a NOT_FOUND error
   * is returned if the Product is not found.
   *
   * @var bool
   */
  public $allowMissing;
  /**
   * Required. A list of place IDs to have their inventory deleted. At most 3000
   * place IDs are allowed per request.
   *
   * @var string[]
   */
  public $placeIds;
  /**
   * The time when the inventory deletions are issued. Used to prevent out-of-
   * order updates and deletions on local inventory fields. If not provided, the
   * internal system time will be used.
   *
   * @var string
   */
  public $removeTime;

  /**
   * If set to true, and the Product is not found, the local inventory removal
   * request will still be processed and retained for at most 1 day and
   * processed once the Product is created. If set to false, a NOT_FOUND error
   * is returned if the Product is not found.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Required. A list of place IDs to have their inventory deleted. At most 3000
   * place IDs are allowed per request.
   *
   * @param string[] $placeIds
   */
  public function setPlaceIds($placeIds)
  {
    $this->placeIds = $placeIds;
  }
  /**
   * @return string[]
   */
  public function getPlaceIds()
  {
    return $this->placeIds;
  }
  /**
   * The time when the inventory deletions are issued. Used to prevent out-of-
   * order updates and deletions on local inventory fields. If not provided, the
   * internal system time will be used.
   *
   * @param string $removeTime
   */
  public function setRemoveTime($removeTime)
  {
    $this->removeTime = $removeTime;
  }
  /**
   * @return string
   */
  public function getRemoveTime()
  {
    return $this->removeTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RemoveLocalInventoriesRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RemoveLocalInventoriesRequest');
