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

namespace Google\Service\Testing;

class ProvidedSoftwareCatalog extends \Google\Model
{
  /**
   * A string representing the current version of AndroidX Test Orchestrator
   * that is used in the environment. The package is available at
   * https://maven.google.com/web/index.html#androidx.test:orchestrator.
   *
   * @var string
   */
  public $androidxOrchestratorVersion;
  /**
   * Deprecated: Use AndroidX Test Orchestrator going forward. A string
   * representing the current version of Android Test Orchestrator that is used
   * in the environment. The package is available at https://maven.google.com/we
   * b/index.html#com.android.support.test:orchestrator.
   *
   * @deprecated
   * @var string
   */
  public $orchestratorVersion;

  /**
   * A string representing the current version of AndroidX Test Orchestrator
   * that is used in the environment. The package is available at
   * https://maven.google.com/web/index.html#androidx.test:orchestrator.
   *
   * @param string $androidxOrchestratorVersion
   */
  public function setAndroidxOrchestratorVersion($androidxOrchestratorVersion)
  {
    $this->androidxOrchestratorVersion = $androidxOrchestratorVersion;
  }
  /**
   * @return string
   */
  public function getAndroidxOrchestratorVersion()
  {
    return $this->androidxOrchestratorVersion;
  }
  /**
   * Deprecated: Use AndroidX Test Orchestrator going forward. A string
   * representing the current version of Android Test Orchestrator that is used
   * in the environment. The package is available at https://maven.google.com/we
   * b/index.html#com.android.support.test:orchestrator.
   *
   * @deprecated
   * @param string $orchestratorVersion
   */
  public function setOrchestratorVersion($orchestratorVersion)
  {
    $this->orchestratorVersion = $orchestratorVersion;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getOrchestratorVersion()
  {
    return $this->orchestratorVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvidedSoftwareCatalog::class, 'Google_Service_Testing_ProvidedSoftwareCatalog');
