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

namespace Google\Service\CertificateAuthorityService;

class IssuanceModes extends \Google\Model
{
  /**
   * Optional. When true, allows callers to create Certificates by specifying a
   * CertificateConfig.
   *
   * @var bool
   */
  public $allowConfigBasedIssuance;
  /**
   * Optional. When true, allows callers to create Certificates by specifying a
   * CSR.
   *
   * @var bool
   */
  public $allowCsrBasedIssuance;

  /**
   * Optional. When true, allows callers to create Certificates by specifying a
   * CertificateConfig.
   *
   * @param bool $allowConfigBasedIssuance
   */
  public function setAllowConfigBasedIssuance($allowConfigBasedIssuance)
  {
    $this->allowConfigBasedIssuance = $allowConfigBasedIssuance;
  }
  /**
   * @return bool
   */
  public function getAllowConfigBasedIssuance()
  {
    return $this->allowConfigBasedIssuance;
  }
  /**
   * Optional. When true, allows callers to create Certificates by specifying a
   * CSR.
   *
   * @param bool $allowCsrBasedIssuance
   */
  public function setAllowCsrBasedIssuance($allowCsrBasedIssuance)
  {
    $this->allowCsrBasedIssuance = $allowCsrBasedIssuance;
  }
  /**
   * @return bool
   */
  public function getAllowCsrBasedIssuance()
  {
    return $this->allowCsrBasedIssuance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IssuanceModes::class, 'Google_Service_CertificateAuthorityService_IssuanceModes');
