<?php
/**
 * Hashing service for snapins
 *
 * PHP version 5
 *
 * @category SnapinHash
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Hashing service for snapins
 *
 * @category SnapinHash
 * @package  FOGProject
 * @author   Tom Elliott <tommygunsster@gmail.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class SnapinHash extends FOGService
{
    /**
     * Where to get the services sleeptime
     *
     * @var string
     */
    public static $sleeptime = 'SNAPINHASHSLEEPTIME';
    /**
     * Initializes the SnapinReplicator Class
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        list(
            $dev,
            $log,
            $zzz
        ) = self::getSubObjectIDs(
            'Service',
            array(
                'name' => array(
                    'SNAPINHASHDEVICEOUTPUT',
                    'SNAPINHASHLOGFILENAME',
                    self::$sleeptime
                )
            ),
            'value',
            false,
            'AND',
            'name',
            false,
            ''
        );
        static::$log = sprintf(
            '%s%s',
            (
                self::$logpath ?
                self::$logpath :
                '/opt/fog/log/'
            ),
            (
                $log ?
                $log :
                'fogsnapinhash.log'
            )
        );
        if (file_exists(static::$log)) {
            unlink(static::$log);
        }
        static::$dev = (
            $dev ?
            $dev :
            '/dev/tty6'
        );
        static::$zzz = (
            $zzz ?
            $zzz :
            1800
        );
    }
    /**
     * This is what almost all services have available
     * but is specific to this service
     *
     * @return void
     */
    private function _commonOutput()
    {
        try {
            $StorageNodes = $this->checkIfNodeMaster();
            foreach ((array)$StorageNodes as &$StorageNode) {
                $myStorageGroupID = $StorageNode->get('storagegroupID');
                $myStorageNodeID = $StorageNode->get('id');
                $StorageGroup = $StorageNode->getStorageGroup();
                self::outall(
                    sprintf(
                        ' * %s.',
                        _('Starting Snapin Hashing Service')
                    )
                );
                self::outall(
                    sprintf(
                        ' * %s: %d. %s: %s',
                        _('We are group ID'),
                        $StorageGroup->get('id'),
                        _('We are group name'),
                        $StorageGroup->get('name')
                    )
                );
                self::outall(
                    sprintf(
                        ' * %s: %d. %s: %s',
                        _('We are node ID'),
                        $StorageNode->get('id'),
                        _('We are node name'),
                        $StorageNode->get('name')
                    )
                );
                self::outall(
                    sprintf(
                        ' * %s %s %s',
                        _('Finding any snapins associated'),
                        _('with this group'),
                        _('as its primary group')
                    )
                );
                $snapinIDs = self::getSubObjectIDs(
                    'SnapinGroupAssociation',
                    array(
                        'primary' => 1,
                        'storagegroupID' => $myStorageGroupID
                    ),
                    'snapinID'
                );
                $SnapinCount = self::getClass('SnapinManager')->count(
                    array(
                        'id' => $snapinIDs,
                        'isEnabled' => 1
                    )
                );
                if ($SnapinCount < 1) {
                    self::outall(
                        sprintf(
                            ' * %s.',
                            _('No snapins associated with this group as master')
                        )
                    );
                    continue;
                }
                self::outall(
                    sprintf(
                        ' * %s %d %s %s.',
                        _('Found'),
                        $SnapinCount,
                        (
                            $SnapinCount != 1 ?
                            _('snapins') :
                            _('snapin')
                        ),
                        _('to update hash values as needed')
                    )
                );
                $Snapins = self::getClass('SnapinManager')->find(
                    array(
                        'id' => $snapinIDs,
                        'isEnabled' => 1
                    )
                );
                foreach ((array)$Snapins as &$Snapin) {
                    if (strlen($Snapin->get('hash')) > 10) {
                        self::outall(
                            sprintf(
                                ' | %s',
                                _('Snapin hash already set')
                            )
                        );
                        continue;
                    }
                    $path = sprintf(
                        '/%s',
                        trim($StorageNode->get('snapinpath'), '/')
                    );
                    $file = basename($Snapin->get('file'));
                    $filepath = sprintf(
                        '%s/%s',
                        $path,
                        $file
                    );
                    self::outall(
                        sprintf(
                            ' * %s: %s.',
                            _('Getting snapin hash and size for'),
                            $Snapin->get('name')
                        )
                    );
                    $hash = hash_file('sha512', $filepath);
                    $size = self::getFilesize($filepath);
                    unset($path, $file);
                    self::outall(
                        sprintf(
                            ' | %s: %s',
                            _('Hash'),
                            $hash
                        )
                    );
                    $Snapin
                        ->set('hash', $hash)
                        ->set('size', $size)
                        ->save();
                    unset($url, $response, $hash, $size);
                    unset($Snapin);
                }
                unset($StorageNode);
            }
            self::outall(
                sprintf(
                    ' * %s.',
                    _('Completed')
                )
            );
            unset($StorageNodes);
        } catch (Exception $e) {
            self::outall(
                sprintf(
                    ' * %s',
                    _($e->getMessage())
                )
            );
        }
    }
    /**
     * This is runs the service
     *
     * @return void
     */
    public function serviceRun()
    {
        self::out(
            ' ',
            static::$dev
        );
        $str = str_pad('+', 75, '-');
        self::out($str, static::$dev);
        $this->_commonOutput();
        self::out($str, static::$dev);
        parent::serviceRun();
    }
}
