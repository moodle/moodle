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

namespace Google\Service\AndroidProvisioningPartner;

class Dpc extends \Google\Model
{
  /**
   * Output only. The title of the DPC app in Google Play. For example, _Google
   * Apps Device Policy_. Useful in an application's user interface.
   *
   * @var string
   */
  public $dpcName;
  /**
   * Output only. The API resource name in the format
   * `customers/[CUSTOMER_ID]/dpcs/[DPC_ID]`. Assigned by the server. To
   * maintain a reference to a DPC across customer accounts, persist and match
   * the last path component (`DPC_ID`).
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The DPC's Android application ID that looks like a Java
   * package name. Zero-touch enrollment installs the DPC app onto a device
   * using this identifier.
   *
   * @var string
   */
  public $packageName;

  /**
   * Output only. The title of the DPC app in Google Play. For example, _Google
   * Apps Device Policy_. Useful in an application's user interface.
   *
   * @param string $dpcName
   */
  public function setDpcName($dpcName)
  {
    $this->dpcName = $dpcName;
  }
  /**
   * @return string
   */
  public function getDpcName()
  {
    return $this->dpcName;
  }
  /**
   * Output only. The API resource name in the format
   * `customers/[CUSTOMER_ID]/dpcs/[DPC_ID]`. Assigned by the server. To
   * maintain a reference to a DPC across customer accounts, persist and match
   * the last path component (`DPC_ID`).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The DPC's Android application ID that looks like a Java
   * package name. Zero-touch enrollment installs the DPC app onto a device
   * using this identifier.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Dpc::class, 'Google_Service_AndroidProvisioningPartner_Dpc');
