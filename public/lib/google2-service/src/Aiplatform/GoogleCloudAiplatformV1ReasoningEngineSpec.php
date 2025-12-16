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

class GoogleCloudAiplatformV1ReasoningEngineSpec extends \Google\Collection
{
  /**
   * Default value. Use a custom service account if the `service_account` field
   * is set, otherwise use the default Vertex AI Reasoning Engine Service Agent
   * in the project. Same behavior as SERVICE_ACCOUNT.
   */
  public const IDENTITY_TYPE_IDENTITY_TYPE_UNSPECIFIED = 'IDENTITY_TYPE_UNSPECIFIED';
  /**
   * Use a custom service account if the `service_account` field is set,
   * otherwise use the default Vertex AI Reasoning Engine Service Agent in the
   * project.
   */
  public const IDENTITY_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * Use Agent Identity. The `service_account` field must not be set.
   */
  public const IDENTITY_TYPE_AGENT_IDENTITY = 'AGENT_IDENTITY';
  protected $collection_key = 'classMethods';
  /**
   * Optional. The OSS agent framework used to develop the agent. Currently
   * supported values: "google-adk", "langchain", "langgraph", "ag2", "llama-
   * index", "custom".
   *
   * @var string
   */
  public $agentFramework;
  /**
   * Optional. Declarations for object class methods in OpenAPI specification
   * format.
   *
   * @var array[]
   */
  public $classMethods;
  protected $deploymentSpecType = GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec::class;
  protected $deploymentSpecDataType = '';
  /**
   * Output only. The identity to use for the Reasoning Engine. It can contain
   * one of the following values: * service-{project}@gcp-sa-aiplatform-
   * re.googleapis.com (for SERVICE_AGENT identity type) *
   * {name}@{project}.gserviceaccount.com (for SERVICE_ACCOUNT identity type) *
   * agents.global.{org}.system.id.goog/resources/aiplatform/projects/{project}/
   * locations/{location}/reasoningEngines/{reasoning_engine} (for
   * AGENT_IDENTITY identity type)
   *
   * @var string
   */
  public $effectiveIdentity;
  /**
   * Optional. The identity type to use for the Reasoning Engine. If not
   * specified, the `service_account` field will be used if set, otherwise the
   * default Vertex AI Reasoning Engine Service Agent in the project will be
   * used.
   *
   * @var string
   */
  public $identityType;
  protected $packageSpecType = GoogleCloudAiplatformV1ReasoningEngineSpecPackageSpec::class;
  protected $packageSpecDataType = '';
  /**
   * Optional. The service account that the Reasoning Engine artifact runs as.
   * It should have "roles/storage.objectViewer" for reading the user project's
   * Cloud Storage and "roles/aiplatform.user" for using Vertex extensions. If
   * not specified, the Vertex AI Reasoning Engine Service Agent in the project
   * will be used.
   *
   * @var string
   */
  public $serviceAccount;
  protected $sourceCodeSpecType = GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec::class;
  protected $sourceCodeSpecDataType = '';

  /**
   * Optional. The OSS agent framework used to develop the agent. Currently
   * supported values: "google-adk", "langchain", "langgraph", "ag2", "llama-
   * index", "custom".
   *
   * @param string $agentFramework
   */
  public function setAgentFramework($agentFramework)
  {
    $this->agentFramework = $agentFramework;
  }
  /**
   * @return string
   */
  public function getAgentFramework()
  {
    return $this->agentFramework;
  }
  /**
   * Optional. Declarations for object class methods in OpenAPI specification
   * format.
   *
   * @param array[] $classMethods
   */
  public function setClassMethods($classMethods)
  {
    $this->classMethods = $classMethods;
  }
  /**
   * @return array[]
   */
  public function getClassMethods()
  {
    return $this->classMethods;
  }
  /**
   * Optional. The specification of a Reasoning Engine deployment.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec $deploymentSpec
   */
  public function setDeploymentSpec(GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec $deploymentSpec)
  {
    $this->deploymentSpec = $deploymentSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecDeploymentSpec
   */
  public function getDeploymentSpec()
  {
    return $this->deploymentSpec;
  }
  /**
   * Output only. The identity to use for the Reasoning Engine. It can contain
   * one of the following values: * service-{project}@gcp-sa-aiplatform-
   * re.googleapis.com (for SERVICE_AGENT identity type) *
   * {name}@{project}.gserviceaccount.com (for SERVICE_ACCOUNT identity type) *
   * agents.global.{org}.system.id.goog/resources/aiplatform/projects/{project}/
   * locations/{location}/reasoningEngines/{reasoning_engine} (for
   * AGENT_IDENTITY identity type)
   *
   * @param string $effectiveIdentity
   */
  public function setEffectiveIdentity($effectiveIdentity)
  {
    $this->effectiveIdentity = $effectiveIdentity;
  }
  /**
   * @return string
   */
  public function getEffectiveIdentity()
  {
    return $this->effectiveIdentity;
  }
  /**
   * Optional. The identity type to use for the Reasoning Engine. If not
   * specified, the `service_account` field will be used if set, otherwise the
   * default Vertex AI Reasoning Engine Service Agent in the project will be
   * used.
   *
   * Accepted values: IDENTITY_TYPE_UNSPECIFIED, SERVICE_ACCOUNT, AGENT_IDENTITY
   *
   * @param self::IDENTITY_TYPE_* $identityType
   */
  public function setIdentityType($identityType)
  {
    $this->identityType = $identityType;
  }
  /**
   * @return self::IDENTITY_TYPE_*
   */
  public function getIdentityType()
  {
    return $this->identityType;
  }
  /**
   * Optional. User provided package spec of the ReasoningEngine. Ignored when
   * users directly specify a deployment image through
   * `deployment_spec.first_party_image_override`, but keeping the
   * field_behavior to avoid introducing breaking changes. The
   * `deployment_source` field should not be set if `package_spec` is specified.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecPackageSpec $packageSpec
   */
  public function setPackageSpec(GoogleCloudAiplatformV1ReasoningEngineSpecPackageSpec $packageSpec)
  {
    $this->packageSpec = $packageSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecPackageSpec
   */
  public function getPackageSpec()
  {
    return $this->packageSpec;
  }
  /**
   * Optional. The service account that the Reasoning Engine artifact runs as.
   * It should have "roles/storage.objectViewer" for reading the user project's
   * Cloud Storage and "roles/aiplatform.user" for using Vertex extensions. If
   * not specified, the Vertex AI Reasoning Engine Service Agent in the project
   * will be used.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Deploy from source code files with a defined entrypoint.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec $sourceCodeSpec
   */
  public function setSourceCodeSpec(GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec $sourceCodeSpec)
  {
    $this->sourceCodeSpec = $sourceCodeSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpecSourceCodeSpec
   */
  public function getSourceCodeSpec()
  {
    return $this->sourceCodeSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReasoningEngineSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngineSpec');
