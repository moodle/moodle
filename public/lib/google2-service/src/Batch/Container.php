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

namespace Google\Service\Batch;

class Container extends \Google\Collection
{
  protected $collection_key = 'volumes';
  /**
   * If set to true, external network access to and from container will be
   * blocked, containers that are with block_external_network as true can still
   * communicate with each other, network cannot be specified in the
   * `container.options` field.
   *
   * @var bool
   */
  public $blockExternalNetwork;
  /**
   * Required for some container images. Overrides the `CMD` specified in the
   * container. If there is an `ENTRYPOINT` (either in the container image or
   * with the `entrypoint` field below) then these commands are appended as
   * arguments to the `ENTRYPOINT`.
   *
   * @var string[]
   */
  public $commands;
  /**
   * Optional. If set to true, this container runnable uses Image streaming. Use
   * Image streaming to allow the runnable to initialize without waiting for the
   * entire container image to download, which can significantly reduce startup
   * time for large container images. When `enableImageStreaming` is set to
   * true, the container runtime is [containerd](https://containerd.io/) instead
   * of Docker. Additionally, this container runnable only supports the
   * following `container` subfields: `imageUri`, `commands[]`, `entrypoint`,
   * and `volumes[]`; any other `container` subfields are ignored. For more
   * information about the requirements and limitations for using Image
   * streaming with Batch, see the [`image-streaming` sample on
   * GitHub](https://github.com/GoogleCloudPlatform/batch-samples/tree/main/api-
   * samples/image-streaming).
   *
   * @var bool
   */
  public $enableImageStreaming;
  /**
   * Required for some container images. Overrides the `ENTRYPOINT` specified in
   * the container.
   *
   * @var string
   */
  public $entrypoint;
  /**
   * Required. The URI to pull the container image from.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Required for some container images. Arbitrary additional options to include
   * in the `docker run` command when running this container—for example,
   * `--network host`. For the `--volume` option, use the `volumes` field for
   * the container.
   *
   * @var string
   */
  public $options;
  /**
   * Required if the container image is from a private Docker registry. The
   * password to login to the Docker registry that contains the image. For
   * security, it is strongly recommended to specify an encrypted password by
   * using a Secret Manager secret: `projects/secrets/versions`. Warning: If you
   * specify the password using plain text, you risk the password being exposed
   * to any users who can view the job or its logs. To avoid this risk, specify
   * a secret that contains the password instead. Learn more about [Secret
   * Manager](https://cloud.google.com/secret-manager/docs/) and [using Secret
   * Manager with Batch](https://cloud.google.com/batch/docs/create-run-job-
   * secret-manager).
   *
   * @var string
   */
  public $password;
  /**
   * Required if the container image is from a private Docker registry. The
   * username to login to the Docker registry that contains the image. You can
   * either specify the username directly by using plain text or specify an
   * encrypted username by using a Secret Manager secret:
   * `projects/secrets/versions`. However, using a secret is recommended for
   * enhanced security. Caution: If you specify the username using plain text,
   * you risk the username being exposed to any users who can view the job or
   * its logs. To avoid this risk, specify a secret that contains the username
   * instead. Learn more about [Secret Manager](https://cloud.google.com/secret-
   * manager/docs/) and [using Secret Manager with
   * Batch](https://cloud.google.com/batch/docs/create-run-job-secret-manager).
   *
   * @var string
   */
  public $username;
  /**
   * Volumes to mount (bind mount) from the host machine files or directories
   * into the container, formatted to match `--volume` option for the `docker
   * run` command—for example, `/foo:/bar` or `/foo:/bar:ro`. If the
   * `TaskSpec.Volumes` field is specified but this field is not, Batch will
   * mount each volume from the host machine to the container with the same
   * mount path by default. In this case, the default mount option for
   * containers will be read-only (`ro`) for existing persistent disks and read-
   * write (`rw`) for other volume types, regardless of the original mount
   * options specified in `TaskSpec.Volumes`. If you need different mount
   * settings, you can explicitly configure them in this field.
   *
   * @var string[]
   */
  public $volumes;

