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

class XPSExportModelOutputConfig extends \Google\Model
{
  protected $coreMlFormatType = XPSCoreMlFormat::class;
  protected $coreMlFormatDataType = '';
  protected $dockerFormatType = XPSDockerFormat::class;
  protected $dockerFormatDataType = '';
  protected $edgeTpuTfLiteFormatType = XPSEdgeTpuTfLiteFormat::class;
  protected $edgeTpuTfLiteFormatDataType = '';
  /**
   * For any model and format: If true, will additionally export
   * FirebaseExportedModelInfo in a firebase.txt file.
   *
   * @var bool
   */
  public $exportFirebaseAuxiliaryInfo;
  /**
   * The Google Contained Registry path the exported files to be pushed to. This
   * location is set if the exported format is DOCKDER.
   *
   * @var string
   */
  public $outputGcrUri;
  /**
   * The Google Cloud Storage directory where XPS will output the exported
   * models and related files. Format: gs://bucket/directory
   *
   * @var string
   */
  public $outputGcsUri;
  protected $tfJsFormatType = XPSTfJsFormat::class;
  protected $tfJsFormatDataType = '';
  protected $tfLiteFormatType = XPSTfLiteFormat::class;
  protected $tfLiteFormatDataType = '';
  protected $tfSavedModelFormatType = XPSTfSavedModelFormat::class;
  protected $tfSavedModelFormatDataType = '';

  /**
   * @param XPSCoreMlFormat $coreMlFormat
   */
  public function setCoreMlFormat(XPSCoreMlFormat $coreMlFormat)
  {
    $this->coreMlFormat = $coreMlFormat;
  }
  /**
   * @return XPSCoreMlFormat
   */
  public function getCoreMlFormat()
  {
    return $this->coreMlFormat;
  }
  /**
   * @param XPSDockerFormat $dockerFormat
   */
  public function setDockerFormat(XPSDockerFormat $dockerFormat)
  {
    $this->dockerFormat = $dockerFormat;
  }
  /**
   * @return XPSDockerFormat
   */
  public function getDockerFormat()
  {
    return $this->dockerFormat;
  }
  /**
   * @param XPSEdgeTpuTfLiteFormat $edgeTpuTfLiteFormat
   */
  public function setEdgeTpuTfLiteFormat(XPSEdgeTpuTfLiteFormat $edgeTpuTfLiteFormat)
  {
    $this->edgeTpuTfLiteFormat = $edgeTpuTfLiteFormat;
  }
  /**
   * @return XPSEdgeTpuTfLiteFormat
   */
  public function getEdgeTpuTfLiteFormat()
  {
    return $this->edgeTpuTfLiteFormat;
  }
  /**
   * For any model and format: If true, will additionally export
   * FirebaseExportedModelInfo in a firebase.txt file.
   *
   * @param bool $exportFirebaseAuxiliaryInfo
   */
  public function setExportFirebaseAuxiliaryInfo($exportFirebaseAuxiliaryInfo)
  {
    $this->exportFirebaseAuxiliaryInfo = $exportFirebaseAuxiliaryInfo;
  }
  /**
   * @return bool
   */
  public function getExportFirebaseAuxiliaryInfo()
  {
    return $this->exportFirebaseAuxiliaryInfo;
  }
  /**
   * The Google Contained Registry path the exported files to be pushed to. This
   * location is set if the exported format is DOCKDER.
   *
   * @param string $outputGcrUri
   */
  public function setOutputGcrUri($outputGcrUri)
  {
    $this->outputGcrUri = $outputGcrUri;
  }
  /**
   * @return string
   */
  public function getOutputGcrUri()
  {
    return $this->outputGcrUri;
  }
  /**
   * The Google Cloud Storage directory where XPS will output the exported
   * models and related files. Format: gs://bucket/directory
   *
   * @param string $outputGcsUri
   */
  public function setOutputGcsUri($outputGcsUri)
  {
    $this->outputGcsUri = $outputGcsUri;
  }
  /**
   * @return string
   */
  public function getOutputGcsUri()
  {
    return $this->outputGcsUri;
  }
  /**
   * @param XPSTfJsFormat $tfJsFormat
   */
  public function setTfJsFormat(XPSTfJsFormat $tfJsFormat)
  {
    $this->tfJsFormat = $tfJsFormat;
  }
  /**
   * @return XPSTfJsFormat
   */
  public function getTfJsFormat()
  {
    return $this->tfJsFormat;
  }
  /**
   * @param XPSTfLiteFormat $tfLiteFormat
   */
  public function setTfLiteFormat(XPSTfLiteFormat $tfLiteFormat)
  {
    $this->tfLiteFormat = $tfLiteFormat;
  }
  /**
   * @return XPSTfLiteFormat
   */
  public function getTfLiteFormat()
  {
    return $this->tfLiteFormat;
  }
  /**
   * @param XPSTfSavedModelFormat $tfSavedModelFormat
   */
  public function setTfSavedModelFormat(XPSTfSavedModelFormat $tfSavedModelFormat)
  {
    $this->tfSavedModelFormat = $tfSavedModelFormat;
  }
  /**
   * @return XPSTfSavedModelFormat
   */
  public function getTfSavedModelFormat()
  {
    return $this->tfSavedModelFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSExportModelOutputConfig::class, 'Google_Service_CloudNaturalLanguage_XPSExportModelOutputConfig');
