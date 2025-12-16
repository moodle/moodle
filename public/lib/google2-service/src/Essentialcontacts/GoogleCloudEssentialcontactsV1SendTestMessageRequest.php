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

namespace Google\Service\Essentialcontacts;

class GoogleCloudEssentialcontactsV1SendTestMessageRequest extends \Google\Collection
{
  /**
   * Notification category is unrecognized or unspecified.
   */
  public const NOTIFICATION_CATEGORY_NOTIFICATION_CATEGORY_UNSPECIFIED = 'NOTIFICATION_CATEGORY_UNSPECIFIED';
  /**
   * All notifications related to the resource, including notifications
   * pertaining to categories added in the future.
   */
  public const NOTIFICATION_CATEGORY_ALL = 'ALL';
  /**
   * Notifications related to imminent account suspension.
   */
  public const NOTIFICATION_CATEGORY_SUSPENSION = 'SUSPENSION';
  /**
   * Notifications related to security/privacy incidents, notifications, and
   * vulnerabilities.
   */
  public const NOTIFICATION_CATEGORY_SECURITY = 'SECURITY';
  /**
   * Notifications related to technical events and issues such as outages,
   * errors, or bugs.
   */
  public const NOTIFICATION_CATEGORY_TECHNICAL = 'TECHNICAL';
  /**
   * Notifications related to billing and payments notifications, price updates,
   * errors, or credits.
   */
  public const NOTIFICATION_CATEGORY_BILLING = 'BILLING';
  /**
   * Notifications related to enforcement actions, regulatory compliance, or
   * government notices.
   */
  public const NOTIFICATION_CATEGORY_LEGAL = 'LEGAL';
  /**
   * Notifications related to new versions, product terms updates, or
   * deprecations.
   */
  public const NOTIFICATION_CATEGORY_PRODUCT_UPDATES = 'PRODUCT_UPDATES';
  /**
   * Child category of TECHNICAL. If assigned, technical incident notifications
   * will go to these contacts instead of TECHNICAL.
   */
  public const NOTIFICATION_CATEGORY_TECHNICAL_INCIDENTS = 'TECHNICAL_INCIDENTS';
  protected $collection_key = 'contacts';
  /**
   * Required. The list of names of the contacts to send a test message to.
   * Format: organizations/{organization_id}/contacts/{contact_id},
   * folders/{folder_id}/contacts/{contact_id} or
   * projects/{project_id}/contacts/{contact_id}
   *
   * @var string[]
   */
  public $contacts;
  /**
   * Required. The notification category to send the test message for. All
   * contacts must be subscribed to this category.
   *
   * @var string
   */
  public $notificationCategory;

  /**
   * Required. The list of names of the contacts to send a test message to.
   * Format: organizations/{organization_id}/contacts/{contact_id},
   * folders/{folder_id}/contacts/{contact_id} or
   * projects/{project_id}/contacts/{contact_id}
   *
   * @param string[] $contacts
   */
  public function setContacts($contacts)
  {
    $this->contacts = $contacts;
  }
  /**
   * @return string[]
   */
  public function getContacts()
  {
    return $this->contacts;
  }
  /**
   * Required. The notification category to send the test message for. All
   * contacts must be subscribed to this category.
   *
   * Accepted values: NOTIFICATION_CATEGORY_UNSPECIFIED, ALL, SUSPENSION,
   * SECURITY, TECHNICAL, BILLING, LEGAL, PRODUCT_UPDATES, TECHNICAL_INCIDENTS
   *
   * @param self::NOTIFICATION_CATEGORY_* $notificationCategory
   */
  public function setNotificationCategory($notificationCategory)
  {
    $this->notificationCategory = $notificationCategory;
  }
  /**
   * @return self::NOTIFICATION_CATEGORY_*
   */
  public function getNotificationCategory()
  {
    return $this->notificationCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEssentialcontactsV1SendTestMessageRequest::class, 'Google_Service_Essentialcontacts_GoogleCloudEssentialcontactsV1SendTestMessageRequest');
