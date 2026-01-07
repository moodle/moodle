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

class CloudAiPlatformCommonCreatePipelineJobApiErrorDetail extends \Google\Model
{
  /**
   * Should never be used.
   */
  public const ERROR_CAUSE_ERROR_CAUSE_UNSPECIFIED = 'ERROR_CAUSE_UNSPECIFIED';
  /**
   * IR Pipeline Spec can not been parsed to yaml or json format.
   */
  public const ERROR_CAUSE_INVALID_PIPELINE_SPEC_FORMAT = 'INVALID_PIPELINE_SPEC_FORMAT';
  /**
   * A pipeline spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_PIPELINE_SPEC = 'INVALID_PIPELINE_SPEC';
  /**
   * A deployment config is invalid.
   */
  public const ERROR_CAUSE_INVALID_DEPLOYMENT_CONFIG = 'INVALID_DEPLOYMENT_CONFIG';
  /**
   * A deployment spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_DEPLOYMENT_SPEC = 'INVALID_DEPLOYMENT_SPEC';
  /**
   * An instance schema is invalid.
   */
  public const ERROR_CAUSE_INVALID_INSTANCE_SCHEMA = 'INVALID_INSTANCE_SCHEMA';
  /**
   * A custom job is invalid.
   */
  public const ERROR_CAUSE_INVALID_CUSTOM_JOB = 'INVALID_CUSTOM_JOB';
  /**
   * A container spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_CONTAINER_SPEC = 'INVALID_CONTAINER_SPEC';
  /**
   * Notification email setup is invalid.
   */
  public const ERROR_CAUSE_INVALID_NOTIFICATION_EMAIL_SETUP = 'INVALID_NOTIFICATION_EMAIL_SETUP';
  /**
   * Service account setup is invalid.
   */
  public const ERROR_CAUSE_INVALID_SERVICE_ACCOUNT_SETUP = 'INVALID_SERVICE_ACCOUNT_SETUP';
  /**
   * KMS setup is invalid.
   */
  public const ERROR_CAUSE_INVALID_KMS_SETUP = 'INVALID_KMS_SETUP';
  /**
   * Network setup is invalid.
   */
  public const ERROR_CAUSE_INVALID_NETWORK_SETUP = 'INVALID_NETWORK_SETUP';
  /**
   * Task spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_PIPELINE_TASK_SPEC = 'INVALID_PIPELINE_TASK_SPEC';
  /**
   * Task artifact is invalid.
   */
  public const ERROR_CAUSE_INVALID_PIPELINE_TASK_ARTIFACT = 'INVALID_PIPELINE_TASK_ARTIFACT';
  /**
   * Importer spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_IMPORTER_SPEC = 'INVALID_IMPORTER_SPEC';
  /**
   * Resolver spec is invalid.
   */
  public const ERROR_CAUSE_INVALID_RESOLVER_SPEC = 'INVALID_RESOLVER_SPEC';
  /**
   * Runtime Parameters are invalid.
   */
  public const ERROR_CAUSE_INVALID_RUNTIME_PARAMETERS = 'INVALID_RUNTIME_PARAMETERS';
  /**
   * Cloud API not enabled.
   */
  public const ERROR_CAUSE_CLOUD_API_NOT_ENABLED = 'CLOUD_API_NOT_ENABLED';
  /**
   * Invalid Cloud Storage input uri
   */
  public const ERROR_CAUSE_INVALID_GCS_INPUT_URI = 'INVALID_GCS_INPUT_URI';
  /**
   * Invalid Cloud Storage output uri
   */
  public const ERROR_CAUSE_INVALID_GCS_OUTPUT_URI = 'INVALID_GCS_OUTPUT_URI';
  /**
   * Component spec of pipeline is invalid.
   */
  public const ERROR_CAUSE_INVALID_COMPONENT_SPEC = 'INVALID_COMPONENT_SPEC';
  /**
   * DagOutputsSpec is invalid.
   */
  public const ERROR_CAUSE_INVALID_DAG_OUTPUTS_SPEC = 'INVALID_DAG_OUTPUTS_SPEC';
  /**
   * DagSpec is invalid.
   */
  public const ERROR_CAUSE_INVALID_DAG_SPEC = 'INVALID_DAG_SPEC';
  /**
   * Project does not have enough quota.
   */
  public const ERROR_CAUSE_INSUFFICIENT_QUOTA = 'INSUFFICIENT_QUOTA';
  /**
   * An internal error with unknown cause.
   */
  public const ERROR_CAUSE_INTERNAL = 'INTERNAL';
  /**
   * The error root cause returned by CreatePipelineJob API.
   *
   * @var string
   */
  public $errorCause;
  /**
   * Public messages contains actionable items for the error cause.
   *
   * @var string
   */
  public $publicMessage;

  /**
   * The error root cause returned by CreatePipelineJob API.
   *
   * Accepted values: ERROR_CAUSE_UNSPECIFIED, INVALID_PIPELINE_SPEC_FORMAT,
   * INVALID_PIPELINE_SPEC, INVALID_DEPLOYMENT_CONFIG, INVALID_DEPLOYMENT_SPEC,
   * INVALID_INSTANCE_SCHEMA, INVALID_CUSTOM_JOB, INVALID_CONTAINER_SPEC,
   * INVALID_NOTIFICATION_EMAIL_SETUP, INVALID_SERVICE_ACCOUNT_SETUP,
   * INVALID_KMS_SETUP, INVALID_NETWORK_SETUP, INVALID_PIPELINE_TASK_SPEC,
   * INVALID_PIPELINE_TASK_ARTIFACT, INVALID_IMPORTER_SPEC,
   * INVALID_RESOLVER_SPEC, INVALID_RUNTIME_PARAMETERS, CLOUD_API_NOT_ENABLED,
   * INVALID_GCS_INPUT_URI, INVALID_GCS_OUTPUT_URI, INVALID_COMPONENT_SPEC,
   * INVALID_DAG_OUTPUTS_SPEC, INVALID_DAG_SPEC, INSUFFICIENT_QUOTA, INTERNAL
   *
   * @param self::ERROR_CAUSE_* $errorCause
   */
  public function setErrorCause($errorCause)
  {
    $this->errorCause = $errorCause;
  }
  /**
   * @return self::ERROR_CAUSE_*
   */
  public function getErrorCause()
  {
    return $this->errorCause;
  }
  /**
   * Public messages contains actionable items for the error cause.
   *
   * @param string $publicMessage
   */
  public function setPublicMessage($publicMessage)
  {
    $this->publicMessage = $publicMessage;
  }
  /**
   * @return string
   */
  public function getPublicMessage()
  {
    return $this->publicMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformCommonCreatePipelineJobApiErrorDetail::class, 'Google_Service_Aiplatform_CloudAiPlatformCommonCreatePipelineJobApiErrorDetail');
