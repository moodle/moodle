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

class GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec extends \Google\Model
{
  /**
   * Optional. The Python module to load as the entrypoint, specified as a fully
   * qualified module name. For example: path.to.agent. If not specified,
   * defaults to "agent". The project root will be added to Python sys.path,
   * allowing imports to be specified relative to the root.
   *
   * @var string
   */
  public $entrypointModule;
  /**
   * Optional. The name of the callable object within the `entrypoint_module` to
   * use as the application If not specified, defaults to "root_agent".
   *
   * @var string
   */
  public $entrypointObject;
  /**
   * Optional. The path to the requirements file, relative to the source root.
   * If not specified, defaults to "requirements.txt".
   *
   * @var string
   */
  public $requirementsFile;
  /**
   * Optional. The version of Python to use. Support version includes 3.9, 3.10,
   * 3.11, 3.12, 3.13. If not specified, default value is 3.10.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. The Python module to load as the entrypoint, specified as a fully
   * qualified module name. For example: path.to.agent. If not specified,
   * defaults to "agent". The project root will be added to Python sys.path,
   * allowing imports to be specified relative to the root.
   *
   * @param string $entrypointModule
   */
  public function setEntrypointModule($entrypointModule)
  {
    $this->entrypointModule = $entrypointModule;
  }
  /**
   * @return string
   */
  public function getEntrypointModule()
  {
    return $this->entrypointModule;
  }
  /**
   * Optional. The name of the callable object within the `entrypoint_module` to
   * use as the application If not specified, defaults to "root_agent".
   *
   * @param string $entrypointObject
   */
  public function setEntrypointObject($entrypointObject)
  {
    $this->entrypointObject = $entrypointObject;
  }
  /**
   * @return string
   */
  public function getEntrypointObject()
  {
    return $this->entrypointObject;
  }
  /**
   * Optional. The path to the requirements file, relative to the source root.
   * If not specified, defaults to "requirements.txt".
   *
   * @param string $requirementsFile
   */
  public function setRequirementsFile($requirementsFile)
  {
    $this->requirementsFile = $requirementsFile;
  }
  /**
   * @return string
   */
  public function getRequirementsFile()
  {
    return $this->requirementsFile;
  }
  /**
   * Optional. The version of Python to use. Support version includes 3.9, 3.10,
   * 3.11, 3.12, 3.13. If not specified, default value is 3.10.
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
class_alias(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpecPythonSpec');
