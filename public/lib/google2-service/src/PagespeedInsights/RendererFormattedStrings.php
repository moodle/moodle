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

class RendererFormattedStrings extends \Google\Model
{
  /**
   * The tooltip text on an expandable chevron icon.
   *
   * @var string
   */
  public $auditGroupExpandTooltip;
  /**
   * Text link pointing to the Lighthouse scoring calculator. This link
   * immediately follows a sentence stating the performance score is calculated
   * from the perf metrics.
   *
   * @var string
   */
  public $calculatorLink;
  /**
   * The label for the initial request in a critical request chain.
   *
   * @var string
   */
  public $crcInitialNavigation;
  /**
   * The label for values shown in the summary of critical request chains.
   *
   * @var string
   */
  public $crcLongestDurationLabel;
  /**
   * Option in a dropdown menu that copies the Lighthouse JSON object to the
   * system clipboard.
   *
   * @var string
   */
  public $dropdownCopyJSON;
  /**
   * Option in a dropdown menu that toggles the themeing of the report between
   * Light(default) and Dark themes.
   *
   * @var string
   */
  public $dropdownDarkTheme;
  /**
   * Option in a dropdown menu that opens a full Lighthouse report in a print
   * dialog.
   *
   * @var string
   */
  public $dropdownPrintExpanded;
  /**
   * Option in a dropdown menu that opens a small, summary report in a print
   * dialog.
   *
   * @var string
   */
  public $dropdownPrintSummary;
  /**
   * Option in a dropdown menu that saves the current report as a new GitHub
   * Gist.
   *
   * @var string
   */
  public $dropdownSaveGist;
  /**
   * Option in a dropdown menu that saves the Lighthouse report HTML locally to
   * the system as a '.html' file.
   *
   * @var string
   */
  public $dropdownSaveHTML;
  /**
   * Option in a dropdown menu that saves the Lighthouse JSON object to the
   * local system as a '.json' file.
   *
   * @var string
   */
  public $dropdownSaveJSON;
  /**
   * Option in a dropdown menu that opens the current report in the Lighthouse
   * Viewer Application.
   *
   * @var string
   */
  public $dropdownViewer;
  /**
   * The label shown next to an audit or metric that has had an error.
   *
   * @var string
   */
  public $errorLabel;
  /**
   * The error string shown next to an erroring audit.
   *
   * @var string
   */
  public $errorMissingAuditInfo;
  /**
   * Label for button to create an issue against the Lighthouse GitHub project.
   *
   * @var string
   */
  public $footerIssue;
  /**
   * The title of the lab data performance category.
   *
   * @var string
   */
  public $labDataTitle;
  /**
   * The disclaimer shown under performance explaining that the network can
   * vary.
   *
   * @var string
   */
  public $lsPerformanceCategoryDescription;
  /**
   * The heading shown above a list of audits that were not computerd in the
   * run.
   *
   * @var string
   */
  public $manualAuditsGroupTitle;
  /**
   * The heading shown above a list of audits that do not apply to a page.
   *
   * @var string
   */
  public $notApplicableAuditsGroupTitle;
  /**
   * The heading for the estimated page load savings opportunity of an audit.
   *
   * @var string
   */
  public $opportunityResourceColumnLabel;
  /**
   * The heading for the estimated page load savings of opportunity audits.
   *
   * @var string
   */
  public $opportunitySavingsColumnLabel;
  /**
   * The heading that is shown above a list of audits that are passing.
   *
   * @var string
   */
  public $passedAuditsGroupTitle;
  /**
   * Descriptive explanation for emulation setting when emulating a generic
   * desktop form factor, as opposed to a mobile-device like form factor.
   *
   * @var string
   */
  public $runtimeDesktopEmulation;
  /**
   * Descriptive explanation for emulation setting when emulating a Nexus 5X
   * mobile device.
   *
   * @var string
   */
  public $runtimeMobileEmulation;
  /**
   * Descriptive explanation for emulation setting when no device emulation is
   * set.
   *
   * @var string
   */
  public $runtimeNoEmulation;
  /**
   * Label for a row in a table that shows the version of the Axe library used
   *
   * @var string
   */
  public $runtimeSettingsAxeVersion;
  /**
   * Label for a row in a table that shows the estimated CPU power of the
   * machine running Lighthouse. Example row values: 532, 1492, 783.
   *
   * @var string
   */
  public $runtimeSettingsBenchmark;
  /**
   * Label for a row in a table that describes the CPU throttling conditions
   * that were used during a Lighthouse run, if any.
   *
   * @var string
   */
  public $runtimeSettingsCPUThrottling;
  /**
   * Label for a row in a table that shows in what tool Lighthouse is being run
   * (e.g. The lighthouse CLI, Chrome DevTools, Lightrider, WebPageTest, etc).
   *
   * @var string
   */
  public $runtimeSettingsChannel;
  /**
   * Label for a row in a table that describes the kind of device that was
   * emulated for the Lighthouse run. Example values for row elements: 'No
   * Emulation', 'Emulated Desktop', etc.
   *
   * @var string
   */
  public $runtimeSettingsDevice;
  /**
   * Label for a row in a table that shows the time at which a Lighthouse run
   * was conducted; formatted as a timestamp, e.g. Jan 1, 1970 12:00 AM UTC.
   *
   * @var string
   */
  public $runtimeSettingsFetchTime;
  /**
   * Label for a row in a table that describes the network throttling conditions
   * that were used during a Lighthouse run, if any.
   *
   * @var string
   */
  public $runtimeSettingsNetworkThrottling;
  /**
   * Title of the Runtime settings table in a Lighthouse report. Runtime
   * settings are the environment configurations that a specific report used at
   * auditing time.
   *
   * @var string
   */
  public $runtimeSettingsTitle;
  /**
   * Label for a row in a table that shows the User Agent that was detected on
   * the Host machine that ran Lighthouse.
   *
   * @var string
   */
  public $runtimeSettingsUA;
  /**
   * Label for a row in a table that shows the User Agent that was used to send
   * out all network requests during the Lighthouse run.
   *
   * @var string
   */
  public $runtimeSettingsUANetwork;
  /**
   * Label for a row in a table that shows the URL that was audited during a
   * Lighthouse run.
   *
   * @var string
   */
  public $runtimeSettingsUrl;
  /**
   * Descriptive explanation for a runtime setting that is set to an unknown
   * value.
   *
   * @var string
   */
  public $runtimeUnknown;
  /**
   * The label that explains the score gauges scale (0-49, 50-89, 90-100).
   *
   * @var string
   */
  public $scorescaleLabel;
  /**
   * Label preceding a radio control for filtering the list of audits. The radio
   * choices are various performance metrics (FCP, LCP, TBT), and if chosen, the
   * audits in the report are hidden if they are not relevant to the selected
   * metric.
   *
   * @var string
   */
  public $showRelevantAudits;
  /**
   * The label for the button to show only a few lines of a snippet
   *
   * @var string
   */
  public $snippetCollapseButtonLabel;
  /**
   * The label for the button to show all lines of a snippet
   *
   * @var string
   */
  public $snippetExpandButtonLabel;
  /**
   * This label is for a filter checkbox above a table of items
   *
   * @var string
   */
  public $thirdPartyResourcesLabel;
  /**
   * Descriptive explanation for environment throttling that was provided by the
   * runtime environment instead of provided by Lighthouse throttling.
   *
   * @var string
   */
  public $throttlingProvided;
  /**
   * The label shown preceding important warnings that may have invalidated an
   * entire report.
   *
   * @var string
   */
  public $toplevelWarningsMessage;
  /**
   * The disclaimer shown below a performance metric value.
   *
   * @var string
   */
  public $varianceDisclaimer;
  /**
   * Label for a button that opens the Treemap App
   *
   * @var string
   */
  public $viewTreemapLabel;
  /**
   * The heading that is shown above a list of audits that have warnings
   *
   * @var string
   */
  public $warningAuditsGroupTitle;
  /**
   * The label shown above a bulleted list of warnings.
   *
   * @var string
   */
  public $warningHeader;

