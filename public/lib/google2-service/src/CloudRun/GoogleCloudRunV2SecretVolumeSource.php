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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2SecretVolumeSource extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Integer representation of mode bits to use on created files by default.
   * Must be a value between 0000 and 0777 (octal), defaulting to 0444.
   * Directories within the path are not affected by this setting. Notes *
   * Internally, a umask of 0222 will be applied to any non-zero value. * This
   * is an integer representation of the mode bits. So, the octal integer value
   * should look exactly as the chmod numeric notation with a leading zero. Some
   * examples: for chmod 640 (u=rw,g=r), set to 0640 (octal) or 416 (base-10).
   * For chmod 755 (u=rwx,g=rx,o=rx), set to 0755 (octal) or 493 (base-10). *
   * This might be in conflict with other options that affect the file mode,
   * like fsGroup, and the result can be other mode bits set. This might be in
   * conflict with other options that affect the file mode, like fsGroup, and as
   * a result, other mode bits could be set.
   *
   * @var int
   */
  public $defaultMode;
  protected $itemsType = GoogleCloudRunV2VersionToPath::class;
  protected $itemsDataType = 'array';
  /**
   * Required. The name of the secret in Cloud Secret Manager. Format: {secret}
   * if the secret is in the same project. projects/{project}/secrets/{secret}
   * if the secret is in a different project.
   *
   * @var string
   */
  public $secret;

  /**
   * Integer representation of mode bits to use on created files by default.
   * Must be a value between 0000 and 0777 (octal), defaulting to 0444.
   * Directories within the path are not affected by this setting. Notes *
   * Internally, a umask of 0222 will be applied to any non-zero value. * This
   * is an integer representation of the mode bits. So, the octal integer value
   * should look exactly as the chmod numeric notation with a leading zero. Some
   * examples: for chmod 640 (u=rw,g=r), set to 0640 (octal) or 416 (base-10).
   * For chmod 755 (u=rwx,g=rx,o=rx), set to 0755 (octal) or 493 (base-10). *
   * This might be in conflict with other options that affect the file mode,
   * like fsGroup, and the result can be other mode bits set. This might be in
   * conflict with other options that affect the file mode, like fsGroup, and as
   * a result, other mode bits could be set.
   *
   * @param int $defaultMode
   */
  public function setDefaultMode($defaultMode)
  {
    $this->defaultMode = $defaultMode;
  }
  /**
   * @return int
   */
  public function getDefaultMode()
  {
    return $this->defaultMode;
  }
  /**
   * If unspecified, the volume will expose a file whose name is the secret,
   * relative to VolumeMount.mount_path + VolumeMount.sub_path. If specified,
   * the key will be used as the version to fetch from Cloud Secret Manager and
   * the path will be the name of the file exposed in the volume. When items are
   * defined, they must specify a path and a version.
   *
   * @param GoogleCloudRunV2VersionToPath[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleCloudRunV2VersionToPath[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Required. The name of the secret in Cloud Secret Manager. Format: {secret}
   * if the secret is in the same project. projects/{project}/secrets/{secret}
   * if the secret is in a different project.
   *
   * @param string $secret
   */
  public function setSecret($secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return string
   */
  public function getSecret()
  {
    return $this->secret;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2SecretVolumeSource::class, 'Google_Service_CloudRun_GoogleCloudRunV2SecretVolumeSource');
