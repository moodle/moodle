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

class GoogleCloudHealthcareV1DicomGcsDestination extends \Google\Model
{
  /**
   * MIME types supported by DICOM spec. Each file is written in the following
   * format:
   * `.../{study_id}/{series_id}/{instance_id}[/{frame_number}].{extension}` The
   * frame_number component exists only for multi-frame instances. Supported
   * MIME types are consistent with supported formats in DICOMweb:
   * https://cloud.google.com/healthcare/docs/dicom#retrieve_transaction.
   * Specifically, the following are supported: - application/dicom; transfer-
   * syntax=1.2.840.10008.1.2.1 (uncompressed DICOM) - application/dicom;
   * transfer-syntax=1.2.840.10008.1.2.4.50 (DICOM with embedded JPEG Baseline)
   * - application/dicom; transfer-syntax=1.2.840.10008.1.2.4.90 (DICOM with
   * embedded JPEG 2000 Lossless Only) - application/dicom; transfer-
   * syntax=1.2.840.10008.1.2.4.91 (DICOM with embedded JPEG 2000) -
   * application/dicom; transfer-syntax=* (DICOM with no transcoding) -
   * application/octet-stream; transfer-syntax=1.2.840.10008.1.2.1 (raw
   * uncompressed PixelData) - application/octet-stream; transfer-syntax=* (raw
   * PixelData in whatever format it was uploaded in) - image/jpeg; transfer-
   * syntax=1.2.840.10008.1.2.4.50 (Consumer JPEG) - image/png The following
   * extensions are used for output files: - application/dicom -> .dcm -
   * image/jpeg -> .jpg - image/png -> .png - application/octet-stream -> no
   * extension If unspecified, the instances are exported in the original DICOM
   * format they were uploaded in.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The Cloud Storage destination to export to. URI for a Cloud Storage
   * directory where the server writes the result files, in the format
   * `gs://{bucket-id}/{path/to/destination/dir}`). If there is no trailing
   * slash, the service appends one when composing the object path. The user is
   * responsible for creating the Cloud Storage bucket referenced in
   * `uri_prefix`.
   *
   * @var string
   */
  public $uriPrefix;

  /**
   * MIME types supported by DICOM spec. Each file is written in the following
   * format:
   * `.../{study_id}/{series_id}/{instance_id}[/{frame_number}].{extension}` The
   * frame_number component exists only for multi-frame instances. Supported
   * MIME types are consistent with supported formats in DICOMweb:
   * https://cloud.google.com/healthcare/docs/dicom#retrieve_transaction.
   * Specifically, the following are supported: - application/dicom; transfer-
   * syntax=1.2.840.10008.1.2.1 (uncompressed DICOM) - application/dicom;
   * transfer-syntax=1.2.840.10008.1.2.4.50 (DICOM with embedded JPEG Baseline)
   * - application/dicom; transfer-syntax=1.2.840.10008.1.2.4.90 (DICOM with
   * embedded JPEG 2000 Lossless Only) - application/dicom; transfer-
   * syntax=1.2.840.10008.1.2.4.91 (DICOM with embedded JPEG 2000) -
   * application/dicom; transfer-syntax=* (DICOM with no transcoding) -
   * application/octet-stream; transfer-syntax=1.2.840.10008.1.2.1 (raw
   * uncompressed PixelData) - application/octet-stream; transfer-syntax=* (raw
   * PixelData in whatever format it was uploaded in) - image/jpeg; transfer-
   * syntax=1.2.840.10008.1.2.4.50 (Consumer JPEG) - image/png The following
   * extensions are used for output files: - application/dicom -> .dcm -
   * image/jpeg -> .jpg - image/png -> .png - application/octet-stream -> no
   * extension If unspecified, the instances are exported in the original DICOM
   * format they were uploaded in.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The Cloud Storage destination to export to. URI for a Cloud Storage
   * directory where the server writes the result files, in the format
   * `gs://{bucket-id}/{path/to/destination/dir}`). If there is no trailing
   * slash, the service appends one when composing the object path. The user is
   * responsible for creating the Cloud Storage bucket referenced in
   * `uri_prefix`.
   *
   * @param string $uriPrefix
   */
  public function setUriPrefix($uriPrefix)
  {
    $this->uriPrefix = $uriPrefix;
  }
  /**
   * @return string
   */
  public function getUriPrefix()
  {
    return $this->uriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudHealthcareV1DicomGcsDestination::class, 'Google_Service_CloudHealthcare_GoogleCloudHealthcareV1DicomGcsDestination');
