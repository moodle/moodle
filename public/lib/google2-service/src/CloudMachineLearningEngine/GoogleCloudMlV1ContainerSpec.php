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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1ContainerSpec extends \Google\Collection
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
   * this field and don't specify the `commmand` field, then the container's
   * [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#cmd) and
   * `CMD` determine what runs based on their default behavior. See the [Docker
   * documentation about how `CMD` and `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). In this field, you can reference [environment
   * variables set by AI Platform Prediction](/ai-
   * platform/prediction/docs/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `args` field of the [Kubernetes Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
   *
   * @var string[]
   */
  public $args;
  /**
   * Immutable. Specifies the command that runs when the container starts. This
   * overrides the container's [`ENTRYPOINT`](https://docs.docker.com/engine/ref
   * erence/builder/#entrypoint). Specify this field as an array of executable
   * and arguments, similar to a Docker `ENTRYPOINT`'s "exec" form, not its
   * "shell" form. If you do not specify this field, then the container's
   * `ENTRYPOINT` runs, in conjunction with the args field or the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd), if either
   * exists. If this field is not specified and the container does not have an
   * `ENTRYPOINT`, then refer to the [Docker documentation about how `CMD` and
   * `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). If you specify this field, then you can also
   * specify the `args` field to provide additional arguments for this command.
   * However, if you specify this field, then the container's `CMD` is ignored.
   * See the [Kubernetes documentation about how the `command` and `args` fields
   * interact with a container's `ENTRYPOINT` and
   * `CMD`](https://kubernetes.io/docs/tasks/inject-data-application/define-
   * command-argument-container/#notes). In this field, you can reference
   * [environment variables set by AI Platform Prediction](/ai-
   * platform/prediction/docs/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `command` field of the [Kubernetes Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
   *
   * @var string[]
   */
  public $command;
  protected $envType = GoogleCloudMlV1EnvVar::class;
  protected $envDataType = 'array';
  /**
   * URI of the Docker image to be used as the custom container for serving
   * predictions. This URI must identify [an image in Artifact
   * Registry](/artifact-registry/docs/overview) and begin with the hostname
   * `{REGION}-docker.pkg.dev`, where `{REGION}` is replaced by the region that
   * matches AI Platform Prediction [regional endpoint](/ai-
   * platform/prediction/docs/regional-endpoints) that you are using. For
   * example, if you are using the `us-central1-ml.googleapis.com` endpoint,
   * then this URI must begin with `us-central1-docker.pkg.dev`. To use a custom
   * container, the [AI Platform Google-managed service account](/ai-
   * platform/prediction/docs/custom-service-account#default) must have
   * permission to pull (read) the Docker image at this URI. The AI Platform
   * Google-managed service account has the following format:
   * `service-{PROJECT_NUMBER}@cloud-ml.google.com.iam.gserviceaccount.com`
   * {PROJECT_NUMBER} is replaced by your Google Cloud project number. By
   * default, this service account has necessary permissions to pull an Artifact
   * Registry image in the same Google Cloud project where you are using AI
   * Platform Prediction. In this case, no configuration is necessary. If you
   * want to use an image from a different Google Cloud project, learn how to
   * [grant the Artifact Registry Reader (roles/artifactregistry.reader) role
   * for a repository](/artifact-registry/docs/access-control#grant-repo) to
   * your projet's AI Platform Google-managed service account. To learn about
   * the requirements for the Docker image itself, read [Custom container
   * requirements](/ai-platform/prediction/docs/custom-container-requirements).
   *
   * @var string
   */
  public $image;
  protected $portsType = GoogleCloudMlV1ContainerPort::class;
  protected $portsDataType = 'array';

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
   * this field and don't specify the `commmand` field, then the container's
   * [`ENTRYPOINT`](https://docs.docker.com/engine/reference/builder/#cmd) and
   * `CMD` determine what runs based on their default behavior. See the [Docker
   * documentation about how `CMD` and `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). In this field, you can reference [environment
   * variables set by AI Platform Prediction](/ai-
   * platform/prediction/docs/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `args` field of the [Kubernetes Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
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
   * overrides the container's [`ENTRYPOINT`](https://docs.docker.com/engine/ref
   * erence/builder/#entrypoint). Specify this field as an array of executable
   * and arguments, similar to a Docker `ENTRYPOINT`'s "exec" form, not its
   * "shell" form. If you do not specify this field, then the container's
   * `ENTRYPOINT` runs, in conjunction with the args field or the container's
   * [`CMD`](https://docs.docker.com/engine/reference/builder/#cmd), if either
   * exists. If this field is not specified and the container does not have an
   * `ENTRYPOINT`, then refer to the [Docker documentation about how `CMD` and
   * `ENTRYPOINT`
   * interact](https://docs.docker.com/engine/reference/builder/#understand-how-
   * cmd-and-entrypoint-interact). If you specify this field, then you can also
   * specify the `args` field to provide additional arguments for this command.
   * However, if you specify this field, then the container's `CMD` is ignored.
   * See the [Kubernetes documentation about how the `command` and `args` fields
   * interact with a container's `ENTRYPOINT` and
   * `CMD`](https://kubernetes.io/docs/tasks/inject-data-application/define-
   * command-argument-container/#notes). In this field, you can reference
   * [environment variables set by AI Platform Prediction](/ai-
   * platform/prediction/docs/custom-container-requirements#aip-variables) and
   * environment variables set in the env field. You cannot reference
   * environment variables set in the Docker image. In order for environment
   * variables to be expanded, reference them by using the following syntax: $(
   * VARIABLE_NAME) Note that this differs from Bash variable expansion, which
   * does not use parentheses. If a variable cannot be resolved, the reference
   * in the input string is used unchanged. To avoid variable expansion, you can
   * escape this syntax with `$$`; for example: $$(VARIABLE_NAME) This field
   * corresponds to the `command` field of the [Kubernetes Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
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
   * Immutable. List of environment variables to set in the container. After the
   * container starts running, code running in the container can read these
   * environment variables. Additionally, the command and args fields can
   * reference these variables. Later entries in this list can also reference
   * earlier entries. For example, the following example sets the variable
   * `VAR_2` to have the value `foo bar`: ```json [ { "name": "VAR_1", "value":
   * "foo" }, { "name": "VAR_2", "value": "$(VAR_1) bar" } ] ``` If you switch
   * the order of the variables in the example, then the expansion does not
   * occur. This field corresponds to the `env` field of the [Kubernetes
   * Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
   *
   * @param GoogleCloudMlV1EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudMlV1EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * URI of the Docker image to be used as the custom container for serving
   * predictions. This URI must identify [an image in Artifact
   * Registry](/artifact-registry/docs/overview) and begin with the hostname
   * `{REGION}-docker.pkg.dev`, where `{REGION}` is replaced by the region that
   * matches AI Platform Prediction [regional endpoint](/ai-
   * platform/prediction/docs/regional-endpoints) that you are using. For
   * example, if you are using the `us-central1-ml.googleapis.com` endpoint,
   * then this URI must begin with `us-central1-docker.pkg.dev`. To use a custom
   * container, the [AI Platform Google-managed service account](/ai-
   * platform/prediction/docs/custom-service-account#default) must have
   * permission to pull (read) the Docker image at this URI. The AI Platform
   * Google-managed service account has the following format:
   * `service-{PROJECT_NUMBER}@cloud-ml.google.com.iam.gserviceaccount.com`
   * {PROJECT_NUMBER} is replaced by your Google Cloud project number. By
   * default, this service account has necessary permissions to pull an Artifact
   * Registry image in the same Google Cloud project where you are using AI
   * Platform Prediction. In this case, no configuration is necessary. If you
   * want to use an image from a different Google Cloud project, learn how to
   * [grant the Artifact Registry Reader (roles/artifactregistry.reader) role
   * for a repository](/artifact-registry/docs/access-control#grant-repo) to
   * your projet's AI Platform Google-managed service account. To learn about
   * the requirements for the Docker image itself, read [Custom container
   * requirements](/ai-platform/prediction/docs/custom-container-requirements).
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Immutable. List of ports to expose from the container. AI Platform
   * Prediction sends any prediction requests that it receives to the first port
   * on this list. AI Platform Prediction also sends [liveness and health
   * checks](/ai-platform/prediction/docs/custom-container-requirements#health)
   * to this port. If you do not specify this field, it defaults to following
   * value: ```json [ { "containerPort": 8080 } ] ``` AI Platform Prediction
   * does not use ports other than the first one listed. This field corresponds
   * to the `ports` field of the [Kubernetes Containers v1 core
   * API](https://kubernetes.io/docs/reference/generated/kubernetes-
   * api/v1.18/#container-v1-core).
   *
   * @param GoogleCloudMlV1ContainerPort[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return GoogleCloudMlV1ContainerPort[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1ContainerSpec::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1ContainerSpec');
