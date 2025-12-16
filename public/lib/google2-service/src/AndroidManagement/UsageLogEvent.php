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

namespace Google\Service\AndroidManagement;

class UsageLogEvent extends \Google\Model
{
  /**
   * This value is not used
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Indicates adb_shell_command_event has been set.
   */
  public const EVENT_TYPE_ADB_SHELL_COMMAND = 'ADB_SHELL_COMMAND';
  /**
   * Indicates adb_shell_interactive_event has been set.
   */
  public const EVENT_TYPE_ADB_SHELL_INTERACTIVE = 'ADB_SHELL_INTERACTIVE';
  /**
   * Indicates app_process_start_event has been set.
   */
  public const EVENT_TYPE_APP_PROCESS_START = 'APP_PROCESS_START';
  /**
   * Indicates keyguard_dismissed_event has been set.
   */
  public const EVENT_TYPE_KEYGUARD_DISMISSED = 'KEYGUARD_DISMISSED';
  /**
   * Indicates keyguard_dismiss_auth_attempt_event has been set.
   */
  public const EVENT_TYPE_KEYGUARD_DISMISS_AUTH_ATTEMPT = 'KEYGUARD_DISMISS_AUTH_ATTEMPT';
  /**
   * Indicates keyguard_secured_event has been set.
   */
  public const EVENT_TYPE_KEYGUARD_SECURED = 'KEYGUARD_SECURED';
  /**
   * Indicates file_pulled_event has been set.
   */
  public const EVENT_TYPE_FILE_PULLED = 'FILE_PULLED';
  /**
   * Indicates file_pushed_event has been set.
   */
  public const EVENT_TYPE_FILE_PUSHED = 'FILE_PUSHED';
  /**
   * Indicates cert_authority_installed_event has been set.
   */
  public const EVENT_TYPE_CERT_AUTHORITY_INSTALLED = 'CERT_AUTHORITY_INSTALLED';
  /**
   * Indicates cert_authority_removed_event has been set.
   */
  public const EVENT_TYPE_CERT_AUTHORITY_REMOVED = 'CERT_AUTHORITY_REMOVED';
  /**
   * Indicates cert_validation_failure_event has been set.
   */
  public const EVENT_TYPE_CERT_VALIDATION_FAILURE = 'CERT_VALIDATION_FAILURE';
  /**
   * Indicates crypto_self_test_completed_event has been set.
   */
  public const EVENT_TYPE_CRYPTO_SELF_TEST_COMPLETED = 'CRYPTO_SELF_TEST_COMPLETED';
  /**
   * Indicates key_destruction_event has been set.
   */
  public const EVENT_TYPE_KEY_DESTRUCTION = 'KEY_DESTRUCTION';
  /**
   * Indicates key_generated_event has been set.
   */
  public const EVENT_TYPE_KEY_GENERATED = 'KEY_GENERATED';
  /**
   * Indicates key_import_event has been set.
   */
  public const EVENT_TYPE_KEY_IMPORT = 'KEY_IMPORT';
  /**
   * Indicates key_integrity_violation_event has been set.
   */
  public const EVENT_TYPE_KEY_INTEGRITY_VIOLATION = 'KEY_INTEGRITY_VIOLATION';
  /**
   * Indicates logging_started_event has been set.
   */
  public const EVENT_TYPE_LOGGING_STARTED = 'LOGGING_STARTED';
  /**
   * Indicates logging_stopped_event has been set.
   */
  public const EVENT_TYPE_LOGGING_STOPPED = 'LOGGING_STOPPED';
  /**
   * Indicates log_buffer_size_critical_event has been set.
   */
  public const EVENT_TYPE_LOG_BUFFER_SIZE_CRITICAL = 'LOG_BUFFER_SIZE_CRITICAL';
  /**
   * Indicates media_mount_event has been set.
   */
  public const EVENT_TYPE_MEDIA_MOUNT = 'MEDIA_MOUNT';
  /**
   * Indicates media_unmount_event has been set.
   */
  public const EVENT_TYPE_MEDIA_UNMOUNT = 'MEDIA_UNMOUNT';
  /**
   * Indicates os_shutdown_event has been set.
   */
  public const EVENT_TYPE_OS_SHUTDOWN = 'OS_SHUTDOWN';
  /**
   * Indicates os_startup_event has been set.
   */
  public const EVENT_TYPE_OS_STARTUP = 'OS_STARTUP';
  /**
   * Indicates remote_lock_event has been set.
   */
  public const EVENT_TYPE_REMOTE_LOCK = 'REMOTE_LOCK';
  /**
   * Indicates wipe_failure_event has been set.
   */
  public const EVENT_TYPE_WIPE_FAILURE = 'WIPE_FAILURE';
  /**
   * Indicates connect_event has been set.
   */
  public const EVENT_TYPE_CONNECT = 'CONNECT';
  /**
   * Indicates dns_event has been set.
   */
  public const EVENT_TYPE_DNS = 'DNS';
  /**
   * Indicates stopLostModeUserAttemptEvent has been set.
   */
  public const EVENT_TYPE_STOP_LOST_MODE_USER_ATTEMPT = 'STOP_LOST_MODE_USER_ATTEMPT';
  /**
   * Indicates lostModeOutgoingPhoneCallEvent has been set.
   */
  public const EVENT_TYPE_LOST_MODE_OUTGOING_PHONE_CALL = 'LOST_MODE_OUTGOING_PHONE_CALL';
  /**
   * Indicates lostModeLocationEvent has been set.
   */
  public const EVENT_TYPE_LOST_MODE_LOCATION = 'LOST_MODE_LOCATION';
  /**
   * Indicates enrollment_complete_event has been set.
   */
  public const EVENT_TYPE_ENROLLMENT_COMPLETE = 'ENROLLMENT_COMPLETE';
  /**
   * Indicates backupServiceToggledEvent has been set.
   */
  public const EVENT_TYPE_BACKUP_SERVICE_TOGGLED = 'BACKUP_SERVICE_TOGGLED';
  protected $adbShellCommandEventType = AdbShellCommandEvent::class;
  protected $adbShellCommandEventDataType = '';
  protected $adbShellInteractiveEventType = AdbShellInteractiveEvent::class;
  protected $adbShellInteractiveEventDataType = '';
  protected $appProcessStartEventType = AppProcessStartEvent::class;
  protected $appProcessStartEventDataType = '';
  protected $backupServiceToggledEventType = BackupServiceToggledEvent::class;
  protected $backupServiceToggledEventDataType = '';
  protected $certAuthorityInstalledEventType = CertAuthorityInstalledEvent::class;
  protected $certAuthorityInstalledEventDataType = '';
  protected $certAuthorityRemovedEventType = CertAuthorityRemovedEvent::class;
  protected $certAuthorityRemovedEventDataType = '';
  protected $certValidationFailureEventType = CertValidationFailureEvent::class;
  protected $certValidationFailureEventDataType = '';
  protected $connectEventType = ConnectEvent::class;
  protected $connectEventDataType = '';
  protected $cryptoSelfTestCompletedEventType = CryptoSelfTestCompletedEvent::class;
  protected $cryptoSelfTestCompletedEventDataType = '';
  protected $dnsEventType = DnsEvent::class;
  protected $dnsEventDataType = '';
  protected $enrollmentCompleteEventType = EnrollmentCompleteEvent::class;
  protected $enrollmentCompleteEventDataType = '';
  /**
   * Unique id of the event.
   *
   * @var string
   */
  public $eventId;
  /**
   * Device timestamp when the event was logged.
   *
   * @var string
   */
  public $eventTime;
  /**
   * The particular usage log event type that was reported on the device. Use
   * this to determine which event field to access.
   *
   * @var string
   */
  public $eventType;
  protected $filePulledEventType = FilePulledEvent::class;
  protected $filePulledEventDataType = '';
  protected $filePushedEventType = FilePushedEvent::class;
  protected $filePushedEventDataType = '';
  protected $keyDestructionEventType = KeyDestructionEvent::class;
  protected $keyDestructionEventDataType = '';
  protected $keyGeneratedEventType = KeyGeneratedEvent::class;
  protected $keyGeneratedEventDataType = '';
  protected $keyImportEventType = KeyImportEvent::class;
  protected $keyImportEventDataType = '';
  protected $keyIntegrityViolationEventType = KeyIntegrityViolationEvent::class;
  protected $keyIntegrityViolationEventDataType = '';
  protected $keyguardDismissAuthAttemptEventType = KeyguardDismissAuthAttemptEvent::class;
  protected $keyguardDismissAuthAttemptEventDataType = '';
  protected $keyguardDismissedEventType = KeyguardDismissedEvent::class;
  protected $keyguardDismissedEventDataType = '';
  protected $keyguardSecuredEventType = KeyguardSecuredEvent::class;
  protected $keyguardSecuredEventDataType = '';
  protected $logBufferSizeCriticalEventType = LogBufferSizeCriticalEvent::class;
  protected $logBufferSizeCriticalEventDataType = '';
  protected $loggingStartedEventType = LoggingStartedEvent::class;
  protected $loggingStartedEventDataType = '';
  protected $loggingStoppedEventType = LoggingStoppedEvent::class;
  protected $loggingStoppedEventDataType = '';
  protected $lostModeLocationEventType = LostModeLocationEvent::class;
  protected $lostModeLocationEventDataType = '';
  protected $lostModeOutgoingPhoneCallEventType = LostModeOutgoingPhoneCallEvent::class;
  protected $lostModeOutgoingPhoneCallEventDataType = '';
  protected $mediaMountEventType = MediaMountEvent::class;
  protected $mediaMountEventDataType = '';
  protected $mediaUnmountEventType = MediaUnmountEvent::class;
  protected $mediaUnmountEventDataType = '';
  protected $osShutdownEventType = OsShutdownEvent::class;
  protected $osShutdownEventDataType = '';
  protected $osStartupEventType = OsStartupEvent::class;
  protected $osStartupEventDataType = '';
  protected $remoteLockEventType = RemoteLockEvent::class;
  protected $remoteLockEventDataType = '';
  protected $stopLostModeUserAttemptEventType = StopLostModeUserAttemptEvent::class;
  protected $stopLostModeUserAttemptEventDataType = '';
  protected $wipeFailureEventType = WipeFailureEvent::class;
  protected $wipeFailureEventDataType = '';

