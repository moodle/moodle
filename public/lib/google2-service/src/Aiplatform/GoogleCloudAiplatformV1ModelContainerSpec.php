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

class GoogleCloudAiplatformV1ModelContainerSpec extends \Google\Collection
{
  protected $collection_key = 'ports';
  /**
   * Immutable. Specifies arguments for the command that runs when the container
   * starts. This overrides the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd). Specify
   * this field as an array of executable and arguments, similar to a Docker
   * `CMD`'s "default parameters" form. If you don't specify this field but do
   * specify the command field, then the command from the `command` field runs
   * without any additional arguments. See the [Kubernetes documentation about
   * how the `command` and `args` fields interact with a container's
   * `ENTRYPOINT` and `CMD`](https://kubernetes.io/docs/tasks/inject-data-
   * application/define-command-argument-container/#notes). If you don't specify
   * this field and don't specify the `command` field, then the container's
   * [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#cmd) and
   * `CMD` determine what runs based on their default behavior. See the Docker
   * documentation about [how `CMD` and `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). In this field, you can reference [environment
   * variables set by Vertex AI](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `args` field of the Kubernetes Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @var string[]
   */
  public $args;
  /**
   * Immutable. Specifies the command that runs when the container starts. This
   * overrides the container's
   * [ENTRYPOINT](https://docs.docker.com/engine/reference/builder/#entrypoint).
   * Specify this field as an array of executable and arguments, similar to a
   * Docker `ENTRYPOINT`'s "exec" form, not its "shell" form. If you do not
   * specify this field, then the container's `ENTRYPOINT` runs, in conjunction
   * with the args field or the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd), if either
   * exists. If this field is not specified and the container does not have an
   * `ENTRYPOINT`, then refer to the Docker documentation about [how `CMD` and
   * `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). If you specify this field, then you can also
   * specify the `args` field to provide additional arguments for this command.
   * However, if you specify this field, then the container's `CMD` is ignored.
   * See the [Kubernetes documentation about how the `command` and `args` fields
   * interact with a container's `ENTRYPOINT` and
   * `CMD`](https://kubernetes.io/docs/tasks/inject-data-application/define-
   * command-argument-container/#notes). In this field, you can reference
   * [environment variables set by Vertex AI](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `command` field of the Kubernetes Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @var string[]
   */
  public $command;
  /**
   * Immutable. Deployment timeout. Limit for deployment timeout is 2 hours.
   *
   * @var string
   */
  public $deploymentTimeout;
  protected $envType = GoogleCloudAiplatformV1EnvVar::class;
  protected $envDataType = 'array';
  protected $grpcPortsType = GoogleCloudAiplatformV1Port::class;
  protected $grpcPortsDataType = 'array';
  protected $healthProbeType = GoogleCloudAiplatformV1Probe::class;
  protected $healthProbeDataType = '';
  /**
   * Immutable. HTTP path on the container to send health checks to. Vertex AI
   * intermittently sends GET requests to this path on the container's IP
   * address and port to check that the container is healthy. Read more about
   * [health checks](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#health). For example, if you set this field to
   * `/bar`, then Vertex AI intermittently sends a GET request to the `/bar`
   * path on the port of your container specified by the first value of this
   * `ModelContainerSpec`'s ports field. If you don't specify this field, it
   * defaults to the following value when you deploy this Model to an Endpoint:
   * /v1/endpoints/ENDPOINT/deployedModels/ DEPLOYED_MODEL:predict The
   * placeholders in this value are replaced as follows: * ENDPOINT: The last
   * segment (following `endpoints/`)of the Endpoint.name][] field of the
   * Endpoint where this Model has been deployed. (Vertex AI makes this value
   * available to your container code as the [`AIP_ENDPOINT_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).) * DEPLOYED_MODEL: DeployedModel.id
   * of the `DeployedModel`. (Vertex AI makes this value available to your
   * container code as the [`AIP_DEPLOYED_MODEL_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).)
   *
   * @var string
   */
  public $healthRoute;
  /**
   * Required. Immutable. URI of the Docker image to be used as the custom
   * container for serving predictions. This URI must identify an image in
   * Artifact Registry or Container Registry. Learn more about the [container
   * publishing requirements](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#publishing), including
   * permissions requirements for the Vertex AI Service Agent. The container
   * image is ingested upon ModelService.UploadModel, stored internally, and
   * this original path is afterwards not used. To learn about the requirements
   * for the Docker image itself, see [Custom container
   * requirements](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#). You can use the URI to one of Vertex AI's [pre-
   * built container images for prediction](https://cloud.google.com/vertex-
   * ai/docs/predictions/pre-built-containers) in this field.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Immutable. Invoke route prefix for the custom container. "" is the only
   * supported value right now. By setting this field, any non-root route on
   * this model will be accessible with invoke http call eg: "/invoke/foo/bar",
   * however the [PredictionService.Invoke] RPC is not supported yet. Only one
   * of `predict_route` or `invoke_route_prefix` can be set, and we default to
   * using `predict_route` if this field is not set. If this field is set, the
   * Model can only be deployed to dedicated endpoint.
   *
   * @var string
   */
  public $invokeRoutePrefix;
  protected $livenessProbeType = GoogleCloudAiplatformV1Probe::class;
  protected $livenessProbeDataType = '';
  protected $portsType = GoogleCloudAiplatformV1Port::class;
  protected $portsDataType = 'array';
  /**
   * Immutable. HTTP path on the container to send prediction requests to.
   * Vertex AI forwards requests sent using projects.locations.endpoints.predict
   * to this path on the container's IP address and port. Vertex AI then returns
   * the container's response in the API response. For example, if you set this
   * field to `/foo`, then when Vertex AI receives a prediction request, it
   * forwards the request body in a POST request to the `/foo` path on the port
   * of your container specified by the first value of this
   * `ModelContainerSpec`'s ports field. If you don't specify this field, it
   * defaults to the following value when you deploy this Model to an Endpoint:
   * /v1/endpoints/ENDPOINT/deployedModels/DEPLOYED_MODEL:predict The
   * placeholders in this value are replaced as follows: * ENDPOINT: The last
   * segment (following `endpoints/`)of the Endpoint.name][] field of the
   * Endpoint where this Model has been deployed. (Vertex AI makes this value
   * available to your container code as the [`AIP_ENDPOINT_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).) * DEPLOYED_MODEL: DeployedModel.id
   * of the `DeployedModel`. (Vertex AI makes this value available to your
   * container code as the [`AIP_DEPLOYED_MODEL_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).)
   *
   * @var string
   */
  public $predictRoute;
  /**
   * Immutable. The amount of the VM memory to reserve as the shared memory for
   * the model in megabytes.
   *
   * @var string
   */
  public $sharedMemorySizeMb;
  protected $startupProbeType = GoogleCloudAiplatformV1Probe::class;
  protected $startupProbeDataType = '';

