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

namespace Google\Service\AndroidEnterprise;

class Notification extends \Google\Model
{
  public const NOTIFICATION_TYPE_unknown = 'unknown';
  /**
   * A test push notification.
   */
  public const NOTIFICATION_TYPE_testNotification = 'testNotification';
  /**
   * Notification about change to a product's approval status.
   */
  public const NOTIFICATION_TYPE_productApproval = 'productApproval';
  /**
   * Notification about an app installation failure.
   */
  public const NOTIFICATION_TYPE_installFailure = 'installFailure';
  /**
   * Notification about app update.
   */
  public const NOTIFICATION_TYPE_appUpdate = 'appUpdate';
  /**
   * Notification about new app permissions.
   */
  public const NOTIFICATION_TYPE_newPermissions = 'newPermissions';
  /**
   * Notification about new app restrictions schema change.
   */
  public const NOTIFICATION_TYPE_appRestricionsSchemaChange = 'appRestricionsSchemaChange';
  /**
   * Notification about product availability change.
   */
  public const NOTIFICATION_TYPE_productAvailabilityChange = 'productAvailabilityChange';
  /**
   * Notification about a new device.
   */
  public const NOTIFICATION_TYPE_newDevice = 'newDevice';
  /**
   * Notification about an updated device report.
   */
  public const NOTIFICATION_TYPE_deviceReportUpdate = 'deviceReportUpdate';
  /**
   * Notification about an enterprise upgrade.
   */
  public const NOTIFICATION_TYPE_enterpriseUpgrade = 'enterpriseUpgrade';
  protected $appRestrictionsSchemaChangeEventType = AppRestrictionsSchemaChangeEvent::class;
  protected $appRestrictionsSchemaChangeEventDataType = '';
  protected $appUpdateEventType = AppUpdateEvent::class;
  protected $appUpdateEventDataType = '';
  protected $deviceReportUpdateEventType = DeviceReportUpdateEvent::class;
  protected $deviceReportUpdateEventDataType = '';
  /**
   * The ID of the enterprise for which the notification is sent. This will
   * always be present.
   *
   * @var string
   */
  public $enterpriseId;
  protected $enterpriseUpgradeEventType = EnterpriseUpgradeEvent::class;
  protected $enterpriseUpgradeEventDataType = '';
  protected $installFailureEventType = InstallFailureEvent::class;
  protected $installFailureEventDataType = '';
  protected $newDeviceEventType = NewDeviceEvent::class;
  protected $newDeviceEventDataType = '';
  protected $newPermissionsEventType = NewPermissionsEvent::class;
  protected $newPermissionsEventDataType = '';
  /**
   * Type of the notification.
   *
   * @var string
   */
  public $notificationType;
  protected $productApprovalEventType = ProductApprovalEvent::class;
  protected $productApprovalEventDataType = '';
  protected $productAvailabilityChangeEventType = ProductAvailabilityChangeEvent::class;
  protected $productAvailabilityChangeEventDataType = '';
  /**
   * The time when the notification was published in milliseconds since
   * 1970-01-01T00:00:00Z. This will always be present.
   *
   * @var string
   */
  public $timestampMillis;

