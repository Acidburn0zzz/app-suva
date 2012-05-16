<?php

/**
 * Suva server class.
 *
 * @category   Apps
 * @package    Suva
 * @subpackage Libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2011 ClearCenter
 * @license    http://www.clearcenter.com/Company/terms.html ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/suva/
 */


///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\suva;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('suva');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Shell as Shell;

clearos_load_library('base/Daemon');
clearos_load_library('base/File');
clearos_load_library('base/Shell');

// Exceptions
//-----------

use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/Validation_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Suva server class.
 *
 * @category   Apps
 * @package    Suva
 * @subpackage Libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2011 ClearCenter
 * @license    http://www.clearcenter.com/Company/terms.html ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/suva/
 */

class Suva extends Daemon
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const COMMAND_NEW_KEY = '/usr/bin/mkhost.sh';
    const FILE_CONFIG = '/etc/suvad.conf';
    const FILE_SUVA_CONFIGURED = '/var/clearos/suva/initialized';

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Suva constructor.
     */

    public function __construct() 
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('suvad');
    }

    /**
     * Returns hostkey.
     *
     * @return string hostkey
     * @throws Engine_Exception
     */

    public function get_hostkey()
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(self::FILE_CONFIG, TRUE);
        $hostkey = $file->lookup_value("/^\s*<hostkey>/");
        $hostkey = preg_replace('/<\/hostkey>/', '', $hostkey);

        // Reset a blank hostkey
        if (preg_match('/^00000000000000000000000000000000/', $hostkey)) {
            $this->reset_hostkey();
            $hostkey = $this->get_hostkey();
        }

        return $hostkey;
    }

    /**
     * Returns device name.
     *
     * @return string device name
     * @throws Engine_Exception
     */

    public function get_device_name()
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(self::FILE_CONFIG, TRUE);
        $device = $file->lookup_value("/^\s*<device>/");
        $device = preg_replace('/<\/device>/', '', $device);

        return $device;
    }

    /**
     * Resets a hostkey.
     *
     * @throws Engine_Exception
     */

    public function reset_hostkey()
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(self::FILE_SUVA_CONFIGURED);

        if ($file->exists())
            $file->delete();

        $shell = new Shell();
        $shell->execute(self::COMMAND_NEW_KEY, self::FILE_CONFIG, TRUE);
    }

    /**
     * Sets device name.
     *
     * @param string $device_name device name
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function set_device_name($device_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_device_name($device_name));

        $file = new File(self::FILE_CONFIG, TRUE);
        $file->replace_lines("/<device>.*<\/device>/", "\t<device>$device_name</device>\n");
        $this->reset();
    }

    ///////////////////////////////////////////////////////////////////////////////
    // V A L I D A T I O N
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Validation for device name.
     *
     * @param string $device_name device name
     *
     * @return error message if device name is invalid
     */

    public function validate_device_name($device_name)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (!preg_match("/^[0-9]+$/", $device_name)) 
            return lang('suva_device_name_invalid');
    }
}