  /**
   * Immutable. Specifies arguments for the command that runs when the container
   * starts. This overrides the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd). Specify
   * this field as an array of executable and arguments, similar to a Docker
   * `CMD`'s "default parameters" form. If you don't specify this field but do
   * specify the command field, then the command from the `command` field runs
   * without any additional arguments. See the [Kubernetes documentation about
   * how the `command` and `args` fields interact with a container's
   * `ENTRYPOINT` and `CMD`](https://kubernetes.io/docs/tasks/inject-data-
   * application/define-command-argument-container/#notes). If you don't specify
   * this field and don't specify the `command` field, then the container's
   * [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#cmd) and
   * `CMD` determine what runs based on their default behavior. See the Docker
   * documentation about [how `CMD` and `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). In this field, you can reference [environment
   * variables set by Vertex AI](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `args` field of the Kubernetes Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Immutable. Specifies the command that runs when the container starts. This
   * overrides the container's
   * [ENTRYPOINT](https://docs.docker.com/engine/reference/builder/#entrypoint).
   * Specify this field as an array of executable and arguments, similar to a
   * Docker `ENTRYPOINT`'s "exec" form, not its "shell" form. If you do not
   * specify this field, then the container's `ENTRYPOINT` runs, in conjunction
   * with the args field or the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd), if either
   * exists. If this field is not specified and the container does not have an
   * `ENTRYPOINT`, then refer to the Docker documentation about [how `CMD` and
   * `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). If you specify this field, then you can also
   * specify the `args` field to provide additional arguments for this command.
   * However, if you specify this field, then the container's `CMD` is ignored.
   * See the [Kubernetes documentation about how the `command` and `args` fields
   * interact with a container's `ENTRYPOINT` and
   * `CMD`](https://kubernetes.io/docs/tasks/inject-data-application/define-
   * command-argument-container/#notes). In this field, you can reference
   * [environment variables set by Vertex AI](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `command` field of the Kubernetes Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @param string[] $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string[]
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Immutable. Deployment timeout. Limit for deployment timeout is 2 hours.
   *
   * @param string $deploymentTimeout
   */
  public function setDeploymentTimeout($deploymentTimeout)
  {
    $this->deploymentTimeout = $deploymentTimeout;
  }
  /**
   * @return string
   */
  public function getDeploymentTimeout()
  {
    return $this->deploymentTimeout;
  }
  /**
   * Immutable. List of environment variables to set in the container. After the
   * container starts running, code running in the container can read these
   * environment variables. Additionally, the command and args fields can
   * reference these variables. Later entries in this list can also reference
   * earlier entries. For example, the following example sets the variable
   * `VAR_2` to have the value `foo bar`: ```json [ { "name": "VAR_1", "value":
   * "foo" }, { "name": "VAR_2", "value": "$(VAR_1) bar" } ] ``` If you switch
   * the order of the variables in the example, then the expansion does not
   * occur. This field corresponds to the `env` field of the Kubernetes
   * Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @param GoogleCloudAiplatformV1EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudAiplatformV1EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Immutable. List of ports to expose from the container. Vertex AI sends gRPC
   * prediction requests that it receives to the first port on this list. Vertex
   * AI also sends liveness and health checks to this port. If you do not
   * specify this field, gRPC requests to the container will be disabled. Vertex
   * AI does not use ports other than the first one listed. This field
   * corresponds to the `ports` field of the Kubernetes Containers v1 core API.
   *
   * @param GoogleCloudAiplatformV1Port[] $grpcPorts
   */
  public function setGrpcPorts($grpcPorts)
  {
    $this->grpcPorts = $grpcPorts;
  }
  /**
   * @return GoogleCloudAiplatformV1Port[]
   */
  public function getGrpcPorts()
  {
    return $this->grpcPorts;
  }
  /**
   * Immutable. Specification for Kubernetes readiness probe.
   *
   * @param GoogleCloudAiplatformV1Probe $healthProbe
   */
  public function setHealthProbe(GoogleCloudAiplatformV1Probe $healthProbe)
  {
    $this->healthProbe = $healthProbe;
  }
  /**
   * @return GoogleCloudAiplatformV1Probe
   */
  public function getHealthProbe()
  {
    return $this->healthProbe;
  }
  /**
   * Immutable. HTTP path on the container to send health checks to. Vertex AI
   * intermittently sends GET requests to this path on the container's IP
   * address and port to check that the container is healthy. Read more about
   * [health checks](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#health). For example, if you set this field to
   * `/bar`, then Vertex AI intermittently sends a GET request to the `/bar`
   * path on the port of your container specified by the first value of this
   * `ModelContainerSpec`'s ports field. If you don't specify this field, it
   * defaults to the following value when you deploy this Model to an Endpoint:
   * /v1/endpoints/ENDPOINT/deployedModels/ DEPLOYED_MODEL:predict The
   * placeholders in this value are replaced as follows: * ENDPOINT: The last
   * segment (following `endpoints/`)of the Endpoint.name][] field of the
   * Endpoint where this Model has been deployed. (Vertex AI makes this value
   * available to your container code as the [`AIP_ENDPOINT_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).) * DEPLOYED_MODEL: DeployedModel.id
   * of the `DeployedModel`. (Vertex AI makes this value available to your
   * container code as the [`AIP_DEPLOYED_MODEL_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).)
   *
   * @param string $healthRoute
   */
  public function setHealthRoute($healthRoute)
  {
    $this->healthRoute = $healthRoute;
  }
  /**
   * @return string
   */
  public function getHealthRoute()
  {
    return $this->healthRoute;
  }
  /**
   * Required. Immutable. URI of the Docker image to be used as the custom
   * container for serving predictions. This URI must identify an image in
   * Artifact Registry or Container Registry. Learn more about the [container
   * publishing requirements](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#publishing), including
   * permissions requirements for the Vertex AI Service Agent. The container
   * image is ingested upon ModelService.UploadModel, stored internally, and
   * this original path is afterwards not used. To learn about the requirements
   * for the Docker image itself, see [Custom container
   * requirements](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#). You can use the URI to one of Vertex AI's [pre-
   * built container images for prediction](https://cloud.google.com/vertex-
   * ai/docs/predictions/pre-built-containers) in this field.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Immutable. Invoke route prefix for the custom container. "" is the only
   * supported value right now. By setting this field, any non-root route on
   * this model will be accessible with invoke http call eg: "/invoke/foo/bar",
   * however the [PredictionService.Invoke] RPC is not supported yet. Only one
   * of `predict_route` or `invoke_route_prefix` can be set, and we default to
   * using `predict_route` if this field is not set. If this field is set, the
   * Model can only be deployed to dedicated endpoint.
   *
   * @param string $invokeRoutePrefix
   */
  public function setInvokeRoutePrefix($invokeRoutePrefix)
  {
    $this->invokeRoutePrefix = $invokeRoutePrefix;
  }
  /**
   * @return string
   */
  public function getInvokeRoutePrefix()
  {
    return $this->invokeRoutePrefix;
  }
  /**
   * Immutable. Specification for Kubernetes liveness probe.
   *
   * @param GoogleCloudAiplatformV1Probe $livenessProbe
   */
  public function setLivenessProbe(GoogleCloudAiplatformV1Probe $livenessProbe)
  {
    $this->livenessProbe = $livenessProbe;
  }
  /**
   * @return GoogleCloudAiplatformV1Probe
   */
  public function getLivenessProbe()
  {
    return $this->livenessProbe;
  }
  /**
   * Immutable. List of ports to expose from the container. Vertex AI sends any
   * prediction requests that it receives to the first port on this list. Vertex
   * AI also sends [liveness and health checks](https://cloud.google.com/vertex-
   * ai/docs/predictions/custom-container-requirements#liveness) to this port.
   * If you do not specify this field, it defaults to following value: ```json [
   * { "containerPort": 8080 } ] ``` Vertex AI does not use ports other than the
   * first one listed. This field corresponds to the `ports` field of the
   * Kubernetes Containers [v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.23/#container-v1-core).
   *
   * @param GoogleCloudAiplatformV1Port[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return GoogleCloudAiplatformV1Port[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
  /**
   * Immutable. HTTP path on the container to send prediction requests to.
   * Vertex AI forwards requests sent using projects.locations.endpoints.predict
   * to this path on the container's IP address and port. Vertex AI then returns
   * the container's response in the API response. For example, if you set this
   * field to `/foo`, then when Vertex AI receives a prediction request, it
   * forwards the request body in a POST request to the `/foo` path on the port
   * of your container specified by the first value of this
   * `ModelContainerSpec`'s ports field. If you don't specify this field, it
   * defaults to the following value when you deploy this Model to an Endpoint:
   * /v1/endpoints/ENDPOINT/deployedModels/DEPLOYED_MODEL:predict The
   * placeholders in this value are replaced as follows: * ENDPOINT: The last
   * segment (following `endpoints/`)of the Endpoint.name][] field of the
   * Endpoint where this Model has been deployed. (Vertex AI makes this value
   * available to your container code as the [`AIP_ENDPOINT_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).) * DEPLOYED_MODEL: DeployedModel.id
   * of the `DeployedModel`. (Vertex AI makes this value available to your
   * container code as the [`AIP_DEPLOYED_MODEL_ID` environment
   * variable](https://cloud.google.com/vertex-ai/docs/predictions/custom-
   * container-requirements#aip-variables).)
   *
   * @param string $predictRoute
   */
  public function setPredictRoute($predictRoute)
  {
    $this->predictRoute = $predictRoute;
  }
  /**
   * @return string
   */
  public function getPredictRoute()
  {
    return $this->predictRoute;
  }
  /**
   * Immutable. The amount of the VM memory to reserve as the shared memory for
   * the model in megabytes.
   *
   * @param string $sharedMemorySizeMb
   */
  public function setSharedMemorySizeMb($sharedMemorySizeMb)
  {
    $this->sharedMemorySizeMb = $sharedMemorySizeMb;
  }
  /**
   * @return string
   */
  public function getSharedMemorySizeMb()
  {
    return $this->sharedMemorySizeMb;
  }
  /**
   * Immutable. Specification for Kubernetes startup probe.
   *
   * @param GoogleCloudAiplatformV1Probe $startupProbe
   */
  public function setStartupProbe(GoogleCloudAiplatformV1Probe $startupProbe)
  {
    $this->startupProbe = $startupProbe;
  }
  /**
   * @return GoogleCloudAiplatformV1Probe
   */
  public function getStartupProbe()
  {
    return $this->startupProbe;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelContainerSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelContainerSpec');
