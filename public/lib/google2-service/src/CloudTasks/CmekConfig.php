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

namespace Google\Service\CloudTasks;

class CmekConfig extends \Google\Model
{
  /**
   * Resource name of the Cloud KMS key, of the form `projects/PROJECT_ID/locati
   * ons/LOCATION_ID/keyRings/KEY_RING_ID/cryptoKeys/KEY_ID`, that will be used
   * to encrypt the Queues & Tasks in the region. Setting this as blank will
   * turn off CMEK encryption.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Output only. The config resource name which includes the project and
   * location and must end in 'cmekConfig', in the format
   * projects/PROJECT_ID/locations/LOCATION_ID/cmekConfig`
   *
   * @var string
   */
  public $name;

  /**
   * Resource name of the Cloud KMS key, of the form `projects/PROJECT_ID/locati
   * ons/LOCATION_ID/keyRings/KEY_RING_ID/cryptoKeys/KEY_ID`, that will be used
   * to encrypt the Queues & Tasks in the region. Setting this as blank will
   * turn off CMEK encryption.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Output only. The config resource name which includes the project and
   * location and must end in 'cmekConfig', in the format
   * projects/PROJECT_ID/locations/LOCATION_ID/cmekConfig`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CmekConfig::class, 'Google_Service_CloudTasks_CmekConfig');
