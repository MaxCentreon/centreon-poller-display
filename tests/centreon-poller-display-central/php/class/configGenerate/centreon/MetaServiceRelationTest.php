<?php
/**
 * Copyright 2016 Centreon
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use \Centreon\Test\Mock\CentreonDB;
use \CentreonPollerDisplayCentral\ConfigGenerate\Centreon\MetaServiceRelation;


/**
 * @package centreon-poller-display-central
 * @version 1.0.0
 * @author Centreon
 */
class CentreonPollerDisplayCentral_MetaServiceRelation extends PHPUnit_Framework_TestCase
{
    protected static $db;
    protected static $pollerDisplay;
    protected static $meta;
    protected static $objectListIn;
    protected static $objectListOut;

    public function setUp()
    {
        self::$db = new CentreonDB();
        self::$pollerDisplay = 1;
        self::$meta = new MetaServiceRelation(self::$db, self::$pollerDisplay);
        self::$objectListIn = array(
            array(
                'nagios_server_id' => '1',
                'host_host_id' => '1'
            ),
            array(
                'nagios_server_id' => '1',
                'host_host_id' => '2'
            )
        );
        self::$objectListOut = array(
            array(
                'msr_id' => '1',
                'meta_id' => '1',
                'host_id' => '1'
            ),
            array(
                'msr_id' => '2',
                'meta_id' => '5',
                'host_id' => '2'
            )
        );
    }

    public function tearDown()
    {
        self::$db = null;
    }

    public function testGetList()
    {
        self::$db->addResultSet(
            'SELECT * FROM meta_service_relation WHERE host_id IN (1,2)',
            array(
                array(
                    'msr_id' => '1',
                    'meta_id' => '1',
                    'host_id' => '1'
                ),
                array(
                    'msr_id' => '2',
                    'meta_id' => '5',
                    'host_id' => '2'
                )
            )
        );

        $sql = self::$meta->getList(self::$objectListIn);
        $this->assertEquals($sql, self::$objectListOut);
    }

    public function testGenerateSql()
    {

        $expectedResult = 'DELETE FROM meta_service_relation;
TRUNCATE meta_service_relation;
INSERT INTO `meta_service_relation` (`msr_id`,`meta_id`,`host_id`) VALUES (\'1\',\'1\',\'1\'),(\'2\',\'5\',\'2\');';

        $sql = self::$meta->generateSql(self::$objectListOut);
        $this->assertEquals($sql, $expectedResult);
    }
}
