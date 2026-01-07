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

namespace Google\Service\Compute;

class InterconnectDiagnosticsMacsecStatus extends \Google\Model
{
  /**
   * Indicates the Connectivity Association Key Name (CKN) currently being used
   * if MACsec is operational.
   *
   * @var string
   */
  public $ckn;
  /**
   * Indicates whether or not MACsec is operational on this link.
   *
   * @var bool
   */
  public $operational;

  /**
   * Indicates the Connectivity Association Key Name (CKN) currently being used
   * if MACsec is operational.
   *
   * @param string $ckn
   */
  public function setCkn($ckn)
  {
    $this->ckn = $ckn;
  }
  /**
   * @return string
   */
  public function getCkn()
  {
    return $this->ckn;
  }
  /**
   * Indicates whether or not MACsec is operational on this link.
   *
   * @param bool $operational
   */
  public function setOperational($operational)
  {
    $this->operational = $operational;
  }
  /**
   * @return bool
   */
  public function getOperational()
  {
    return $this->operational;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectDiagnosticsMacsecStatus::class, 'Google_Service_Compute_InterconnectDiagnosticsMacsecStatus');
