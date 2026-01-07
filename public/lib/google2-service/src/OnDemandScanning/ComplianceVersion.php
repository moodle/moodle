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

namespace Google\Service\OnDemandScanning;

class ComplianceVersion extends \Google\Model
{
  /**
   * The name of the document that defines this benchmark, e.g. "CIS Container-
   * Optimized OS".
   *
   * @var string
   */
  public $benchmarkDocument;
  /**
   * The CPE URI (https://cpe.mitre.org/specification/) this benchmark is
   * applicable to.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The version of the benchmark. This is set to the version of the OS-specific
   * CIS document the benchmark is defined in.
   *
   * @var string
   */
  public $version;

  /**
   * The name of the document that defines this benchmark, e.g. "CIS Container-
   * Optimized OS".
   *
   * @param string $benchmarkDocument
   */
  public function setBenchmarkDocument($benchmarkDocument)
  {
    $this->benchmarkDocument = $benchmarkDocument;
  }
  /**
   * @return string
   */
  public function getBenchmarkDocument()
  {
    return $this->benchmarkDocument;
  }
  /**
   * The CPE URI (https://cpe.mitre.org/specification/) this benchmark is
   * applicable to.
   *
   * @param string $cpeUri
   */
  public function setCpeUri($cpeUri)
  {
    $this->cpeUri = $cpeUri;
  }
  /**
   * @return string
   */
  public function getCpeUri()
  {
    return $this->cpeUri;
  }
  /**
   * The version of the benchmark. This is set to the version of the OS-specific
   * CIS document the benchmark is defined in.
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
class_alias(ComplianceVersion::class, 'Google_Service_OnDemandScanning_ComplianceVersion');
