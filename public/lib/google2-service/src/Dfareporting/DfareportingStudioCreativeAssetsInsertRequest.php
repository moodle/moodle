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

namespace Google\Service\Dfareporting;

class DfareportingStudioCreativeAssetsInsertRequest extends \Google\Model
{
  /**
   * Optional. Studio account ID of the studio creative asset. It is a optional.
   *
   * @var string
   */
  public $studioAccountId;
  /**
   * Required. Studio advertiser ID of the studio creative asset. It is a
   * required field on insertion.
   *
   * @var string
   */
  public $studioAdvertiserId;
  /**
   * Optional. Studio creative ID of the studio creative asset. It is a optional
   * field. If it is set, the asset will be associated to the creative.
   *
   * @var string
   */
  public $studioCreativeId;

  /**
   * Optional. Studio account ID of the studio creative asset. It is a optional.
   *
   * @param string $studioAccountId
   */
  public function setStudioAccountId($studioAccountId)
  {
    $this->studioAccountId = $studioAccountId;
  }
  /**
   * @return string
   */
  public function getStudioAccountId()
  {
    return $this->studioAccountId;
  }
  /**
   * Required. Studio advertiser ID of the studio creative asset. It is a
   * required field on insertion.
   *
   * @param string $studioAdvertiserId
   */
  public function setStudioAdvertiserId($studioAdvertiserId)
  {
    $this->studioAdvertiserId = $studioAdvertiserId;
  }
  /**
   * @return string
   */
  public function getStudioAdvertiserId()
  {
    return $this->studioAdvertiserId;
  }
  /**
   * Optional. Studio creative ID of the studio creative asset. It is a optional
   * field. If it is set, the asset will be associated to the creative.
   *
   * @param string $studioCreativeId
   */
  public function setStudioCreativeId($studioCreativeId)
  {
    $this->studioCreativeId = $studioCreativeId;
  }
  /**
   * @return string
   */
  public function getStudioCreativeId()
  {
    return $this->studioCreativeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DfareportingStudioCreativeAssetsInsertRequest::class, 'Google_Service_Dfareporting_DfareportingStudioCreativeAssetsInsertRequest');
