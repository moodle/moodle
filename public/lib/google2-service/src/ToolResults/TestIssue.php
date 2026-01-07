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

namespace Google\Service\ToolResults;

class TestIssue extends \Google\Model
{
  /**
   * Default unspecified category. Do not use. For versioning only.
   */
  public const CATEGORY_unspecifiedCategory = 'unspecifiedCategory';
  /**
   * Issue is not specific to a particular test kind (e.g., a native crash).
   */
  public const CATEGORY_common = 'common';
  /**
   * Issue is specific to Robo run.
   */
  public const CATEGORY_robo = 'robo';
  /**
   * Default unspecified severity. Do not use. For versioning only.
   */
  public const SEVERITY_unspecifiedSeverity = 'unspecifiedSeverity';
  /**
   * Non critical issue, providing users with some info about the test run.
   */
  public const SEVERITY_info = 'info';
  /**
   * Non critical issue, providing users with some hints on improving their
   * testing experience, e.g., suggesting to use Game Loops.
   */
  public const SEVERITY_suggestion = 'suggestion';
  /**
   * Potentially critical issue.
   */
  public const SEVERITY_warning = 'warning';
  /**
   * Critical issue.
   */
  public const SEVERITY_severe = 'severe';
  /**
   * Default unspecified type. Do not use. For versioning only.
   */
  public const TYPE_unspecifiedType = 'unspecifiedType';
  /**
   * Issue is a fatal exception.
   */
  public const TYPE_fatalException = 'fatalException';
  /**
   * Issue is a native crash.
   */
  public const TYPE_nativeCrash = 'nativeCrash';
  /**
   * Issue is an ANR crash.
   */
  public const TYPE_anr = 'anr';
  /**
   * Issue is an unused robo directive.
   */
  public const TYPE_unusedRoboDirective = 'unusedRoboDirective';
  /**
   * Issue is a suggestion to use orchestrator.
   */
  public const TYPE_compatibleWithOrchestrator = 'compatibleWithOrchestrator';
  /**
   * Issue with finding a launcher activity
   */
  public const TYPE_launcherActivityNotFound = 'launcherActivityNotFound';
  /**
   * Issue with resolving a user-provided intent to start an activity
   */
  public const TYPE_startActivityNotFound = 'startActivityNotFound';
  /**
   * A Robo script was not fully executed.
   */
  public const TYPE_incompleteRoboScriptExecution = 'incompleteRoboScriptExecution';
  /**
   * A Robo script was fully and successfully executed.
   */
  public const TYPE_completeRoboScriptExecution = 'completeRoboScriptExecution';
  /**
   * The APK failed to install.
   */
  public const TYPE_failedToInstall = 'failedToInstall';
  /**
   * The app-under-test has deep links, but none were provided to Robo.
   */
  public const TYPE_availableDeepLinks = 'availableDeepLinks';
  /**
   * App accessed a non-sdk Api.
   */
  public const TYPE_nonSdkApiUsageViolation = 'nonSdkApiUsageViolation';
  /**
   * App accessed a non-sdk Api (new detailed report)
   */
  public const TYPE_nonSdkApiUsageReport = 'nonSdkApiUsageReport';
  /**
   * Robo crawl encountered at least one screen with elements that are not
   * Android UI widgets.
   */
  public const TYPE_encounteredNonAndroidUiWidgetScreen = 'encounteredNonAndroidUiWidgetScreen';
  /**
   * Robo crawl encountered at least one probable login screen.
   */
  public const TYPE_encounteredLoginScreen = 'encounteredLoginScreen';
  /**
   * Robo signed in with Google.
   */
  public const TYPE_performedGoogleLogin = 'performedGoogleLogin';
  /**
   * iOS App crashed with an exception.
   */
  public const TYPE_iosException = 'iosException';
  /**
   * iOS App crashed without an exception (e.g. killed).
   */
  public const TYPE_iosCrash = 'iosCrash';
  /**
   * Robo crawl involved performing some monkey actions.
   */
  public const TYPE_performedMonkeyActions = 'performedMonkeyActions';
  /**
   * Robo crawl used a Robo directive.
   */
  public const TYPE_usedRoboDirective = 'usedRoboDirective';
  /**
   * Robo crawl used a Robo directive to ignore an UI element.
   */
  public const TYPE_usedRoboIgnoreDirective = 'usedRoboIgnoreDirective';
  /**
   * Robo did not crawl some potentially important parts of the app.
   */
  public const TYPE_insufficientCoverage = 'insufficientCoverage';
  /**
   * Robo crawl involved some in-app purchases.
   */
  public const TYPE_inAppPurchases = 'inAppPurchases';
  /**
   * Crash dialog was detected during the test execution
   */
  public const TYPE_crashDialogError = 'crashDialogError';
  /**
   * UI element depth is greater than the threshold
   */
  public const TYPE_uiElementsTooDeep = 'uiElementsTooDeep';
  /**
   * Blank screen is found in the Robo crawl
   */
  public const TYPE_blankScreen = 'blankScreen';
  /**
   * Overlapping UI elements are found in the Robo crawl
   */
  public const TYPE_overlappingUiElements = 'overlappingUiElements';
  /**
   * An uncaught Unity exception was detected (these don't crash apps).
   */
  public const TYPE_unityException = 'unityException';
  /**
   * Device running out of memory was detected
   */
  public const TYPE_deviceOutOfMemory = 'deviceOutOfMemory';
  /**
   * Problems detected while collecting logcat
   */
  public const TYPE_logcatCollectionError = 'logcatCollectionError';
  /**
   * Robo detected a splash screen provided by app (vs. Android OS splash
   * screen).
   */
  public const TYPE_detectedAppSplashScreen = 'detectedAppSplashScreen';
  /**
   * There was an issue with the assets in this test.
   */
  public const TYPE_assetIssue = 'assetIssue';
  /**
   * Category of issue. Required.
   *
   * @var string
   */
  public $category;
  /**
   * A brief human-readable message describing the issue. Required.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Severity of issue. Required.
   *
   * @var string
   */
  public $severity;
  protected $stackTraceType = StackTrace::class;
  protected $stackTraceDataType = '';
  /**
   * Type of issue. Required.
   *
   * @var string
   */
  public $type;
  protected $warningType = Any::class;
  protected $warningDataType = '';