  /**
   * If set to true, external network access to and from container will be
   * blocked, containers that are with block_external_network as true can still
   * communicate with each other, network cannot be specified in the
   * `container.options` field.
   *
   * @param bool $blockExternalNetwork
   */
  public function setBlockExternalNetwork($blockExternalNetwork)
  {
    $this->blockExternalNetwork = $blockExternalNetwork;
  }
  /**
   * @return bool
   */
  public function getBlockExternalNetwork()
  {
    return $this->blockExternalNetwork;
  }
  /**
   * Required for some container images. Overrides the `CMD` specified in the
   * container. If there is an `ENTRYPOINT` (either in the container image or
   * with the `entrypoint` field below) then these commands are appended as
   * arguments to the `ENTRYPOINT`.
   *
   * @param string[] $commands
   */
  public function setCommands($commands)
  {
    $this->commands = $commands;
  }
  /**
   * @return string[]
   */
  public function getCommands()
  {
    return $this->commands;
  }
  /**
   * Optional. If set to true, this container runnable uses Image streaming. Use
   * Image streaming to allow the runnable to initialize without waiting for the
   * entire container image to download, which can significantly reduce startup
   * time for large container images. When `enableImageStreaming` is set to
   * true, the container runtime is [containerd](https://containerd.io/) instead
   * of Docker. Additionally, this container runnable only supports the
   * following `container` subfields: `imageUri`, `commands[]`, `entrypoint`,
   * and `volumes[]`; any other `container` subfields are ignored. For more
   * information about the requirements and limitations for using Image
   * streaming with Batch, see the [`image-streaming` sample on
   * GitHub](https://github.com/GoogleCloudPlatform/batch-samples/tree/main/api-
   * samples/image-streaming).
   *
   * @param bool $enableImageStreaming
   */
  public function setEnableImageStreaming($enableImageStreaming)
  {
    $this->enableImageStreaming = $enableImageStreaming;
  }
  /**
   * @return bool
   */
  public function getEnableImageStreaming()
  {
    return $this->enableImageStreaming;
  }
  /**
   * Required for some container images. Overrides the `ENTRYPOINT` specified in
   * the container.
   *
   * @param string $entrypoint
   */
  public function setEntrypoint($entrypoint)
  {
    $this->entrypoint = $entrypoint;
  }
  /**
   * @return string
   */
  public function getEntrypoint()
  {
    return $this->entrypoint;
  }
  /**
   * Required. The URI to pull the container image from.
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
   * Required for some container images. Arbitrary additional options to include
   * in the `docker run` command when running this container—for example,
   * `--network host`. For the `--volume` option, use the `volumes` field for
   * the container.
   *
   * @param string $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return string
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Required if the container image is from a private Docker registry. The
   * password to login to the Docker registry that contains the image. For
   * security, it is strongly recommended to specify an encrypted password by
   * using a Secret Manager secret: `projects/secrets/versions`. Warning: If you
   * specify the password using plain text, you risk the password being exposed
   * to any users who can view the job or its logs. To avoid this risk, specify
   * a secret that contains the password instead. Learn more about [Secret
   * Manager](https://cloud.google.com/secret-manager/docs/) and [using Secret
   * Manager with Batch](https://cloud.google.com/batch/docs/create-run-job-
   * secret-manager).
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Required if the container image is from a private Docker registry. The
   * username to login to the Docker registry that contains the image. You can
   * either specify the username directly by using plain text or specify an
   * encrypted username by using a Secret Manager secret:
   * `projects/secrets/versions`. However, using a secret is recommended for
   * enhanced security. Caution: If you specify the username using plain text,
   * you risk the username being exposed to any users who can view the job or
   * its logs. To avoid this risk, specify a secret that contains the username
   * instead. Learn more about [Secret Manager](https://cloud.google.com/secret-
   * manager/docs/) and [using Secret Manager with
   * Batch](https://cloud.google.com/batch/docs/create-run-job-secret-manager).
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * Volumes to mount (bind mount) from the host machine files or directories
   * into the container, formatted to match `--volume` option for the `docker
   * run` command—for example, `/foo:/bar` or `/foo:/bar:ro`. If the
   * `TaskSpec.Volumes` field is specified but this field is not, Batch will
   * mount each volume from the host machine to the container with the same
   * mount path by default. In this case, the default mount option for
   * containers will be read-only (`ro`) for existing persistent disks and read-
   * write (`rw`) for other volume types, regardless of the original mount
   * options specified in `TaskSpec.Volumes`. If you need different mount
   * settings, you can explicitly configure them in this field.
   *
   * @param string[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return string[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Container::class, 'Google_Service_Batch_Container');
