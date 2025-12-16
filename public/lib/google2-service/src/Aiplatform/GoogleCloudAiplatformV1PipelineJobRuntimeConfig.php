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

class GoogleCloudAiplatformV1PipelineJobRuntimeConfig extends \Google\Model
{
  /**
   * Default value, and follows fail slow behavior.
   */
  public const FAILURE_POLICY_PIPELINE_FAILURE_POLICY_UNSPECIFIED = 'PIPELINE_FAILURE_POLICY_UNSPECIFIED';
  /**
   * Indicates that the pipeline should continue to run until all possible tasks
   * have been scheduled and completed.
   */
  public const FAILURE_POLICY_PIPELINE_FAILURE_POLICY_FAIL_SLOW = 'PIPELINE_FAILURE_POLICY_FAIL_SLOW';
  /**
   * Indicates that the pipeline should stop scheduling new tasks after a task
   * has failed.
   */
  public const FAILURE_POLICY_PIPELINE_FAILURE_POLICY_FAIL_FAST = 'PIPELINE_FAILURE_POLICY_FAIL_FAST';
  /**
   * Represents the failure policy of a pipeline. Currently, the default of a
   * pipeline is that the pipeline will continue to run until no more tasks can
   * be executed, also known as PIPELINE_FAILURE_POLICY_FAIL_SLOW. However, if a
   * pipeline is set to PIPELINE_FAILURE_POLICY_FAIL_FAST, it will stop
   * scheduling any new tasks when a task has failed. Any scheduled tasks will
   * continue to completion.
   *
   * @var string
   */
  public $failurePolicy;
  /**
   * Required. A path in a Cloud Storage bucket, which will be treated as the
   * root output directory of the pipeline. It is used by the system to generate
   * the paths of output artifacts. The artifact paths are generated with a sub-
   * path pattern `{job_id}/{task_id}/{output_key}` under the specified output
   * directory. The service account specified in this pipeline must have the
   * `storage.objects.get` and `storage.objects.create` permissions for this
   * bucket.
   *
   * @var string
   */
  public $gcsOutputDirectory;
  protected $inputArtifactsType = GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact::class;
  protected $inputArtifactsDataType = 'map';
  /**
   * The runtime parameters of the PipelineJob. The parameters will be passed
   * into PipelineJob.pipeline_spec to replace the placeholders at runtime. This
   * field is used by pipelines built using
   * `PipelineJob.pipeline_spec.schema_version` 2.1.0, such as pipelines built
   * using Kubeflow Pipelines SDK 1.9 or higher and the v2 DSL.
   *
   * @var array[]
   */
  public $parameterValues;
  protected $parametersType = GoogleCloudAiplatformV1Value::class;
  protected $parametersDataType = 'map';

  /**
   * Represents the failure policy of a pipeline. Currently, the default of a
   * pipeline is that the pipeline will continue to run until no more tasks can
   * be executed, also known as PIPELINE_FAILURE_POLICY_FAIL_SLOW. However, if a
   * pipeline is set to PIPELINE_FAILURE_POLICY_FAIL_FAST, it will stop
   * scheduling any new tasks when a task has failed. Any scheduled tasks will
   * continue to completion.
   *
   * Accepted values: PIPELINE_FAILURE_POLICY_UNSPECIFIED,
   * PIPELINE_FAILURE_POLICY_FAIL_SLOW, PIPELINE_FAILURE_POLICY_FAIL_FAST
   *
   * @param self::FAILURE_POLICY_* $failurePolicy
   */
  public function setFailurePolicy($failurePolicy)
  {
    $this->failurePolicy = $failurePolicy;
  }
  /**
   * @return self::FAILURE_POLICY_*
   */
  public function getFailurePolicy()
  {
    return $this->failurePolicy;
  }
  /**
   * Required. A path in a Cloud Storage bucket, which will be treated as the
   * root output directory of the pipeline. It is used by the system to generate
   * the paths of output artifacts. The artifact paths are generated with a sub-
   * path pattern `{job_id}/{task_id}/{output_key}` under the specified output
   * directory. The service account specified in this pipeline must have the
   * `storage.objects.get` and `storage.objects.create` permissions for this
   * bucket.
   *
   * @param string $gcsOutputDirectory
   */
  public function setGcsOutputDirectory($gcsOutputDirectory)
  {
    $this->gcsOutputDirectory = $gcsOutputDirectory;
  }
  /**
   * @return string
   */
  public function getGcsOutputDirectory()
  {
    return $this->gcsOutputDirectory;
  }
  /**
   * The runtime artifacts of the PipelineJob. The key will be the input
   * artifact name and the value would be one of the InputArtifact.
   *
   * @param GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact[] $inputArtifacts
   */
  public function setInputArtifacts($inputArtifacts)
  {
    $this->inputArtifacts = $inputArtifacts;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineJobRuntimeConfigInputArtifact[]
   */
  public function getInputArtifacts()
  {
    return $this->inputArtifacts;
  }
  /**
   * The runtime parameters of the PipelineJob. The parameters will be passed
   * into PipelineJob.pipeline_spec to replace the placeholders at runtime. This
   * field is used by pipelines built using
   * `PipelineJob.pipeline_spec.schema_version` 2.1.0, such as pipelines built
   * using Kubeflow Pipelines SDK 1.9 or higher and the v2 DSL.
   *
   * @param array[] $parameterValues
   */
  public function setParameterValues($parameterValues)
  {
    $this->parameterValues = $parameterValues;
  }
  /**
   * @return array[]
   */
  public function getParameterValues()
  {
    return $this->parameterValues;
  }
  /**
   * Deprecated. Use RuntimeConfig.parameter_values instead. The runtime
   * parameters of the PipelineJob. The parameters will be passed into
   * PipelineJob.pipeline_spec to replace the placeholders at runtime. This
   * field is used by pipelines built using
   * `PipelineJob.pipeline_spec.schema_version` 2.0.0 or lower, such as
   * pipelines built using Kubeflow Pipelines SDK 1.8 or lower.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1Value[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1Value[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineJobRuntimeConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineJobRuntimeConfig');
