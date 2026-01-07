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

namespace Google\Service\Script;

class GoogleAppsScriptTypeAddOnEntryPoint extends \Google\Model
{
  /**
   * Default value, unknown add-on type.
   */
  public const ADD_ON_TYPE_UNKNOWN_ADDON_TYPE = 'UNKNOWN_ADDON_TYPE';
  /**
   * Add-on type for Gmail.
   */
  public const ADD_ON_TYPE_GMAIL = 'GMAIL';
  /**
   * Add-on type for Data Studio.
   */
  public const ADD_ON_TYPE_DATA_STUDIO = 'DATA_STUDIO';
  /**
   * The add-on's required list of supported container types.
   *
   * @var string
   */
  public $addOnType;
  /**
   * The add-on's optional description.
   *
   * @var string
   */
  public $description;
  /**
   * The add-on's optional help URL.
   *
   * @var string
   */
  public $helpUrl;
  /**
   * The add-on's required post install tip URL.
   *
   * @var string
   */
  public $postInstallTipUrl;
  /**
   * The add-on's optional report issue URL.
   *
   * @var string
   */
  public $reportIssueUrl;
  /**
   * The add-on's required title.
   *
   * @var string
   */
  public $title;

  /**
   * The add-on's required list of supported container types.
   *
   * Accepted values: UNKNOWN_ADDON_TYPE, GMAIL, DATA_STUDIO
   *
   * @param self::ADD_ON_TYPE_* $addOnType
   */
  public function setAddOnType($addOnType)
  {
    $this->addOnType = $addOnType;
  }
  /**
   * @return self::ADD_ON_TYPE_*
   */
  public function getAddOnType()
  {
    return $this->addOnType;
  }
  /**
   * The add-on's optional description.
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
   * The add-on's optional help URL.
   *
   * @param string $helpUrl
   */
  public function setHelpUrl($helpUrl)
  {
    $this->helpUrl = $helpUrl;
  }
  /**
   * @return string
   */
  public function getHelpUrl()
  {
    return $this->helpUrl;
  }
  /**
   * The add-on's required post install tip URL.
   *
   * @param string $postInstallTipUrl
   */
  public function setPostInstallTipUrl($postInstallTipUrl)
  {
    $this->postInstallTipUrl = $postInstallTipUrl;
  }
  /**
   * @return string
   */
  public function getPostInstallTipUrl()
  {
    return $this->postInstallTipUrl;
  }
  /**
   * The add-on's optional report issue URL.
   *
   * @param string $reportIssueUrl
   */
  public function setReportIssueUrl($reportIssueUrl)
  {
    $this->reportIssueUrl = $reportIssueUrl;
  }
  /**
   * @return string
   */
  public function getReportIssueUrl()
  {
    return $this->reportIssueUrl;
  }
  /**
   * The add-on's required title.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsScriptTypeAddOnEntryPoint::class, 'Google_Service_Script_GoogleAppsScriptTypeAddOnEntryPoint');
