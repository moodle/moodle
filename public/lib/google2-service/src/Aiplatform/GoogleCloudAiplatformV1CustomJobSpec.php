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

class GoogleCloudAiplatformV1CustomJobSpec extends \Google\Collection
{
  protected $collection_key = 'workerPoolSpecs';
  protected $baseOutputDirectoryType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $baseOutputDirectoryDataType = '';
  /**
   * Optional. Whether you want Vertex AI to enable access to the customized
   * dashboard in training chief container. If set to `true`, you can access the
   * dashboard at the URIs given by CustomJob.web_access_uris or
   * Trial.web_access_uris (within HyperparameterTuningJob.trials).
   *
   * @var bool
   */
  public $enableDashboardAccess;
  /**
   * Optional. Whether you want Vertex AI to enable [interactive shell
   * access](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) to training containers. If set to `true`, you can access
   * interactive shells at the URIs given by CustomJob.web_access_uris or
   * Trial.web_access_uris (within HyperparameterTuningJob.trials).
   *
   * @var bool
   */
  public $enableWebAccess;
  /**
   * Optional. The Experiment associated with this job. Format: `projects/{proje
   * ct}/locations/{location}/metadataStores/{metadataStores}/contexts/{experime
   * nt-name}`
   *
   * @var string
   */
  public $experiment;
  /**
   * Optional. The Experiment Run associated with this job. Format: `projects/{p
   * roject}/locations/{location}/metadataStores/{metadataStores}/contexts/{expe
   * riment-name}-{experiment-run-name}`
   *
   * @var string
   */
  public $experimentRun;
  /**
   * Optional. The name of the Model resources for which to generate a mapping
   * to artifact URIs. Applicable only to some of the Google-provided custom
   * jobs. Format: `projects/{project}/locations/{location}/models/{model}` In
   * order to retrieve a specific version of the model, also provide the version
   * ID or version alias. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` If no
   * version ID or alias is specified, the "default" version will be returned.
   * The "default" version alias is created for the first version of the model,
   * and can be moved to other versions later on. There will be exactly one
   * default version.
   *
   * @var string[]
   */
  public $models;
  /**
   * Optional. The full name of the Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to which the Job
   * should be peered. For example, `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. To specify
   * this field, you must have already [configured VPC Network Peering for
   * Vertex AI](https://cloud.google.com/vertex-ai/docs/general/vpc-peering). If
   * this field is left unspecified, the job is not peered with any network.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The ID of the PersistentResource in the same Project and Location
   * which to run If this is specified, the job will be run on existing machines
   * held by the PersistentResource instead of on-demand short-live machines.
   * The network and CMEK configs on the job should be consistent with those on
   * the PersistentResource, otherwise, the job will be rejected.
   *
   * @var string
   */
  public $persistentResourceId;
  /**
   * The ID of the location to store protected artifacts. e.g. us-central1.
   * Populate only when the location is different than CustomJob location. List
   * of supported locations: https://cloud.google.com/vertex-
   * ai/docs/general/locations
   *
   * @var string
   */
  public $protectedArtifactLocationId;
  protected $pscInterfaceConfigType = GoogleCloudAiplatformV1PscInterfaceConfig::class;
  protected $pscInterfaceConfigDataType = '';
  /**
   * Optional. A list of names for the reserved ip ranges under the VPC network
   * that can be used for this job. If set, we will deploy the job within the
   * provided ip ranges. Otherwise, the job will be deployed to any ip ranges
   * under the provided VPC network. Example: ['vertex-ai-ip-range'].
   *
   * @var string[]
   */
  public $reservedIpRanges;
  protected $schedulingType = GoogleCloudAiplatformV1Scheduling::class;
  protected $schedulingDataType = '';
  /**
   * Specifies the service account for workload run-as account. Users submitting
   * jobs must have act-as permission on this run-as account. If unspecified,
   * the [Vertex AI Custom Code Service Agent](https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) for the CustomJob's project
   * is used.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. The name of a Vertex AI Tensorboard resource to which this
   * CustomJob will upload Tensorboard logs. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   *
   * @var string
   */
  public $tensorboard;
  protected $workerPoolSpecsType = GoogleCloudAiplatformV1WorkerPoolSpec::class;
  protected $workerPoolSpecsDataType = 'array';

