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

namespace Google\Service\ServiceManagement;

class ClientLibrarySettings extends \Google\Model
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $cppSettingsType = CppSettings::class;
  protected $cppSettingsDataType = '';
  protected $dotnetSettingsType = DotnetSettings::class;
  protected $dotnetSettingsDataType = '';
  protected $goSettingsType = GoSettings::class;
  protected $goSettingsDataType = '';
  protected $javaSettingsType = JavaSettings::class;
  protected $javaSettingsDataType = '';
  /**
   * Launch stage of this version of the API.
   *
   * @var string
   */
  public $launchStage;
  protected $nodeSettingsType = NodeSettings::class;
  protected $nodeSettingsDataType = '';
  protected $phpSettingsType = PhpSettings::class;
  protected $phpSettingsDataType = '';
  protected $pythonSettingsType = PythonSettings::class;
  protected $pythonSettingsDataType = '';
  /**
   * When using transport=rest, the client request will encode enums as numbers
   * rather than strings.
   *
   * @var bool
   */
  public $restNumericEnums;
  protected $rubySettingsType = RubySettings::class;
  protected $rubySettingsDataType = '';
  /**
   * Version of the API to apply these settings to. This is the full protobuf
   * package for the API, ending in the version element. Examples:
   * "google.cloud.speech.v1" and "google.spanner.admin.database.v1".
   *
   * @var string
   */
  public $version;

  /**
   * Settings for C++ client libraries.
   *
   * @param CppSettings $cppSettings
   */
  public function setCppSettings(CppSettings $cppSettings)
  {
    $this->cppSettings = $cppSettings;
  }
  /**
   * @return CppSettings
   */
  public function getCppSettings()
  {
    return $this->cppSettings;
  }
  /**
   * Settings for .NET client libraries.
   *
   * @param DotnetSettings $dotnetSettings
   */
  public function setDotnetSettings(DotnetSettings $dotnetSettings)
  {
    $this->dotnetSettings = $dotnetSettings;
  }
  /**
   * @return DotnetSettings
   */
  public function getDotnetSettings()
  {
    return $this->dotnetSettings;
  }
  /**
   * Settings for Go client libraries.
   *
   * @param GoSettings $goSettings
   */
  public function setGoSettings(GoSettings $goSettings)
  {
    $this->goSettings = $goSettings;
  }
  /**
   * @return GoSettings
   */
  public function getGoSettings()
  {
    return $this->goSettings;
  }
  /**
   * Settings for legacy Java features, supported in the Service YAML.
   *
   * @param JavaSettings $javaSettings
   */
  public function setJavaSettings(JavaSettings $javaSettings)
  {
    $this->javaSettings = $javaSettings;
  }
  /**
   * @return JavaSettings
   */
  public function getJavaSettings()
  {
    return $this->javaSettings;
  }
  /**
   * Launch stage of this version of the API.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * Settings for Node client libraries.
   *
   * @param NodeSettings $nodeSettings
   */
  public function setNodeSettings(NodeSettings $nodeSettings)
  {
    $this->nodeSettings = $nodeSettings;
  }
  /**
   * @return NodeSettings
   */
  public function getNodeSettings()
  {
    return $this->nodeSettings;
  }
  /**
   * Settings for PHP client libraries.
   *
   * @param PhpSettings $phpSettings
   */
  public function setPhpSettings(PhpSettings $phpSettings)
  {
    $this->phpSettings = $phpSettings;
  }
  /**
   * @return PhpSettings
   */
  public function getPhpSettings()
  {
    return $this->phpSettings;
  }
  /**
   * Settings for Python client libraries.
   *
   * @param PythonSettings $pythonSettings
   */
  public function setPythonSettings(PythonSettings $pythonSettings)
  {
    $this->pythonSettings = $pythonSettings;
  }
  /**
   * @return PythonSettings
   */
  public function getPythonSettings()
  {
    return $this->pythonSettings;
  }
  /**
   * When using transport=rest, the client request will encode enums as numbers
   * rather than strings.
   *
   * @param bool $restNumericEnums
   */
  public function setRestNumericEnums($restNumericEnums)
  {
    $this->restNumericEnums = $restNumericEnums;
  }
  /**
   * @return bool
   */
  public function getRestNumericEnums()
  {
    return $this->restNumericEnums;
  }
  /**
   * Settings for Ruby client libraries.
   *
   * @param RubySettings $rubySettings
   */
  public function setRubySettings(RubySettings $rubySettings)
  {
    $this->rubySettings = $rubySettings;
  }
  /**
   * @return RubySettings
   */
  public function getRubySettings()
  {
    return $this->rubySettings;
  }
  /**
   * Version of the API to apply these settings to. This is the full protobuf
   * package for the API, ending in the version element. Examples:
   * "google.cloud.speech.v1" and "google.spanner.admin.database.v1".
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
class_alias(ClientLibrarySettings::class, 'Google_Service_ServiceManagement_ClientLibrarySettings');