  /**
   * Category of issue. Required.
   *
   * Accepted values: unspecifiedCategory, common, robo
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * A brief human-readable message describing the issue. Required.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Severity of issue. Required.
   *
   * Accepted values: unspecifiedSeverity, info, suggestion, warning, severe
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Deprecated in favor of stack trace fields inside specific warnings.
   *
   * @deprecated
   * @param StackTrace $stackTrace
   */
  public function setStackTrace(StackTrace $stackTrace)
  {
    $this->stackTrace = $stackTrace;
  }
  /**
   * @deprecated
   * @return StackTrace
   */
  public function getStackTrace()
  {
    return $this->stackTrace;
  }
  /**
   * Type of issue. Required.
   *
   * Accepted values: unspecifiedType, fatalException, nativeCrash, anr,
   * unusedRoboDirective, compatibleWithOrchestrator, launcherActivityNotFound,
   * startActivityNotFound, incompleteRoboScriptExecution,
   * completeRoboScriptExecution, failedToInstall, availableDeepLinks,
   * nonSdkApiUsageViolation, nonSdkApiUsageReport,
   * encounteredNonAndroidUiWidgetScreen, encounteredLoginScreen,
   * performedGoogleLogin, iosException, iosCrash, performedMonkeyActions,
   * usedRoboDirective, usedRoboIgnoreDirective, insufficientCoverage,
   * inAppPurchases, crashDialogError, uiElementsTooDeep, blankScreen,
   * overlappingUiElements, unityException, deviceOutOfMemory,
   * logcatCollectionError, detectedAppSplashScreen, assetIssue
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Warning message with additional details of the issue. Should always be a
   * message from com.google.devtools.toolresults.v1.warnings
   *
   * @param Any $warning
   */
  public function setWarning(Any $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return Any
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestIssue::class, 'Google_Service_ToolResults_TestIssue');
