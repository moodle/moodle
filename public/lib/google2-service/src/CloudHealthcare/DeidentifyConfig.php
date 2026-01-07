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

namespace Google\Service\CloudHealthcare;

class DeidentifyConfig extends \Google\Model
{
  protected $dicomType = DicomConfig::class;
  protected $dicomDataType = '';
  protected $fhirType = FhirConfig::class;
  protected $fhirDataType = '';
  protected $imageType = ImageConfig::class;
  protected $imageDataType = '';
  protected $textType = TextConfig::class;
  protected $textDataType = '';
  /**
   * Optional. Ensures in-flight data remains in the region of origin during de-
   * identification. The default value is false. Using this option results in a
   * significant reduction of throughput, and is not compatible with `LOCATION`
   * or `ORGANIZATION_NAME` infoTypes. `LOCATION` must be excluded within
   * TextConfig, and must also be excluded within ImageConfig if image redaction
   * is required.
   *
   * @var bool
   */
  public $useRegionalDataProcessing;

  /**
   * Optional. Configures de-id of application/DICOM content.
   *
   * @param DicomConfig $dicom
   */
  public function setDicom(DicomConfig $dicom)
  {
    $this->dicom = $dicom;
  }
  /**
   * @return DicomConfig
   */
  public function getDicom()
  {
    return $this->dicom;
  }
  /**
   * Optional. Configures de-id of application/FHIR content.
   *
   * @param FhirConfig $fhir
   */
  public function setFhir(FhirConfig $fhir)
  {
    $this->fhir = $fhir;
  }
  /**
   * @return FhirConfig
   */
  public function getFhir()
  {
    return $this->fhir;
  }
  /**
   * Optional. Configures de-identification of image pixels wherever they are
   * found in the source_dataset.
   *
   * @param ImageConfig $image
   */
  public function setImage(ImageConfig $image)
  {
    $this->image = $image;
  }
  /**
   * @return ImageConfig
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Optional. Configures de-identification of text wherever it is found in the
   * source_dataset.
   *
   * @param TextConfig $text
   */
  public function setText(TextConfig $text)
  {
    $this->text = $text;
  }
  /**
   * @return TextConfig
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Optional. Ensures in-flight data remains in the region of origin during de-
   * identification. The default value is false. Using this option results in a
   * significant reduction of throughput, and is not compatible with `LOCATION`
   * or `ORGANIZATION_NAME` infoTypes. `LOCATION` must be excluded within
   * TextConfig, and must also be excluded within ImageConfig if image redaction
   * is required.
   *
   * @param bool $useRegionalDataProcessing
   */
  public function setUseRegionalDataProcessing($useRegionalDataProcessing)
  {
    $this->useRegionalDataProcessing = $useRegionalDataProcessing;
  }
  /**
   * @return bool
   */
  public function getUseRegionalDataProcessing()
  {
    return $this->useRegionalDataProcessing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeidentifyConfig::class, 'Google_Service_CloudHealthcare_DeidentifyConfig');
