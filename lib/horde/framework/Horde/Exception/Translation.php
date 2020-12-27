<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Exception
 */

/**
 * Horde_Exception_Translation is the translation wrapper class for Horde_Exception.
 *
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL
 * @package   Exception
 */
class Horde_Exception_Translation extends Horde_Translation_Autodetect
{
    /**
     * The translation domain
     *
     * @var string
     */
    protected static $_domain = 'Horde_Exception';

    /**
     * The absolute PEAR path to the translations for the default gettext handler.
     *
     * @var string
     */
    protected static $_pearDirectory = '@data_dir@';
}
