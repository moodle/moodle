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

namespace Google\Service\VMMigrationService;

class DataDiskImageImport extends \Google\Collection
{
  protected $collection_key = 'guestOsFeatures';
  /**
   * Optional. A list of guest OS features to apply to the imported image. These
   * features are flags that are used by Compute Engine to enable certain
   * capabilities for virtual machine instances that are created from the image.
   * This field does not change the OS of the image; it only marks the image
   * with the specified features. The user must ensure that the OS is compatible
   * with the features. For a list of available features, see
   * https://cloud.google.com/compute/docs/images/create-custom#guest-os-
   * features.
   *
   * @var string[]
   */
  public $guestOsFeatures;

  /**
   * Optional. A list of guest OS features to apply to the imported image. These
   * features are flags that are used by Compute Engine to enable certain
   * capabilities for virtual machine instances that are created from the image.
   * This field does not change the OS of the image; it only marks the image
   * with the specified features. The user must ensure that the OS is compatible
   * with the features. For a list of available features, see
   * https://cloud.google.com/compute/docs/images/create-custom#guest-os-
   * features.
   *
   * @param string[] $guestOsFeatures
   */
  public function setGuestOsFeatures($guestOsFeatures)
  {
    $this->guestOsFeatures = $guestOsFeatures;
  }
  /**
   * @return string[]
   */
  public function getGuestOsFeatures()
  {
    return $this->guestOsFeatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataDiskImageImport::class, 'Google_Service_VMMigrationService_DataDiskImageImport');
