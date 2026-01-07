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

class Risk extends \Google\Model
{
  protected $cisaKevType = CISAKnownExploitedVulnerabilities::class;
  protected $cisaKevDataType = '';
  protected $epssType = ExploitPredictionScoringSystem::class;
  protected $epssDataType = '';

  /**
   * CISA maintains the authoritative source of vulnerabilities that have been
   * exploited in the wild.
   *
   * @param CISAKnownExploitedVulnerabilities $cisaKev
   */
  public function setCisaKev(CISAKnownExploitedVulnerabilities $cisaKev)
  {
    $this->cisaKev = $cisaKev;
  }
  /**
   * @return CISAKnownExploitedVulnerabilities
   */
  public function getCisaKev()
  {
    return $this->cisaKev;
  }
  /**
   * The Exploit Prediction Scoring System (EPSS) estimates the likelihood
   * (probability) that a software vulnerability will be exploited in the wild.
   *
   * @param ExploitPredictionScoringSystem $epss
   */
  public function setEpss(ExploitPredictionScoringSystem $epss)
  {
    $this->epss = $epss;
  }
  /**
   * @return ExploitPredictionScoringSystem
   */
  public function getEpss()
  {
    return $this->epss;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Risk::class, 'Google_Service_ContainerAnalysis_Risk');
