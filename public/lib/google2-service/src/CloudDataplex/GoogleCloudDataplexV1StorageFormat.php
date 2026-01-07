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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1StorageFormat extends \Google\Model
{
  /**
   * CompressionFormat unspecified. Implies uncompressed data.
   */
  public const COMPRESSION_FORMAT_COMPRESSION_FORMAT_UNSPECIFIED = 'COMPRESSION_FORMAT_UNSPECIFIED';
  /**
   * GZip compressed set of files.
   */
  public const COMPRESSION_FORMAT_GZIP = 'GZIP';
  /**
   * BZip2 compressed set of files.
   */
  public const COMPRESSION_FORMAT_BZIP2 = 'BZIP2';
  /**
   * Format unspecified.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * Parquet-formatted structured data.
   */
  public const FORMAT_PARQUET = 'PARQUET';
  /**
   * Avro-formatted structured data.
   */
  public const FORMAT_AVRO = 'AVRO';
  /**
   * Orc-formatted structured data.
   */
  public const FORMAT_ORC = 'ORC';
  /**
   * Csv-formatted semi-structured data.
   */
  public const FORMAT_CSV = 'CSV';
  /**
   * Json-formatted semi-structured data.
   */
  public const FORMAT_JSON = 'JSON';
  /**
   * Image data formats (such as jpg and png).
   */
  public const FORMAT_IMAGE = 'IMAGE';
  /**
   * Audio data formats (such as mp3, and wav).
   */
  public const FORMAT_AUDIO = 'AUDIO';
  /**
   * Video data formats (such as mp4 and mpg).
   */
  public const FORMAT_VIDEO = 'VIDEO';
  /**
   * Textual data formats (such as txt and xml).
   */
  public const FORMAT_TEXT = 'TEXT';
  /**
   * TensorFlow record format.
   */
  public const FORMAT_TFRECORD = 'TFRECORD';
  /**
   * Data that doesn't match a specific format.
   */
  public const FORMAT_OTHER = 'OTHER';
  /**
   * Data of an unknown format.
   */
  public const FORMAT_UNKNOWN = 'UNKNOWN';
  /**
   * Optional. The compression type associated with the stored data. If
   * unspecified, the data is uncompressed.
   *
   * @var string
   */
  public $compressionFormat;
  protected $csvType = GoogleCloudDataplexV1StorageFormatCsvOptions::class;
  protected $csvDataType = '';
  /**
   * Output only. The data format associated with the stored data, which
   * represents content type values. The value is inferred from mime type.
   *
   * @var string
   */
  public $format;
  protected $icebergType = GoogleCloudDataplexV1StorageFormatIcebergOptions::class;
  protected $icebergDataType = '';
  protected $jsonType = GoogleCloudDataplexV1StorageFormatJsonOptions::class;
  protected $jsonDataType = '';
  /**
   * Required. The mime type descriptor for the data. Must match the pattern
   * {type}/{subtype}. Supported values: application/x-parquet
   * application/x-avro application/x-orc application/x-tfrecord
   * application/x-parquet+iceberg application/x-avro+iceberg
   * application/x-orc+iceberg application/json application/{subtypes} text/csv
   * text/ image/{image subtype} video/{video subtype} audio/{audio subtype}
   *
   * @var string
   */
  public $mimeType;

  /**
   * Optional. The compression type associated with the stored data. If
   * unspecified, the data is uncompressed.
   *
   * Accepted values: COMPRESSION_FORMAT_UNSPECIFIED, GZIP, BZIP2
   *
   * @param self::COMPRESSION_FORMAT_* $compressionFormat
   */
  public function setCompressionFormat($compressionFormat)
  {
    $this->compressionFormat = $compressionFormat;
  }
  /**
   * @return self::COMPRESSION_FORMAT_*
   */
  public function getCompressionFormat()
  {
    return $this->compressionFormat;
  }
  /**
   * Optional. Additional information about CSV formatted data.
   *
   * @param GoogleCloudDataplexV1StorageFormatCsvOptions $csv
   */
  public function setCsv(GoogleCloudDataplexV1StorageFormatCsvOptions $csv)
  {
    $this->csv = $csv;
  }
  /**
   * @return GoogleCloudDataplexV1StorageFormatCsvOptions
   */
  public function getCsv()
  {
    return $this->csv;
  }
  /**
   * Output only. The data format associated with the stored data, which
   * represents content type values. The value is inferred from mime type.
   *
   * Accepted values: FORMAT_UNSPECIFIED, PARQUET, AVRO, ORC, CSV, JSON, IMAGE,
   * AUDIO, VIDEO, TEXT, TFRECORD, OTHER, UNKNOWN
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Optional. Additional information about iceberg tables.
   *
   * @param GoogleCloudDataplexV1StorageFormatIcebergOptions $iceberg
   */
  public function setIceberg(GoogleCloudDataplexV1StorageFormatIcebergOptions $iceberg)
  {
    $this->iceberg = $iceberg;
  }
  /**
   * @return GoogleCloudDataplexV1StorageFormatIcebergOptions
   */
  public function getIceberg()
  {
    return $this->iceberg;
  }
  /**
   * Optional. Additional information about CSV formatted data.
   *
   * @param GoogleCloudDataplexV1StorageFormatJsonOptions $json
   */
  public function setJson(GoogleCloudDataplexV1StorageFormatJsonOptions $json)
  {
    $this->json = $json;
  }
  /**
   * @return GoogleCloudDataplexV1StorageFormatJsonOptions
   */
  public function getJson()
  {
    return $this->json;
  }
  /**
   * Required. The mime type descriptor for the data. Must match the pattern
   * {type}/{subtype}. Supported values: application/x-parquet
   * application/x-avro application/x-orc application/x-tfrecord
   * application/x-parquet+iceberg application/x-avro+iceberg
   * application/x-orc+iceberg application/json application/{subtypes} text/csv
   * text/ image/{image subtype} video/{video subtype} audio/{audio subtype}
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1StorageFormat::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1StorageFormat');
