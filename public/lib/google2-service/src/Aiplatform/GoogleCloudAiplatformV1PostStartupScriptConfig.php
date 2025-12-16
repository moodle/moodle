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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PostStartupScriptConfig extends \Google\Model
{
  /**
   * Unspecified post startup script behavior.
   */
  public const POST_STARTUP_SCRIPT_BEHAVIOR_POST_STARTUP_SCRIPT_BEHAVIOR_UNSPECIFIED = 'POST_STARTUP_SCRIPT_BEHAVIOR_UNSPECIFIED';
  /**
   * Run post startup script after runtime is started.
   */
  public const POST_STARTUP_SCRIPT_BEHAVIOR_RUN_ONCE = 'RUN_ONCE';
  /**
   * Run post startup script after runtime is stopped.
   */
  public const POST_STARTUP_SCRIPT_BEHAVIOR_RUN_EVERY_START = 'RUN_EVERY_START';
  /**
   * Download and run post startup script every time runtime is started.
   */
  public const POST_STARTUP_SCRIPT_BEHAVIOR_DOWNLOAD_AND_RUN_EVERY_START = 'DOWNLOAD_AND_RUN_EVERY_START';
  /**
   * Optional. Post startup script to run after runtime is started.
   *
   * @var string
   */
  public $postStartupScript;
  /**
   * Optional. Post startup script behavior that defines download and execution
   * behavior.
   *
   * @var string
   */
  public $postStartupScriptBehavior;
  /**
   * Optional. Post startup script url to download. Example:
   * `gs://bucket/script.sh`
   *
   * @var string
   */
  public $postStartupScriptUrl;

  /**
   * Optional. Post startup script to run after runtime is started.
   *
   * @param string $postStartupScript
   */
  public function setPostStartupScript($postStartupScript)
  {
    $this->postStartupScript = $postStartupScript;
  }
  /**
   * @return string
   */
  public function getPostStartupScript()
  {
    return $this->postStartupScript;
  }
  /**
   * Optional. Post startup script behavior that defines download and execution
   * behavior.
   *
   * Accepted values: POST_STARTUP_SCRIPT_BEHAVIOR_UNSPECIFIED, RUN_ONCE,
   * RUN_EVERY_START, DOWNLOAD_AND_RUN_EVERY_START
   *
   * @param self::POST_STARTUP_SCRIPT_BEHAVIOR_* $postStartupScriptBehavior
   */
  public function setPostStartupScriptBehavior($postStartupScriptBehavior)
  {
    $this->postStartupScriptBehavior = $postStartupScriptBehavior;
  }
  /**
   * @return self::POST_STARTUP_SCRIPT_BEHAVIOR_*
   */
  public function getPostStartupScriptBehavior()
  {
    return $this->postStartupScriptBehavior;
  }
  /**
   * Optional. Post startup script url to download. Example:
   * `gs://bucket/script.sh`
   *
   * @param string $postStartupScriptUrl
   */
  public function setPostStartupScriptUrl($postStartupScriptUrl)
  {
    $this->postStartupScriptUrl = $postStartupScriptUrl;
  }
  /**
   * @return string
   */
  public function getPostStartupScriptUrl()
  {
    return $this->postStartupScriptUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PostStartupScriptConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PostStartupScriptConfig');
