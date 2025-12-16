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

namespace Google\Service\PagespeedInsights;

class MetricSavings extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "cLS" => "CLS",
        "fCP" => "FCP",
        "iNP" => "INP",
        "lCP" => "LCP",
        "tBT" => "TBT",
  ];
  /**
   * Optional. Optional numeric value representing the audit's savings for the
   * CLS metric.
   *
   * @var 
   */
  public $cLS;
  /**
   * Optional. Optional numeric value representing the audit's savings for the
   * FCP metric.
   *
   * @var 
   */
  public $fCP;
  /**
   * Optional. Optional numeric value representing the audit's savings for the
   * INP metric.
   *
   * @var 
   */
  public $iNP;
  /**
   * Optional. Optional numeric value representing the audit's savings for the
   * LCP metric.
   *
   * @var 
   */
  public $lCP;
  /**
   * Optional. Optional numeric value representing the audit's savings for the
   * TBT metric.
   *
   * @var 
   */
  public $tBT;

  public function setCLS($cLS)
  {
    $this->cLS = $cLS;
  }
  public function getCLS()
  {
    return $this->cLS;
  }
  public function setFCP($fCP)
  {
    $this->fCP = $fCP;
  }
  public function getFCP()
  {
    return $this->fCP;
  }
  public function setINP($iNP)
  {
    $this->iNP = $iNP;
  }
  public function getINP()
  {
    return $this->iNP;
  }
  public function setLCP($lCP)
  {
    $this->lCP = $lCP;
  }
  public function getLCP()
  {
    return $this->lCP;
  }
  public function setTBT($tBT)
  {
    $this->tBT = $tBT;
  }
  public function getTBT()
  {
    return $this->tBT;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricSavings::class, 'Google_Service_PagespeedInsights_MetricSavings');