  /**
   * Notifications about new app restrictions schema changes.
   *
   * @param AppRestrictionsSchemaChangeEvent $appRestrictionsSchemaChangeEvent
   */
  public function setAppRestrictionsSchemaChangeEvent(AppRestrictionsSchemaChangeEvent $appRestrictionsSchemaChangeEvent)
  {
    $this->appRestrictionsSchemaChangeEvent = $appRestrictionsSchemaChangeEvent;
  }
  /**
   * @return AppRestrictionsSchemaChangeEvent
   */
  public function getAppRestrictionsSchemaChangeEvent()
  {
    return $this->appRestrictionsSchemaChangeEvent;
  }
  /**
   * Notifications about app updates.
   *
   * @param AppUpdateEvent $appUpdateEvent
   */
  public function setAppUpdateEvent(AppUpdateEvent $appUpdateEvent)
  {
    $this->appUpdateEvent = $appUpdateEvent;
  }
  /**
   * @return AppUpdateEvent
   */
  public function getAppUpdateEvent()
  {
    return $this->appUpdateEvent;
  }
  /**
   * Notifications about device report updates.
   *
   * @param DeviceReportUpdateEvent $deviceReportUpdateEvent
   */
  public function setDeviceReportUpdateEvent(DeviceReportUpdateEvent $deviceReportUpdateEvent)
  {
    $this->deviceReportUpdateEvent = $deviceReportUpdateEvent;
  }
  /**
   * @return DeviceReportUpdateEvent
   */
  public function getDeviceReportUpdateEvent()
  {
    return $this->deviceReportUpdateEvent;
  }
  /**
   * The ID of the enterprise for which the notification is sent. This will
   * always be present.
   *
   * @param string $enterpriseId
   */
  public function setEnterpriseId($enterpriseId)
  {
    $this->enterpriseId = $enterpriseId;
  }
  /**
   * @return string
   */
  public function getEnterpriseId()
  {
    return $this->enterpriseId;
  }
  /**
   * Notifications about enterprise upgrade.
   *
   * @param EnterpriseUpgradeEvent $enterpriseUpgradeEvent
   */
  public function setEnterpriseUpgradeEvent(EnterpriseUpgradeEvent $enterpriseUpgradeEvent)
  {
    $this->enterpriseUpgradeEvent = $enterpriseUpgradeEvent;
  }
  /**
   * @return EnterpriseUpgradeEvent
   */
  public function getEnterpriseUpgradeEvent()
  {
    return $this->enterpriseUpgradeEvent;
  }
  /**
   * Notifications about an app installation failure.
   *
   * @param InstallFailureEvent $installFailureEvent
   */
  public function setInstallFailureEvent(InstallFailureEvent $installFailureEvent)
  {
    $this->installFailureEvent = $installFailureEvent;
  }
  /**
   * @return InstallFailureEvent
   */
  public function getInstallFailureEvent()
  {
    return $this->installFailureEvent;
  }
  /**
   * Notifications about new devices.
   *
   * @param NewDeviceEvent $newDeviceEvent
   */
  public function setNewDeviceEvent(NewDeviceEvent $newDeviceEvent)
  {
    $this->newDeviceEvent = $newDeviceEvent;
  }
  /**
   * @return NewDeviceEvent
   */
  public function getNewDeviceEvent()
  {
    return $this->newDeviceEvent;
  }
  /**
   * Notifications about new app permissions.
   *
   * @param NewPermissionsEvent $newPermissionsEvent
   */
  public function setNewPermissionsEvent(NewPermissionsEvent $newPermissionsEvent)
  {
    $this->newPermissionsEvent = $newPermissionsEvent;
  }
  /**
   * @return NewPermissionsEvent
   */
  public function getNewPermissionsEvent()
  {
    return $this->newPermissionsEvent;
  }
  /**
   * Type of the notification.
   *
   * Accepted values: unknown, testNotification, productApproval,
   * installFailure, appUpdate, newPermissions, appRestricionsSchemaChange,
   * productAvailabilityChange, newDevice, deviceReportUpdate, enterpriseUpgrade
   *
   * @param self::NOTIFICATION_TYPE_* $notificationType
   */
  public function setNotificationType($notificationType)
  {
    $this->notificationType = $notificationType;
  }
  /**
   * @return self::NOTIFICATION_TYPE_*
   */
  public function getNotificationType()
  {
    return $this->notificationType;
  }
  /**
   * Notifications about changes to a product's approval status.
   *
   * @param ProductApprovalEvent $productApprovalEvent
   */
  public function setProductApprovalEvent(ProductApprovalEvent $productApprovalEvent)
  {
    $this->productApprovalEvent = $productApprovalEvent;
  }
  /**
   * @return ProductApprovalEvent
   */
  public function getProductApprovalEvent()
  {
    return $this->productApprovalEvent;
  }
  /**
   * Notifications about product availability changes.
   *
   * @param ProductAvailabilityChangeEvent $productAvailabilityChangeEvent
   */
  public function setProductAvailabilityChangeEvent(ProductAvailabilityChangeEvent $productAvailabilityChangeEvent)
  {
    $this->productAvailabilityChangeEvent = $productAvailabilityChangeEvent;
  }
  /**
   * @return ProductAvailabilityChangeEvent
   */
  public function getProductAvailabilityChangeEvent()
  {
    return $this->productAvailabilityChangeEvent;
  }
  /**
   * The time when the notification was published in milliseconds since
   * 1970-01-01T00:00:00Z. This will always be present.
   *
   * @param string $timestampMillis
   */
  public function setTimestampMillis($timestampMillis)
  {
    $this->timestampMillis = $timestampMillis;
  }
  /**
   * @return string
   */
  public function getTimestampMillis()
  {
    return $this->timestampMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Notification::class, 'Google_Service_AndroidEnterprise_Notification');
