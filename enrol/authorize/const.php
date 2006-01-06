<?php // $Id$

/**
 * New order. No transaction was made.
 */
define('AN_STATUS_NONE',    0x00);

/**
 * Authorized.
 */
define('AN_STATUS_AUTH',    0x01);

/**
 * Captured.
 */
define('AN_STATUS_CAPTURE', 0x02);

/**
 * AN_STATUS_AUTH|AN_STATUS_CAPTURE.
 */
define('AN_STATUS_AUTHCAPTURE', 0x03);

/**
 * Refunded.
 */
define('AN_STATUS_CREDIT', 0x04);

/**
 * Voided.
 */
define('AN_STATUS_VOID', 0x08);

/**
 * Expired.
 */
define('AN_STATUS_EXPIRE', 0x10);

/**
 * No action.
 */
define('AN_ACTION_NONE', 0x00);

/**
 * Used to authorize only, don't capture.
 */
define('AN_ACTION_AUTH_ONLY', 0x01);

/**
 * Used to capture, it was authorized before.
 */
define('AN_ACTION_PRIOR_AUTH_CAPTURE', 0x02);

/**
 * Used to authorize and capture.
 */
define('AN_ACTION_AUTH_CAPTURE', 0x03);

/**
 * Used to return funds to a customer's credit card.
 *
 * - Can be credited within 120 days after the original authorization was obtained.
 * - Amount can be any amount up to the original amount charged.
 * - Captured/pending settlement transactions cannot be credited,
 *   instead a void must be issued to cancel the settlement.
 * NOTE: Assigns a new transactionID to the original transaction.
 *       SAVE IT, so we can cancel new refund if it is a fault return.
 */
define('AN_ACTION_CREDIT', 0x04);

/**
 * Used to cancel an exiting transaction with a status of
 * authorized/pending capture, captured/pending settlement or
 * settled/refunded.
 *
 * - Void requests effectively cancel the Capture request
 *   that would start the funds transfer process.
 * - Also used to cancel existing transaction with a status of
 *   settled/refunded. Credited mistakenly, so cancel it
 *   and return funds to our account.
 */
define('AN_ACTION_VOID', 0x08);

?>
