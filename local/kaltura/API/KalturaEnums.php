<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2018  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/KalturaClientBase.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppTokenStatus extends KalturaEnumBase
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppearInListType extends KalturaEnumBase
{
	const PARTNER_ONLY = 1;
	const CATEGORY_MEMBERS_ONLY = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsDeletePolicy extends KalturaEnumBase
{
	const KEEP = 0;
	const DELETE = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsOrigin extends KalturaEnumBase
{
	const CONVERT = 0;
	const INGEST = 1;
	const CONVERT_WHEN_MISSING = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobErrorTypes extends KalturaEnumBase
{
	const APP = 0;
	const RUNTIME = 1;
	const HTTP = 2;
	const CURL = 3;
	const KALTURA_API = 4;
	const KALTURA_CLIENT = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobStatus extends KalturaEnumBase
{
	const PENDING = 0;
	const QUEUED = 1;
	const PROCESSING = 2;
	const PROCESSED = 3;
	const MOVEFILE = 4;
	const FINISHED = 5;
	const FAILED = 6;
	const ABORTED = 7;
	const ALMOST_DONE = 8;
	const RETRY = 9;
	const FATAL = 10;
	const DONT_PROCESS = 11;
	const FINISHED_PARTIALLY = 12;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBitRateMode extends KalturaEnumBase
{
	const CBR = 1;
	const VBR = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryStatus extends KalturaEnumBase
{
	const PENDING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const REJECTED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryStatus extends KalturaEnumBase
{
	const UPDATING = 1;
	const ACTIVE = 2;
	const DELETED = 3;
	const PURGED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserPermissionLevel extends KalturaEnumBase
{
	const MANAGER = 0;
	const MODERATOR = 1;
	const CONTRIBUTOR = 2;
	const MEMBER = 3;
	const NONE = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const PENDING = 2;
	const NOT_ACTIVE = 3;
	const DELETED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaChinaCacheAlgorithmType extends KalturaEnumBase
{
	const SHA1 = 1;
	const SHA256 = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCommercialUseType extends KalturaEnumBase
{
	const NON_COMMERCIAL_USE = 0;
	const COMMERCIAL_USE = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContributionPolicyType extends KalturaEnumBase
{
	const ALL = 1;
	const MEMBERS_WITH_CONTRIBUTION_PERMISSION = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandStatus extends KalturaEnumBase
{
	const PENDING = 1;
	const HANDLED = 2;
	const DONE = 3;
	const FAILED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandTargetType extends KalturaEnumBase
{
	const DATA_CENTER = 1;
	const SCHEDULER = 2;
	const JOB_TYPE = 3;
	const JOB = 4;
	const BATCH = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandType extends KalturaEnumBase
{
	const KILL = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCountryRestrictionType extends KalturaEnumBase
{
	const RESTRICT_COUNTRY_LIST = 0;
	const ALLOW_COUNTRY_LIST = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDVRStatus extends KalturaEnumBase
{
	const DISABLED = 0;
	const ENABLED = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryStatus extends KalturaEnumBase
{
	const ACTIVE = 0;
	const DELETED = 1;
	const STAGING_IN = 2;
	const STAGING_OUT = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDirectoryRestrictionType extends KalturaEnumBase
{
	const DONT_DISPLAY = 0;
	const DISPLAY_WITH_LINK = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEditorType extends KalturaEnumBase
{
	const SIMPLE = 1;
	const ADVANCED = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailIngestionProfileStatus extends KalturaEnumBase
{
	const INACTIVE = 0;
	const ACTIVE = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDisplayInSearchType extends KalturaEnumBase
{
	const SYSTEM = -1;
	const NONE = 0;
	const PARTNER_ONLY = 1;
	const KALTURA_NETWORK = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryModerationStatus extends KalturaEnumBase
{
	const PENDING_MODERATION = 1;
	const APPROVED = 2;
	const REJECTED = 3;
	const DELETED = 4;
	const FLAGGED_FOR_REVIEW = 5;
	const AUTO_APPROVED = 6;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeRecordingStatus extends KalturaEnumBase
{
	const STOPPED = 0;
	const ON_GOING = 1;
	const DONE = 2;
	const DISMISSED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeStatus extends KalturaEnumBase
{
	const STOPPED = 0;
	const PLAYABLE = 1;
	const BROADCASTING = 2;
	const AUTHENTICATED = 3;
	const MARKED_FOR_DELETION = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeatureStatusType extends KalturaEnumBase
{
	const LOCK_CATEGORY = 1;
	const CATEGORY = 2;
	const CATEGORY_ENTRY = 3;
	const ENTRY = 4;
	const CATEGORY_USER = 5;
	const USER = 6;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetStatus extends KalturaEnumBase
{
	const ERROR = -1;
	const QUEUED = 0;
	const CONVERTING = 1;
	const READY = 2;
	const DELETED = 3;
	const NOT_APPLICABLE = 4;
	const TEMP = 5;
	const WAIT_FOR_CONVERT = 6;
	const IMPORTING = 7;
	const VALIDATING = 8;
	const EXPORTING = 9;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorReadyBehaviorType extends KalturaEnumBase
{
	const NO_IMPACT = 0;
	const INHERIT_FLAVOR_PARAMS = 0;
	const REQUIRED = 1;
	const OPTIONAL = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGender extends KalturaEnumBase
{
	const UNKNOWN = 0;
	const MALE = 1;
	const FEMALE = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGroupUserStatus extends KalturaEnumBase
{
	const ACTIVE = 0;
	const DELETED = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaInheritanceType extends KalturaEnumBase
{
	const INHERIT = 1;
	const MANUAL = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIpAddressRestrictionType extends KalturaEnumBase
{
	const RESTRICT_LIST = 0;
	const ALLOW_LIST = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLicenseType extends KalturaEnumBase
{
	const UNKNOWN = -1;
	const NONE = 0;
	const COPYRIGHTED = 1;
	const PUBLIC_DOMAIN = 2;
	const CREATIVECOMMONS_ATTRIBUTION = 3;
	const CREATIVECOMMONS_ATTRIBUTION_SHARE_ALIKE = 4;
	const CREATIVECOMMONS_ATTRIBUTION_NO_DERIVATIVES = 5;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL = 6;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_SHARE_ALIKE = 7;
	const CREATIVECOMMONS_ATTRIBUTION_NON_COMMERCIAL_NO_DERIVATIVES = 8;
	const GFDL = 9;
	const GPL = 10;
	const AFFERO_GPL = 11;
	const LGPL = 12;
	const BSD = 13;
	const APACHE = 14;
	const MOZILLA = 15;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLimitFlavorsRestrictionType extends KalturaEnumBase
{
	const RESTRICT_LIST = 0;
	const ALLOW_LIST = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLivePublishStatus extends KalturaEnumBase
{
	const DISABLED = 0;
	const ENABLED = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportExportType extends KalturaEnumBase
{
	const PARTNER_TOTAL_ALL = 1;
	const PARTNER_TOTAL_LIVE = 2;
	const ENTRY_TIME_LINE_ALL = 11;
	const ENTRY_TIME_LINE_LIVE = 12;
	const LOCATION_ALL = 21;
	const LOCATION_LIVE = 22;
	const SYNDICATION_ALL = 31;
	const SYNDICATION_LIVE = 32;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStatsEventType extends KalturaEnumBase
{
	const LIVE = 1;
	const DVR = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMailJobStatus extends KalturaEnumBase
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const QUEUED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaType extends KalturaEnumBase
{
	const VIDEO = 1;
	const IMAGE = 2;
	const AUDIO = 5;
	const LIVE_STREAM_FLASH = 201;
	const LIVE_STREAM_WINDOWS_MEDIA = 202;
	const LIVE_STREAM_REAL_MEDIA = 203;
	const LIVE_STREAM_QUICKTIME = 204;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaModerationFlagType extends KalturaEnumBase
{
	const SEXUAL_CONTENT = 1;
	const VIOLENT_REPULSIVE = 2;
	const HARMFUL_DANGEROUS = 3;
	const SPAM_COMMERCIALS = 4;
	const COPYRIGHT = 5;
	const TERMS_OF_USE_VIOLATION = 6;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMrssExtensionMode extends KalturaEnumBase
{
	const APPEND = 1;
	const REPLACE = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNotificationObjectType extends KalturaEnumBase
{
	const ENTRY = 1;
	const KSHOW = 2;
	const USER = 3;
	const BATCH_JOB = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNotificationStatus extends KalturaEnumBase
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const SHOULD_RESEND = 4;
	const ERROR_RESENDING = 5;
	const SENT_SYNCH = 6;
	const QUEUED = 7;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNotificationType extends KalturaEnumBase
{
	const ENTRY_ADD = 1;
	const ENTR_UPDATE_PERMISSIONS = 2;
	const ENTRY_DELETE = 3;
	const ENTRY_BLOCK = 4;
	const ENTRY_UPDATE = 5;
	const ENTRY_UPDATE_THUMBNAIL = 6;
	const ENTRY_UPDATE_MODERATION = 7;
	const USER_ADD = 21;
	const USER_BANNED = 26;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNullableBoolean extends KalturaEnumBase
{
	const NULL_VALUE = -1;
	const FALSE_VALUE = 0;
	const TRUE_VALUE = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerGroupType extends KalturaEnumBase
{
	const PUBLISHER = 1;
	const VAR_GROUP = 2;
	const GROUP = 3;
	const TEMPLATE = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerStatus extends KalturaEnumBase
{
	const DELETED = 0;
	const ACTIVE = 1;
	const BLOCKED = 2;
	const FULL_BLOCK = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerType extends KalturaEnumBase
{
	const KMC = 1;
	const WIKI = 100;
	const WORDPRESS = 101;
	const DRUPAL = 102;
	const DEKIWIKI = 103;
	const MOODLE = 104;
	const COMMUNITY_EDITION = 105;
	const JOOMLA = 106;
	const BLACKBOARD = 107;
	const SAKAI = 108;
	const ADMIN_CONSOLE = 109;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionType extends KalturaEnumBase
{
	const NORMAL = 1;
	const SPECIAL_FEATURE = 2;
	const PLUGIN = 3;
	const PARTNER_GROUP = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistType extends KalturaEnumBase
{
	const STATIC_LIST = 3;
	const DYNAMIC = 10;
	const EXTERNAL = 101;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPrivacyType extends KalturaEnumBase
{
	const ALL = 1;
	const AUTHENTICATED_USERS = 2;
	const MEMBERS_ONLY = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRecordStatus extends KalturaEnumBase
{
	const DISABLED = 0;
	const APPENDED = 1;
	const PER_SESSION = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRecordingStatus extends KalturaEnumBase
{
	const STOPPED = 0;
	const PAUSED = 1;
	const ACTIVE = 2;
	const DISABLED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileStatus extends KalturaEnumBase
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileType extends KalturaEnumBase
{
	const INCLUDE_FIELDS = 1;
	const EXCLUDE_FIELDS = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseType extends KalturaEnumBase
{
	const RESPONSE_TYPE_JSON = 1;
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;
	const RESPONSE_TYPE_PHP_ARRAY = 4;
	const RESPONSE_TYPE_HTML = 7;
	const RESPONSE_TYPE_MRSS = 8;
	const RESPONSE_TYPE_JSONP = 9;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerStatusType extends KalturaEnumBase
{
	const RUNNING_BATCHES_COUNT = 1;
	const RUNNING_BATCHES_CPU = 2;
	const RUNNING_BATCHES_MEMORY = 3;
	const RUNNING_BATCHES_NETWORK = 4;
	const RUNNING_BATCHES_DISC_IO = 5;
	const RUNNING_BATCHES_DISC_SPACE = 6;
	const RUNNING_BATCHES_IS_RUNNING = 7;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchOperatorType extends KalturaEnumBase
{
	const SEARCH_AND = 1;
	const SEARCH_OR = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchProviderType extends KalturaEnumBase
{
	const FLICKR = 3;
	const YOUTUBE = 4;
	const MYSPACE = 7;
	const PHOTOBUCKET = 8;
	const JAMENDO = 9;
	const CCMIXTER = 10;
	const NYPL = 11;
	const CURRENT = 12;
	const MEDIA_COMMONS = 13;
	const KALTURA = 20;
	const KALTURA_USER_CLIPS = 21;
	const ARCHIVE_ORG = 22;
	const KALTURA_PARTNER = 23;
	const METACAFE = 24;
	const SEARCH_PROXY = 28;
	const PARTNER_SPECIFIC = 100;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerNodeStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const DISABLED = 2;
	const DELETED = 3;
	const NOT_REGISTERED = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSessionType extends KalturaEnumBase
{
	const USER = 0;
	const ADMIN = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSiteRestrictionType extends KalturaEnumBase
{
	const RESTRICT_SITE_LIST = 0;
	const ALLOW_SITE_LIST = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsEventType extends KalturaEnumBase
{
	const WIDGET_LOADED = 1;
	const MEDIA_LOADED = 2;
	const PLAY = 3;
	const PLAY_REACHED_25 = 4;
	const PLAY_REACHED_50 = 5;
	const PLAY_REACHED_75 = 6;
	const PLAY_REACHED_100 = 7;
	const OPEN_EDIT = 8;
	const OPEN_VIRAL = 9;
	const OPEN_DOWNLOAD = 10;
	const OPEN_REPORT = 11;
	const BUFFER_START = 12;
	const BUFFER_END = 13;
	const OPEN_FULL_SCREEN = 14;
	const CLOSE_FULL_SCREEN = 15;
	const REPLAY = 16;
	const SEEK = 17;
	const OPEN_UPLOAD = 18;
	const SAVE_PUBLISH = 19;
	const CLOSE_EDITOR = 20;
	const PRE_BUMPER_PLAYED = 21;
	const POST_BUMPER_PLAYED = 22;
	const BUMPER_CLICKED = 23;
	const PREROLL_STARTED = 24;
	const MIDROLL_STARTED = 25;
	const POSTROLL_STARTED = 26;
	const OVERLAY_STARTED = 27;
	const PREROLL_CLICKED = 28;
	const MIDROLL_CLICKED = 29;
	const POSTROLL_CLICKED = 30;
	const OVERLAY_CLICKED = 31;
	const PREROLL_25 = 32;
	const PREROLL_50 = 33;
	const PREROLL_75 = 34;
	const MIDROLL_25 = 35;
	const MIDROLL_50 = 36;
	const MIDROLL_75 = 37;
	const POSTROLL_25 = 38;
	const POSTROLL_50 = 39;
	const POSTROLL_75 = 40;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsFeatureType extends KalturaEnumBase
{
	const NONE = 0;
	const RELATED = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsKmcEventType extends KalturaEnumBase
{
	const CONTENT_PAGE_VIEW = 1001;
	const CONTENT_ADD_PLAYLIST = 1010;
	const CONTENT_EDIT_PLAYLIST = 1011;
	const CONTENT_DELETE_PLAYLIST = 1012;
	const CONTENT_EDIT_ENTRY = 1013;
	const CONTENT_CHANGE_THUMBNAIL = 1014;
	const CONTENT_ADD_TAGS = 1015;
	const CONTENT_REMOVE_TAGS = 1016;
	const CONTENT_ADD_ADMIN_TAGS = 1017;
	const CONTENT_REMOVE_ADMIN_TAGS = 1018;
	const CONTENT_DOWNLOAD = 1019;
	const CONTENT_APPROVE_MODERATION = 1020;
	const CONTENT_REJECT_MODERATION = 1021;
	const CONTENT_BULK_UPLOAD = 1022;
	const CONTENT_ADMIN_KCW_UPLOAD = 1023;
	const ACCOUNT_CHANGE_PARTNER_INFO = 1030;
	const ACCOUNT_CHANGE_LOGIN_INFO = 1031;
	const ACCOUNT_CONTACT_US_USAGE = 1032;
	const ACCOUNT_UPDATE_SERVER_SETTINGS = 1033;
	const ACCOUNT_ACCOUNT_OVERVIEW = 1034;
	const ACCOUNT_ACCESS_CONTROL = 1035;
	const ACCOUNT_TRANSCODING_SETTINGS = 1036;
	const ACCOUNT_ACCOUNT_UPGRADE = 1037;
	const ACCOUNT_SAVE_SERVER_SETTINGS = 1038;
	const ACCOUNT_ACCESS_CONTROL_DELETE = 1039;
	const ACCOUNT_SAVE_TRANSCODING_SETTINGS = 1040;
	const LOGIN = 1041;
	const DASHBOARD_IMPORT_CONTENT = 1042;
	const DASHBOARD_UPDATE_CONTENT = 1043;
	const DASHBOARD_ACCOUNT_CONTACT_US = 1044;
	const DASHBOARD_VIEW_REPORTS = 1045;
	const DASHBOARD_EMBED_PLAYER = 1046;
	const DASHBOARD_EMBED_PLAYLIST = 1047;
	const DASHBOARD_CUSTOMIZE_PLAYERS = 1048;
	const APP_STUDIO_NEW_PLAYER_SINGLE_VIDEO = 1050;
	const APP_STUDIO_NEW_PLAYER_PLAYLIST = 1051;
	const APP_STUDIO_NEW_PLAYER_MULTI_TAB_PLAYLIST = 1052;
	const APP_STUDIO_EDIT_PLAYER_SINGLE_VIDEO = 1053;
	const APP_STUDIO_EDIT_PLAYER_PLAYLIST = 1054;
	const APP_STUDIO_EDIT_PLAYER_MULTI_TAB_PLAYLIST = 1055;
	const APP_STUDIO_DUPLICATE_PLAYER = 1056;
	const CONTENT_CONTENT_GO_TO_PAGE = 1057;
	const CONTENT_DELETE_ITEM = 1058;
	const CONTENT_DELETE_MIX = 1059;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_TAB = 1070;
	const REPORTS_AND_ANALYTICS_CONTENT_REPORTS_TAB = 1071;
	const REPORTS_AND_ANALYTICS_USERS_AND_COMMUNITY_REPORTS_TAB = 1072;
	const REPORTS_AND_ANALYTICS_TOP_CONTRIBUTORS = 1073;
	const REPORTS_AND_ANALYTICS_MAP_OVERLAYS = 1074;
	const REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS = 1075;
	const REPORTS_AND_ANALYTICS_TOP_CONTENT = 1076;
	const REPORTS_AND_ANALYTICS_CONTENT_DROPOFF = 1077;
	const REPORTS_AND_ANALYTICS_CONTENT_INTERACTIONS = 1078;
	const REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS = 1079;
	const REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN = 1080;
	const REPORTS_AND_ANALYTICS_CONTENT_DRILL_DOWN_INTERACTION = 1081;
	const REPORTS_AND_ANALYTICS_CONTENT_CONTRIBUTIONS_DRILLDOWN = 1082;
	const REPORTS_AND_ANALYTICS_VIDEO_DRILL_DOWN_DROPOFF = 1083;
	const REPORTS_AND_ANALYTICS_MAP_OVERLAYS_DRILLDOWN = 1084;
	const REPORTS_AND_ANALYTICS_TOP_SYNDICATIONS_DRILL_DOWN = 1085;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_MONTHLY = 1086;
	const REPORTS_AND_ANALYTICS_BANDWIDTH_USAGE_VIEW_YEARLY = 1087;
	const CONTENT_ENTRY_DRILLDOWN = 1088;
	const CONTENT_OPEN_PREVIEW_AND_EMBED = 1089;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileDeliveryStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const BLOCKED = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileReadyBehavior extends KalturaEnumBase
{
	const NO_IMPACT = 0;
	const REQUIRED = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileStatus extends KalturaEnumBase
{
	const DISABLED = 1;
	const AUTOMATIC = 2;
	const MANUAL = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationFeedStatus extends KalturaEnumBase
{
	const DELETED = -1;
	const ACTIVE = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationFeedType extends KalturaEnumBase
{
	const GOOGLE_VIDEO = 1;
	const YAHOO = 2;
	const ITUNES = 3;
	const TUBE_MOGUL = 4;
	const KALTURA = 5;
	const KALTURA_XSLT = 6;
	const ROKU_DIRECT_PUBLISHER = 7;
	const OPERA_TV_SNAP = 8;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAssetStatus extends KalturaEnumBase
{
	const ERROR = -1;
	const QUEUED = 0;
	const CAPTURING = 1;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbCropType extends KalturaEnumBase
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	const RESIZE_WITH_FORCE = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfCreationMode extends KalturaEnumBase
{
	const WIZARD = 2;
	const ADVANCED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfObjType extends KalturaEnumBase
{
	const PLAYER = 1;
	const CONTRIBUTION_WIZARD = 2;
	const SIMPLE_EDITOR = 3;
	const ADVANCED_EDITOR = 4;
	const PLAYLIST = 5;
	const APP_STUDIO = 6;
	const KRECORD = 7;
	const PLAYER_V3 = 8;
	const KMC_ACCOUNT = 9;
	const KMC_ANALYTICS = 10;
	const KMC_CONTENT = 11;
	const KMC_DASHBOARD = 12;
	const KMC_LOGIN = 13;
	const PLAYER_SL = 14;
	const CLIENTSIDE_ENCODER = 15;
	const KMC_GENERAL = 16;
	const KMC_ROLES_AND_PERMISSIONS = 17;
	const CLIPPER = 18;
	const KSR = 19;
	const KUPLOAD = 20;
	const WEBCASTING = 21;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUpdateMethodType extends KalturaEnumBase
{
	const MANUAL = 0;
	const AUTOMATIC = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadErrorCode extends KalturaEnumBase
{
	const NO_ERROR = 0;
	const GENERAL_ERROR = 1;
	const PARTIAL_UPLOAD = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadTokenStatus extends KalturaEnumBase
{
	const PENDING = 0;
	const PARTIAL_UPLOAD = 1;
	const FULL_UPLOAD = 2;
	const CLOSED = 3;
	const TIMED_OUT = 4;
	const DELETED = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserAgentRestrictionType extends KalturaEnumBase
{
	const RESTRICT_LIST = 0;
	const ALLOW_LIST = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserJoinPolicyType extends KalturaEnumBase
{
	const AUTO_JOIN = 1;
	const REQUEST_TO_JOIN = 2;
	const NOT_ALLOWED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const BLOCKED = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserStatus extends KalturaEnumBase
{
	const BLOCKED = 0;
	const ACTIVE = 1;
	const DELETED = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserType extends KalturaEnumBase
{
	const USER = 0;
	const GROUP = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaViewMode extends KalturaEnumBase
{
	const PREVIEW = 0;
	const ALLOW_ALL = 1;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidgetSecurityType extends KalturaEnumBase
{
	const NONE = 1;
	const TIMEHASH = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAdminUserOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAkamaiUniversalStreamType extends KalturaEnumBase
{
	const HD_IPHONE_IPAD_LIVE = "HD iPhone/iPad Live";
	const UNIVERSAL_STREAMING_LIVE = "Universal Streaming Live";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAmazonS3StorageProfileFilesPermissionLevel extends KalturaEnumBase
{
	const ACL_AUTHENTICATED_READ = "authenticated-read";
	const ACL_PRIVATE = "private";
	const ACL_PUBLIC_READ = "public-read";
	const ACL_PUBLIC_READ_WRITE = "public-read-write";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAmazonS3StorageProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiActionPermissionItemOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiParameterPermissionItemAction extends KalturaEnumBase
{
	const USAGE = "all";
	const INSERT = "insert";
	const READ = "read";
	const UPDATE = "update";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiParameterPermissionItemOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppTokenHashType extends KalturaEnumBase
{
	const MD5 = "MD5";
	const SHA1 = "SHA1";
	const SHA256 = "SHA256";
	const SHA512 = "SHA512";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppTokenOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DELETED_AT_ASC = "+deletedAt";
	const SIZE_ASC = "+size";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DELETED_AT_DESC = "-deletedAt";
	const SIZE_DESC = "-size";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsOutputOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetType extends KalturaEnumBase
{
	const ATTACHMENT = "attachment.Attachment";
	const CAPTION = "caption.Caption";
	const DOCUMENT = "document.Document";
	const IMAGE = "document.Image";
	const PDF = "document.PDF";
	const SWF = "document.SWF";
	const TIMED_THUMB_ASSET = "thumbCuePoint.timedThumb";
	const TRANSCRIPT = "transcript.Transcript";
	const WIDEVINE_FLAVOR = "widevine.WidevineFlavor";
	const FLAVOR = "1";
	const THUMBNAIL = "2";
	const LIVE = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAudioCodec extends KalturaEnumBase
{
	const NONE = "";
	const AAC = "aac";
	const AACHE = "aache";
	const AC3 = "ac3";
	const AMRNB = "amrnb";
	const COPY = "copy";
	const EAC3 = "eac3";
	const MP3 = "mp3";
	const MPEG2 = "mpeg2";
	const PCM = "pcm";
	const VORBIS = "vorbis";
	const WMA = "wma";
	const WMAPRO = "wmapro";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryCloneOptions extends KalturaEnumBase
{
	const AD_CUE_POINTS = "adCuePoint.AD_CUE_POINTS";
	const ANNOTATION_CUE_POINTS = "annotation.ANNOTATION_CUE_POINTS";
	const CODE_CUE_POINTS = "codeCuePoint.CODE_CUE_POINTS";
	const THUMB_CUE_POINTS = "thumbCuePoint.THUMB_CUE_POINTS";
	const USERS = "1";
	const CATEGORIES = "2";
	const CHILD_ENTRIES = "3";
	const ACCESS_CONTROL = "4";
	const METADATA = "5";
	const FLAVORS = "6";
	const CAPTIONS = "7";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const END_DATE_ASC = "+endDate";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const END_DATE_DESC = "-endDate";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobObjectType extends KalturaEnumBase
{
	const ENTRY_DISTRIBUTION = "contentDistribution.EntryDistribution";
	const DROP_FOLDER_FILE = "dropFolderXmlBulkUpload.DropFolderFile";
	const METADATA = "metadata.Metadata";
	const METADATA_PROFILE = "metadata.MetadataProfile";
	const SCHEDULED_TASK_PROFILE = "scheduledTask.ScheduledTaskProfile";
	const ENTRY = "1";
	const CATEGORY = "2";
	const FILE_SYNC = "3";
	const ASSET = "4";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ESTIMATED_EFFORT_ASC = "+estimatedEffort";
	const EXECUTION_ATTEMPTS_ASC = "+executionAttempts";
	const FINISH_TIME_ASC = "+finishTime";
	const LOCK_VERSION_ASC = "+lockVersion";
	const PRIORITY_ASC = "+priority";
	const QUEUE_TIME_ASC = "+queueTime";
	const STATUS_ASC = "+status";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ESTIMATED_EFFORT_DESC = "-estimatedEffort";
	const EXECUTION_ATTEMPTS_DESC = "-executionAttempts";
	const FINISH_TIME_DESC = "-finishTime";
	const LOCK_VERSION_DESC = "-lockVersion";
	const PRIORITY_DESC = "-priority";
	const QUEUE_TIME_DESC = "-queueTime";
	const STATUS_DESC = "-status";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobType extends KalturaEnumBase
{
	const PARSE_MULTI_LANGUAGE_CAPTION_ASSET = "caption.parsemultilanguagecaptionasset";
	const CONVERT = "0";
	const PARSE_CAPTION_ASSET = "captionSearch.parseCaptionAsset";
	const DISTRIBUTION_DELETE = "contentDistribution.DistributionDelete";
	const DISTRIBUTION_DISABLE = "contentDistribution.DistributionDisable";
	const DISTRIBUTION_ENABLE = "contentDistribution.DistributionEnable";
	const DISTRIBUTION_FETCH_REPORT = "contentDistribution.DistributionFetchReport";
	const DISTRIBUTION_SUBMIT = "contentDistribution.DistributionSubmit";
	const DISTRIBUTION_SYNC = "contentDistribution.DistributionSync";
	const DISTRIBUTION_UPDATE = "contentDistribution.DistributionUpdate";
	const DROP_FOLDER_CONTENT_PROCESSOR = "dropFolder.DropFolderContentProcessor";
	const DROP_FOLDER_WATCHER = "dropFolder.DropFolderWatcher";
	const EVENT_NOTIFICATION_HANDLER = "eventNotification.EventNotificationHandler";
	const INTEGRATION = "integration.Integration";
	const SCHEDULED_TASK = "scheduledTask.ScheduledTask";
	const INDEX_TAGS = "tagSearch.IndexTagsByPrivacyContext";
	const TAG_RESOLVE = "tagSearch.TagResolve";
	const VIRUS_SCAN = "virusScan.VirusScan";
	const WIDEVINE_REPOSITORY_SYNC = "widevine.WidevineRepositorySync";
	const IMPORT = "1";
	const DELETE = "2";
	const FLATTEN = "3";
	const BULKUPLOAD = "4";
	const DVDCREATOR = "5";
	const DOWNLOAD = "6";
	const OOCONVERT = "7";
	const CONVERT_PROFILE = "10";
	const POSTCONVERT = "11";
	const EXTRACT_MEDIA = "14";
	const MAIL = "15";
	const NOTIFICATION = "16";
	const CLEANUP = "17";
	const SCHEDULER_HELPER = "18";
	const BULKDOWNLOAD = "19";
	const DB_CLEANUP = "20";
	const PROVISION_PROVIDE = "21";
	const CONVERT_COLLECTION = "22";
	const STORAGE_EXPORT = "23";
	const PROVISION_DELETE = "24";
	const STORAGE_DELETE = "25";
	const EMAIL_INGESTION = "26";
	const METADATA_IMPORT = "27";
	const METADATA_TRANSFORM = "28";
	const FILESYNC_IMPORT = "29";
	const CAPTURE_THUMB = "30";
	const DELETE_FILE = "31";
	const INDEX = "32";
	const MOVE_CATEGORY_ENTRIES = "33";
	const COPY = "34";
	const CONCAT = "35";
	const CONVERT_LIVE_SEGMENT = "36";
	const COPY_PARTNER = "37";
	const VALIDATE_LIVE_MEDIA_SERVERS = "38";
	const SYNC_CATEGORY_PRIVACY_CONTEXT = "39";
	const LIVE_REPORT_EXPORT = "40";
	const RECALCULATE_CACHE = "41";
	const LIVE_TO_VOD = "42";
	const COPY_CAPTIONS = "43";
	const CHUNKED_ENCODE_JOB_SCHEDULER = "44";
	const SERVER_NODE_MONITOR = "45";
	const USERS_CSV = "46";
	const CLIP_CONCAT = "47";
	const COPY_CUE_POINTS = "48";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadAction extends KalturaEnumBase
{
	const CANCEL = "scheduleBulkUpload.CANCEL";
	const ADD = "1";
	const UPDATE = "2";
	const DELETE = "3";
	const REPLACE = "4";
	const TRANSFORM_XSLT = "5";
	const ADD_OR_UPDATE = "6";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadObjectType extends KalturaEnumBase
{
	const SCHEDULE_EVENT = "scheduleBulkUpload.SCHEDULE_EVENT";
	const SCHEDULE_RESOURCE = "scheduleBulkUpload.SCHEDULE_RESOURCE";
	const ENTRY = "1";
	const CATEGORY = "2";
	const USER = "3";
	const CATEGORY_USER = "4";
	const CATEGORY_ENTRY = "5";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultStatus extends KalturaEnumBase
{
	const ERROR = "1";
	const OK = "2";
	const IN_PROGRESS = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadType extends KalturaEnumBase
{
	const CSV = "bulkUploadCsv.CSV";
	const FILTER = "bulkUploadFilter.FILTER";
	const XML = "bulkUploadXml.XML";
	const DROP_FOLDER_XML = "dropFolderXmlBulkUpload.DROP_FOLDER_XML";
	const ICAL = "scheduleBulkUpload.ICAL";
	const DROP_FOLDER_ICAL = "scheduleDropFolder.DROP_FOLDER_ICAL";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryAdvancedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryIdentifierField extends KalturaEnumBase
{
	const FULL_NAME = "fullName";
	const ID = "id";
	const REFERENCE_ID = "referenceId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DEPTH_ASC = "+depth";
	const DIRECT_ENTRIES_COUNT_ASC = "+directEntriesCount";
	const DIRECT_SUB_CATEGORIES_COUNT_ASC = "+directSubCategoriesCount";
	const ENTRIES_COUNT_ASC = "+entriesCount";
	const FULL_NAME_ASC = "+fullName";
	const MEMBERS_COUNT_ASC = "+membersCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DEPTH_DESC = "-depth";
	const DIRECT_ENTRIES_COUNT_DESC = "-directEntriesCount";
	const DIRECT_SUB_CATEGORIES_COUNT_DESC = "-directSubCategoriesCount";
	const ENTRIES_COUNT_DESC = "-entriesCount";
	const FULL_NAME_DESC = "-fullName";
	const MEMBERS_COUNT_DESC = "-membersCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCloneComponentSelectorType extends KalturaEnumBase
{
	const INCLUDE_COMPONENT = "0";
	const EXCLUDE_COMPONENT = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConditionType extends KalturaEnumBase
{
	const ABC_WATERMARK = "abcScreenersWatermarkAccessControl.abcWatermark";
	const EVENT_NOTIFICATION_FIELD = "eventNotification.BooleanField";
	const EVENT_NOTIFICATION_OBJECT_CHANGED = "eventNotification.ObjectChanged";
	const METADATA_FIELD_CHANGED = "metadata.FieldChanged";
	const METADATA_FIELD_COMPARE = "metadata.FieldCompare";
	const METADATA_FIELD_MATCH = "metadata.FieldMatch";
	const AUTHENTICATED = "1";
	const COUNTRY = "2";
	const IP_ADDRESS = "3";
	const SITE = "4";
	const USER_AGENT = "5";
	const FIELD_MATCH = "6";
	const FIELD_COMPARE = "7";
	const ASSET_PROPERTIES_COMPARE = "8";
	const USER_ROLE = "9";
	const GEO_DISTANCE = "10";
	const OR_OPERATOR = "11";
	const HASH = "12";
	const DELIVERY_PROFILE = "13";
	const ACTIVE_EDGE_VALIDATE = "14";
	const ANONYMOUS_IP = "15";
	const ASSET_TYPE = "16";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContainerFormat extends KalturaEnumBase
{
	const _3GP = "3gp";
	const APPLEHTTP = "applehttp";
	const AVI = "avi";
	const BMP = "bmp";
	const COPY = "copy";
	const FLV = "flv";
	const HLS = "hls";
	const ISMA = "isma";
	const ISMV = "ismv";
	const JPG = "jpg";
	const M2TS = "m2ts";
	const M4V = "m4v";
	const MKV = "mkv";
	const MOV = "mov";
	const MP3 = "mp3";
	const MP4 = "mp4";
	const MPEG = "mpeg";
	const MPEGTS = "mpegts";
	const MXF = "mxf";
	const OGG = "ogg";
	const OGV = "ogv";
	const PDF = "pdf";
	const PNG = "png";
	const SWF = "swf";
	const WAV = "wav";
	const WEBM = "webm";
	const WMA = "wma";
	const WMV = "wmv";
	const WVM = "wvm";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContextType extends KalturaEnumBase
{
	const PLAY = "1";
	const DOWNLOAD = "2";
	const THUMBNAIL = "3";
	const METADATA = "4";
	const EXPORT = "5";
	const SERVE = "6";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileStatus extends KalturaEnumBase
{
	const DISABLED = "1";
	const ENABLED = "2";
	const DELETED = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileType extends KalturaEnumBase
{
	const MEDIA = "1";
	const LIVE_STREAM = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const END_DATE_ASC = "+endDate";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const END_DATE_DESC = "-endDate";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiAppleHttpManifestOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHdsOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHttpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericAppleHttpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHdsOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHttpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericRtmpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericSilverLightOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileLiveAppleHttpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileRtmpOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileType extends KalturaEnumBase
{
	const EDGE_CAST_HTTP = "edgeCast.EDGE_CAST_HTTP";
	const EDGE_CAST_RTMP = "edgeCast.EDGE_CAST_RTMP";
	const FORENSIC_WATERMARK_APPLE_HTTP = "forensicWatermark.FORENSIC_WATERMARK_APPLE_HTTP";
	const FORENSIC_WATERMARK_DASH = "forensicWatermark.FORENSIC_WATERMARK_DASH";
	const KONTIKI_HTTP = "kontiki.KONTIKI_HTTP";
	const UPLYNK_HTTP = "uplynk.UPLYNK_HTTP";
	const UPLYNK_RTMP = "uplynk.UPLYNK_RTMP";
	const VELOCIX_HDS = "velocix.VELOCIX_HDS";
	const VELOCIX_HLS = "velocix.VELOCIX_HLS";
	const APPLE_HTTP = "1";
	const HDS = "3";
	const HTTP = "4";
	const RTMP = "5";
	const RTSP = "6";
	const SILVER_LIGHT = "7";
	const AKAMAI_HLS_DIRECT = "10";
	const AKAMAI_HLS_MANIFEST = "11";
	const AKAMAI_HD = "12";
	const AKAMAI_HDS = "13";
	const AKAMAI_HTTP = "14";
	const AKAMAI_RTMP = "15";
	const AKAMAI_RTSP = "16";
	const AKAMAI_SS = "17";
	const GENERIC_HLS = "21";
	const GENERIC_HDS = "23";
	const GENERIC_HTTP = "24";
	const GENERIC_HLS_MANIFEST = "25";
	const GENERIC_HDS_MANIFEST = "26";
	const GENERIC_SS = "27";
	const GENERIC_RTMP = "28";
	const LEVEL3_HLS = "31";
	const LEVEL3_HTTP = "34";
	const LEVEL3_RTMP = "35";
	const LIMELIGHT_HTTP = "44";
	const LIMELIGHT_RTMP = "45";
	const LOCAL_PATH_APPLE_HTTP = "51";
	const LOCAL_PATH_HDS = "53";
	const LOCAL_PATH_HTTP = "54";
	const LOCAL_PATH_RTMP = "55";
	const VOD_PACKAGER_HLS = "61";
	const VOD_PACKAGER_HDS = "63";
	const VOD_PACKAGER_MSS = "67";
	const VOD_PACKAGER_DASH = "68";
	const VOD_PACKAGER_HLS_MANIFEST = "69";
	const LIVE_HLS = "1001";
	const LIVE_HDS = "1002";
	const LIVE_DASH = "1003";
	const LIVE_RTMP = "1005";
	const LIVE_HLS_TO_MULTICAST = "1006";
	const LIVE_PACKAGER_HLS = "1007";
	const LIVE_PACKAGER_HDS = "1008";
	const LIVE_PACKAGER_DASH = "1009";
	const LIVE_PACKAGER_MSS = "1010";
	const LIVE_AKAMAI_HDS = "1013";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const HEARTBEAT_TIME_ASC = "+heartbeatTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const HEARTBEAT_TIME_DESC = "-heartbeatTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDocumentEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDocumentEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDrmSchemeName extends KalturaEnumBase
{
	const PLAYREADY_CENC = "drm.PLAYREADY_CENC";
	const WIDEVINE_CENC = "drm.WIDEVINE_CENC";
	const FAIRPLAY = "fairplay.FAIRPLAY";
	const PLAYREADY = "playReady.PLAYREADY";
	const WIDEVINE = "widevine.WIDEVINE";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDurationType extends KalturaEnumBase
{
	const LONG = "long";
	const MEDIUM = "medium";
	const NOT_AVAILABLE = "notavailable";
	const SHORT = "short";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaESearchLanguage extends KalturaEnumBase
{
	const ARABIC = "Arabic";
	const BASQUE = "Basque";
	const BRAZILIAN = "Brazilian";
	const BULGARIAN = "Bulgarian";
	const CATALAN = "Catalan";
	const CHINESE = "Chinese";
	const CZECH = "Czech";
	const DANISH = "Danish";
	const DUTCH = "Dutch";
	const ENGLISH = "English";
	const FINNISH = "Finnish";
	const FRENCH = "French";
	const GALICIAN = "Galician";
	const GERMAN = "German";
	const GREEK = "Greek";
	const HINDI = "Hindi";
	const HUNGRIAN = "Hungarian";
	const INDONESIAN = "Indonesian";
	const ITALIAN = "Italian";
	const JAPANESE = "Japanese";
	const KOREAN = "Korean";
	const LATVIAN = "Latvian";
	const LITHUANIAN = "Lithuanian";
	const NORWEGIAN = "Norwegian";
	const PERSIAN = "Persian";
	const PORTUGUESE = "Prtuguese";
	const ROMANIAN = "Romanian";
	const RUSSIAN = "Russian";
	const SORANI = "Sorani";
	const SPANISH = "Spanish";
	const SWEDISH = "Swedish";
	const THAI = "Thai";
	const TURKISH = "Turkish";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEdgeServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const HEARTBEAT_TIME_ASC = "+heartbeatTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const HEARTBEAT_TIME_DESC = "-heartbeatTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryIdentifierField extends KalturaEnumBase
{
	const ID = "id";
	const REFERENCE_ID = "referenceId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryReplacementStatus extends KalturaEnumBase
{
	const NONE = "0";
	const APPROVED_BUT_NOT_READY = "1";
	const READY_BUT_NOT_APPROVED = "2";
	const NOT_READY_AND_NOT_APPROVED = "3";
	const FAILED = "4";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeType extends KalturaEnumBase
{
	const LIVE_PRIMARY = "0";
	const LIVE_BACKUP = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryStatus extends KalturaEnumBase
{
	const ERROR_IMPORTING = "-2";
	const ERROR_CONVERTING = "-1";
	const SCAN_FAILURE = "virusScan.ScanFailure";
	const IMPORT = "0";
	const INFECTED = "virusScan.Infected";
	const PRECONVERT = "1";
	const READY = "2";
	const DELETED = "3";
	const PENDING = "4";
	const MODERATE = "5";
	const BLOCKED = "6";
	const NO_CONTENT = "7";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryType extends KalturaEnumBase
{
	const AUTOMATIC = "-1";
	const CONFERENCE_ENTRY_SERVER = "conference.CONFERENCE_ENTRY_SERVER";
	const EXTERNAL_MEDIA = "externalMedia.externalMedia";
	const MEDIA_CLIP = "1";
	const MIX = "2";
	const PLAYLIST = "5";
	const DATA = "6";
	const LIVE_STREAM = "7";
	const LIVE_CHANNEL = "8";
	const DOCUMENT = "10";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetObjectType extends KalturaEnumBase
{
	const UI_CONF = "2";
	const ENTRY = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetStatus extends KalturaEnumBase
{
	const PENDING = "0";
	const UPLOADING = "1";
	const READY = "2";
	const DELETED = "3";
	const ERROR = "4";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileSyncObjectType extends KalturaEnumBase
{
	const DISTRIBUTION_PROFILE = "contentDistribution.DistributionProfile";
	const ENTRY_DISTRIBUTION = "contentDistribution.EntryDistribution";
	const GENERIC_DISTRIBUTION_ACTION = "contentDistribution.GenericDistributionAction";
	const EMAIL_NOTIFICATION_TEMPLATE = "emailNotification.EmailNotificationTemplate";
	const HTTP_NOTIFICATION_TEMPLATE = "httpNotification.HttpNotificationTemplate";
	const ENTRY = "1";
	const UICONF = "2";
	const BATCHJOB = "3";
	const ASSET = "4";
	const FLAVOR_ASSET = "4";
	const METADATA = "5";
	const METADATA_PROFILE = "6";
	const SYNDICATION_FEED = "7";
	const CONVERSION_PROFILE = "8";
	const FILE_ASSET = "9";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DELETED_AT_ASC = "+deletedAt";
	const SIZE_ASC = "+size";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DELETED_AT_DESC = "-deletedAt";
	const SIZE_DESC = "-size";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsOutputOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericXsltSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGeoCoderType extends KalturaEnumBase
{
	const KALTURA = "1";
	const MAX_MIND = "2";
	const DIGITAL_ELEMENT = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGoogleSyndicationFeedAdultValues extends KalturaEnumBase
{
	const NO = "No";
	const YES = "Yes";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGoogleVideoSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGroupUserOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedAdultValues extends KalturaEnumBase
{
	const CLEAN = "clean";
	const NO = "no";
	const YES = "yes";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedCategories extends KalturaEnumBase
{
	const ARTS = "Arts";
	const ARTS_DESIGN = "Arts/Design";
	const ARTS_FASHION_BEAUTY = "Arts/Fashion &amp; Beauty";
	const ARTS_FOOD = "Arts/Food";
	const ARTS_LITERATURE = "Arts/Literature";
	const ARTS_PERFORMING_ARTS = "Arts/Performing Arts";
	const ARTS_VISUAL_ARTS = "Arts/Visual Arts";
	const BUSINESS = "Business";
	const BUSINESS_BUSINESS_NEWS = "Business/Business News";
	const BUSINESS_CAREERS = "Business/Careers";
	const BUSINESS_INVESTING = "Business/Investing";
	const BUSINESS_MANAGEMENT_MARKETING = "Business/Management &amp; Marketing";
	const BUSINESS_SHOPPING = "Business/Shopping";
	const COMEDY = "Comedy";
	const EDUCATION = "Education";
	const EDUCATION_TECHNOLOGY = "Education/Education Technology";
	const EDUCATION_HIGHER_EDUCATION = "Education/Higher Education";
	const EDUCATION_K_12 = "Education/K-12";
	const EDUCATION_LANGUAGE_COURSES = "Education/Language Courses";
	const EDUCATION_TRAINING = "Education/Training";
	const GAMES_HOBBIES = "Games &amp; Hobbies";
	const GAMES_HOBBIES_AUTOMOTIVE = "Games &amp; Hobbies/Automotive";
	const GAMES_HOBBIES_AVIATION = "Games &amp; Hobbies/Aviation";
	const GAMES_HOBBIES_HOBBIES = "Games &amp; Hobbies/Hobbies";
	const GAMES_HOBBIES_OTHER_GAMES = "Games &amp; Hobbies/Other Games";
	const GAMES_HOBBIES_VIDEO_GAMES = "Games &amp; Hobbies/Video Games";
	const GOVERNMENT_ORGANIZATIONS = "Government &amp; Organizations";
	const GOVERNMENT_ORGANIZATIONS_LOCAL = "Government &amp; Organizations/Local";
	const GOVERNMENT_ORGANIZATIONS_NATIONAL = "Government &amp; Organizations/National";
	const GOVERNMENT_ORGANIZATIONS_NON_PROFIT = "Government &amp; Organizations/Non-Profit";
	const GOVERNMENT_ORGANIZATIONS_REGIONAL = "Government &amp; Organizations/Regional";
	const HEALTH = "Health";
	const HEALTH_ALTERNATIVE_HEALTH = "Health/Alternative Health";
	const HEALTH_FITNESS_NUTRITION = "Health/Fitness &amp; Nutrition";
	const HEALTH_SELF_HELP = "Health/Self-Help";
	const HEALTH_SEXUALITY = "Health/Sexuality";
	const KIDS_FAMILY = "Kids &amp; Family";
	const MUSIC = "Music";
	const NEWS_POLITICS = "News &amp; Politics";
	const RELIGION_SPIRITUALITY = "Religion &amp; Spirituality";
	const RELIGION_SPIRITUALITY_BUDDHISM = "Religion &amp; Spirituality/Buddhism";
	const RELIGION_SPIRITUALITY_CHRISTIANITY = "Religion &amp; Spirituality/Christianity";
	const RELIGION_SPIRITUALITY_HINDUISM = "Religion &amp; Spirituality/Hinduism";
	const RELIGION_SPIRITUALITY_ISLAM = "Religion &amp; Spirituality/Islam";
	const RELIGION_SPIRITUALITY_JUDAISM = "Religion &amp; Spirituality/Judaism";
	const RELIGION_SPIRITUALITY_OTHER = "Religion &amp; Spirituality/Other";
	const RELIGION_SPIRITUALITY_SPIRITUALITY = "Religion &amp; Spirituality/Spirituality";
	const SCIENCE_MEDICINE = "Science &amp; Medicine";
	const SCIENCE_MEDICINE_MEDICINE = "Science &amp; Medicine/Medicine";
	const SCIENCE_MEDICINE_NATURAL_SCIENCES = "Science &amp; Medicine/Natural Sciences";
	const SCIENCE_MEDICINE_SOCIAL_SCIENCES = "Science &amp; Medicine/Social Sciences";
	const SOCIETY_CULTURE = "Society &amp; Culture";
	const SOCIETY_CULTURE_HISTORY = "Society &amp; Culture/History";
	const SOCIETY_CULTURE_PERSONAL_JOURNALS = "Society &amp; Culture/Personal Journals";
	const SOCIETY_CULTURE_PHILOSOPHY = "Society &amp; Culture/Philosophy";
	const SOCIETY_CULTURE_PLACES_TRAVEL = "Society &amp; Culture/Places &amp; Travel";
	const SPORTS_RECREATION = "Sports &amp; Recreation";
	const SPORTS_RECREATION_AMATEUR = "Sports &amp; Recreation/Amateur";
	const SPORTS_RECREATION_COLLEGE_HIGH_SCHOOL = "Sports &amp; Recreation/College &amp; High School";
	const SPORTS_RECREATION_OUTDOOR = "Sports &amp; Recreation/Outdoor";
	const SPORTS_RECREATION_PROFESSIONAL = "Sports &amp; Recreation/Professional";
	const TV_FILM = "TV &amp; Film";
	const TECHNOLOGY = "Technology";
	const TECHNOLOGY_GADGETS = "Technology/Gadgets";
	const TECHNOLOGY_PODCASTING = "Technology/Podcasting";
	const TECHNOLOGY_SOFTWARE_HOW_TO = "Technology/Software How-To";
	const TECHNOLOGY_TECH_NEWS = "Technology/Tech News";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLanguage extends KalturaEnumBase
{
	const ABQ = "Abaza";
	const AB = "Abkhazian";
	const ABE = "Abnaki Western";
	const ABU = "Abure";
	const ACN = "Achang";
	const ACE = "Achinese";
	const ACT = "Achterhooks";
	const ACV = "Achumawi";
	const ADJ = "Adioukrou";
	const ADY = "Adyghe; Adygei";
	const ADT = "Adynyamathanha";
	const AAL = "Afade";
	const AA = "Afar";
	const AF = "Afrikaans";
	const AGQ = "Aghem";
	const AGX = "Aghul";
	const AGU = "Aguacateco";
	const AGR = "Aguaruna";
	const AIN = "Ainu (Japan)";
	const AKK = "Akkadian";
	const AKL = "Aklanon";
	const AKU = "Akum";
	const AKZ = "Alabama";
	const SQ = "Albanian";
	const ALN = "Albanian (Gheg)";
	const ALS = "Albanian (Tosk)";
	const ALE = "Aleut";
	const ALQ = "Algonquin";
	const ALT = "Altai (Southern)";
	const AM = "Amharic";
	const RME = "Angloromani";
	const APJ = "Apache (Jicarilla)";
	const APW = "Apache (Western)";
	const AR = "Arabic";
	const ARB = "Arabic (standard)";
	const B_T = "Arabic Tunisian Spoken";
	const ARC = "Aramaic";
	const SAM = "Aramaic Samaritan";
	const ARP = "Arapaho";
	const ARN = "Araucanian";
	const ARI = "Arikara";
	const HY = "Armenian";
	const RUP = "Aromanian";
	const AS_ = "Assamese";
	const ASB = "Assiniboine";
	const AII = "Assyrian Neo-Aramaic";
	const AST = "Asturian";
	const ATJ = "Atikamekw";
	const AWA = "Awadhi";
	const AY = "Aymara";
	const AZ = "Azerbaijani";
	const BCR = "Babine";
	const BFQ = "Badaga";
	const BDJ = "Bai";
	const BAN = "Balinese";
	const BCC = "Balochi Southern";
	const BFT = "Balti";
	const BAL = "Baluchi";
	const BAS = "Basa (Cameroon)";
	const BA = "Bashkir";
	const EU = "Basque";
	const BAR = "Bavarian";
	const BEA = "Beaver";
	const BEJ = "Beja";
	const BEM = "Bemba (Zambia)";
	const BN = "Bengali (Bangla)";
	const BEW = "Betawi";
	const KAP = "Bezhta";
	const BHB = "Bhili";
	const BHO = "Bhojpuri";
	const DZ = "Bhutani";
	const BH = "Bihari";
	const BIK = "Bikol";
	const BIN = "Bini";
	const BPY = "Bishnupriya Manipuri";
	const BI = "Bislama";
	const BR = "Breton";
	const BUG = "Buginese";
	const BG = "Bulgarian";
	const BUA = "Buriat";
	const MY = "Burmese";
	const BE = "Byelorussian (Belarusian)";
	const CAD = "Caddo";
	const KM = "Cambodian";
	const YUE = "Cantonese";
	const CRX = "Carrier";
	const CAF = "Carrier Southern";
	const CA = "Catalan";
	const CHC = "Catawba";
	const CAY = "Cayuga";
	const CEB = "Cebuano";
	const CHG = "Chagatai";
	const CLD = "Chaldean Neo-Aramaic";
	const CHR = "Cherokee";
	const CHY = "Cheyenne";
	const CIC = "Chickasaw";
	const CLC = "Chilcotin";
	const ZH = "Chinese";
	const CHN = "Chinook jargon";
	const CHP = "Chipewyan";
	const CIW = "Chippewa";
	const CHO = "Choctaw";
	const CAA = "Chor";
	const CKT = "Chukot";
	const CIM = "Cimbrian";
	const CLM = "Clallam Klallam";
	const COJ = "Cochimi";
	const COC = "Cocopa";
	const KSH = "Colognian";
	const COM = "Comanche";
	const SWB = "Comorian";
	const COO = "Comox";
	const COP = "Coptic";
	const CO = "Corsican";
	const MUS = "Creek";
	const CRH = "Crimean Tatar";
	const HR = "Croatian";
	const CUP = "Cupeo";
	const CS = "Czech";
	const DAK = "Dakota";
	const DA = "Danish";
	const DAR = "Dargwa";
	const PRD = "Dari (Persian)";
	const GBZ = "Dari Zoroastrian";
	const DHV = "Dehu";
	const DEL = "Delaware";
	const DIN = "Dinka";
	const DOI = "Dogri (generic)";
	const DGR = "Dogrib";
	const DLG = "Dolgan";
	const DOH = "Dong";
	const DUA = "Duala";
	const DNG = "Dungan";
	const NL = "Dutch";
	const DYU = "Dyula";
	const EEE = "E";
	const EGL = "Emilian";
	const EN = "English";
	const EN_US = "English (American)";
	const EN_GB = "English (British)";
	const ENM = "English Middle (1100-1500)";
	const MYV = "Erzya";
	const EO = "Esperanto";
	const ET = "Estonian";
	const EVE = "Even";
	const EVN = "Evenki";
	const FO = "Faeroese";
	const FAX = "Fala";
	const FAN = "Fang (Equatorial Guinea)";
	const FA = "Farsi";
	const FJ = "Fiji";
	const FIL = "Filipino";
	const FI = "Finnish";
	const FIT = "Finnish (Tornedalen)";
	const FON = "Fon";
	const FRP = "Franco-Prove";
	const FRK = "Frankish";
	const FR = "French";
	const FY = "Frisian";
	const FRR = "Frisian Northern";
	const FUR = "Friulian";
	const FVR = "Fur";
	const GAA = "Ga";
	const GV = "Gaelic (Manx)";
	const GD = "Gaelic (Scottish)";
	const GAG = "Gagauz";
	const GL = "Galician";
	const GAN = "Gan";
	const GEZ = "Geez";
	const KA = "Georgian";
	const DE = "German";
	const GEH = "German Hutterite";
	const PDC = "German Pennsylvania";
	const GIL = "Gilbertese";
	const NIV = "Gilyak Nivkh";
	const GIT = "Gitxsan";
	const EL = "Greek";
	const GRC = "Greek Ancient (to 1453)";
	const KL = "Greenlandic";
	const GN = "Guarani";
	const GU = "Gujarati";
	const GWI = "Gwichin";
	const HAI = "Haida";
	const HNN = "Hainanese";
	const HAS = "Haisla";
	const HAK = "Hakka";
	const HUR = "Halkomelem";
	const HAA = "Han";
	const HNI = "Hani";
	const HA = "Hausa";
	const HAW = "Hawaiian";
	const HE = "Hebrew";
	const IW = "Hebrew";
	const HEI = "Heiltsuk";
	const HID = "Hidatsa";
	const HIL = "Hiligaynon";
	const HI = "Hindi";
	const HMN = "Hmong";
	const HKK = "Hokkien";
	const HOP = "Hopi";
	const CZH = "Huizhou Chinese";
	const HU = "Hungarian";
	const IS = "Icelandic";
	const KPO = "Ikposo";
	const ILO = "Iloko";
	const SMN = "Inari Sami";
	const IN = "Indonesian";
	const ID = "Indonesian";
	const IZH = "Ingrian";
	const INH = "Ingush";
	const IA = "Interlingua";
	const IE = "Interlingue";
	const IU = "Inuktitut";
	const IK = "Inupiak";
	const GA = "Irish";
	const IT = "Italian";
	const ITL = "Itelmen";
	const JA = "Japanese";
	const JV = "Javanese";
	const CJY = "Jinyu Chinese";
	const KAJ = "Jju";
	const JCT = "Judeo-Crimean Tatar";
	const JGE = "Judeo-Georgian";
	const JUT = "Jutish";
	const KBD = "Kabardian";
	const KEA = "Kabuverdianu";
	const KAB = "Kabyle";
	const KFR = "Kachchi";
	const KJV = "Kaikavian literary language (Kajkavian)";
	const XAL = "Kalmyk Oirat";
	const KN = "Kannada";
	const KSK = "Kansa";
	const KRC = "Karachay-Balkar";
	const KIM = "Karagas";
	const KDR = "Karaim";
	const KAA = "Karakalpak";
	const KRL = "Karelian";
	const KS = "Kashmiri";
	const CSB = "Kashubian";
	const KKZ = "Kaska";
	const KAW = "Kawi";
	const KK = "Kazakh";
	const KJH = "Khakas";
	const KLJ = "Khalaj Turkic";
	const KCA = "Khanty";
	const KHA = "Khasi";
	const KXM = "Khmer Northern";
	const KIC = "Kickapoo";
	const RW = "Kinyarwanda (Ruanda)";
	const KIO = "Kiowa";
	const KY = "Kirghiz";
	const RN = "Kirundi (Rundi)";
	const TLH = "Klingon tlhIngan-Hol";
	const KFA = "Kodava";
	const KOI = "Komi-Permyak";
	const KOK = "Konkani (generic)";
	const KNN = "Konkani (specific)";
	const GOM = "Konkani Goan";
	const KO = "Korean";
	const KPY = "Koryak";
	const KOS = "Kosraean";
	const AVK = "Kotava";
	const KPE = "Kpelle";
	const DIH = "Kumiai";
	const KUM = "Kumyk";
	const KU = "Kurdish";
	const KUT = "Kutenai";
	const KWK = "Kwakiutl";
	const GDM = "Laal";
	const LLD = "Ladin";
	const LAD = "Ladino";
	const LAH = "Lahnda";
	const LHU = "Lahu";
	const LBE = "Lak";
	const LKI = "Laki";
	const LKT = "Lakota";
	const LO = "Laothian";
	const LA = "Latin";
	const LV = "Latvian (Lettish)";
	const LZZ = "Laz";
	const LEZ = "Lezghian";
	const LIJ = "Ligurian";
	const LIL = "Lillooet";
	const LIF = "Limbu";
	const LI = "Limburgish ( Limburger)";
	const LN = "Lingala";
	const LT = "Lithuanian";
	const JBO = "Lojban";
	const LOM = "Loma (Liberia)";
	const LMO = "Lombard";
	const NDS = "Low German Low Saxon";
	const LOZ = "Lozi";
	const LUA = "Luba-Lulua";
	const LUQ = "Lucumi";
	const LUD = "Ludian";
	const SMJ = "Lule Sami";
	const LUN = "Lunda";
	const LUO = "Luo (Kenya and Tanzania)";
	const LUT = "Lushootseed";
	const MK = "Macedonian";
	const MAD = "Madurese";
	const MAG = "Magahi";
	const MAI = "Maithili";
	const MG = "Malagasy";
	const MS = "Malay";
	const ML = "Malayalam";
	const PQM = "Malecite-Passamaquoddy";
	const MT = "Maltese";
	const MNC = "Manchu";
	const MID = "Mandaic";
	const MHQ = "Mandan";
	const CMN = "Mandarin Chinese";
	const MNS = "Mansi";
	const MI = "Maori";
	const MRW = "Maranao";
	const MR = "Marathi";
	const CHM = "Mari (Russia)";
	const MWR = "Marwari";
	const MAS = "Masai";
	const MFY = "Mayo";
	const MNI = "Meitei";
	const MEN = "Mende (Sierra Leone)";
	const MEZ = "Menominee";
	const MIC = "Micmac";
	const MNP = "Min Bei Chinese";
	const CDO = "Min Dong Chinese";
	const MIN = "Minangkabau";
	const XMF = "Mingrelian";
	const MWL = "Mirandese";
	const MOH = "Mohawk";
	const MDF = "Moksha";
	const MO = "Moldavian";
	const MNW = "Mon";
	const MN = "Mongolian";
	const MFE = "Morisyen";
	const MOS = "Mossi";
	const MXI = "Mozarabic";
	const MU = "Multilingual";
	const MTQ = "Muong";
	const NAQ = "Nama (Namibia)";
	const GLD = "Nanai";
	const NSK = "Naskapi";
	const NA = "Nauru";
	const NAP = "Neapolitan";
	const NE = "Nepali";
	const NEW_ = "Newari Nepal Bhasa";
	const NIO = "Nganasan";
	const NCG = "Nisgaa";
	const NIU = "Niuean";
	const NOG = "Nogai";
	const NON = "Norse Old";
	const NSO = "Northern Sotho Pedi Sepedi";
	const NO = "Norwegian";
	const NOV = "Novial";
	const NYM = "Nyamwezi";
	const NYO = "Nyoro";
	const NYS = "Nyungah";
	const OC = "Occitan";
	const OJC = "Ojibwa Central";
	const OJG = "Ojibwa Eastern";
	const OJB = "Ojibwa Northwestern";
	const OJS = "Ojibwa Severn";
	const OJW = "Ojibwa Western";
	const RYU = "Okinawan Central";
	const ANG = "Old English";
	const ONE = "Oneida";
	const ONO = "Onondaga";
	const OR_ = "Oriya";
	const OM = "Oromo (Afan, Galla)";
	const OTW = "Ottawa";
	const PPI = "Paipai";
	const PAU = "Palauan";
	const PAM = "Pampanga";
	const PAG = "Pangasinan";
	const PAP = "Papiamento";
	const PS = "Pashto (Pushto)";
	const PRP = "Persian";
	const PRS = "Persian (Dari)";
	const PFL = "Pfaelzisch";
	const PCD = "Picard";
	const PMS = "Piedmontese";
	const MYP = "Pirah";
	const PIH = "Pitcairn-Norfolk";
	const PDT = "Plautdietsch";
	const PL = "Polish";
	const PNT = "Pontic";
	const PT = "Portuguese";
	const POT = "Potawatomi";
	const PRG = "Prussian";
	const FUC = "Pulaar";
	const PA = "Punjabi";
	const QXQ = "Qashqai";
	const ALC = "Qawasqar";
	const QU = "Quechua";
	const QUC = "Quich Central";
	const RAP = "Rapanui";
	const RAR = "Rarotongan";
	const QTZ = "Reserved for local use.";
	const RM = "Rhaeto-Romance";
	const RGN = "Romagnol";
	const RMF = "Romani Kalo Finnish";
	const RMO = "Romani Sinte";
	const RO = "Romanian";
	const RUO = "Romanian Istro";
	const RUQ = "Romanian Megleno";
	const ROM = "Romany";
	const RCF = "Runion Creole French";
	const RU = "Russian";
	const RUE = "Rusyn";
	const ACF = "Saint Lucian Creole French";
	const SAH = "Sakha";
	const SLR = "Salar";
	const STR = "Salish Straits";
	const SJD = "Sami Kildin";
	const SM = "Samoan";
	const SG = "Sangro";
	const SA = "Sanskrit";
	const SAT = "Santali";
	const SRM = "Saramaccan";
	const SDC = "Sardinian Sassarese";
	const STQ = "Saterland Frisian";
	const SXU = "Saxon Upper";
	const SCO = "Scots";
	const SEC = "Sechelt";
	const TRV = "Seediq";
	const SEK = "Sekani";
	const SEL = "Selkup";
	const SEE = "Seneca";
	const SR = "Serbian";
	const SH = "Serbo-Croatian";
	const SEI = "Seri";
	const ST = "Sesotho";
	const TN = "Setswana";
	const SJW = "Shawnee";
	const SN = "Shona";
	const CJS = "Shor";
	const SHH = "Shoshoni";
	const SHS = "Shuswap";
	const SCN = "Sicilian";
	const SID = "Sidamo";
	const SZL = "Silesian";
	const SD = "Sindhi";
	const SI = "Sinhalese";
	const SS = "Siswati";
	const SMS = "Skolt Sami";
	const SCS = "Slavey North";
	const XSL = "Slavey South";
	const SK = "Slovak";
	const SL = "Slovenian";
	const SO = "Somali";
	const SNK = "Soninke";
	const DSB = "Sorbian Lower";
	const HSB = "Sorbian Upper";
	const SMA = "Southern Sami";
	const ES = "Spanish";
	const SRN = "Sranan";
	const STO = "Stoney";
	const XSV = "Sudovian";
	const SUX = "Sumerian";
	const SU = "Sundanese";
	const SVA = "Svan";
	const SWG = "Swabian";
	const SW = "Swahili (Kiswahili)";
	const SV = "Swedish";
	const SWL = "Swedish Sign Language";
	const GSW = "Swiss German Alemannic Alsatian";
	const SYR = "Syriac";
	const TAB = "Tabassaran";
	const SHY = "Tachawit";
	const SHI = "Tachelhit";
	const TL = "Tagalog";
	const TBW = "Tagbanwa";
	const TGX = "Tagish";
	const THT = "Tahltan";
	const TDD = "Tai Na";
	const TG = "Tajik";
	const TLY = "Talysh";
	const TTQ = "Tamajaq Tawallammat";
	const TAQ = "Tamasheq";
	const TZM = "Tamazight Central Atlas";
	const TA = "Tamil";
	const TAR = "Tarahumara Central";
	const TTT = "Tat Muslim";
	const TT = "Tatar";
	const TE = "Telugu";
	const TEO = "Teo Chew";
	const TET = "Tetum";
	const TH = "Thai";
	const NOD = "Thai (Northern)";
	const TTS = "Thai Northeastern";
	const THP = "Thompson";
	const BO = "Tibetan";
	const TIG = "Tigre";
	const TI = "Tigrinya";
	const TLI = "Tlingit";
	const TCX = "Toda";
	const OOD = "Tohono Oodham";
	const TPI = "Tok Pisin";
	const TO = "Tonga";
	const TOG = "Tonga (Nyasa)";
	const DDO = "Tsez";
	const TSI = "Tsimshian";
	const TS = "Tsonga";
	const TCY = "Tulu";
	const TUM = "Tumbuka";
	const MZB = "Tumzabt";
	const TPN = "Tupinamb";
	const TUV = "Turkana";
	const TR = "Turkish";
	const OTA = "Turkish Ottoman";
	const TK = "Turkmen";
	const TUS = "Tuscarora";
	const TVL = "Tuvalu";
	const TYV = "Tuvinian";
	const TW = "Twi";
	const UBY = "Ubykh";
	const UDI = "Udi";
	const UDM = "Udmurt";
	const UG = "Uighur";
	const UK = "Ukrainian";
	const UN = "Undefined";
	const UR = "Urdu";
	const UUM = "Urum";
	const UZ = "Uzbek";
	const VEC = "Venetian";
	const VEP = "Veps";
	const VI = "Vietnamese";
	const VO = "Volapuk";
	const VOR = "Voro";
	const VOT = "Votic";
	const VRO = "Vro";
	const AUC = "Waorani";
	const WAR = "Waray (Philippines)";
	const CY = "Welsh";
	const PES = "Western Farsi";
	const AMW = "Western Neo-Aramaic";
	const WIY = "Wiyot";
	const WO = "Wolof";
	const WUU = "Wu Chinese";
	const WYM = "Wymysorys";
	const XH = "Xhosa";
	const AME = "Yanesha";
	const YI = "Yiddish";
	const JI = "Yiddish";
	const YO = "Yoruba";
	const ZAI = "Zapotec Isthmus";
	const DJE = "Zarma";
	const ZU = "Zulu";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLanguageCode extends KalturaEnumBase
{
	const AA = "aa";
	const AB = "ab";
	const AF = "af";
	const AM = "am";
	const AR = "ar";
	const AS_ = "as";
	const AY = "ay";
	const AZ = "az";
	const BA = "ba";
	const BE = "be";
	const BG = "bg";
	const BH = "bh";
	const BI = "bi";
	const BN = "bn";
	const BO = "bo";
	const BR = "br";
	const CA = "ca";
	const CO = "co";
	const CS = "cs";
	const CY = "cy";
	const DA = "da";
	const DE = "de";
	const DZ = "dz";
	const EL = "el";
	const EN = "en";
	const EN_GB = "en_gb";
	const EN_US = "en_us";
	const EO = "eo";
	const ES = "es";
	const ET = "et";
	const EU = "eu";
	const FA = "fa";
	const FI = "fi";
	const FJ = "fj";
	const FO = "fo";
	const FR = "fr";
	const FY = "fy";
	const GA = "ga";
	const GD = "gd";
	const GL = "gl";
	const GN = "gn";
	const GU = "gu";
	const GV = "gv";
	const HA = "ha";
	const HE = "he";
	const HI = "hi";
	const HR = "hr";
	const HU = "hu";
	const HY = "hy";
	const IA = "ia";
	const ID = "id";
	const IE = "ie";
	const IK = "ik";
	const IN = "in";
	const IS = "is";
	const IT = "it";
	const IU = "iu";
	const IW = "iw";
	const JA = "ja";
	const JI = "ji";
	const JV = "jv";
	const KA = "ka";
	const KK = "kk";
	const KL = "kl";
	const KM = "km";
	const KN = "kn";
	const KO = "ko";
	const KS = "ks";
	const KU = "ku";
	const KY = "ky";
	const LA = "la";
	const LI = "li";
	const LN = "ln";
	const LO = "lo";
	const LT = "lt";
	const LV = "lv";
	const MG = "mg";
	const MI = "mi";
	const MK = "mk";
	const ML = "ml";
	const MN = "mn";
	const MO = "mo";
	const MR = "mr";
	const MS = "ms";
	const MT = "mt";
	const MU = "multilingual";
	const MY = "my";
	const NA = "na";
	const NE = "ne";
	const NL = "nl";
	const NO = "no";
	const OC = "oc";
	const OM = "om";
	const OR_ = "or";
	const PA = "pa";
	const PL = "pl";
	const PS = "ps";
	const PT = "pt";
	const QU = "qu";
	const RM = "rm";
	const RN = "rn";
	const RO = "ro";
	const RU = "ru";
	const RW = "rw";
	const SA = "sa";
	const SD = "sd";
	const SG = "sg";
	const SH = "sh";
	const SI = "si";
	const SK = "sk";
	const SL = "sl";
	const SM = "sm";
	const SN = "sn";
	const SO = "so";
	const SQ = "sq";
	const SR = "sr";
	const SS = "ss";
	const ST = "st";
	const SU = "su";
	const SV = "sv";
	const SW = "sw";
	const TA = "ta";
	const TE = "te";
	const TG = "tg";
	const TH = "th";
	const TI = "ti";
	const TK = "tk";
	const TL = "tl";
	const TN = "tn";
	const TO = "to";
	const TR = "tr";
	const TS = "ts";
	const TT = "tt";
	const TW = "tw";
	const UG = "ug";
	const UK = "uk";
	const UR = "ur";
	const UZ = "uz";
	const VI = "vi";
	const VO = "vo";
	const WO = "wo";
	const XH = "xh";
	const YI = "yi";
	const YO = "yo";
	const ZH = "zh";
	const ZU = "zu";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveAssetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DELETED_AT_ASC = "+deletedAt";
	const SIZE_ASC = "+size";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DELETED_AT_DESC = "-deletedAt";
	const SIZE_DESC = "-size";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const FIRST_BROADCAST_ASC = "+firstBroadcast";
	const LAST_BROADCAST_ASC = "+lastBroadcast";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const FIRST_BROADCAST_DESC = "-firstBroadcast";
	const LAST_BROADCAST_DESC = "-lastBroadcast";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const START_TIME_ASC = "+startTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const START_TIME_DESC = "-startTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentStatus extends KalturaEnumBase
{
	const ACTIVE = "2";
	const DELETED = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentTriggerType extends KalturaEnumBase
{
	const CHANNEL_RELATIVE = "1";
	const ABSOLUTE_TIME = "2";
	const SEGMENT_START_RELATIVE = "3";
	const SEGMENT_END_RELATIVE = "4";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentType extends KalturaEnumBase
{
	const VIDEO_AND_AUDIO = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const FIRST_BROADCAST_ASC = "+firstBroadcast";
	const LAST_BROADCAST_ASC = "+lastBroadcast";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const FIRST_BROADCAST_DESC = "-firstBroadcast";
	const LAST_BROADCAST_DESC = "-lastBroadcast";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportOrderBy extends KalturaEnumBase
{
	const NAME_ASC = "+name";
	const AUDIENCE_DESC = "-audience";
	const EVENT_TIME_DESC = "-eventTime";
	const PLAYS_DESC = "-plays";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportType extends KalturaEnumBase
{
	const ENTRY_GEO_TIME_LINE = "ENTRY_GEO_TIME_LINE";
	const ENTRY_SYNDICATION_TOTAL = "ENTRY_SYNDICATION_TOTAL";
	const ENTRY_TIME_LINE = "ENTRY_TIME_LINE";
	const ENTRY_TOTAL = "ENTRY_TOTAL";
	const PARTNER_TOTAL = "PARTNER_TOTAL";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const FIRST_BROADCAST_ASC = "+firstBroadcast";
	const LAST_BROADCAST_ASC = "+lastBroadcast";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const FIRST_BROADCAST_DESC = "-firstBroadcast";
	const LAST_BROADCAST_DESC = "-lastBroadcast";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const FIRST_BROADCAST_ASC = "+firstBroadcast";
	const LAST_BROADCAST_ASC = "+lastBroadcast";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const FIRST_BROADCAST_DESC = "-firstBroadcast";
	const LAST_BROADCAST_DESC = "-lastBroadcast";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMailType extends KalturaEnumBase
{
	const MAIL_TYPE_KALTURA_NEWSLETTER = "10";
	const MAIL_TYPE_ADDED_TO_FAVORITES = "11";
	const MAIL_TYPE_ADDED_TO_CLIP_FAVORITES = "12";
	const MAIL_TYPE_NEW_COMMENT_IN_PROFILE = "13";
	const MAIL_TYPE_CLIP_ADDED_YOUR_KALTURA = "20";
	const MAIL_TYPE_VIDEO_ADDED = "21";
	const MAIL_TYPE_ROUGHCUT_CREATED = "22";
	const MAIL_TYPE_ADDED_KALTURA_TO_YOUR_FAVORITES = "23";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA = "24";
	const MAIL_TYPE_CLIP_ADDED = "30";
	const MAIL_TYPE_VIDEO_CREATED = "31";
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES = "32";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_CONTRIBUTED = "33";
	const MAIL_TYPE_CLIP_CONTRIBUTED = "40";
	const MAIL_TYPE_ROUGHCUT_CREATED_SUBSCRIBED = "41";
	const MAIL_TYPE_ADDED_KALTURA_TO_HIS_FAVORITES_SUBSCRIBED = "42";
	const MAIL_TYPE_NEW_COMMENT_IN_KALTURA_YOU_SUBSCRIBED = "43";
	const MAIL_TYPE_REGISTER_CONFIRM = "50";
	const MAIL_TYPE_PASSWORD_RESET = "51";
	const MAIL_TYPE_LOGIN_MAIL_RESET = "52";
	const MAIL_TYPE_REGISTER_CONFIRM_VIDEO_SERVICE = "54";
	const MAIL_TYPE_VIDEO_READY = "60";
	const MAIL_TYPE_VIDEO_IS_READY = "62";
	const MAIL_TYPE_BULK_DOWNLOAD_READY = "63";
	const MAIL_TYPE_BULKUPLOAD_FINISHED = "64";
	const MAIL_TYPE_BULKUPLOAD_FAILED = "65";
	const MAIL_TYPE_BULKUPLOAD_ABORTED = "66";
	const MAIL_TYPE_NOTIFY_ERR = "70";
	const MAIL_TYPE_ACCOUNT_UPGRADE_CONFIRM = "80";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE = "81";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED = "82";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED = "83";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED = "84";
	const MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER = "85";
	const MAIL_TYPE_ACCOUNT_REACTIVE_CONFIRM = "86";
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD = "110";
	const MAIL_TYPE_SYSTEM_USER_RESET_PASSWORD_SUCCESS = "111";
	const MAIL_TYPE_SYSTEM_USER_NEW_PASSWORD = "112";
	const MAIL_TYPE_SYSTEM_USER_CREDENTIALS_SAVED = "113";
	const MAIL_TYPE_LIVE_REPORT_EXPORT_SUCCESS = "130";
	const MAIL_TYPE_LIVE_REPORT_EXPORT_FAILURE = "131";
	const MAIL_TYPE_LIVE_REPORT_EXPORT_ABORT = "132";
	const MAIL_TYPE_USERS_CSV = "133";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMatchConditionType extends KalturaEnumBase
{
	const MATCH_ANY = "1";
	const MATCH_ALL = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MEDIA_DATE = "mediaDate";
	const MEDIA_TYPE = "mediaType";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const FLAVOR_PARAMS_IDS = "flavorParamsIds";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MEDIA_TYPE_ASC = "+mediaType";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MEDIA_TYPE_DESC = "-mediaType";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutputOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaInfoOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaParserType extends KalturaEnumBase
{
	const MEDIAINFO = "0";
	const REMOTE_MEDIAINFO = "remoteMediaInfo.RemoteMediaInfo";
	const FFMPEG = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const HEARTBEAT_TIME_ASC = "+heartbeatTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const HEARTBEAT_TIME_DESC = "-heartbeatTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaModerationFlagStatus extends KalturaEnumBase
{
	const PENDING = "1";
	const MODERATED = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaModerationObjectType extends KalturaEnumBase
{
	const ENTRY = "2";
	const USER = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerOrderBy extends KalturaEnumBase
{
	const ADMIN_EMAIL_ASC = "+adminEmail";
	const ADMIN_NAME_ASC = "+adminName";
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const NAME_ASC = "+name";
	const STATUS_ASC = "+status";
	const WEBSITE_ASC = "+website";
	const ADMIN_EMAIL_DESC = "-adminEmail";
	const ADMIN_NAME_DESC = "-adminName";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const NAME_DESC = "-name";
	const STATUS_DESC = "-status";
	const WEBSITE_DESC = "-website";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionItemOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionItemType extends KalturaEnumBase
{
	const API_ACTION_ITEM = "kApiActionPermissionItem";
	const API_PARAMETER_ITEM = "kApiParameterPermissionItem";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const NAME_ASC = "+name";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const NAME_DESC = "-name";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const LAST_PLAYED_AT = "lastPlayedAt";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const MS_DURATION = "msDuration";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const PLAYS = "plays";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
	const VIEWS = "views";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const DURATION_TYPE = "durationType";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_DATE_ASC = "+endDate";
	const LAST_PLAYED_AT_ASC = "+lastPlayedAt";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PLAYS_ASC = "+plays";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const VIEWS_ASC = "+views";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_DATE_DESC = "-endDate";
	const LAST_PLAYED_AT_DESC = "-lastPlayedAt";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const PLAYS_DESC = "-plays";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const VIEWS_DESC = "-views";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaybackProtocol extends KalturaEnumBase
{
	const APPLE_HTTP = "applehttp";
	const APPLE_HTTP_TO_MC = "applehttp_to_mc";
	const AUTO = "auto";
	const AKAMAI_HD = "hdnetwork";
	const AKAMAI_HDS = "hdnetworkmanifest";
	const HDS = "hds";
	const HLS = "hls";
	const HTTP = "http";
	const MPEG_DASH = "mpegdash";
	const MULTICAST_SL = "multicast_silverlight";
	const RTMP = "rtmp";
	const RTSP = "rtsp";
	const SILVER_LIGHT = "sl";
	const URL = "url";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistCompareAttribute extends KalturaEnumBase
{
	const ACCESS_CONTROL_ID = "accessControlId";
	const CREATED_AT = "createdAt";
	const END_DATE = "endDate";
	const MODERATION_COUNT = "moderationCount";
	const MODERATION_STATUS = "moderationStatus";
	const PARTNER_ID = "partnerId";
	const PARTNER_SORT_VALUE = "partnerSortValue";
	const RANK = "rank";
	const REPLACEMENT_STATUS = "replacementStatus";
	const START_DATE = "startDate";
	const STATUS = "status";
	const TOTAL_RANK = "totalRank";
	const TYPE = "type";
	const UPDATED_AT = "updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistMatchAttribute extends KalturaEnumBase
{
	const ADMIN_TAGS = "adminTags";
	const CATEGORIES_IDS = "categoriesIds";
	const CREATOR_ID = "creatorId";
	const DESCRIPTION = "description";
	const GROUP_ID = "groupId";
	const ID = "id";
	const NAME = "name";
	const REFERENCE_ID = "referenceId";
	const REPLACED_ENTRY_ID = "replacedEntryId";
	const REPLACING_ENTRY_ID = "replacingEntryId";
	const SEARCH_TEXT = "searchText";
	const TAGS = "tags";
	const USER_ID = "userId";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const END_DATE_ASC = "+endDate";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const NAME_ASC = "+name";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const RANK_ASC = "+rank";
	const RECENT_ASC = "+recent";
	const START_DATE_ASC = "+startDate";
	const TOTAL_RANK_ASC = "+totalRank";
	const UPDATED_AT_ASC = "+updatedAt";
	const WEIGHT_ASC = "+weight";
	const CREATED_AT_DESC = "-createdAt";
	const END_DATE_DESC = "-endDate";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const NAME_DESC = "-name";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RANK_DESC = "-rank";
	const RECENT_DESC = "-recent";
	const START_DATE_DESC = "-startDate";
	const TOTAL_RANK_DESC = "-totalRank";
	const UPDATED_AT_DESC = "-updatedAt";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizUserEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportInterval extends KalturaEnumBase
{
	const DAYS = "days";
	const MONTHS = "months";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportType extends KalturaEnumBase
{
	const QUIZ = "quiz.QUIZ";
	const QUIZ_AGGREGATE_BY_QUESTION = "quiz.QUIZ_AGGREGATE_BY_QUESTION";
	const QUIZ_USER_AGGREGATE_BY_QUESTION = "quiz.QUIZ_USER_AGGREGATE_BY_QUESTION";
	const QUIZ_USER_PERCENTAGE = "quiz.QUIZ_USER_PERCENTAGE";
	const TOP_CONTENT = "1";
	const CONTENT_DROPOFF = "2";
	const CONTENT_INTERACTIONS = "3";
	const MAP_OVERLAY = "4";
	const TOP_CONTRIBUTORS = "5";
	const TOP_SYNDICATION = "6";
	const CONTENT_CONTRIBUTIONS = "7";
	const USER_ENGAGEMENT = "11";
	const SPECIFIC_USER_ENGAGEMENT = "12";
	const USER_TOP_CONTENT = "13";
	const USER_CONTENT_DROPOFF = "14";
	const USER_CONTENT_INTERACTIONS = "15";
	const APPLICATIONS = "16";
	const USER_USAGE = "17";
	const SPECIFIC_USER_USAGE = "18";
	const VAR_USAGE = "19";
	const TOP_CREATORS = "20";
	const PLATFORMS = "21";
	const OPERATING_SYSTEM = "22";
	const BROWSERS = "23";
	const LIVE = "24";
	const TOP_PLAYBACK_CONTEXT = "25";
	const VPAAS_USAGE = "26";
	const ENTRY_USAGE = "27";
	const PARTNER_USAGE = "201";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRuleActionType extends KalturaEnumBase
{
	const DRM_POLICY = "drm.DRM_POLICY";
	const BLOCK = "1";
	const PREVIEW = "2";
	const LIMIT_FLAVORS = "3";
	const ADD_TO_STORAGE = "4";
	const LIMIT_DELIVERY_PROFILES = "5";
	const SERVE_FROM_REMOTE_SERVER = "6";
	const REQUEST_HOST_REGEX = "7";
	const LIMIT_THUMBNAIL_CAPTURE = "8";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchemaType extends KalturaEnumBase
{
	const BULK_UPLOAD_RESULT_XML = "bulkUploadXml.bulkUploadResultXML";
	const BULK_UPLOAD_XML = "bulkUploadXml.bulkUploadXML";
	const INGEST_API = "cuePoint.ingestAPI";
	const SERVE_API = "cuePoint.serveAPI";
	const DROP_FOLDER_XML = "dropFolderXmlBulkUpload.dropFolderXml";
	const SYNDICATION = "syndication";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchConditionComparison extends KalturaEnumBase
{
	const EQUAL = "1";
	const GREATER_THAN = "2";
	const GREATER_THAN_OR_EQUAL = "3";
	const LESS_THAN = "4";
	const LESS_THAN_OR_EQUAL = "5";
	const NOT_EQUAL = "6";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerNodeOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const HEARTBEAT_TIME_ASC = "+heartbeatTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const HEARTBEAT_TIME_DESC = "-heartbeatTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerNodeType extends KalturaEnumBase
{
	const CONFERENCE_SERVER = "conference.CONFERENCE_SERVER";
	const WOWZA_MEDIA_SERVER = "wowza.WOWZA_MEDIA_SERVER";
	const EDGE = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSourceType extends KalturaEnumBase
{
	const LIMELIGHT_LIVE = "limeLight.LIVE_STREAM";
	const VELOCIX_LIVE = "velocix.VELOCIX_LIVE";
	const FILE = "1";
	const WEBCAM = "2";
	const URL = "5";
	const SEARCH_PROVIDER = "6";
	const AKAMAI_LIVE = "29";
	const MANUAL_LIVE_STREAM = "30";
	const AKAMAI_UNIVERSAL_LIVE = "31";
	const LIVE_STREAM = "32";
	const LIVE_CHANNEL = "33";
	const RECORDED_LIVE = "34";
	const CLIP = "35";
	const KALTURA_RECORDED_LIVE = "36";
	const LECTURE_CAPTURE = "37";
	const LIVE_STREAM_ONTEXTDATA_CAPTIONS = "42";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileProtocol extends KalturaEnumBase
{
	const KONTIKI = "kontiki.KONTIKI";
	const KALTURA_DC = "0";
	const FTP = "1";
	const SCP = "2";
	const SFTP = "3";
	const S3 = "6";
	const LOCAL = "7";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationFeedEntriesOrderBy extends KalturaEnumBase
{
	const CREATED_AT_DESC = "-createdAt";
	const RECENT = "recent";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTaggedObjectType extends KalturaEnumBase
{
	const ENTRY = "1";
	const CATEGORY = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAssetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const DELETED_AT_ASC = "+deletedAt";
	const SIZE_ASC = "+size";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DELETED_AT_DESC = "-deletedAt";
	const SIZE_DESC = "-size";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOutputOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeedCategories extends KalturaEnumBase
{
	const ANIMALS_AND_PETS = "Animals &amp; Pets";
	const ARTS_AND_ANIMATION = "Arts &amp; Animation";
	const AUTOS = "Autos";
	const COMEDY = "Comedy";
	const COMMERCIALS_PROMOTIONAL = "Commercials/Promotional";
	const ENTERTAINMENT = "Entertainment";
	const FAMILY_AND_KIDS = "Family &amp; Kids";
	const HOW_TO_INSTRUCTIONAL_DIY = "How To/Instructional/DIY";
	const MUSIC = "Music";
	const NEWS_AND_BLOGS = "News &amp; Blogs";
	const SCIENCE_AND_TECHNOLOGY = "Science &amp; Technology";
	const SPORTS = "Sports";
	const TRAVEL_AND_PLACES = "Travel &amp; Places";
	const VIDEO_GAMES = "Video Games";
	const VLOGS_PEOPLE = "Vlogs &amp; People";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadTokenOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryExtendedStatus extends KalturaEnumBase
{
	const PLAYBACK_COMPLETE = "viewHistory.PLAYBACK_COMPLETE";
	const PLAYBACK_STARTED = "viewHistory.PLAYBACK_STARTED";
	const VIEWED = "viewHistory.VIEWED";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryStatus extends KalturaEnumBase
{
	const QUIZ_SUBMITTED = "quiz.3";
	const ACTIVE = "1";
	const DELETED = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryType extends KalturaEnumBase
{
	const QUIZ = "quiz.QUIZ";
	const VIEW_HISTORY = "viewHistory.VIEW_HISTORY";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserLoginDataOrderBy extends KalturaEnumBase
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const NAME_ASC = "+name";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const NAME_DESC = "-name";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaVideoCodec extends KalturaEnumBase
{
	const NONE = "";
	const APCH = "apch";
	const APCN = "apcn";
	const APCO = "apco";
	const APCS = "apcs";
	const COPY = "copy";
	const DNXHD = "dnxhd";
	const DV = "dv";
	const FLV = "flv";
	const H263 = "h263";
	const H264 = "h264";
	const H264B = "h264b";
	const H264H = "h264h";
	const H264M = "h264m";
	const H265 = "h265";
	const MPEG2 = "mpeg2";
	const MPEG4 = "mpeg4";
	const THEORA = "theora";
	const VP6 = "vp6";
	const VP8 = "vp8";
	const VP9 = "vp9";
	const WMV2 = "wmv2";
	const WMV3 = "wmv3";
	const WVC1A = "wvc1a";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidgetOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedAdultValues extends KalturaEnumBase
{
	const ADULT = "adult";
	const NON_ADULT = "nonadult";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedCategories extends KalturaEnumBase
{
	const ACTION = "Action";
	const ANIMALS = "Animals";
	const ART_AND_ANIMATION = "Art &amp; Animation";
	const COMMERCIALS = "Commercials";
	const ENTERTAINMENT_AND_TV = "Entertainment &amp; TV";
	const FAMILY = "Family";
	const FOOD = "Food";
	const FUNNY_VIDEOS = "Funny Videos";
	const GAMES = "Games";
	const HEALTH_AND_BEAUTY = "Health &amp; Beauty";
	const HOW_TO = "How-To";
	const MOVIES_AND_SHORTS = "Movies &amp; Shorts";
	const MUSIC = "Music";
	const NEWS_AND_POLITICS = "News &amp; Politics";
	const PEOPLE_AND_VLOGS = "People &amp; Vlogs";
	const PRODUCTS_AND_TECH = "Products &amp; Tech.";
	const SCIENCE_AND_ENVIRONMENT = "Science &amp; Environment";
	const SPORTS = "Sports";
	const TRANSPORTATION = "Transportation";
	const TRAVEL = "Travel";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const NAME_ASC = "+name";
	const PLAYLIST_ID_ASC = "+playlistId";
	const TYPE_ASC = "+type";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const NAME_DESC = "-name";
	const PLAYLIST_ID_DESC = "-playlistId";
	const TYPE_DESC = "-type";
	const UPDATED_AT_DESC = "-updatedAt";
}

