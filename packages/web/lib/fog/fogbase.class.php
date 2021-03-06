<?php
/**
 * FOGBase, the base class for pretty much all of fog.
 *
 * PHP version 5
 *
 * This gives all the rest of the classes a common frame to work from.
 *
 * @category FOGBase
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * FOGBase, the base class for pretty much all of fog.
 *
 * @category FOGBase
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
abstract class FOGBase
{
    /**
     * Language variables brought in from text.php.
     *
     * @var array
     */
    public static $foglang;
    /**
     * Sets if the requesting call is ajax requested.
     *
     * @var bool
     */
    public static $ajax = false;
    /**
     * Sets if this is a form submit.
     *
     * @var bool
     */
    public static $post = false;
    /**
     * Tells whether or not its a fog/service request.
     *
     * @var bool
     */
    public static $service = false;
    /**
     * Tells if we are json or not
     *
     * @var bool
     */
    public static $json = false;
    /**
     * Tells if we are new service or not
     *
     * @var bool
     */
    public static $newService = false;
    /**
     * Tests/sets if a given key is loaded already.
     *
     * @var array
     */
    protected $isLoaded = array();
    /**
     * The length of a given string item.
     *
     * @var int
     */
    protected static $strlen;
    /**
     * Display debug information.
     *
     * @var bool
     */
    protected static $debug = false;
    /**
     * Display extra information about items.
     *
     * @var bool
     */
    protected static $info = false;
    /**
     * Select box creator function stored in variable.
     *
     * @var callable
     */
    protected static $buildSelectBox;
    /**
     * Sets what's selected for the select box.
     *
     * @var bool|int
     */
    protected static $selected;
    /**
     * The database handler.
     *
     * @var object
     */
    protected static $DB;
    /**
     * FTP Handler.
     *
     * @var object
     */
    protected static $FOGFTP;
    /**
     * Core usage elements as FOGBase is abstract.
     *
     * @var object
     */
    protected static $FOGCore;
    /**
     * Event handling.
     *
     * @var object
     */
    protected static $EventManager;
    /**
     * Hook handling.
     *
     * @var object
     */
    protected static $HookManager;
    /**
     * The default timezone for all of fog to use.
     *
     * @var object
     */
    protected static $TimeZone;
    /**
     * The logged in user.
     *
     * @var object
     */
    protected static $FOGUser;
    /**
     * View/Page Controller-Manager.
     *
     * @var object
     */
    protected static $FOGPageManager;
    /**
     * URL Manager | mainly for ajax, and externel getters.
     *
     * @var object
     */
    protected static $FOGURLRequests;
    /**
     * Side/Sub menu manager.
     *
     * @var object
     */
    protected static $FOGSubMenu;
    /**
     * Current requests script name.
     *
     * @var string
     */
    protected static $scriptname;
    /**
     * Current requests query string.
     *
     * @var string
     */
    protected static $querystring;
    /**
     * Current requests http requested with string.
     *
     * @var string
     */
    protected static $httpreqwith;
    /**
     * Current request method.
     *
     * @var string
     */
    protected static $reqmethod;
    /**
     * Is this a mobile request?
     *
     * @var int|bool
     */
    protected static $isMobile;
    /**
     * The current server's IP information.
     *
     * @var array
     */
    protected static $ips = array();
    /**
     * The current server's Interface information.
     *
     * @var array
     */
    protected static $interface = array();
    /**
     * The current base pages requiring search functionality.
     *
     * @var array
     */
    protected static $searchPages = array(
        'user',
        'host',
        'group',
        'image',
        'storage',
        'snapin',
        'printer',
        'task',
    );
    /**
     * Is our current element already initialized?
     *
     * @var bool
     */
    private static $_initialized = false;
    /**
     * The current running schema information.
     *
     * @var int
     */
    public static $mySchema = 0;
    /**
     * Initializes the FOG System if needed.
     *
     * @return void
     */
    private static function _init()
    {
        if (self::$_initialized === true) {
            return;
        }
        global $foglang;
        global $FOGFTP;
        global $FOGCore;
        global $DB;
        global $currentUser;
        global $EventManager;
        global $HookManager;
        global $FOGURLRequests;
        global $FOGPageManager;
        global $TimeZone;
        self::$foglang = &$foglang;
        self::$FOGFTP = &$FOGFTP;
        self::$FOGCore = &$FOGCore;
        self::$DB = &$DB;
        self::$EventManager = &$EventManager;
        self::$HookManager = &$HookManager;
        self::$FOGUser = &$currentUser;
        $scriptPattern = '#/service/#i';
        $queryPattern = '#sub=requestClientInfo#i';
        self::$querystring = $_SERVER['QUERY_STRING'];
        if (isset($_SERVER['SCRIPT_NAME'])) {
            self::$scriptname = htmlentities(
                $_SERVER['SCRIPT_NAME'],
                ENT_QUOTES,
                'utf-8'
            );
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            self::$httpreqwith = htmlentities(
                $_SERVER['HTTP_X_REQUESTED_WITH'],
                ENT_QUOTES,
                'utf-8'
            );
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            self::$reqmethod = htmlentities(
                $_SERVER['REQUEST_METHOD'],
                ENT_QUOTES,
                'utf-8'
            );
        }
        if (preg_match('#/mobile/#i', self::$scriptname)) {
            self::$isMobile = true;
        }
        if (preg_match($scriptPattern, self::$scriptname)) {
            self::$service = true;
        } elseif (preg_match($queryPattern, self::$querystring)) {
            self::$service = true;
        }
        self::$ajax = preg_match('#^xmlhttprequest$#i', self::$httpreqwith);
        self::$post = preg_match('#^post$#i', self::$reqmethod);
        self::$newService = isset($_REQUEST['newService'])
            || $_REQUEST['sub'] == 'requestClientInfo';
        self::$json = isset($_REQUEST['json'])
            || self::$newService
            || $_REQUEST['sub'] == 'requestClientInfo';
        self::$FOGURLRequests = &$FOGURLRequests;
        self::$FOGPageManager = &$FOGPageManager;
        self::$TimeZone = &$TimeZone;
        /*
         * Lambda function to allow building of select boxes.
         *
         * @param string $option the option to iterate
         * @param bool|int $index the index to operate on if needed.
         *
         * @return void
         */
        self::$buildSelectBox = function ($option, $index = false) {
            $value = $option;
            if ($index) {
                $value = $index;
            }
            printf(
                '<option value="%s"%s>%s</option>',
                $value,
                (self::$selected == $value ? ' selected' : ''),
                $option
            );
        };
        self::$_initialized = true;
    }
    /**
     * Initiates the base class for FOG.
     *
     * @return this
     */
    public function __construct()
    {
        self::_init();

        return $this;
    }
    /**
     * Defines string as class name.
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
    /**
     * Returns the class after verifying reflection of the class.
     *
     * @param string $class the name of the class to load
     * @param mixed  $data  the data to load into the class
     * @param bool   $props return just properties or full object
     *
     * @throws Exception
     *
     * @return class Returns the instantiated class
     */
    public static function getClass($class, $data = '', $props = false)
    {
        if (!is_string($class)) {
            throw new Exception(_('Class name must be a string'));
        }
        // Get all args, even unnamed args.
        $args = func_get_args();
        array_shift($args);

        // Trim the class var
        $class = trim($class);

        // Test what the class is and return if it is Reflection.
        $lClass = strtolower($class);
        if ($lClass === 'reflectionclass') {
            return new ReflectionClass(count($args) === 1 ? $args[0] : $args);
        }

        global $sub;
        // If class is Storage, test if sub is group or node.
        if ($class === 'Storage') {
            $class = 'StorageNode';
            if (preg_match('#storage[-|_]group#i', $sub)) {
                $class = 'StorageGroup';
            }
        }

        // Initiate Reflection item.
        $obj = new ReflectionClass($class);

        // If props is set to true return the properties of the class.
        if ($props === true) {
            return $obj->getDefaultProperties();
        }

        // Return the main object
        if ($obj->getConstructor()) {
            // If there's only one argument return the instance using it.
            // Otherwise return with full call.
            if (count($args) === 1) {
                $class = $obj->newInstance($args[0]);
            } else {
                $class = $obj->newInstanceArgs($args);
            }
        } else {
            $class = $obj->newInstanceWithoutConstructor();
        }

        return $class;
    }
    /**
     * Get's the relevant host item.
     *
     * @param bool $service         Is this a service request
     * @param bool $encoded         Is this data encoded
     * @param bool $hostnotrequired Is the host return needed
     * @param bool $returnmacs      Only return macs?
     * @param bool $override        Perform an override of the items?
     *
     * @throws Exception
     *
     * @return array|object Returns either th macs or the host
     */
    public function getHostItem(
        $service = true,
        $encoded = false,
        $hostnotrequired = false,
        $returnmacs = false,
        $override = false
    ) {
        // Store the mac
        $mac = $_REQUEST['mac'];

        // If encoded decode and store value
        if ($encoded === true) {
            $mac = base64_decode($mac);
        }

        // Trim the mac list.
        $mac = trim($mac);

        // Parsing the macs
        $MACs = $this->parseMacList($mac, !$service, $service);

        foreach ((array) $MACs as &$mac) {
            if (!$mac->isValid()) {
                continue;
            }
            $macs[] = $mac->__toString();
            unset($mac);
        }

        // If no macs are returned and the host is not required,
        // throw message that it's an invalid mac.
        if (count($macs) < 1 && $hostnotrequired === false) {
            if ($service) {
                $msg = '#!im';
            } else {
                $msg = sprintf('%s %s', self::$foglang, $_REQUEST['mac']);
            }
            throw new Exception($msg);
        }

        // If returnmacs parameter is true, return the macs as an array
        if ($returnmacs) {
            if (!is_array($macs)) {
                $macs = (array) $macs;
            }

            return $macs;
        }

        // Get the host element based on the mac address
        $Host = self::getClass('HostManager')->getHostByMacAddresses($macs);
        if ($hostnotrequired === false && $override === false) {
            if ($Host->get('pending')) {
                $Host = new Host(0);
            }
            if (!($Host->isValid())) {
                if ($service) {
                    $msg = '#!ih';
                } else {
                    $msg = _('Invalid Host');
                }
                throw new Exception($msg);
            }
        }

        return $Host;
    }
    /**
     * Get's blamed nodes for failures.
     *
     * @return array
     */
    public function getAllBlamedNodes()
    {
        $DateInterval = self::niceDate()->modify('-5 minutes');
        /**
         * Returns the node id if still accurate
         * or will clean up past time nodes.
         *
         * @param object $NodeFailure the node that is in failed state
         *
         * @return int|bool
         */
        $nodeFail = function ($NodeFailure) use ($DateInterval) {
            if ($NodeFailure->isValid()) {
                return false;
            }
            $DateTime = self::niceDate($NodeFailure->get('failureTime'));
            if ($DateTime < $DateInterval) {
                $NodeFailure->destroy();

                return false;
            }

            return $NodeFailure->get('id');
        };
        $FailedNodes = self::getClass('NodeFailureManager')
            ->find(
                array(
                    'taskID' => $this->Host->get('task')->get('id'),
                    'hostID' => $this->Host->get('id'),
                )
            );
        $nodeRet = array_map($nodeFail, (array) $FailedNodes);
        $nodeRet = array_filter($nodeRet);
        $nodeRet = array_unique($nodeRet);
        $nodeRet = array_values($nodeRet);

        return $nodeRet;
    }
    /**
     * Returns array of plugins installed.
     *
     * @return array
     */
    protected static function getActivePlugins()
    {
        $plugins = self::getSubObjectIDs(
            'Plugin',
            array(
                'installed' => 1,
                'state' => 1,
            ),
            'name'
        );

        return array_map('strtolower', (array) $plugins);
    }
    /**
     * Converts our string if needed.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return string
     */
    private static function _setString($txt, $data = array())
    {
        if (count($data)) {
            $data = vsprintf($txt, $data);
        } else {
            $data = $txt;
        }

        return $data;
    }
    /**
     * Prints fatal errors.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return void
     */
    protected function fatalError($txt, $data = array())
    {
        if (self::$service || self::$ajax) {
            return;
        }
        $data = self::_setString($txt, $data);
        $string = sprintf(
            'FOG FATAL ERROR: %s: %s',
            get_class($this),
            $data
        );
        printf('<div class="debug-error">%s</div>', $string);
    }
    /**
     * Prints error.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return void
     */
    protected function error($txt, $data = array())
    {
        if ((self::$service || self::$ajax) || !self::$debug) {
            return;
        }
        $data = self::_setString($txt, $data);
        $string = sprintf(
            'FOG ERROR: %s: %s',
            get_class($this),
            $data
        );
        printf('<div class="debug-error">%s</div>', $string);
    }
    /**
     * Prints debug.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return void
     */
    protected function debug($txt, $data = array())
    {
        if ((self::$service || self::$ajax) || !self::$debug) {
            return;
        }
        $data = self::_setString($txt, $data);
        $string = sprintf(
            'FOG DEBUG: %s: %s',
            get_class($this),
            $data
        );
        printf('<div class="debug-error">%s</div>', $string);
    }
    /**
     * Prints info.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return void
     */
    protected function info($txt, $data = array())
    {
        if (!self::$info || self::$service || self::$ajax) {
            return;
        }
        $data = self::_setString($txt, $data);
        $string = sprintf(
            'FOG INFO: %s: %s',
            get_class($this),
            $data
        );
        printf('<div class="debug-info">%s</div>', $string);
    }
    /**
     * Sets message banner at top of pages.
     *
     * @param string $txt  the string to use
     * @param array  $data the data if txt is formatted string
     *
     * @return void
     */
    protected function setMessage($txt, $data = array())
    {
        $_SESSION['FOG_MESSAGES'] = self::_setString($txt, $data);
    }
    /**
     * Gets message banner and prepares to display it.
     *
     * @return string
     */
    protected function getMessages()
    {
        if (!isset($_SESSION['FOG_MESSAGES'])) {
            $_SESSION['FOG_MESSAGES'] = array();
        }
        $messages = (array) $_SESSION['FOG_MESSAGES'];
        unset($_SESSION['FOG_MESSAGES']);
        // Create a hook in for messages
        if (self::$HookManager instanceof HookManager) {
            self::$HookManager->processEvent(
                'MessageBox',
                array('data' => &$messages)
            );
        }
        /**
         * Lambda that simply prints the messages as passed.
         *
         * @param string $message the message to print
         */
        $print_messages = function ($message) {
            printf('<div class="fog-message-box">%s</div>', $message);
        };
        // Print the messages
        array_map($print_messages, $messages);
        unset($messages);
    }
    /**
     * Redirect pages where/when necessary.
     *
     * @param string $url The url to redirect to
     *
     * @return void
     */
    protected function redirect($url = '')
    {
        if (self::$service) {
            return;
        }
        header('Strict-Transport-Security: "max-age=15768000"');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Robots-Tag: none');
        header('X-Frame-Options: SAMEORIGIN');
        header("Location: $url");
        exit;
    }
    /**
     * Insert before key in array.
     *
     * @param string $key       the key to insert before
     * @param array  $array     the array to modify
     * @param string $new_key   the new key to insert
     * @param mixed  $new_value the value to insert
     *
     * @throws Exception
     * @return void
     */
    protected function arrayInsertBefore($key, array &$array, $new_key, $new_value)
    {
        if (!is_string($key)) {
            throw new Exception(_('Key must be a string or index'));
        }
        $new = array();
        foreach ($array as $k => &$value) {
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
            $new[$k] = $value;
            unset($k, $value);
        }
        $array = $new;
    }
    /**
     * Insert after key in array.
     *
     * @param string $key       the key to insert after
     * @param array  $array     the array to modify
     * @param string $new_key   the new key to insert
     * @param mixed  $new_value the value to insert
     *
     * @throws Exception
     * @return void
     */
    protected function arrayInsertAfter($key, array &$array, $new_key, $new_value)
    {
        if (!is_string($key) && !is_numeric($key)) {
            throw new Exception(_('Key must be a string or index'));
        }
        $new = array();
        foreach ($array as $k => &$value) {
            $new[$k] = $value;
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
            unset($k, $value);
        }
        $array = $new;
    }
    /**
     * Remove value based on the key from array.
     *
     * @param string|array $key   the key to remove
     * @param array        $array the array to work with
     *
     * @throws Exception
     * @return void
     */
    protected function arrayRemove($key, array &$array)
    {
        if (!(is_string($key) || is_array($key))) {
            throw new Exception(_('Key must be an array of keys or a string.'));
        }
        if (is_array($key)) {
            foreach ($key as &$k) {
                $this->arrayRemove($k, $array);
                unset($k);
            }
        } else {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $this->arrayRemove($key, $value);
                } else {
                    unset($array[$key]);
                }
                unset($value);
            }
        }
    }
    /**
     * Find the key of a needle within the haystack that is an array.
     *
     * @param mixed      $needle     the needle to find
     * @param array      $haystack   the array to search in
     * @param bool|mixed $ignorecase whether to care about case
     *
     * @return key or false
     */
    protected function arrayFind($needle, array $haystack, $ignorecase = false)
    {
        $cmd = $ignorecase !== false ? 'stripos' : 'strpos';
        foreach ($haystack as $key => &$value) {
            if (false !== $cmd($value, $needle)) {
                return $key;
            }
            unset($value);
        }

        return false;
    }
    /**
     * Check if isLoaded.
     *
     * @param string|int $key the key to see if loaded
     *
     * @return bool|string
     */
    protected function isLoaded($key)
    {
        $key = $this->key($key);
        $result = isset($this->isLoaded[$key]) ? $this->isLoaded[$key] : 0;
        ++$this->isLoaded[$key];

        return $result ? $result : false;
    }
    /**
     * Reset request variables.
     *
     * @return void
     */
    protected function resetRequest()
    {
        if (!isset($_SESSION['post_request_vals'])) {
            $_SESSION['post_request_vals'] = array();
        }
        $sesVars = $_SESSION['post_request_vals'];
        $setReq = function (&$val, &$key) {
            $_REQUEST[$key] = $val;
            unset($val, $key);
        };
        array_walk($sesVars, $setReq);
        unset($_SESSION['post_request_vals'], $sesVars, $reqVars);
    }
    /**
     * Set request vars particularly for post failures really.
     *
     * @return void
     */
    protected function setRequest()
    {
        if (!isset($_SESSION['post_request_vals'])) {
            $_SESSION['post_request_vals'] = array();
        }
        if (!$_SESSION['post_request_vals'] && self::$post) {
            $_SESSION['post_request_vals'] = $_POST;
        }
    }
    /**
     * Return nicely formatted byte sizes.
     *
     * @param int|float $size the size to convert
     *
     * @return float
     */
    protected function formatByteSize($size)
    {
        $units = array('iB', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $factor = floor((strlen($size) - 1) / 3);

        return sprintf('%3.2f %s', $size / pow(1024, $factor), $units[$factor]);
    }
    /**
     * Gets the global module status.
     *
     * Can return just the shortnames or the long.
     *
     * @param bool $names if set will return the services as set
     * @param bool $keys  will return just the shortnames if set
     *
     * @return array
     */
    protected function getGlobalModuleStatus($names = false, $keys = false)
    {
        // The shortnames are on the left, the long names are on the right
        // If the right is true it means the short is accurate.
        // If the left is not the right caller in form of:
        //     FOG_CLIENT_<name>_ENABLED in lowercase.
        $services = array(
            'autologout' => 'autologoff',
            'clientupdater' => true,
            'dircleanup' => 'directorycleaner',
            'displaymanager' => true,
            'greenfog' => true,
            'hostnamechanger' => true,
            'hostregister' => true,
            'powermanagement' => true,
            'printermanager' => true,
            'snapinclient' => 'snapin',
            'taskreboot' => true,
            'usercleanup' => true,
            'usertracker' => true,
        );
        // If keys is set, return just the keys.
        if ($keys) {
            $keys = array_keys($services);
            $keys = array_filter($keys);
            $keys = array_unique($keys);

            return array_values($keys);
        }
        // Change the keys values
        foreach ($services as $short => &$value) {
            $tmp = $value === true ? $short : $value;
            $value = sprintf('FOG_CLIENT_%s_ENABLED', strtoupper($tmp));
            unset($value);
        }
        // If names is set, send back the short and long names together.
        if ($names) {
            return $services;
        }
        // Now lets get their status'
        $serviceEn = self::getSubObjectIDs(
            'Service',
            array(
                'name' => array_values($services),
            ),
            'value',
            false,
            'AND',
            'name',
            false,
            false
        );

        return array_combine(array_keys($services), $serviceEn);
    }
    /**
     * Sets the date.
     *
     * @param mixed $date The date stamp, defaults to now if not set
     * @param bool  $utc  Whether to use utc timezone or not
     *
     * @return DateTime
     */
    public static function niceDate($date = 'now', $utc = false)
    {
        if ($utc || empty(self::$TimeZone)) {
            $tz = new DateTimeZone('UTC');
        } else {
            $tz = new DateTimeZone(self::$TimeZone);
        }

        return new DateTime($date, $tz);
    }
    /**
     * Do formatting things.
     *
     * @param mixed $time   The time to work from
     * @param mixed $format Specified format to return
     * @param bool  $utc    Use UTC Timezone?
     *
     * @return mixed
     */
    public function formatTime($time, $format = false, $utc = false)
    {
        if (!$time instanceof DateTime) {
            $time = self::niceDate($time, $utc);
        }
        if ($format) {
            if (!$this->validDate($time)) {
                return _('No Data');
            }

            return $time->format($format);
        }
        $now = self::niceDate('now', $utc);
        // Get difference of the current to supplied.
        $diff = $now->format('U') - $time->format('U');
        $absolute = abs($diff);
        if (is_nan($diff)) {
            return _('Not a number');
        }
        if (!$this->validDate($time)) {
            return _('No Data');
        }
        $date = $time->format('Y/m/d');
        if ($now->format('Y/m/d') == $date) {
            if (0 <= $diff && $absolute < 60) {
                return 'Moments ago';
            } elseif ($diff < 0 && $absolute < 60) {
                return 'Seconds from now';
            } elseif ($absolute < 3600) {
                return $this->humanify($diff / 60, 'minute');
            } else {
                return $this->humanify($diff / 3600, 'hour');
            }
        }
        $dayAgo = clone $now;
        $dayAgo->modify('-1 day');
        $dayAhead = clone $now;
        $dayAhead->modify('+1 day');
        if ($dayAgo->format('Y/m/d') == $date) {
            return 'Ran Yesterday at '.$time->format('H:i');
        } elseif ($dayAhead->format('Y/m/d') == $date) {
            return 'Runs today at '.$time->format('H:i');
        } elseif ($absolute / 86400 <= 7) {
            return $this->humanify($diff / 86400, 'day');
        } elseif ($absolute / 604800 <= 5) {
            return $this->humanify($diff / 604800, 'week');
        } elseif ($absolute / 2628000 < 12) {
            return $this->humanify($diff / 2628000, 'month');
        }

        return $this->humanify($diff / 31536000, 'year');
    }
    /**
     * Checks if the time passed is valid or not.
     *
     * @param mixed $date   the date to use
     * @param mixed $format the format to test
     *
     * @return object
     */
    protected function validDate($date, $format = '')
    {
        if ($format == 'N') {
            if ($date instanceof DateTime) {
                return $date->format('N') >= 0;
            } else {
                return $date >= 0 && $date <= 7;
            }
        }
        if (!$date instanceof DateTime) {
            $date = self::niceDate($date);
        }
        if (!$format) {
            $format = 'm/d/Y';
        }
        $tz = new DateTimeZone(self::$TimeZone);

        return DateTime::createFromFormat(
            $format,
            $date->format($format),
            $tz
        );
    }
    /**
     * Simply returns if the item should be with an s or not.
     *
     * @param int    $count The count of the element
     * @param string $text  The string to append to
     * @param bool   $space Use a space or not
     *
     * @throws Exception
     *
     * @return string
     */
    protected function pluralize($count, $text, $space = false)
    {
        if (!is_bool($space)) {
            throw new Exception(_('Space variable must be boolean'));
        }

        return sprintf(
            '%d %s%s%s',
            $count,
            $text,
            $count != 1 ? 's' : '',
            $space === true ? ' ' : ''
        );
    }
    /**
     * Returns the difference given from a start and end time.
     *
     * @param mixed $start the starting date
     * @param mixed $end   the ending date
     * @param bool  $ago   Return immediate highest down
     *
     * @throws Exception
     *
     * @return DateTime
     */
    protected function diff($start, $end, $ago = false)
    {
        if (!is_bool($ago)) {
            throw new Exception(_('Ago must be boolean'));
        }
        if (!$start instanceof DateTime) {
            $start = self::niceDate($start);
        }
        if (!$end instanceof DateTime) {
            $end = self::niceDate($end);
        }
        $Duration = $start->diff($end);
        $str = '';
        $suffix = '';
        if ($ago === true) {
            $str = '%s %s';
            if ($Duration->invert) {
                $suffix = 'ago';
            }
            if (($v = $Duration->y) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'year'),
                    $suffix
                );
            }
            if (($v = $Duration->m) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'month'),
                    $suffix
                );
            }
            if (($v = $Duration->d) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'day'),
                    $suffix
                );
            }
            if (($v = $Duration->h) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'hour'),
                    $suffix
                );
            }
            if (($v = $Duration->i) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'minute'),
                    $suffix
                );
            }
            if (($v = $Duration->s) > 0) {
                return sprintf(
                    $str,
                    $this->pluralize($v, 'second'),
                    $suffix
                );
            }
        }
        if (($v = $Duration->y) > 0) {
            $str .= $this->pluralize($v, 'year', true);
        }
        if (($v = $Duration->m) > 0) {
            $str .= $this->pluralize($v, 'month', true);
        }
        if (($v = $Duration->d) > 0) {
            $str .= $this->pluralize($v, 'day', true);
        }
        if (($v = $Duration->h) > 0) {
            $str .= $this->pluralize($v, 'hour', true);
        }
        if (($v = $Duration->i) > 0) {
            $str .= $this->pluralize($v, 'minute', true);
        }
        if (($v = $Duration->s) > 0) {
            $str .= $this->pluralize($v, 'second');
        }

        return $str;
    }
    /**
     * Return more human friendly time.
     *
     * @param int    $diff the difference passed
     * @param string $unit the unit of time (minute, hour, etc...)
     *
     * @throws Exception
     *
     * @return string
     */
    protected function humanify($diff, $unit)
    {
        if (!is_numeric($diff)) {
            throw new Exception(_('Diff parameter must be numeric'));
        }
        if (!is_string($unit)) {
            throw new Exception(_('Unit of time must be a string'));
        }
        $before = $after = '';
        if ($diff < 0) {
            $before = sprintf('%s ', _('In'));
        }
        if ($diff < 0) {
            $after = sprintf(' %s', _('ago'));
        }
        $diff = floor(abs($diff));
        if ($diff != 1) {
            $unit .= 's';
        }

        return sprintf(
            '%s%d %s%s',
            $before,
            $diff,
            $unit,
            $after
        );
    }
    /**
     * Returns size of item after checking via FTP.
     *
     * @param StorageNode $StorageNode the node to test
     * @param string      $file        the file to look for
     *
     * @throws Exception
     *
     * @return float
     */
    protected function getFTPByteSize(StorageNode $StorageNode, $file)
    {
        if (!$StorageNode->isValid()) {
            throw new Exception(_('StorageNode must be a valid node'));
        }
        if (!is_string($file)) {
            throw new Exception(_('File must be a string'));
        }
        self::$FOGFTP
            ->set('username', $StorageNode->get('user'))
            ->set('password', $StorageNode->get('pass'))
            ->set('host', $StorageNode->get('ip'));
        if (!self::$FOGFTP->connect()) {
            throw new Exception(_('Cannot connect to node.'));
        }
        $size = $this->formatByteSize((float) self::$FOGFTP->size($file));
        self::$FOGFTP->close();

        return $size;
    }
    /**
     * Filters an array recursively.
     *
     * @param array $input    the array to filter
     * @param bool  $keepkeys keep the keys
     *
     * @throws Exception
     *
     * @return array
     */
    protected function arrayFilterRecursive(array &$input, $keepkeys = false)
    {
        if (!is_bool($keepkeys)) {
            throw new Exception(_('Keepkeys must be boolean'));
        }
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->arrayFilterRecursive($value, $keepkeys);
            }
            unset($value);
        }
        $input = array_filter($input);

        return $keepkeys ? $input : array_values($input);
    }
    /**
     * Changes the keys around as needed.
     *
     * @param array  $array   the array to change key for
     * @param string $old_key the original key
     * @param string $new_key the key to change to
     *
     * @throws Exception
     * @return void
     */
    protected function arrayChangeKey(array &$array, $old_key, $new_key)
    {
        if (!is_string($old_key)) {
            throw new Exception(_('Old key must be a string'));
        }
        if (!is_string($new_key)) {
            throw new Exception(_('New key must be a string'));
        }
        $array[$old_key] = trim($array[$old_key]);
        if (!self::$service && is_string($array[$old_key])) {
            $array[$new_key] = htmlentities(
                $array[$old_key],
                ENT_QUOTES,
                'utf-8'
            );
        } else {
            $array[$new_key] = $array[$old_key];
        }
        if ($old_key != $new_key) {
            unset($array[$old_key]);
        }
    }
    /**
     * Converts to bits.
     *
     * @param int|float $kilobytes the bytes to convert
     *
     * @return float
     */
    protected function byteconvert($kilobytes)
    {
        return ($kilobytes / 8) * 1024;
    }
    /**
     * Converts hex to binary equivalent.
     *
     * @param mixed $hex The hex to convert.
     *
     * @return string
     */
    protected function hex2bin($hex)
    {
        if (function_exists('hex2bin')) {
            return hex2bin($hex);
        }
        $n = strlen($hex);
        $i = 0;
        $sbin = '';
        while ($i < $n) {
            $a = substr($hex, $i, 2);
            $sbin .= pack('H*', $a);
            $i += 2;
        }

        return $sbin;
    }
    /**
     * Create security token.
     *
     * @return string
     */
    protected function createSecToken()
    {
        /**
         * Lambda to create random data.
         *
         * @return string
         */
        $randGen = function () {
            $rand = mt_rand();
            $uniq = uniqid($rand, true);

            return md5($uniq);
        };
        $token = sprintf(
            '%s%s',
            $randGen(),
            $randGen()
        );
        $token = bin2hex($token);

        return trim($token);
    }
    /**
     * Encrypt the data passed.
     *
     * @param string $pass the item to encrypt
     *
     * @return string
     */
    protected function encryptpw($pass)
    {
        $pass = trim($pass);
        if (empty($pass)) {
            return '';
        }
        $decrypt = $this->aesdecrypt($pass);
        $newpass = $pass;
        if ($decrypt && mb_detect_encoding($decrypt, 'utf-8', true)) {
            $newpass = $decrypt;
        }

        return $newpass ? $this->aesencrypt($newpass) : '';
    }
    /**
     * AES Encrypt function.
     *
     * @param mixed  $data    the item to encrypt
     * @param string $key     the key to use if false will generate own
     * @param int    $enctype the type of encryption to use
     * @param int    $mode    the mode of encryption
     *
     * @return string
     */
    public function aesencrypt(
        $data,
        $key = false,
        $enctype = MCRYPT_RIJNDAEL_128,
        $mode = MCRYPT_MODE_CBC
    ) {
        $iv_size = mcrypt_get_iv_size($enctype, $mode);
        if (!$key) {
            $addKey = true;
            $key = openssl_random_pseudo_bytes($iv_size, $cstrong);
        } else {
            $key = $this->hex2bin($key);
        }
        $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
        $cipher = mcrypt_encrypt($enctype, $key, $data, $mode, $iv);
        $iv = bin2hex($iv);
        $cipher = bin2hex($cipher);
        $key = bin2hex($key);

        return sprintf(
            '%s|%s%s',
            $iv,
            $cipher,
            $addKey ? sprintf('|%s', $key) : ''
        );
    }
    /**
     * AES Decrypt function.
     *
     * @param mixed  $encdata the item to decrypt
     * @param string $key     the key to use
     * @param int    $enctype the type of encryption to use
     * @param int    $mode    the mode of encryption
     *
     * @return string
     */
    public function aesdecrypt(
        $encdata,
        $key = false,
        $enctype = MCRYPT_RIJNDAEL_128,
        $mode = MCRYPT_MODE_CBC
    ) {
        $iv_size = mcrypt_get_iv_size($enctype, $mode);
        if (false === strpos($encdata, '|')) {
            return $encdata;
        }
        $data = explode('|', $encdata);
        $iv = pack('H*', $data[0]);
        $encoded = pack('H*', $data[1]);
        if (!$key && $data[2]) {
            $key = pack('H*', $data[2]);
        }
        if (empty($key)) {
            return '';
        }
        $decipher = mcrypt_decrypt($enctype, $key, $encoded, $mode, $iv);

        return trim($decipher);
    }
    /**
     * Encrypts the data using the host information.
     * Really just an alias to aesencrypt for now.
     *
     * @param mixed $data the data to encrypt
     * @param Host  $Host the host item to use
     *
     * @throws Exception
     *
     * @return string
     */
    protected function certEncrypt($data, $Host)
    {
        if (!$Host || !$Host->isValid()) {
            throw new Exception('#!ih');
        }
        if (!$Host->get('pub_key')) {
            throw new Exception('#!ihc');
        }

        return $this->aesencrypt($data, $Host->get('pub_key'));
    }
    /**
     * Decrypts the information passed.
     *
     * @param mixed $dataArr the data to decrypt
     * @param bool  $padding to use padding or not
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function certDecrypt($dataArr, $padding = true)
    {
        if ($padding) {
            $padding = OPENSSL_PKCS1_PADDING;
        } else {
            $padding = OPENSSL_NO_PADDING;
        }
        $tmpssl = array();
        $sslfile = self::getSubObjectIDs('StorageNode', '', 'sslpath');
        foreach ($sslfile as &$path) {
            if (!file_exists($path) || !is_readable($path)) {
                continue;
            }
            $tmpssl[] = $path;
            unset($path);
        }
        if (count($tmpssl) < 1) {
            throw new Exception(_('Private key path not found'));
        }
        $sslfile = sprintf(
            '%s%s.srvprivate.key',
            preg_replace(
                '#[\\/]#',
                DIRECTORY_SEPARATOR,
                $tmpssl[0]
            ),
            DIRECTORY_SEPARATOR
        );
        unset($tmpssl);
        if (!file_exists($sslfile)) {
            throw new Exception(_('Private key not found'));
        }
        if (!is_readable($sslfile)) {
            throw new Exception(_('Private key not readable'));
        }
        $sslfilecontents = file_get_contents($sslfile);
        $priv_key = openssl_pkey_get_private($sslfilecontents);
        if (!$priv_key) {
            throw new Exception(_('Private key failed'));
        }
        $a_key = openssl_pkey_get_details($priv_key);
        $chunkSize = ceil($a_key['bits'] / 8);
        $output = array();
        foreach ((array) $dataArr as &$data) {
            $dataun = '';
            while ($data) {
                $data = $this->hex2bin($data);
                $chunk = substr($data, 0, $chunkSize);
                $data = substr($data, $chunkSize);
                $decrypt = '';
                $test = openssl_private_decrypt(
                    $chunk,
                    $decrypt,
                    $priv_key,
                    $padding
                );
                if (!$test) {
                    throw new Exception(_('Failed to decrypt data'));
                }
                $dataun .= $decrypt;
            }
            unset($data);
            $output[] = $dataun;
        }
        openssl_free_key($priv_key);

        return (array) $output;
    }
    /**
     * Cycle the macs and return valid.
     *
     * @param string|array $stringlist the macs to parse
     * @param bool         $image      check if image type ignored
     * @param bool         $client     check if client type ignored
     *
     * @return array
     */
    public function parseMacList(
        $stringlist,
        $image = false,
        $client = false
    ) {
        $MAClist = array();
        $MACs = $stringlist;
        $lowerAndTrim = function ($element) {
            return strtolower(trim($element));
        };
        if (!is_array($stringlist)) {
            $MACs = array_map($lowerAndTrim, explode('|', $stringlist));
        } else {
            $MACs = array_map($lowerAndTrim, $stringlist);
        }
        $MACs = array_filter($MACs);
        $MACs = array_unique($MACs);
        $MACs = array_values($MACs);
        if (count($MACs) < 1) {
            return array();
        }
        $pending_filter = explode(
            ',',
            self::getSetting('FOG_QUICKREG_PENDING_MAC_FILTER')
        );
        $Ignore = array_map($lowerAndTrim, $pending_filter);
        $Ignore = array_filter($Ignore);
        if (count($Ignore) > 0) {
            $pattern = sprintf(
                '#%s#i',
                implode('|', (array) $Ignore)
            );
            $found_macs = preg_grep($pattern, $MACs);
            $MACs = array_diff($MACs, $found_macs);
            $MACs = array_filter($MACs);
            $MACs = array_unique($MACs);
            $MACs = array_values($MACs);
        }
        if (count($MACs) < 1) {
            return array();
        }
        $count = self::getClass('MACAddressAssociationManager')->count(
            array(
                'mac' => $MACs,
                'pending' => array(0, ''),
            )
        );
        if ($count > 0) {
            $existingMACs = self::getSubObjectIDs(
                'MACAddressAssociation',
                array(
                    'mac' => $MACs,
                    'pending' => array(0, ''),
                ),
                'mac'
            );
            $existingMACs = array_map($lowerAndTrim, $existingMACs);
            $existingMACs = array_filter($existingMACs);
            $existingMACs = array_unique($existingMACs);
            $existingMACs = array_values($existingMACs);
            $MACs = array_merge((array) $MACs, (array) $existingMACs);
            $MACs = array_unique($MACs);
        }
        if ($client) {
            $clientIgnored = self::getSubObjectIDs(
                'MACAddressAssociation',
                array(
                    'mac' => $MACs,
                    'clientIgnore' => 1,
                ),
                'mac'
            );
            $clientIgnored = array_map($lowerAndTrim, $clientIgnored);
            $MACs = array_diff((array) $MACs, (array) $clientIgnored);
            unset($clientIgnored);
        }
        if ($image) {
            $imageIgnored = self::getSubObjectIDs(
                'MACAddressAssociation',
                array(
                    'mac' => $MACs,
                    'imageIgnore' => 1,
                ),
                'mac'
            );
            $imageIgnored = array_map($lowerAndTrim, (array) $imageIgnored);
            $MACs = array_diff((array) $MACs, (array) $imageIgnored);
            unset($imageIgnored);
        }
        $MACs = array_filter($MACs);
        $MACs = array_unique($MACs);
        $MACs = array_values($MACs);
        if (count($MACs) < 1) {
            return array();
        }
        $validMACs = array();
        foreach ($MACs as &$MAC) {
            $MAC = self::getClass('MACAddress', $MAC);
            if (!$MAC->isValid()) {
                continue;
            }
            $validMACs[] = $MAC;
            unset($MAC);
        }
        $validMACs = array_filter($validMACs);

        return $validMACs;
    }
    /**
     * Prints the data encrypted as needed.
     *
     * @param string $datatosend the data to send
     * @param bool   $service    if not a service simpy return
     * @param array  $array      The non-encoded array data.
     *
     * @return string
     */
    protected function sendData(
        $datatosend,
        $service = true,
        $array = array()
    ) {
        global $sub;
        if (false === $service) {
            return;
        }
        try {
            $datatosend = trim($datatosend);
            $curdate = self::niceDate();
            $secdate = self::niceDate($this->Host->get('sec_time'));
            if ($curdate >= $secdate) {
                $this->Host->set('pub_key', '')->save();
            }
            if (self::$newService) {
                printf(
                    '#!enkey=%s',
                    $this->certEncrypt($datatosend, $this->Host)
                );
                exit;
            } else {
                echo $datatosend;
                exit;
            }
        } catch (Exception $e) {
            if (self::$json) {
                if ($e->getMessage() === '#!ihc') {
                    echo $e->getMessage();
                    exit;
                }
                $repData = preg_replace('/^[#][!]?/', '', $e->getMessage());
                $array['error'] = $repData;
                $data = array('error' => $repData);
                if ($sub === 'requestClientInfo') {
                    echo json_encode($array);
                    exit;
                } else {
                    return $data;
                }
            }
            throw new Exception($e->getMessage());
        }
    }
    /**
     * Checks if an array of needles is found in the main array.
     *
     * @param array $haystack the array to search
     * @param array $needles  the items to test for
     * @param bool  $case     whether to be case insensitive
     *
     * @return bool
     */
    protected function arrayStrpos($haystack, $needles, $case = true)
    {
        $cmd = sprintf('str%spos', ($case ? 'i' : ''));
        $mapinfo = array();
        foreach ((array) $needles as &$needle) {
            $mapinfo[] = $cmd($haystack, $needle);
            unset($needle);
        }
        $mapinfo = array_filter($mapinfo);

        return count($mapinfo) > 0;
    }
    /**
     * Log the data.
     *
     * @param string $txt   the text to log
     * @param int    $level the level of the logging
     *
     * @throws Exception
     * @return void
     */
    protected function log($txt, $level = 1)
    {
        if (!is_string($txt)) {
            throw new Exception(_('Txt must be a string'));
        }
        if (!is_int($level)) {
            throw new Exception(_('Level must be an integer'));
        }
        if (self::$ajax) {
            return;
        }
        $findStr = array("#\r#", "#\n#", '#\s+#', '# ,#');
        $repStr = array('', ' ', ' ', ',');
        $txt = preg_replace($findStr, $repStr, $txt);
        $txt = trim($txt);
        if (empty($txt)) {
            return;
        }
        $txt = sprintf('[%s] %s', self::niceDate()->format('Y-m-d H:i:s'), $txt);
        if ($this->logLevel >= $level) {
            echo $txt;
        }
        $this->logHistory($txt);
    }
    /**
     * Log to history table.
     *
     * @param string $string the string to store
     *
     * @return void
     */
    protected function logHistory($string)
    {
        if (!is_string($string)) {
            throw new Exception(_('String must be a string'));
        }
        if (!(self::$FOGUser instanceof User && self::$FOGUser->isValid())) {
            return;
        }
        $string = sprintf(
            '[%s] %s',
            self::niceDate()->format('Y-m-d H:i:s'),
            $string
        );
        $string = trim($string);
        if (!$string) {
            return;
        }
        $name = $_SESSION['FOG_USERNAME'] ? $_SESSION['FOG_USERNAME'] : 'fog';
        if ($_SESSION['FOG_USER'] < 1) {
            return;
        }
        if (self::$DB) {
            self::getClass('History')
                ->set('info', $string)
                ->set('ip', $_SERVER['REMOTE_ADDR'])
                ->save();
        }
    }
    /**
     * Sets the order by element of sql.
     *
     * @param string $orderBy the string to order by
     *
     * @return void
     */
    public function orderBy(&$orderBy)
    {
        if (empty($orderBy)) {
            $orderBy = 'name';
            if (!array_key_exists($orderBy, $this->databaseFields)) {
                $orderBy = 'id';
            }
        } else {
            if (!is_array($orderBy)) {
                $orderBy = trim($orderBy);
                if (!array_key_exists($orderBy, $this->databaseFields)) {
                    $orderBy = 'name';
                }
                if (!array_key_exists($orderBy, $this->databaseFields)) {
                    $orderBy = 'id';
                }
            }
        }
    }
    /**
     * Gets the object ids only.
     *
     * @param string $object    The object to use
     * @param array  $findWhere How to find the elements we need
     * @param string $getField  The field value to return
     * @param mixed  $not       DB to search with not or no not
     * @param string $operator  How to join strings (And or Or)
     * @param mixed  $orderBy   Order the return by
     * @param mixed  $groupBy   Group the return by
     * @param string $filter    How to filter the data returning
     *
     * @return array
     */
    public static function getSubObjectIDs(
        $object = 'Host',
        $findWhere = array(),
        $getField = 'id',
        $not = false,
        $operator = 'AND',
        $orderBy = 'name',
        $groupBy = false,
        $filter = 'array_unique'
    ) {
        if (empty($object)) {
            $object = 'Host';
        }
        if (empty($getField)) {
            $getField = 'id';
        }
        if (empty($operator)) {
            $operator = 'AND';
        }

        return self::getClass($object)->getManager()->find(
            $findWhere,
            $operator,
            $orderBy,
            '',
            '',
            $groupBy,
            $not,
            $getField,
            '',
            $filter
        );
    }
    /**
     * Get global setting value by key.
     *
     * @param string $key What to get
     *
     * @throws Exception
     *
     * @return string
     */
    public static function getSetting($key)
    {
        if (!is_string($key)) {
            throw new Exception(_('Key must be a string'));
        }
        $findStr = '\r\n';
        $repStr = "\n";
        $value = self::getClass('Service')
            ->set('name', $key)
            ->load('name')
            ->get('value');

        return trim(str_replace($findStr, $repStr, $value));
    }
    /**
     * Set global setting value by key.
     *
     * @param string $key   What to set
     * @param string $value Value to set
     *
     * @throws Exception
     *
     * @return this
     */
    public function setSetting($key, $value)
    {
        self::getClass('ServiceManager')->update(
            array('name' => $key),
            '',
            array('value' => trim($value))
        );

        return $this;
    }
    /**
     * Gets queued state ids.
     *
     * @return array
     */
    public function getQueuedStates()
    {
        return (array) self::getClass('TaskState')->getQueuedStates();
    }
    /**
     * Get queued state main id.
     *
     * @return int
     */
    public function getQueuedState()
    {
        return self::getClass('TaskState')->getQueuedState();
    }
    /**
     * Get checked in state id.
     *
     * @return int
     */
    public function getCheckedInState()
    {
        return self::getClass('TaskState')->getCheckedInState();
    }
    /**
     * Get in progress state id.
     *
     * @return int
     */
    public function getProgressState()
    {
        return self::getClass('TaskState')->getProgressState();
    }
    /**
     * Get complete state id.
     *
     * @return int
     */
    public function getCompleteState()
    {
        return self::getClass('TaskState')->getCompleteState();
    }
    /**
     * Get cancelled state id.
     *
     * @return int
     */
    public function getCancelledState()
    {
        return self::getClass('TaskState')->getCancelledState();
    }
    /**
     * Put string between two strings.
     *
     * @param string $string the string to insert
     * @param string $start  the string to place after
     * @param string $end    the string to place before
     *
     * @return string
     */
    public function stringBetween($string, $start, $end)
    {
        $string = " $string";
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
    /**
     * Strips and decodes items.
     *
     * @param mixed $item the item to strip and decode
     *
     * @return mixed
     */
    public static function stripAndDecode(&$item)
    {
        foreach ((array) $item as $key => &$val) {
            $tmp = preg_replace('# #', '+', $val);
            $tmp = base64_decode($tmp);
            $tmp = trim($tmp);
            if (mb_detect_encoding($tmp, 'utf-8', true)) {
                $val = $tmp;
            }
            unset($tmp);
            $item[$key] = trim($val);
            unset($val);
        }

        return $item;
    }
    /**
     * Gets the master interface based on the ip found.
     *
     * @param string $ip_find the interface ip's to find
     *
     * @return string
     */
    public static function getMasterInterface($ip_find)
    {
        if (count(self::$interface) > 0) {
            return self::$interface;
        }
        self::getIPAddress();
        exec(
            "/sbin/ip route | grep '$ip_find' | awk -F'[ /]+' '/src/ {print $4}'",
            $Interfaces,
            $retVal
        );
        $ip_find = trim($ip_find);
        if (!$ip_find) {
            return;
        }
        self::$interface = array();
        $index = 0;
        foreach ((array) self::$ips as &$ip) {
            $ip = trim($ip);
            if ($ip_find !== $ip) {
                continue;
            }
            self::$interface[] = $Interfaces[$index++];
            unset($ip);
        }
        if (count(self::$interface) < 1) {
            return false;
        }

        return array_shift(self::$interface);
    }
    /**
     * Get IP Addresses of the server.
     *
     * @return array
     */
    protected static function getIPAddress()
    {
        if (count(self::$ips) > 0) {
            return self::$ips;
        }
        $output = array();
        exec(
            "/sbin/ip -4 addr | awk -F'[ /]+' '/global/ {print $3}'",
            $IPs,
            $retVal
        );
        if (!count($IPs)) {
            exec(
                "/sbin/ifconfig -a | awk -F'[ /:]+' '/(cast)/ {print $4}'",
                $IPs,
                $retVal
            );
        }
        $test = self::$FOGURLRequests->isAvailable('http://ipinfo.io/ip');
        $test = array_shift($test);
        if (false !== $test) {
            $res = self::$FOGURLRequests->process('http://ipinfo.io/ip');
            $IPs[] = $res[0];
        }
        natcasesort($IPs);
        $retIPs = function (&$IP) {
            $IP = trim($IP);
            if (!filter_var($IP, FILTER_VALIDATE_IP)) {
                $IP = gethostbyname($IP);
            }
            if (filter_var($IP, FILTER_VALIDATE_IP)) {
                return $IP;
            }
        };
        $retNames = function (&$IP) {
            $IP = trim($IP);
            if (filter_var($IP, FILTER_VALIDATE_IP)) {
                return gethostbyaddr($IP);
            }

            return $IP;
        };
        $IPs = array_map($retIPs, (array) $IPs);
        $Names = array_map($retNames, (array) $IPs);
        $output = array_merge($IPs, $Names);
        unset($IPs, $Names);
        natcasesort($output);
        self::$ips = array_values(array_filter(array_unique((array) $output)));

        return self::$ips;
    }
    /**
     * Returns the last error.
     *
     * @return string
     */
    public static function lasterror()
    {
        $error = error_get_last();

        return sprintf(
            '%s: %s, %s: %s, %s: %s, %s: %s',
            _('Type'),
            $error['type'],
            _('File'),
            $error['file'],
            _('Line'),
            $error['line'],
            _('Message'),
            $error['message']
        );
    }
    /**
     * Gets the filesize in a non-arch dependent way.
     *
     * @param string $file the file to get size of
     *
     * @return string|int|float
     */
    public static function getFilesize($file)
    {
        $file = escapeshellarg($file);

        return shell_exec("ls -l $file | awk '{print $5}'");
    }
    /**
     * Perform enmass wake on lan.
     *
     * @param array $macs The macs to send
     *
     * @return void
     */
    public function wakeUp($macs)
    {
        if (!is_array($macs)) {
            $macs = array($macs);
        }
        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);
        $macs = $this->parseMacList($macs);
        if (count($macs) < 1) {
            return;
        }
        $macStr = implode(
            '|',
            $macs
        );
        $macStr = trim($macStr);
        if (empty($macStr)) {
            return;
        }
        $url = 'http://%s%smanagement/index.php?';
        $url .= 'node=client&sub=wakeEmUp';
        $nodeURLs = array();
        $macCount = count($macs);
        if ($macCount < 1) {
            return;
        }
        $Nodes = self::getClass('StorageNodeManager')
            ->find(
                array(
                    'isEnabled' => 1,
                )
            );
        foreach ((array) $Nodes as &$Node) {
            if (!$Node->isValid()) {
                continue;
            }
            $curroot = trim($Node->get('webroot'), '/');
            $curroot = trim($curroot);
            $webroot = sprintf(
                '/%s',
                (
                    strlen($curroot) > 1 ?
                    sprintf(
                        '%s/',
                        $curroot
                    ) :
                    ''
                )
            );
            $ip = $Node->get('ip');
            $testurls[] = sprintf(
                'http://%s%smanagement/index.php',
                $ip,
                $webroot
            );
            $nodeURLs[] = sprintf(
                $url,
                $ip,
                $webroot
            );
            unset($Node);
        }
        list(
            $gHost,
            $gRoot
        ) = self::getSubObjectIDs(
            'Service',
            array(
                'name' => array(
                    'FOG_WEB_HOST',
                    'FOG_WEB_ROOT',
                ),
            ),
            'value',
            false,
            'AND',
            'name',
            false,
            ''
        );
        $curroot = $gRoot;
        $curroot = trim($curroot, '/');
        $curroot = trim($curroot);
        $webroot = sprintf(
            '/%s',
            (
                strlen($curroot) > 1 ?
                sprintf(
                    '%s/',
                    $curroot
                ) :
                ''
            )
        );
        $ip = $gHost;
        $testurls[] = sprintf(
            'http://%s%smanagement/index.php',
            $ip,
            $webroot
        );
        $test = array_filter(self::$FOGURLRequests->isAvailable($testurls));
        $nodeURLs = array_intersect_key($nodeURLs, $test);
        if (count($nodeURLs) < 1) {
            return;
        }
        self::$FOGURLRequests->process(
            $nodeURLs,
            'POST',
            array('mac' => $macStr),
            false,
            false,
            false,
            false
        );
    }
}
