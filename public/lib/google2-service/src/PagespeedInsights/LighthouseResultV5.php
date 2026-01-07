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

class LighthouseResultV5 extends \Google\Collection
{
  protected $collection_key = 'stackPacks';
  protected $auditsType = LighthouseAuditResultV5::class;
  protected $auditsDataType = 'map';
  protected $categoriesType = Categories::class;
  protected $categoriesDataType = '';
  protected $categoryGroupsType = CategoryGroupV5::class;
  protected $categoryGroupsDataType = 'map';
  protected $configSettingsType = ConfigSettings::class;
  protected $configSettingsDataType = '';
  protected $entitiesType = LhrEntity::class;
  protected $entitiesDataType = 'array';
  protected $environmentType = Environment::class;
  protected $environmentDataType = '';
  /**
   * The time that this run was fetched.
   *
   * @var string
   */
  public $fetchTime;
  /**
   * URL displayed on the page after Lighthouse finishes.
   *
   * @var string
   */
  public $finalDisplayedUrl;
  /**
   * The final resolved url that was audited.
   *
   * @var string
   */
  public $finalUrl;
  /**
   * Screenshot data of the full page, along with node rects relevant to the
   * audit results.
   *
   * @var array
   */
  public $fullPageScreenshot;
  protected $i18nType = I18n::class;
  protected $i18nDataType = '';
  /**
   * The lighthouse version that was used to generate this LHR.
   *
   * @var string
   */
  public $lighthouseVersion;
  /**
   * URL of the main document request of the final navigation.
   *
   * @var string
   */
  public $mainDocumentUrl;
  /**
   * The original requested url.
   *
   * @var string
   */
  public $requestedUrl;
  /**
   * List of all run warnings in the LHR. Will always output to at least `[]`.
   *
   * @var array[]
   */
  public $runWarnings;
  protected $runtimeErrorType = RuntimeError::class;
  protected $runtimeErrorDataType = '';
  protected $stackPacksType = StackPack::class;
  protected $stackPacksDataType = 'array';
  protected $timingType = Timing::class;
  protected $timingDataType = '';
  /**
   * The user agent that was used to run this LHR.
   *
   * @var string
   */
  public $userAgent;

