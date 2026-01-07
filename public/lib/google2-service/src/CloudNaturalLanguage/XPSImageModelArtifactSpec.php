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

namespace Google\Service\CloudNaturalLanguage;

class XPSImageModelArtifactSpec extends \Google\Collection
{
  protected $collection_key = 'exportArtifact';
  protected $checkpointArtifactType = XPSModelArtifactItem::class;
  protected $checkpointArtifactDataType = '';
  protected $exportArtifactType = XPSModelArtifactItem::class;
  protected $exportArtifactDataType = 'array';
  /**
   * Google Cloud Storage URI of decoded labels file for model export
   * 'dict.txt'.
   *
   * @var string
   */
  public $labelGcsUri;
  protected $servingArtifactType = XPSModelArtifactItem::class;
  protected $servingArtifactDataType = '';
  /**
   * Google Cloud Storage URI prefix of Tensorflow JavaScript binary files
   * 'groupX-shardXofX.bin'. Deprecated.
   *
   * @var string
   */
  public $tfJsBinaryGcsPrefix;
  /**
   * Google Cloud Storage URI of Tensorflow Lite metadata
   * 'tflite_metadata.json'.
   *
   * @var string
   */
  public $tfLiteMetadataGcsUri;

  /**
   * The Tensorflow checkpoint files. e.g. Used for resumable training.
   *
   * @param XPSModelArtifactItem $checkpointArtifact
   */
  public function setCheckpointArtifact(XPSModelArtifactItem $checkpointArtifact)
  {
    $this->checkpointArtifact = $checkpointArtifact;
  }
  /**
   * @return XPSModelArtifactItem
   */
  public function getCheckpointArtifact()
  {
    return $this->checkpointArtifact;
  }
  /**
   * The model binary files in different formats for model export.
   *
   * @param XPSModelArtifactItem[] $exportArtifact
   */
  public function setExportArtifact($exportArtifact)
  {
    $this->exportArtifact = $exportArtifact;
  }
  /**
   * @return XPSModelArtifactItem[]
   */
  public function getExportArtifact()
  {
    return $this->exportArtifact;
  }
  /**
   * Google Cloud Storage URI of decoded labels file for model export
   * 'dict.txt'.
   *
   * @param string $labelGcsUri
   */
  public function setLabelGcsUri($labelGcsUri)
  {
    $this->labelGcsUri = $labelGcsUri;
  }
  /**
   * @return string
   */
  public function getLabelGcsUri()
  {
    return $this->labelGcsUri;
  }
  /**
   * The default model binary file used for serving (e.g. online predict, batch
   * predict) via public Cloud AI Platform API.
   *
   * @param XPSModelArtifactItem $servingArtifact
   */
  public function setServingArtifact(XPSModelArtifactItem $servingArtifact)
  {
    $this->servingArtifact = $servingArtifact;
  }
  /**
   * @return XPSModelArtifactItem
   */
  public function getServingArtifact()
  {
    return $this->servingArtifact;
  }
  /**
   * Google Cloud Storage URI prefix of Tensorflow JavaScript binary files
   * 'groupX-shardXofX.bin'. Deprecated.
   *
   * @param string $tfJsBinaryGcsPrefix
   */
  public function setTfJsBinaryGcsPrefix($tfJsBinaryGcsPrefix)
  {
    $this->tfJsBinaryGcsPrefix = $tfJsBinaryGcsPrefix;
  }
  /**
   * @return string
   */
  public function getTfJsBinaryGcsPrefix()
  {
    return $this->tfJsBinaryGcsPrefix;
  }
  /**
   * Google Cloud Storage URI of Tensorflow Lite metadata
   * 'tflite_metadata.json'.
   *
   * @param string $tfLiteMetadataGcsUri
   */
  public function setTfLiteMetadataGcsUri($tfLiteMetadataGcsUri)
  {
    $this->tfLiteMetadataGcsUri = $tfLiteMetadataGcsUri;
  }
  /**
   * @return string
   */
  public function getTfLiteMetadataGcsUri()
  {
    return $this->tfLiteMetadataGcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageModelArtifactSpec::class, 'Google_Service_CloudNaturalLanguage_XPSImageModelArtifactSpec');