  /**
   * A shell command was issued over ADB via “adb shell command”. Part of
   * SECURITY_LOGS.
   *
   * @param AdbShellCommandEvent $adbShellCommandEvent
   */
  public function setAdbShellCommandEvent(AdbShellCommandEvent $adbShellCommandEvent)
  {
    $this->adbShellCommandEvent = $adbShellCommandEvent;
  }
  /**
   * @return AdbShellCommandEvent
   */
  public function getAdbShellCommandEvent()
  {
    return $this->adbShellCommandEvent;
  }
  /**
   * An ADB interactive shell was opened via “adb shell”. Part of SECURITY_LOGS.
   *
   * @param AdbShellInteractiveEvent $adbShellInteractiveEvent
   */
  public function setAdbShellInteractiveEvent(AdbShellInteractiveEvent $adbShellInteractiveEvent)
  {
    $this->adbShellInteractiveEvent = $adbShellInteractiveEvent;
  }
  /**
   * @return AdbShellInteractiveEvent
   */
  public function getAdbShellInteractiveEvent()
  {
    return $this->adbShellInteractiveEvent;
  }
  /**
   * An app process was started. Part of SECURITY_LOGS.
   *
   * @param AppProcessStartEvent $appProcessStartEvent
   */
  public function setAppProcessStartEvent(AppProcessStartEvent $appProcessStartEvent)
  {
    $this->appProcessStartEvent = $appProcessStartEvent;
  }
  /**
   * @return AppProcessStartEvent
   */
  public function getAppProcessStartEvent()
  {
    return $this->appProcessStartEvent;
  }
  /**
   * An admin has enabled or disabled backup service. Part of SECURITY_LOGS.
   *
   * @param BackupServiceToggledEvent $backupServiceToggledEvent
   */
  public function setBackupServiceToggledEvent(BackupServiceToggledEvent $backupServiceToggledEvent)
  {
    $this->backupServiceToggledEvent = $backupServiceToggledEvent;
  }
  /**
   * @return BackupServiceToggledEvent
   */
  public function getBackupServiceToggledEvent()
  {
    return $this->backupServiceToggledEvent;
  }
  /**
   * A new root certificate was installed into the system's trusted credential
   * storage. Part of SECURITY_LOGS.
   *
   * @param CertAuthorityInstalledEvent $certAuthorityInstalledEvent
   */
  public function setCertAuthorityInstalledEvent(CertAuthorityInstalledEvent $certAuthorityInstalledEvent)
  {
    $this->certAuthorityInstalledEvent = $certAuthorityInstalledEvent;
  }
  /**
   * @return CertAuthorityInstalledEvent
   */
  public function getCertAuthorityInstalledEvent()
  {
    return $this->certAuthorityInstalledEvent;
  }
  /**
   * A root certificate was removed from the system's trusted credential
   * storage. Part of SECURITY_LOGS.
   *
   * @param CertAuthorityRemovedEvent $certAuthorityRemovedEvent
   */
  public function setCertAuthorityRemovedEvent(CertAuthorityRemovedEvent $certAuthorityRemovedEvent)
  {
    $this->certAuthorityRemovedEvent = $certAuthorityRemovedEvent;
  }
  /**
   * @return CertAuthorityRemovedEvent
   */
  public function getCertAuthorityRemovedEvent()
  {
    return $this->certAuthorityRemovedEvent;
  }
  /**
   * An X.509v3 certificate failed to validate, currently this validation is
   * performed on the Wi-FI access point and failure may be due to a mismatch
   * upon server certificate validation. However it may in the future include
   * other validation events of an X.509v3 certificate. Part of SECURITY_LOGS.
   *
   * @param CertValidationFailureEvent $certValidationFailureEvent
   */
  public function setCertValidationFailureEvent(CertValidationFailureEvent $certValidationFailureEvent)
  {
    $this->certValidationFailureEvent = $certValidationFailureEvent;
  }
  /**
   * @return CertValidationFailureEvent
   */
  public function getCertValidationFailureEvent()
  {
    return $this->certValidationFailureEvent;
  }
  /**
   * A TCP connect event was initiated through the standard network stack. Part
   * of NETWORK_ACTIVITY_LOGS.
   *
   * @param ConnectEvent $connectEvent
   */
  public function setConnectEvent(ConnectEvent $connectEvent)
  {
    $this->connectEvent = $connectEvent;
  }
  /**
   * @return ConnectEvent
   */
  public function getConnectEvent()
  {
    return $this->connectEvent;
  }
  /**
   * Validates whether Android’s built-in cryptographic library (BoringSSL) is
   * valid. Should always succeed on device boot, if it fails, the device should
   * be considered untrusted. Part of SECURITY_LOGS.
   *
   * @param CryptoSelfTestCompletedEvent $cryptoSelfTestCompletedEvent
   */
  public function setCryptoSelfTestCompletedEvent(CryptoSelfTestCompletedEvent $cryptoSelfTestCompletedEvent)
  {
    $this->cryptoSelfTestCompletedEvent = $cryptoSelfTestCompletedEvent;
  }
  /**
   * @return CryptoSelfTestCompletedEvent
   */
  public function getCryptoSelfTestCompletedEvent()
  {
    return $this->cryptoSelfTestCompletedEvent;
  }
  /**
   * A DNS lookup event was initiated through the standard network stack. Part
   * of NETWORK_ACTIVITY_LOGS.
   *
   * @param DnsEvent $dnsEvent
   */
  public function setDnsEvent(DnsEvent $dnsEvent)
  {
    $this->dnsEvent = $dnsEvent;
  }
  /**
   * @return DnsEvent
   */
  public function getDnsEvent()
  {
    return $this->dnsEvent;
  }
  /**
   * Device has completed enrollment. Part of AMAPI_LOGS.
   *
   * @param EnrollmentCompleteEvent $enrollmentCompleteEvent
   */
  public function setEnrollmentCompleteEvent(EnrollmentCompleteEvent $enrollmentCompleteEvent)
  {
    $this->enrollmentCompleteEvent = $enrollmentCompleteEvent;
  }
  /**
   * @return EnrollmentCompleteEvent
   */
  public function getEnrollmentCompleteEvent()
  {
    return $this->enrollmentCompleteEvent;
  }
  /**
   * Unique id of the event.
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * Device timestamp when the event was logged.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The particular usage log event type that was reported on the device. Use
   * this to determine which event field to access.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, ADB_SHELL_COMMAND,
   * ADB_SHELL_INTERACTIVE, APP_PROCESS_START, KEYGUARD_DISMISSED,
   * KEYGUARD_DISMISS_AUTH_ATTEMPT, KEYGUARD_SECURED, FILE_PULLED, FILE_PUSHED,
   * CERT_AUTHORITY_INSTALLED, CERT_AUTHORITY_REMOVED, CERT_VALIDATION_FAILURE,
   * CRYPTO_SELF_TEST_COMPLETED, KEY_DESTRUCTION, KEY_GENERATED, KEY_IMPORT,
   * KEY_INTEGRITY_VIOLATION, LOGGING_STARTED, LOGGING_STOPPED,
   * LOG_BUFFER_SIZE_CRITICAL, MEDIA_MOUNT, MEDIA_UNMOUNT, OS_SHUTDOWN,
   * OS_STARTUP, REMOTE_LOCK, WIPE_FAILURE, CONNECT, DNS,
   * STOP_LOST_MODE_USER_ATTEMPT, LOST_MODE_OUTGOING_PHONE_CALL,
   * LOST_MODE_LOCATION, ENROLLMENT_COMPLETE, BACKUP_SERVICE_TOGGLED
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * A file was downloaded from the device. Part of SECURITY_LOGS.
   *
   * @param FilePulledEvent $filePulledEvent
   */
  public function setFilePulledEvent(FilePulledEvent $filePulledEvent)
  {
    $this->filePulledEvent = $filePulledEvent;
  }
  /**
   * @return FilePulledEvent
   */
  public function getFilePulledEvent()
  {
    return $this->filePulledEvent;
  }
  /**
   * A file was uploaded onto the device. Part of SECURITY_LOGS.
   *
   * @param FilePushedEvent $filePushedEvent
   */
  public function setFilePushedEvent(FilePushedEvent $filePushedEvent)
  {
    $this->filePushedEvent = $filePushedEvent;
  }
  /**
   * @return FilePushedEvent
   */
  public function getFilePushedEvent()
  {
    return $this->filePushedEvent;
  }
  /**
   * A cryptographic key including user installed, admin installed and system
   * maintained private key is removed from the device either by the user or
   * management. Part of SECURITY_LOGS.
   *
   * @param KeyDestructionEvent $keyDestructionEvent
   */
  public function setKeyDestructionEvent(KeyDestructionEvent $keyDestructionEvent)
  {
    $this->keyDestructionEvent = $keyDestructionEvent;
  }
  /**
   * @return KeyDestructionEvent
   */
  public function getKeyDestructionEvent()
  {
    return $this->keyDestructionEvent;
  }
  /**
   * A cryptographic key including user installed, admin installed and system
   * maintained private key is installed on the device either by the user or
   * management. Part of SECURITY_LOGS.
   *
   * @param KeyGeneratedEvent $keyGeneratedEvent
   */
  public function setKeyGeneratedEvent(KeyGeneratedEvent $keyGeneratedEvent)
  {
    $this->keyGeneratedEvent = $keyGeneratedEvent;
  }
  /**
   * @return KeyGeneratedEvent
   */
  public function getKeyGeneratedEvent()
  {
    return $this->keyGeneratedEvent;
  }
  /**
   * A cryptographic key including user installed, admin installed and system
   * maintained private key is imported on the device either by the user or
   * management. Part of SECURITY_LOGS.
   *
   * @param KeyImportEvent $keyImportEvent
   */
  public function setKeyImportEvent(KeyImportEvent $keyImportEvent)
  {
    $this->keyImportEvent = $keyImportEvent;
  }
  /**
   * @return KeyImportEvent
   */
  public function getKeyImportEvent()
  {
    return $this->keyImportEvent;
  }
  /**
   * A cryptographic key including user installed, admin installed and system
   * maintained private key is determined to be corrupted due to storage
   * corruption, hardware failure or some OS issue. Part of SECURITY_LOGS.
   *
   * @param KeyIntegrityViolationEvent $keyIntegrityViolationEvent
   */
  public function setKeyIntegrityViolationEvent(KeyIntegrityViolationEvent $keyIntegrityViolationEvent)
  {
    $this->keyIntegrityViolationEvent = $keyIntegrityViolationEvent;
  }
  /**
   * @return KeyIntegrityViolationEvent
   */
  public function getKeyIntegrityViolationEvent()
  {
    return $this->keyIntegrityViolationEvent;
  }
  /**
   * An attempt was made to unlock the device. Part of SECURITY_LOGS.
   *
   * @param KeyguardDismissAuthAttemptEvent $keyguardDismissAuthAttemptEvent
   */
  public function setKeyguardDismissAuthAttemptEvent(KeyguardDismissAuthAttemptEvent $keyguardDismissAuthAttemptEvent)
  {
    $this->keyguardDismissAuthAttemptEvent = $keyguardDismissAuthAttemptEvent;
  }
  /**
   * @return KeyguardDismissAuthAttemptEvent
   */
  public function getKeyguardDismissAuthAttemptEvent()
  {
    return $this->keyguardDismissAuthAttemptEvent;
  }
  /**
   * The keyguard was dismissed. Part of SECURITY_LOGS.
   *
   * @param KeyguardDismissedEvent $keyguardDismissedEvent
   */
  public function setKeyguardDismissedEvent(KeyguardDismissedEvent $keyguardDismissedEvent)
  {
    $this->keyguardDismissedEvent = $keyguardDismissedEvent;
  }
  /**
   * @return KeyguardDismissedEvent
   */
  public function getKeyguardDismissedEvent()
  {
    return $this->keyguardDismissedEvent;
  }
  /**
   * The device was locked either by user or timeout. Part of SECURITY_LOGS.
   *
   * @param KeyguardSecuredEvent $keyguardSecuredEvent
   */
  public function setKeyguardSecuredEvent(KeyguardSecuredEvent $keyguardSecuredEvent)
  {
    $this->keyguardSecuredEvent = $keyguardSecuredEvent;
  }
  /**
   * @return KeyguardSecuredEvent
   */
  public function getKeyguardSecuredEvent()
  {
    return $this->keyguardSecuredEvent;
  }
  /**
   * The audit log buffer has reached 90% of its capacity, therefore older
   * events may be dropped. Part of SECURITY_LOGS.
   *
   * @param LogBufferSizeCriticalEvent $logBufferSizeCriticalEvent
   */
  public function setLogBufferSizeCriticalEvent(LogBufferSizeCriticalEvent $logBufferSizeCriticalEvent)
  {
    $this->logBufferSizeCriticalEvent = $logBufferSizeCriticalEvent;
  }
  /**
   * @return LogBufferSizeCriticalEvent
   */
  public function getLogBufferSizeCriticalEvent()
  {
    return $this->logBufferSizeCriticalEvent;
  }
  /**
   * usageLog policy has been enabled. Part of SECURITY_LOGS.
   *
   * @param LoggingStartedEvent $loggingStartedEvent
   */
  public function setLoggingStartedEvent(LoggingStartedEvent $loggingStartedEvent)
  {
    $this->loggingStartedEvent = $loggingStartedEvent;
  }
  /**
   * @return LoggingStartedEvent
   */
  public function getLoggingStartedEvent()
  {
    return $this->loggingStartedEvent;
  }
  /**
   * usageLog policy has been disabled. Part of SECURITY_LOGS.
   *
   * @param LoggingStoppedEvent $loggingStoppedEvent
   */
  public function setLoggingStoppedEvent(LoggingStoppedEvent $loggingStoppedEvent)
  {
    $this->loggingStoppedEvent = $loggingStoppedEvent;
  }
  /**
   * @return LoggingStoppedEvent
   */
  public function getLoggingStoppedEvent()
  {
    return $this->loggingStoppedEvent;
  }
  /**
   * A lost mode location update when a device in lost mode.
   *
   * @param LostModeLocationEvent $lostModeLocationEvent
   */
  public function setLostModeLocationEvent(LostModeLocationEvent $lostModeLocationEvent)
  {
    $this->lostModeLocationEvent = $lostModeLocationEvent;
  }
  /**
   * @return LostModeLocationEvent
   */
  public function getLostModeLocationEvent()
  {
    return $this->lostModeLocationEvent;
  }
  /**
   * An outgoing phone call has been made when a device in lost mode.
   *
   * @param LostModeOutgoingPhoneCallEvent $lostModeOutgoingPhoneCallEvent
   */
  public function setLostModeOutgoingPhoneCallEvent(LostModeOutgoingPhoneCallEvent $lostModeOutgoingPhoneCallEvent)
  {
    $this->lostModeOutgoingPhoneCallEvent = $lostModeOutgoingPhoneCallEvent;
  }
  /**
   * @return LostModeOutgoingPhoneCallEvent
   */
  public function getLostModeOutgoingPhoneCallEvent()
  {
    return $this->lostModeOutgoingPhoneCallEvent;
  }
  /**
   * Removable media was mounted. Part of SECURITY_LOGS.
   *
   * @param MediaMountEvent $mediaMountEvent
   */
  public function setMediaMountEvent(MediaMountEvent $mediaMountEvent)
  {
    $this->mediaMountEvent = $mediaMountEvent;
  }
  /**
   * @return MediaMountEvent
   */
  public function getMediaMountEvent()
  {
    return $this->mediaMountEvent;
  }
  /**
   * Removable media was unmounted. Part of SECURITY_LOGS.
   *
   * @param MediaUnmountEvent $mediaUnmountEvent
   */
  public function setMediaUnmountEvent(MediaUnmountEvent $mediaUnmountEvent)
  {
    $this->mediaUnmountEvent = $mediaUnmountEvent;
  }
  /**
   * @return MediaUnmountEvent
   */
  public function getMediaUnmountEvent()
  {
    return $this->mediaUnmountEvent;
  }
  /**
   * Device was shutdown. Part of SECURITY_LOGS.
   *
   * @param OsShutdownEvent $osShutdownEvent
   */
  public function setOsShutdownEvent(OsShutdownEvent $osShutdownEvent)
  {
    $this->osShutdownEvent = $osShutdownEvent;
  }
  /**
   * @return OsShutdownEvent
   */
  public function getOsShutdownEvent()
  {
    return $this->osShutdownEvent;
  }
  /**
   * Device was started. Part of SECURITY_LOGS.
   *
   * @param OsStartupEvent $osStartupEvent
   */
  public function setOsStartupEvent(OsStartupEvent $osStartupEvent)
  {
    $this->osStartupEvent = $osStartupEvent;
  }
  /**
   * @return OsStartupEvent
   */
  public function getOsStartupEvent()
  {
    return $this->osStartupEvent;
  }
  /**
   * The device or profile has been remotely locked via the LOCK command. Part
   * of SECURITY_LOGS.
   *
   * @param RemoteLockEvent $remoteLockEvent
   */
  public function setRemoteLockEvent(RemoteLockEvent $remoteLockEvent)
  {
    $this->remoteLockEvent = $remoteLockEvent;
  }
  /**
   * @return RemoteLockEvent
   */
  public function getRemoteLockEvent()
  {
    return $this->remoteLockEvent;
  }
  /**
   * An attempt to take a device out of lost mode.
   *
   * @param StopLostModeUserAttemptEvent $stopLostModeUserAttemptEvent
   */
  public function setStopLostModeUserAttemptEvent(StopLostModeUserAttemptEvent $stopLostModeUserAttemptEvent)
  {
    $this->stopLostModeUserAttemptEvent = $stopLostModeUserAttemptEvent;
  }
  /**
   * @return StopLostModeUserAttemptEvent
   */
  public function getStopLostModeUserAttemptEvent()
  {
    return $this->stopLostModeUserAttemptEvent;
  }
  /**
   * The work profile or company-owned device failed to wipe when requested.
   * This could be user initiated or admin initiated e.g. delete was received.
   * Part of SECURITY_LOGS.
   *
   * @param WipeFailureEvent $wipeFailureEvent
   */
  public function setWipeFailureEvent(WipeFailureEvent $wipeFailureEvent)
  {
    $this->wipeFailureEvent = $wipeFailureEvent;
  }
  /**
   * @return WipeFailureEvent
   */
  public function getWipeFailureEvent()
  {
    return $this->wipeFailureEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageLogEvent::class, 'Google_Service_AndroidManagement_UsageLogEvent');
