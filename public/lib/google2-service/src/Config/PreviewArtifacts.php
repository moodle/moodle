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

namespace Google\Service\Config;

class PreviewArtifacts extends \Google\Model
{
  /**
   * Output only. Location of artifacts in Google Cloud Storage. Format:
   * `gs://{bucket}/{object}`
   *
   * @var string
   */
  public $artifacts;
  /**
   * Output only. Location of a blueprint copy and other content in Google Cloud
   * Storage. Format: `gs://{bucket}/{object}`
   *
   * @var string
   */
  public $content;

  /**
   * Output only. Location of artifacts in Google Cloud Storage. Format:
   * `gs://{bucket}/{object}`
   *
   * @param string $artifacts
   */
  public function setArtifacts($artifacts)
  {
    $this->artifacts = $artifacts;
  }
  /**
   * @return string
   */
  public function getArtifacts()
  {
    return $this->artifacts;
  }
  /**
   * Output only. Location of a blueprint copy and other content in Google Cloud
   * Storage. Format: `gs://{bucket}/{object}`
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreviewArtifacts::class, 'Google_Service_Config_PreviewArtifacts');
