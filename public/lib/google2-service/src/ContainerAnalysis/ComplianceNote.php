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

namespace Google\Service\ContainerAnalysis;

class ComplianceNote extends \Google\Collection
{
  protected $collection_key = 'version';
  protected $cisBenchmarkType = CisBenchmark::class;
  protected $cisBenchmarkDataType = '';
  /**
   * A description about this compliance check.
   *
   * @var string
   */
  public $description;
  /**
   * @var string
   */
  public $impact;
  /**
   * A rationale for the existence of this compliance check.
   *
   * @var string
   */
  public $rationale;
  /**
   * A description of remediation steps if the compliance check fails.
   *
   * @var string
   */
  public $remediation;
  /**
   * Serialized scan instructions with a predefined format.
   *
   * @var string
   */
  public $scanInstructions;
  /**
   * The title that identifies this compliance check.
   *
   * @var string
   */
  public $title;
  protected $versionType = ComplianceVersion::class;
  protected $versionDataType = 'array';

  /**
   * @param CisBenchmark $cisBenchmark
   */
  public function setCisBenchmark(CisBenchmark $cisBenchmark)
  {
    $this->cisBenchmark = $cisBenchmark;
  }
  /**
   * @return CisBenchmark
   */
  public function getCisBenchmark()
  {
    return $this->cisBenchmark;
  }
  /**
   * A description about this compliance check.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param string $impact
   */
  public function setImpact($impact)
  {
    $this->impact = $impact;
  }
  /**
   * @return string
   */
  public function getImpact()
  {
    return $this->impact;
  }
  /**
   * A rationale for the existence of this compliance check.
   *
   * @param string $rationale
   */
  public function setRationale($rationale)
  {
    $this->rationale = $rationale;
  }
  /**
   * @return string
   */
  public function getRationale()
  {
    return $this->rationale;
  }
  /**
   * A description of remediation steps if the compliance check fails.
   *
   * @param string $remediation
   */
  public function setRemediation($remediation)
  {
    $this->remediation = $remediation;
  }
  /**
   * @return string
   */
  public function getRemediation()
  {
    return $this->remediation;
  }
  /**
   * Serialized scan instructions with a predefined format.
   *
   * @param string $scanInstructions
   */
  public function setScanInstructions($scanInstructions)
  {
    $this->scanInstructions = $scanInstructions;
  }
  /**
   * @return string
   */
  public function getScanInstructions()
  {
    return $this->scanInstructions;
  }
  /**
   * The title that identifies this compliance check.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The OS and config versions the benchmark applies to.
   *
   * @param ComplianceVersion[] $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return ComplianceVersion[]
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComplianceNote::class, 'Google_Service_ContainerAnalysis_ComplianceNote');
