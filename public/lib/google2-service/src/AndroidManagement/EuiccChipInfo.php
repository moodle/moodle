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

namespace Google\Service\AndroidManagement;

class EuiccChipInfo extends \Google\Model
{
  /**
   * Output only. The Embedded Identity Document (EID) that identifies the eUICC
   * chip for each eUICC chip on the device. This is available on company owned
   * devices running Android 13 and above.
   *
   * @var string
   */
  public $eid;

  /**
   * Output only. The Embedded Identity Document (EID) that identifies the eUICC
   * chip for each eUICC chip on the device. This is available on company owned
   * devices running Android 13 and above.
   *
   * @param string $eid
   */
  public function setEid($eid)
  {
    $this->eid = $eid;
  }
  /**
   * @return string
   */
  public function getEid()
  {
    return $this->eid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EuiccChipInfo::class, 'Google_Service_AndroidManagement_EuiccChipInfo');
