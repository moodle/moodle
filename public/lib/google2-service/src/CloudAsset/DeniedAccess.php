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

namespace Google\Service\CloudAsset;

class DeniedAccess extends \Google\Collection
{
  protected $collection_key = 'denyDetails';
  protected $deniedAccessTupleType = GoogleCloudAssetV1DeniedAccessAccessTuple::class;
  protected $deniedAccessTupleDataType = '';
  protected $denyDetailsType = GoogleCloudAssetV1DeniedAccessDenyDetail::class;
  protected $denyDetailsDataType = 'array';

  /**
   * @param GoogleCloudAssetV1DeniedAccessAccessTuple
   */
  public function setDeniedAccessTuple(GoogleCloudAssetV1DeniedAccessAccessTuple $deniedAccessTuple)
  {
    $this->deniedAccessTuple = $deniedAccessTuple;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessAccessTuple
   */
  public function getDeniedAccessTuple()
  {
    return $this->deniedAccessTuple;
  }
  /**
   * @param GoogleCloudAssetV1DeniedAccessDenyDetail[]
   */
  public function setDenyDetails($denyDetails)
  {
    $this->denyDetails = $denyDetails;
  }
  /**
   * @return GoogleCloudAssetV1DeniedAccessDenyDetail[]
   */
  public function getDenyDetails()
  {
    return $this->denyDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeniedAccess::class, 'Google_Service_CloudAsset_DeniedAccess');