  /**
   * The Cloud Storage location to store the output of this CustomJob or
   * HyperparameterTuningJob. For HyperparameterTuningJob, the
   * baseOutputDirectory of each child CustomJob backing a Trial is set to a
   * subdirectory of name id under its parent HyperparameterTuningJob's
   * baseOutputDirectory. The following Vertex AI environment variables will be
   * passed to containers or python modules when this field is set: For
   * CustomJob: * AIP_MODEL_DIR = `/model/` * AIP_CHECKPOINT_DIR =
   * `/checkpoints/` * AIP_TENSORBOARD_LOG_DIR = `/logs/` For CustomJob backing
   * a Trial of HyperparameterTuningJob: * AIP_MODEL_DIR = `//model/` *
   * AIP_CHECKPOINT_DIR = `//checkpoints/` * AIP_TENSORBOARD_LOG_DIR = `//logs/`
   *
   * @param GoogleCloudAiplatformV1GcsDestination $baseOutputDirectory
   */
  public function setBaseOutputDirectory(GoogleCloudAiplatformV1GcsDestination $baseOutputDirectory)
  {
    $this->baseOutputDirectory = $baseOutputDirectory;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getBaseOutputDirectory()
  {
    return $this->baseOutputDirectory;
  }
  /**
   * Optional. Whether you want Vertex AI to enable access to the customized
   * dashboard in training chief container. If set to `true`, you can access the
   * dashboard at the URIs given by CustomJob.web_access_uris or
   * Trial.web_access_uris (within HyperparameterTuningJob.trials).
   *
   * @param bool $enableDashboardAccess
   */
  public function setEnableDashboardAccess($enableDashboardAccess)
  {
    $this->enableDashboardAccess = $enableDashboardAccess;
  }
  /**
   * @return bool
   */
  public function getEnableDashboardAccess()
  {
    return $this->enableDashboardAccess;
  }
  /**
   * Optional. Whether you want Vertex AI to enable [interactive shell
   * access](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) to training containers. If set to `true`, you can access
   * interactive shells at the URIs given by CustomJob.web_access_uris or
   * Trial.web_access_uris (within HyperparameterTuningJob.trials).
   *
   * @param bool $enableWebAccess
   */
  public function setEnableWebAccess($enableWebAccess)
  {
    $this->enableWebAccess = $enableWebAccess;
  }
  /**
   * @return bool
   */
  public function getEnableWebAccess()
  {
    return $this->enableWebAccess;
  }
  /**
   * Optional. The Experiment associated with this job. Format: `projects/{proje
   * ct}/locations/{location}/metadataStores/{metadataStores}/contexts/{experime
   * nt-name}`
   *
   * @param string $experiment
   */
  public function setExperiment($experiment)
  {
    $this->experiment = $experiment;
  }
  /**
   * @return string
   */
  public function getExperiment()
  {
    return $this->experiment;
  }
  /**
   * Optional. The Experiment Run associated with this job. Format: `projects/{p
   * roject}/locations/{location}/metadataStores/{metadataStores}/contexts/{expe
   * riment-name}-{experiment-run-name}`
   *
   * @param string $experimentRun
   */
  public function setExperimentRun($experimentRun)
  {
    $this->experimentRun = $experimentRun;
  }
  /**
   * @return string
   */
  public function getExperimentRun()
  {
    return $this->experimentRun;
  }
  /**
   * Optional. The name of the Model resources for which to generate a mapping
   * to artifact URIs. Applicable only to some of the Google-provided custom
   * jobs. Format: `projects/{project}/locations/{location}/models/{model}` In
   * order to retrieve a specific version of the model, also provide the version
   * ID or version alias. Example:
   * `projects/{project}/locations/{location}/models/{model}@2` or
   * `projects/{project}/locations/{location}/models/{model}@golden` If no
   * version ID or alias is specified, the "default" version will be returned.
   * The "default" version alias is created for the first version of the model,
   * and can be moved to other versions later on. There will be exactly one
   * default version.
   *
   * @param string[] $models
   */
  public function setModels($models)
  {
    $this->models = $models;
  }
  /**
   * @return string[]
   */
  public function getModels()
  {
    return $this->models;
  }
  /**
   * Optional. The full name of the Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to which the Job
   * should be peered. For example, `projects/12345/global/networks/myVPC`.
   * [Format](/compute/docs/reference/rest/v1/networks/insert) is of the form
   * `projects/{project}/global/networks/{network}`. Where {project} is a
   * project number, as in `12345`, and {network} is a network name. To specify
   * this field, you must have already [configured VPC Network Peering for
   * Vertex AI](https://cloud.google.com/vertex-ai/docs/general/vpc-peering). If
   * this field is left unspecified, the job is not peered with any network.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. The ID of the PersistentResource in the same Project and Location
   * which to run If this is specified, the job will be run on existing machines
   * held by the PersistentResource instead of on-demand short-live machines.
   * The network and CMEK configs on the job should be consistent with those on
   * the PersistentResource, otherwise, the job will be rejected.
   *
   * @param string $persistentResourceId
   */
  public function setPersistentResourceId($persistentResourceId)
  {
    $this->persistentResourceId = $persistentResourceId;
  }
  /**
   * @return string
   */
  public function getPersistentResourceId()
  {
    return $this->persistentResourceId;
  }
  /**
   * The ID of the location to store protected artifacts. e.g. us-central1.
   * Populate only when the location is different than CustomJob location. List
   * of supported locations: https://cloud.google.com/vertex-
   * ai/docs/general/locations
   *
   * @param string $protectedArtifactLocationId
   */
  public function setProtectedArtifactLocationId($protectedArtifactLocationId)
  {
    $this->protectedArtifactLocationId = $protectedArtifactLocationId;
  }
  /**
   * @return string
   */
  public function getProtectedArtifactLocationId()
  {
    return $this->protectedArtifactLocationId;
  }
  /**
   * Optional. Configuration for PSC-I for CustomJob.
   *
   * @param GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig
   */
  public function setPscInterfaceConfig(GoogleCloudAiplatformV1PscInterfaceConfig $pscInterfaceConfig)
  {
    $this->pscInterfaceConfig = $pscInterfaceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PscInterfaceConfig
   */
  public function getPscInterfaceConfig()
  {
    return $this->pscInterfaceConfig;
  }
  /**
   * Optional. A list of names for the reserved ip ranges under the VPC network
   * that can be used for this job. If set, we will deploy the job within the
   * provided ip ranges. Otherwise, the job will be deployed to any ip ranges
   * under the provided VPC network. Example: ['vertex-ai-ip-range'].
   *
   * @param string[] $reservedIpRanges
   */
  public function setReservedIpRanges($reservedIpRanges)
  {
    $this->reservedIpRanges = $reservedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getReservedIpRanges()
  {
    return $this->reservedIpRanges;
  }
  /**
   * Scheduling options for a CustomJob.
   *
   * @param GoogleCloudAiplatformV1Scheduling $scheduling
   */
  public function setScheduling(GoogleCloudAiplatformV1Scheduling $scheduling)
  {
    $this->scheduling = $scheduling;
  }
  /**
   * @return GoogleCloudAiplatformV1Scheduling
   */
  public function getScheduling()
  {
    return $this->scheduling;
  }
  /**
   * Specifies the service account for workload run-as account. Users submitting
   * jobs must have act-as permission on this run-as account. If unspecified,
   * the [Vertex AI Custom Code Service Agent](https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) for the CustomJob's project
   * is used.
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
   * Optional. The name of a Vertex AI Tensorboard resource to which this
   * CustomJob will upload Tensorboard logs. Format:
   * `projects/{project}/locations/{location}/tensorboards/{tensorboard}`
   *
   * @param string $tensorboard
   */
  public function setTensorboard($tensorboard)
  {
    $this->tensorboard = $tensorboard;
  }
  /**
   * @return string
   */
  public function getTensorboard()
  {
    return $this->tensorboard;
  }
  /**
   * Required. The spec of the worker pools including machine type and Docker
   * image. All worker pools except the first one are optional and can be
   * skipped by providing an empty value.
   *
   * @param GoogleCloudAiplatformV1WorkerPoolSpec[] $workerPoolSpecs
   */
  public function setWorkerPoolSpecs($workerPoolSpecs)
  {
    $this->workerPoolSpecs = $workerPoolSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1WorkerPoolSpec[]
   */
  public function getWorkerPoolSpecs()
  {
    return $this->workerPoolSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CustomJobSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CustomJobSpec');
