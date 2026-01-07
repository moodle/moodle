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

namespace Google\Service\ArtifactRegistry;

class Attachment extends \Google\Collection
{
  protected $collection_key = 'files';
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Artifact Registry. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * The namespace this attachment belongs to. E.g. If an attachment is created
   * by artifact analysis, namespace is set to
   * `artifactanalysis.googleapis.com`.
   *
   * @var string
   */
  public $attachmentNamespace;
  /**
   * Output only. The time when the attachment was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The files that belong to this attachment. If the file ID part
   * contains slashes, they are escaped. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/files/sha:`.
   *
   * @var string[]
   */
  public $files;
  /**
   * The name of the attachment. E.g.
   * `projects/p1/locations/us/repositories/repo/attachments/sbom`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The name of the OCI version that this attachment created. Only
   * populated for Docker attachments. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/packages/p1/versions/v1`.
   *
   * @var string
   */
  public $ociVersionName;
  /**
   * Required. The target the attachment is for, can be a Version, Package or
   * Repository. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/packages/p1/versions/v1`.
   *
   * @var string
   */
  public $target;
  /**
   * Type of attachment. E.g. `application/vnd.spdx+json`
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time when the attachment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user, and not by Artifact Registry. See
   * https://google.aip.dev/128#annotations for more details such as format and
   * size limitations.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * The namespace this attachment belongs to. E.g. If an attachment is created
   * by artifact analysis, namespace is set to
   * `artifactanalysis.googleapis.com`.
   *
   * @param string $attachmentNamespace
   */
  public function setAttachmentNamespace($attachmentNamespace)
  {
    $this->attachmentNamespace = $attachmentNamespace;
  }
  /**
   * @return string
   */
  public function getAttachmentNamespace()
  {
    return $this->attachmentNamespace;
  }
  /**
   * Output only. The time when the attachment was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The files that belong to this attachment. If the file ID part
   * contains slashes, they are escaped. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/files/sha:`.
   *
   * @param string[] $files
   */
  public function setFiles($files)
  {
    $this->files = $files;
  }
  /**
   * @return string[]
   */
  public function getFiles()
  {
    return $this->files;
  }
  /**
   * The name of the attachment. E.g.
   * `projects/p1/locations/us/repositories/repo/attachments/sbom`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The name of the OCI version that this attachment created. Only
   * populated for Docker attachments. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/packages/p1/versions/v1`.
   *
   * @param string $ociVersionName
   */
  public function setOciVersionName($ociVersionName)
  {
    $this->ociVersionName = $ociVersionName;
  }
  /**
   * @return string
   */
  public function getOciVersionName()
  {
    return $this->ociVersionName;
  }
  /**
   * Required. The target the attachment is for, can be a Version, Package or
   * Repository. E.g. `projects/p1/locations/us-
   * central1/repositories/repo1/packages/p1/versions/v1`.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Type of attachment. E.g. `application/vnd.spdx+json`
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The time when the attachment was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Attachment::class, 'Google_Service_ArtifactRegistry_Attachment');
