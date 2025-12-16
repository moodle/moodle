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

class UpgradeDistribution extends \Google\Collection
{
  protected $collection_key = 'cve';
  /**
   * The operating system classification of this Upgrade, as specified by the
   * upstream operating system upgrade feed. For Windows the classification is
   * one of the category_ids listed at https://docs.microsoft.com/en-
   * us/previous-versions/windows/desktop/ff357803(v=vs.85)
   *
   * @var string
   */
  public $classification;
  /**
   * Required - The specific operating system this metadata applies to. See
   * https://cpe.mitre.org/specification/.
   *
   * @var string
   */
  public $cpeUri;
  /**
   * The cve tied to this Upgrade.
   *
   * @var string[]
   */
  public $cve;
  /**
   * The severity as specified by the upstream operating system.
   *
   * @var string
   */
  public $severity;

  /**
   * The operating system classification of this Upgrade, as specified by the
   * upstream operating system upgrade feed. For Windows the classification is
   * one of the category_ids listed at https://docs.microsoft.com/en-
   * us/previous-versions/windows/desktop/ff357803(v=vs.85)
   *
   * @param string $classification
   */
  public function setClassification($classification)
  {
    $this->classification = $classification;
  }
  /**
   * @return string
   */
  public function getClassification()
  {
    return $this->classification;
  }
  /**
   * Required - The specific operating system this metadata applies to. See
   * https://cpe.mitre.org/specification/.
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
   * The cve tied to this Upgrade.
   *
   * @param string[] $cve
   */
  public function setCve($cve)
  {
    $this->cve = $cve;
  }
  /**
   * @return string[]
   */
  public function getCve()
  {
    return $this->cve;
  }
  /**
   * The severity as specified by the upstream operating system.
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeDistribution::class, 'Google_Service_OnDemandScanning_UpgradeDistribution');