  /**
   * The tooltip text on an expandable chevron icon.
   *
   * @param string $auditGroupExpandTooltip
   */
  public function setAuditGroupExpandTooltip($auditGroupExpandTooltip)
  {
    $this->auditGroupExpandTooltip = $auditGroupExpandTooltip;
  }
  /**
   * @return string
   */
  public function getAuditGroupExpandTooltip()
  {
    return $this->auditGroupExpandTooltip;
  }
  /**
   * Text link pointing to the Lighthouse scoring calculator. This link
   * immediately follows a sentence stating the performance score is calculated
   * from the perf metrics.
   *
   * @param string $calculatorLink
   */
  public function setCalculatorLink($calculatorLink)
  {
    $this->calculatorLink = $calculatorLink;
  }
  /**
   * @return string
   */
  public function getCalculatorLink()
  {
    return $this->calculatorLink;
  }
  /**
   * The label for the initial request in a critical request chain.
   *
   * @param string $crcInitialNavigation
   */
  public function setCrcInitialNavigation($crcInitialNavigation)
  {
    $this->crcInitialNavigation = $crcInitialNavigation;
  }
  /**
   * @return string
   */
  public function getCrcInitialNavigation()
  {
    return $this->crcInitialNavigation;
  }
  /**
   * The label for values shown in the summary of critical request chains.
   *
   * @param string $crcLongestDurationLabel
   */
  public function setCrcLongestDurationLabel($crcLongestDurationLabel)
  {
    $this->crcLongestDurationLabel = $crcLongestDurationLabel;
  }
  /**
   * @return string
   */
  public function getCrcLongestDurationLabel()
  {
    return $this->crcLongestDurationLabel;
  }
  /**
   * Option in a dropdown menu that copies the Lighthouse JSON object to the
   * system clipboard.
   *
   * @param string $dropdownCopyJSON
   */
  public function setDropdownCopyJSON($dropdownCopyJSON)
  {
    $this->dropdownCopyJSON = $dropdownCopyJSON;
  }
  /**
   * @return string
   */
  public function getDropdownCopyJSON()
  {
    return $this->dropdownCopyJSON;
  }
  /**
   * Option in a dropdown menu that toggles the themeing of the report between
   * Light(default) and Dark themes.
   *
   * @param string $dropdownDarkTheme
   */
  public function setDropdownDarkTheme($dropdownDarkTheme)
  {
    $this->dropdownDarkTheme = $dropdownDarkTheme;
  }
  /**
   * @return string
   */
  public function getDropdownDarkTheme()
  {
    return $this->dropdownDarkTheme;
  }
  /**
   * Option in a dropdown menu that opens a full Lighthouse report in a print
   * dialog.
   *
   * @param string $dropdownPrintExpanded
   */
  public function setDropdownPrintExpanded($dropdownPrintExpanded)
  {
    $this->dropdownPrintExpanded = $dropdownPrintExpanded;
  }
  /**
   * @return string
   */
  public function getDropdownPrintExpanded()
  {
    return $this->dropdownPrintExpanded;
  }
  /**
   * Option in a dropdown menu that opens a small, summary report in a print
   * dialog.
   *
   * @param string $dropdownPrintSummary
   */
  public function setDropdownPrintSummary($dropdownPrintSummary)
  {
    $this->dropdownPrintSummary = $dropdownPrintSummary;
  }
  /**
   * @return string
   */
  public function getDropdownPrintSummary()
  {
    return $this->dropdownPrintSummary;
  }
  /**
   * Option in a dropdown menu that saves the current report as a new GitHub
   * Gist.
   *
   * @param string $dropdownSaveGist
   */
  public function setDropdownSaveGist($dropdownSaveGist)
  {
    $this->dropdownSaveGist = $dropdownSaveGist;
  }
  /**
   * @return string
   */
  public function getDropdownSaveGist()
  {
    return $this->dropdownSaveGist;
  }
  /**
   * Option in a dropdown menu that saves the Lighthouse report HTML locally to
   * the system as a '.html' file.
   *
   * @param string $dropdownSaveHTML
   */
  public function setDropdownSaveHTML($dropdownSaveHTML)
  {
    $this->dropdownSaveHTML = $dropdownSaveHTML;
  }
  /**
   * @return string
   */
  public function getDropdownSaveHTML()
  {
    return $this->dropdownSaveHTML;
  }
  /**
   * Option in a dropdown menu that saves the Lighthouse JSON object to the
   * local system as a '.json' file.
   *
   * @param string $dropdownSaveJSON
   */
  public function setDropdownSaveJSON($dropdownSaveJSON)
  {
    $this->dropdownSaveJSON = $dropdownSaveJSON;
  }
  /**
   * @return string
   */
  public function getDropdownSaveJSON()
  {
    return $this->dropdownSaveJSON;
  }
  /**
   * Option in a dropdown menu that opens the current report in the Lighthouse
   * Viewer Application.
   *
   * @param string $dropdownViewer
   */
  public function setDropdownViewer($dropdownViewer)
  {
    $this->dropdownViewer = $dropdownViewer;
  }
  /**
   * @return string
   */
  public function getDropdownViewer()
  {
    return $this->dropdownViewer;
  }
  /**
   * The label shown next to an audit or metric that has had an error.
   *
   * @param string $errorLabel
   */
  public function setErrorLabel($errorLabel)
  {
    $this->errorLabel = $errorLabel;
  }
  /**
   * @return string
   */
  public function getErrorLabel()
  {
    return $this->errorLabel;
  }
  /**
   * The error string shown next to an erroring audit.
   *
   * @param string $errorMissingAuditInfo
   */
  public function setErrorMissingAuditInfo($errorMissingAuditInfo)
  {
    $this->errorMissingAuditInfo = $errorMissingAuditInfo;
  }
  /**
   * @return string
   */
  public function getErrorMissingAuditInfo()
  {
    return $this->errorMissingAuditInfo;
  }
  /**
   * Label for button to create an issue against the Lighthouse GitHub project.
   *
   * @param string $footerIssue
   */
  public function setFooterIssue($footerIssue)
  {
    $this->footerIssue = $footerIssue;
  }
  /**
   * @return string
   */
  public function getFooterIssue()
  {
    return $this->footerIssue;
  }
  /**
   * The title of the lab data performance category.
   *
   * @param string $labDataTitle
   */
  public function setLabDataTitle($labDataTitle)
  {
    $this->labDataTitle = $labDataTitle;
  }
  /**
   * @return string
   */
  public function getLabDataTitle()
  {
    return $this->labDataTitle;
  }
  /**
   * The disclaimer shown under performance explaining that the network can
   * vary.
   *
   * @param string $lsPerformanceCategoryDescription
   */
  public function setLsPerformanceCategoryDescription($lsPerformanceCategoryDescription)
  {
    $this->lsPerformanceCategoryDescription = $lsPerformanceCategoryDescription;
  }
  /**
   * @return string
   */
  public function getLsPerformanceCategoryDescription()
  {
    return $this->lsPerformanceCategoryDescription;
  }
  /**
   * The heading shown above a list of audits that were not computerd in the
   * run.
   *
   * @param string $manualAuditsGroupTitle
   */
  public function setManualAuditsGroupTitle($manualAuditsGroupTitle)
  {
    $this->manualAuditsGroupTitle = $manualAuditsGroupTitle;
  }
  /**
   * @return string
   */
  public function getManualAuditsGroupTitle()
  {
    return $this->manualAuditsGroupTitle;
  }
  /**
   * The heading shown above a list of audits that do not apply to a page.
   *
   * @param string $notApplicableAuditsGroupTitle
   */
  public function setNotApplicableAuditsGroupTitle($notApplicableAuditsGroupTitle)
  {
    $this->notApplicableAuditsGroupTitle = $notApplicableAuditsGroupTitle;
  }
  /**
   * @return string
   */
  public function getNotApplicableAuditsGroupTitle()
  {
    return $this->notApplicableAuditsGroupTitle;
  }
  /**
   * The heading for the estimated page load savings opportunity of an audit.
   *
   * @param string $opportunityResourceColumnLabel
   */
  public function setOpportunityResourceColumnLabel($opportunityResourceColumnLabel)
  {
    $this->opportunityResourceColumnLabel = $opportunityResourceColumnLabel;
  }
  /**
   * @return string
   */
  public function getOpportunityResourceColumnLabel()
  {
    return $this->opportunityResourceColumnLabel;
  }
  /**
   * The heading for the estimated page load savings of opportunity audits.
   *
   * @param string $opportunitySavingsColumnLabel
   */
  public function setOpportunitySavingsColumnLabel($opportunitySavingsColumnLabel)
  {
    $this->opportunitySavingsColumnLabel = $opportunitySavingsColumnLabel;
  }
  /**
   * @return string
   */
  public function getOpportunitySavingsColumnLabel()
  {
    return $this->opportunitySavingsColumnLabel;
  }
  /**
   * The heading that is shown above a list of audits that are passing.
   *
   * @param string $passedAuditsGroupTitle
   */
  public function setPassedAuditsGroupTitle($passedAuditsGroupTitle)
  {
    $this->passedAuditsGroupTitle = $passedAuditsGroupTitle;
  }
  /**
   * @return string
   */
  public function getPassedAuditsGroupTitle()
  {
    return $this->passedAuditsGroupTitle;
  }
  /**
   * Descriptive explanation for emulation setting when emulating a generic
   * desktop form factor, as opposed to a mobile-device like form factor.
   *
   * @param string $runtimeDesktopEmulation
   */
  public function setRuntimeDesktopEmulation($runtimeDesktopEmulation)
  {
    $this->runtimeDesktopEmulation = $runtimeDesktopEmulation;
  }
  /**
   * @return string
   */
  public function getRuntimeDesktopEmulation()
  {
    return $this->runtimeDesktopEmulation;
  }
  /**
   * Descriptive explanation for emulation setting when emulating a Nexus 5X
   * mobile device.
   *
   * @param string $runtimeMobileEmulation
   */
  public function setRuntimeMobileEmulation($runtimeMobileEmulation)
  {
    $this->runtimeMobileEmulation = $runtimeMobileEmulation;
  }
  /**
   * @return string
   */
  public function getRuntimeMobileEmulation()
  {
    return $this->runtimeMobileEmulation;
  }
  /**
   * Descriptive explanation for emulation setting when no device emulation is
   * set.
   *
   * @param string $runtimeNoEmulation
   */
  public function setRuntimeNoEmulation($runtimeNoEmulation)
  {
    $this->runtimeNoEmulation = $runtimeNoEmulation;
  }
  /**
   * @return string
   */
  public function getRuntimeNoEmulation()
  {
    return $this->runtimeNoEmulation;
  }
  /**
   * Label for a row in a table that shows the version of the Axe library used
   *
   * @param string $runtimeSettingsAxeVersion
   */
  public function setRuntimeSettingsAxeVersion($runtimeSettingsAxeVersion)
  {
    $this->runtimeSettingsAxeVersion = $runtimeSettingsAxeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsAxeVersion()
  {
    return $this->runtimeSettingsAxeVersion;
  }
  /**
   * Label for a row in a table that shows the estimated CPU power of the
   * machine running Lighthouse. Example row values: 532, 1492, 783.
   *
   * @param string $runtimeSettingsBenchmark
   */
  public function setRuntimeSettingsBenchmark($runtimeSettingsBenchmark)
  {
    $this->runtimeSettingsBenchmark = $runtimeSettingsBenchmark;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsBenchmark()
  {
    return $this->runtimeSettingsBenchmark;
  }
  /**
   * Label for a row in a table that describes the CPU throttling conditions
   * that were used during a Lighthouse run, if any.
   *
   * @param string $runtimeSettingsCPUThrottling
   */
  public function setRuntimeSettingsCPUThrottling($runtimeSettingsCPUThrottling)
  {
    $this->runtimeSettingsCPUThrottling = $runtimeSettingsCPUThrottling;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsCPUThrottling()
  {
    return $this->runtimeSettingsCPUThrottling;
  }
  /**
   * Label for a row in a table that shows in what tool Lighthouse is being run
   * (e.g. The lighthouse CLI, Chrome DevTools, Lightrider, WebPageTest, etc).
   *
   * @param string $runtimeSettingsChannel
   */
  public function setRuntimeSettingsChannel($runtimeSettingsChannel)
  {
    $this->runtimeSettingsChannel = $runtimeSettingsChannel;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsChannel()
  {
    return $this->runtimeSettingsChannel;
  }
  /**
   * Label for a row in a table that describes the kind of device that was
   * emulated for the Lighthouse run. Example values for row elements: 'No
   * Emulation', 'Emulated Desktop', etc.
   *
   * @param string $runtimeSettingsDevice
   */
  public function setRuntimeSettingsDevice($runtimeSettingsDevice)
  {
    $this->runtimeSettingsDevice = $runtimeSettingsDevice;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsDevice()
  {
    return $this->runtimeSettingsDevice;
  }
  /**
   * Label for a row in a table that shows the time at which a Lighthouse run
   * was conducted; formatted as a timestamp, e.g. Jan 1, 1970 12:00 AM UTC.
   *
   * @param string $runtimeSettingsFetchTime
   */
  public function setRuntimeSettingsFetchTime($runtimeSettingsFetchTime)
  {
    $this->runtimeSettingsFetchTime = $runtimeSettingsFetchTime;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsFetchTime()
  {
    return $this->runtimeSettingsFetchTime;
  }
  /**
   * Label for a row in a table that describes the network throttling conditions
   * that were used during a Lighthouse run, if any.
   *
   * @param string $runtimeSettingsNetworkThrottling
   */
  public function setRuntimeSettingsNetworkThrottling($runtimeSettingsNetworkThrottling)
  {
    $this->runtimeSettingsNetworkThrottling = $runtimeSettingsNetworkThrottling;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsNetworkThrottling()
  {
    return $this->runtimeSettingsNetworkThrottling;
  }
  /**
   * Title of the Runtime settings table in a Lighthouse report. Runtime
   * settings are the environment configurations that a specific report used at
   * auditing time.
   *
   * @param string $runtimeSettingsTitle
   */
  public function setRuntimeSettingsTitle($runtimeSettingsTitle)
  {
    $this->runtimeSettingsTitle = $runtimeSettingsTitle;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsTitle()
  {
    return $this->runtimeSettingsTitle;
  }
  /**
   * Label for a row in a table that shows the User Agent that was detected on
   * the Host machine that ran Lighthouse.
   *
   * @param string $runtimeSettingsUA
   */
  public function setRuntimeSettingsUA($runtimeSettingsUA)
  {
    $this->runtimeSettingsUA = $runtimeSettingsUA;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsUA()
  {
    return $this->runtimeSettingsUA;
  }
  /**
   * Label for a row in a table that shows the User Agent that was used to send
   * out all network requests during the Lighthouse run.
   *
   * @param string $runtimeSettingsUANetwork
   */
  public function setRuntimeSettingsUANetwork($runtimeSettingsUANetwork)
  {
    $this->runtimeSettingsUANetwork = $runtimeSettingsUANetwork;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsUANetwork()
  {
    return $this->runtimeSettingsUANetwork;
  }
  /**
   * Label for a row in a table that shows the URL that was audited during a
   * Lighthouse run.
   *
   * @param string $runtimeSettingsUrl
   */
  public function setRuntimeSettingsUrl($runtimeSettingsUrl)
  {
    $this->runtimeSettingsUrl = $runtimeSettingsUrl;
  }
  /**
   * @return string
   */
  public function getRuntimeSettingsUrl()
  {
    return $this->runtimeSettingsUrl;
  }
  /**
   * Descriptive explanation for a runtime setting that is set to an unknown
   * value.
   *
   * @param string $runtimeUnknown
   */
  public function setRuntimeUnknown($runtimeUnknown)
  {
    $this->runtimeUnknown = $runtimeUnknown;
  }
  /**
   * @return string
   */
  public function getRuntimeUnknown()
  {
    return $this->runtimeUnknown;
  }
  /**
   * The label that explains the score gauges scale (0-49, 50-89, 90-100).
   *
   * @param string $scorescaleLabel
   */
  public function setScorescaleLabel($scorescaleLabel)
  {
    $this->scorescaleLabel = $scorescaleLabel;
  }
  /**
   * @return string
   */
  public function getScorescaleLabel()
  {
    return $this->scorescaleLabel;
  }
  /**
   * Label preceding a radio control for filtering the list of audits. The radio
   * choices are various performance metrics (FCP, LCP, TBT), and if chosen, the
   * audits in the report are hidden if they are not relevant to the selected
   * metric.
   *
   * @param string $showRelevantAudits
   */
  public function setShowRelevantAudits($showRelevantAudits)
  {
    $this->showRelevantAudits = $showRelevantAudits;
  }
  /**
   * @return string
   */
  public function getShowRelevantAudits()
  {
    return $this->showRelevantAudits;
  }
  /**
   * The label for the button to show only a few lines of a snippet
   *
   * @param string $snippetCollapseButtonLabel
   */
  public function setSnippetCollapseButtonLabel($snippetCollapseButtonLabel)
  {
    $this->snippetCollapseButtonLabel = $snippetCollapseButtonLabel;
  }
  /**
   * @return string
   */
  public function getSnippetCollapseButtonLabel()
  {
    return $this->snippetCollapseButtonLabel;
  }
  /**
   * The label for the button to show all lines of a snippet
   *
   * @param string $snippetExpandButtonLabel
   */
  public function setSnippetExpandButtonLabel($snippetExpandButtonLabel)
  {
    $this->snippetExpandButtonLabel = $snippetExpandButtonLabel;
  }
  /**
   * @return string
   */
  public function getSnippetExpandButtonLabel()
  {
    return $this->snippetExpandButtonLabel;
  }
  /**
   * This label is for a filter checkbox above a table of items
   *
   * @param string $thirdPartyResourcesLabel
   */
  public function setThirdPartyResourcesLabel($thirdPartyResourcesLabel)
  {
    $this->thirdPartyResourcesLabel = $thirdPartyResourcesLabel;
  }
  /**
   * @return string
   */
  public function getThirdPartyResourcesLabel()
  {
    return $this->thirdPartyResourcesLabel;
  }
  /**
   * Descriptive explanation for environment throttling that was provided by the
   * runtime environment instead of provided by Lighthouse throttling.
   *
   * @param string $throttlingProvided
   */
  public function setThrottlingProvided($throttlingProvided)
  {
    $this->throttlingProvided = $throttlingProvided;
  }
  /**
   * @return string
   */
  public function getThrottlingProvided()
  {
    return $this->throttlingProvided;
  }
  /**
   * The label shown preceding important warnings that may have invalidated an
   * entire report.
   *
   * @param string $toplevelWarningsMessage
   */
  public function setToplevelWarningsMessage($toplevelWarningsMessage)
  {
    $this->toplevelWarningsMessage = $toplevelWarningsMessage;
  }
  /**
   * @return string
   */
  public function getToplevelWarningsMessage()
  {
    return $this->toplevelWarningsMessage;
  }
  /**
   * The disclaimer shown below a performance metric value.
   *
   * @param string $varianceDisclaimer
   */
  public function setVarianceDisclaimer($varianceDisclaimer)
  {
    $this->varianceDisclaimer = $varianceDisclaimer;
  }
  /**
   * @return string
   */
  public function getVarianceDisclaimer()
  {
    return $this->varianceDisclaimer;
  }
  /**
   * Label for a button that opens the Treemap App
   *
   * @param string $viewTreemapLabel
   */
  public function setViewTreemapLabel($viewTreemapLabel)
  {
    $this->viewTreemapLabel = $viewTreemapLabel;
  }
  /**
   * @return string
   */
  public function getViewTreemapLabel()
  {
    return $this->viewTreemapLabel;
  }
  /**
   * The heading that is shown above a list of audits that have warnings
   *
   * @param string $warningAuditsGroupTitle
   */
  public function setWarningAuditsGroupTitle($warningAuditsGroupTitle)
  {
    $this->warningAuditsGroupTitle = $warningAuditsGroupTitle;
  }
  /**
   * @return string
   */
  public function getWarningAuditsGroupTitle()
  {
    return $this->warningAuditsGroupTitle;
  }
  /**
   * The label shown above a bulleted list of warnings.
   *
   * @param string $warningHeader
   */
  public function setWarningHeader($warningHeader)
  {
    $this->warningHeader = $warningHeader;
  }
  /**
   * @return string
   */
  public function getWarningHeader()
  {
    return $this->warningHeader;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RendererFormattedStrings::class, 'Google_Service_PagespeedInsights_RendererFormattedStrings');