  /**
   * Map of audits in the LHR.
   *
   * @param LighthouseAuditResultV5[] $audits
   */
  public function setAudits($audits)
  {
    $this->audits = $audits;
  }
  /**
   * @return LighthouseAuditResultV5[]
   */
  public function getAudits()
  {
    return $this->audits;
  }
  /**
   * Map of categories in the LHR.
   *
   * @param Categories $categories
   */
  public function setCategories(Categories $categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return Categories
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Map of category groups in the LHR.
   *
   * @param CategoryGroupV5[] $categoryGroups
   */
  public function setCategoryGroups($categoryGroups)
  {
    $this->categoryGroups = $categoryGroups;
  }
  /**
   * @return CategoryGroupV5[]
   */
  public function getCategoryGroups()
  {
    return $this->categoryGroups;
  }
  /**
   * The configuration settings for this LHR.
   *
   * @param ConfigSettings $configSettings
   */
  public function setConfigSettings(ConfigSettings $configSettings)
  {
    $this->configSettings = $configSettings;
  }
  /**
   * @return ConfigSettings
   */
  public function getConfigSettings()
  {
    return $this->configSettings;
  }
  /**
   * Entity classification data.
   *
   * @param LhrEntity[] $entities
   */
  public function setEntities($entities)
  {
    $this->entities = $entities;
  }
  /**
   * @return LhrEntity[]
   */
  public function getEntities()
  {
    return $this->entities;
  }
  /**
   * Environment settings that were used when making this LHR.
   *
   * @param Environment $environment
   */
  public function setEnvironment(Environment $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return Environment
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * The time that this run was fetched.
   *
   * @param string $fetchTime
   */
  public function setFetchTime($fetchTime)
  {
    $this->fetchTime = $fetchTime;
  }
  /**
   * @return string
   */
  public function getFetchTime()
  {
    return $this->fetchTime;
  }
  /**
   * URL displayed on the page after Lighthouse finishes.
   *
   * @param string $finalDisplayedUrl
   */
  public function setFinalDisplayedUrl($finalDisplayedUrl)
  {
    $this->finalDisplayedUrl = $finalDisplayedUrl;
  }
  /**
   * @return string
   */
  public function getFinalDisplayedUrl()
  {
    return $this->finalDisplayedUrl;
  }
  /**
   * The final resolved url that was audited.
   *
   * @param string $finalUrl
   */
  public function setFinalUrl($finalUrl)
  {
    $this->finalUrl = $finalUrl;
  }
  /**
   * @return string
   */
  public function getFinalUrl()
  {
    return $this->finalUrl;
  }
  /**
   * Screenshot data of the full page, along with node rects relevant to the
   * audit results.
   *
   * @param array $fullPageScreenshot
   */
  public function setFullPageScreenshot($fullPageScreenshot)
  {
    $this->fullPageScreenshot = $fullPageScreenshot;
  }
  /**
   * @return array
   */
  public function getFullPageScreenshot()
  {
    return $this->fullPageScreenshot;
  }
  /**
   * The internationalization strings that are required to render the LHR.
   *
   * @param I18n $i18n
   */
  public function setI18n(I18n $i18n)
  {
    $this->i18n = $i18n;
  }
  /**
   * @return I18n
   */
  public function getI18n()
  {
    return $this->i18n;
  }
  /**
   * The lighthouse version that was used to generate this LHR.
   *
   * @param string $lighthouseVersion
   */
  public function setLighthouseVersion($lighthouseVersion)
  {
    $this->lighthouseVersion = $lighthouseVersion;
  }
  /**
   * @return string
   */
  public function getLighthouseVersion()
  {
    return $this->lighthouseVersion;
  }
  /**
   * URL of the main document request of the final navigation.
   *
   * @param string $mainDocumentUrl
   */
  public function setMainDocumentUrl($mainDocumentUrl)
  {
    $this->mainDocumentUrl = $mainDocumentUrl;
  }
  /**
   * @return string
   */
  public function getMainDocumentUrl()
  {
    return $this->mainDocumentUrl;
  }
  /**
   * The original requested url.
   *
   * @param string $requestedUrl
   */
  public function setRequestedUrl($requestedUrl)
  {
    $this->requestedUrl = $requestedUrl;
  }
  /**
   * @return string
   */
  public function getRequestedUrl()
  {
    return $this->requestedUrl;
  }
  /**
   * List of all run warnings in the LHR. Will always output to at least `[]`.
   *
   * @param array[] $runWarnings
   */
  public function setRunWarnings($runWarnings)
  {
    $this->runWarnings = $runWarnings;
  }
  /**
   * @return array[]
   */
  public function getRunWarnings()
  {
    return $this->runWarnings;
  }
  /**
   * A top-level error message that, if present, indicates a serious enough
   * problem that this Lighthouse result may need to be discarded.
   *
   * @param RuntimeError $runtimeError
   */
  public function setRuntimeError(RuntimeError $runtimeError)
  {
    $this->runtimeError = $runtimeError;
  }
  /**
   * @return RuntimeError
   */
  public function getRuntimeError()
  {
    return $this->runtimeError;
  }
  /**
   * The Stack Pack advice strings.
   *
   * @param StackPack[] $stackPacks
   */
  public function setStackPacks($stackPacks)
  {
    $this->stackPacks = $stackPacks;
  }
  /**
   * @return StackPack[]
   */
  public function getStackPacks()
  {
    return $this->stackPacks;
  }
  /**
   * Timing information for this LHR.
   *
   * @param Timing $timing
   */
  public function setTiming(Timing $timing)
  {
    $this->timing = $timing;
  }
  /**
   * @return Timing
   */
  public function getTiming()
  {
    return $this->timing;
  }
  /**
   * The user agent that was used to run this LHR.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LighthouseResultV5::class, 'Google_Service_PagespeedInsights_LighthouseResultV5');
