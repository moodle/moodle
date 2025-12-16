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

class PagespeedApiPagespeedResponseV5 extends \Google\Model
{
  /**
   * The UTC timestamp of this analysis.
   *
   * @var string
   */
  public $analysisUTCTimestamp;
  /**
   * The captcha verify result
   *
   * @var string
   */
  public $captchaResult;
  /**
   * Canonicalized and final URL for the document, after following page
   * redirects (if any).
   *
   * @var string
   */
  public $id;
  /**
   * Kind of result.
   *
   * @var string
   */
  public $kind;
  protected $lighthouseResultType = LighthouseResultV5::class;
  protected $lighthouseResultDataType = '';
  protected $loadingExperienceType = PagespeedApiLoadingExperienceV5::class;
  protected $loadingExperienceDataType = '';
  protected $originLoadingExperienceType = PagespeedApiLoadingExperienceV5::class;
  protected $originLoadingExperienceDataType = '';
  protected $versionType = PagespeedVersion::class;
  protected $versionDataType = '';

  /**
   * The UTC timestamp of this analysis.
   *
   * @param string $analysisUTCTimestamp
   */
  public function setAnalysisUTCTimestamp($analysisUTCTimestamp)
  {
    $this->analysisUTCTimestamp = $analysisUTCTimestamp;
  }
  /**
   * @return string
   */
  public function getAnalysisUTCTimestamp()
  {
    return $this->analysisUTCTimestamp;
  }
  /**
   * The captcha verify result
   *
   * @param string $captchaResult
   */
  public function setCaptchaResult($captchaResult)
  {
    $this->captchaResult = $captchaResult;
  }
  /**
   * @return string
   */
  public function getCaptchaResult()
  {
    return $this->captchaResult;
  }
  /**
   * Canonicalized and final URL for the document, after following page
   * redirects (if any).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Kind of result.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Lighthouse response for the audit url as an object.
   *
   * @param LighthouseResultV5 $lighthouseResult
   */
  public function setLighthouseResult(LighthouseResultV5 $lighthouseResult)
  {
    $this->lighthouseResult = $lighthouseResult;
  }
  /**
   * @return LighthouseResultV5
   */
  public function getLighthouseResult()
  {
    return $this->lighthouseResult;
  }
  /**
   * Metrics of end users' page loading experience.
   *
   * @param PagespeedApiLoadingExperienceV5 $loadingExperience
   */
  public function setLoadingExperience(PagespeedApiLoadingExperienceV5 $loadingExperience)
  {
    $this->loadingExperience = $loadingExperience;
  }
  /**
   * @return PagespeedApiLoadingExperienceV5
   */
  public function getLoadingExperience()
  {
    return $this->loadingExperience;
  }
  /**
   * Metrics of the aggregated page loading experience of the origin
   *
   * @param PagespeedApiLoadingExperienceV5 $originLoadingExperience
   */
  public function setOriginLoadingExperience(PagespeedApiLoadingExperienceV5 $originLoadingExperience)
  {
    $this->originLoadingExperience = $originLoadingExperience;
  }
  /**
   * @return PagespeedApiLoadingExperienceV5
   */
  public function getOriginLoadingExperience()
  {
    return $this->originLoadingExperience;
  }
  /**
   * The version of PageSpeed used to generate these results.
   *
   * @param PagespeedVersion $version
   */
  public function setVersion(PagespeedVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return PagespeedVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PagespeedApiPagespeedResponseV5::class, 'Google_Service_PagespeedInsights_PagespeedApiPagespeedResponseV5');
