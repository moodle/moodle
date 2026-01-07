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

namespace Google\Service\CloudRedis;

class Compliance extends \Google\Model
{
  /**
   * Industry-wide compliance standards or benchmarks, such as CIS, PCI, and
   * OWASP.
   *
   * @var string
   */
  public $standard;
  /**
   * Version of the standard or benchmark, for example, 1.1
   *
   * @var string
   */
  public $version;

  /**
   * Industry-wide compliance standards or benchmarks, such as CIS, PCI, and
   * OWASP.
   *
   * @param string $standard
   */
  public function setStandard($standard)
  {
    $this->standard = $standard;
  }
  /**
   * @return string
   */
  public function getStandard()
  {
    return $this->standard;
  }
  /**
   * Version of the standard or benchmark, for example, 1.1
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Compliance::class, 'Google_Service_CloudRedis_Compliance');
