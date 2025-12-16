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

namespace Google\Service\Fitness;

class DataSource extends \Google\Collection
{
  public const TYPE_raw = 'raw';
  public const TYPE_derived = 'derived';
  protected $collection_key = 'dataQualityStandard';
  protected $applicationType = Application::class;
  protected $applicationDataType = '';
  /**
   * DO NOT POPULATE THIS FIELD. It is never populated in responses from the
   * platform, and is ignored in queries. It will be removed in a future version
   * entirely.
   *
   * @deprecated
   * @var string[]
   */
  public $dataQualityStandard;
  /**
   * A unique identifier for the data stream produced by this data source. The
   * identifier includes: - The physical device's manufacturer, model, and
   * serial number (UID). - The application's package name or name. Package name
   * is used when the data source was created by an Android application. The
   * developer project number is used when the data source was created by a REST
   * client. - The data source's type. - The data source's stream name. Note
   * that not all attributes of the data source are used as part of the stream
   * identifier. In particular, the version of the hardware/the application
   * isn't used. This allows us to preserve the same stream through version
   * updates. This also means that two DataSource objects may represent the same
   * data stream even if they're not equal. The exact format of the data stream
   * ID created by an Android application is: type:dataType.name:application.pac
   * kageName:device.manufacturer:device.model:device.uid:dataStreamName The
   * exact format of the data stream ID created by a REST client is:
   * type:dataType.name:developer project
   * number:device.manufacturer:device.model:device.uid:dataStreamName When any
   * of the optional fields that make up the data stream ID are absent, they
   * will be omitted from the data stream ID. The minimum viable data stream ID
   * would be: type:dataType.name:developer project number Finally, the
   * developer project number and device UID are obfuscated when read by any
   * REST or Android client that did not create the data source. Only the data
   * source creator will see the developer project number in clear and normal
   * form. This means a client will see a different set of data_stream_ids than
   * another client with different credentials.
   *
   * @var string
   */
  public $dataStreamId;
  /**
   * The stream name uniquely identifies this particular data source among other
   * data sources of the same type from the same underlying producer. Setting
   * the stream name is optional, but should be done whenever an application
   * exposes two streams for the same data type, or when a device has two
   * equivalent sensors.
   *
   * @var string
   */
  public $dataStreamName;
  protected $dataTypeType = DataType::class;
  protected $dataTypeDataType = '';
  protected $deviceType = Device::class;
  protected $deviceDataType = '';
  /**
   * An end-user visible name for this data source.
   *
   * @deprecated
   * @var string
   */
  public $name;
  /**
   * A constant describing the type of this data source. Indicates whether this
   * data source produces raw or derived data.
   *
   * @var string
   */
  public $type;

  /**
   * Information about an application which feeds sensor data into the platform.
   *
   * @param Application $application
   */
  public function setApplication(Application $application)
  {
    $this->application = $application;
  }
  /**
   * @return Application
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * DO NOT POPULATE THIS FIELD. It is never populated in responses from the
   * platform, and is ignored in queries. It will be removed in a future version
   * entirely.
   *
   * @deprecated
   * @param string[] $dataQualityStandard
   */
  public function setDataQualityStandard($dataQualityStandard)
  {
    $this->dataQualityStandard = $dataQualityStandard;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getDataQualityStandard()
  {
    return $this->dataQualityStandard;
  }
  /**
   * A unique identifier for the data stream produced by this data source. The
   * identifier includes: - The physical device's manufacturer, model, and
   * serial number (UID). - The application's package name or name. Package name
   * is used when the data source was created by an Android application. The
   * developer project number is used when the data source was created by a REST
   * client. - The data source's type. - The data source's stream name. Note
   * that not all attributes of the data source are used as part of the stream
   * identifier. In particular, the version of the hardware/the application
   * isn't used. This allows us to preserve the same stream through version
   * updates. This also means that two DataSource objects may represent the same
   * data stream even if they're not equal. The exact format of the data stream
   * ID created by an Android application is: type:dataType.name:application.pac
   * kageName:device.manufacturer:device.model:device.uid:dataStreamName The
   * exact format of the data stream ID created by a REST client is:
   * type:dataType.name:developer project
   * number:device.manufacturer:device.model:device.uid:dataStreamName When any
   * of the optional fields that make up the data stream ID are absent, they
   * will be omitted from the data stream ID. The minimum viable data stream ID
   * would be: type:dataType.name:developer project number Finally, the
   * developer project number and device UID are obfuscated when read by any
   * REST or Android client that did not create the data source. Only the data
   * source creator will see the developer project number in clear and normal
   * form. This means a client will see a different set of data_stream_ids than
   * another client with different credentials.
   *
   * @param string $dataStreamId
   */
  public function setDataStreamId($dataStreamId)
  {
    $this->dataStreamId = $dataStreamId;
  }
  /**
   * @return string
   */
  public function getDataStreamId()
  {
    return $this->dataStreamId;
  }
  /**
   * The stream name uniquely identifies this particular data source among other
   * data sources of the same type from the same underlying producer. Setting
   * the stream name is optional, but should be done whenever an application
   * exposes two streams for the same data type, or when a device has two
   * equivalent sensors.
   *
   * @param string $dataStreamName
   */
  public function setDataStreamName($dataStreamName)
  {
    $this->dataStreamName = $dataStreamName;
  }
  /**
   * @return string
   */
  public function getDataStreamName()
  {
    return $this->dataStreamName;
  }
  /**
   * The data type defines the schema for a stream of data being collected by,
   * inserted into, or queried from the Fitness API.
   *
   * @param DataType $dataType
   */
  public function setDataType(DataType $dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return DataType
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Representation of an integrated device (such as a phone or a wearable) that
   * can hold sensors.
   *
   * @param Device $device
   */
  public function setDevice(Device $device)
  {
    $this->device = $device;
  }
  /**
   * @return Device
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * An end-user visible name for this data source.
   *
   * @deprecated
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A constant describing the type of this data source. Indicates whether this
   * data source produces raw or derived data.
   *
   * Accepted values: raw, derived
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSource::class, 'Google_Service_Fitness_DataSource');
