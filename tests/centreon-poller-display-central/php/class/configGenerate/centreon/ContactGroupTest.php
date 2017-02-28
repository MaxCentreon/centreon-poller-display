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
use \CentreonPollerDisplayCentral\ConfigGenerate\Centreon\Contactgroup;


/**
 * @package centreon-poller-display-central
 * @version 1.0.0
 * @author Centreon
 */
class CentreonPollerDisplayCentral_Contactgroup extends PHPUnit_Framework_TestCase
{
    protected static $db;
    protected static $pollerDisplay;
    protected static $contact;

    public function setUp()
    {
        self::$db = new CentreonDB();
        self::$pollerDisplay = 1;
        self::$contact = new Contactgroup(self::$db, self::$pollerDisplay);
    }

    public function tearDown()
    {
        self::$db = null;
    }

    public function testGenerateSql()
    {

        $expectedResult = 'TRUNCATE contactgroup;
INSERT INTO `contactgroup` (`cg_id`,`cg_name`) VALUES (\'3\',\'group1\'),(\'5\',\'group2\');';

        self::$db->addResultSet(
            'SELECT * FROM ns_host_relation WHERE nagios_server_id = 1',
            array(
                array(
                    'nagios_server_id' => '1',
                    'host_host_id' => '1'
                ),
                array(
                    'nagios_server_id' => '1',
                    'host_host_id' => '2'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM contact_host_relation WHERE host_host_id IN (1,2)',
            array(
                array(
                    'host_host_id' => '1',
                    'contact_id' => '2'
                ),
                array(
                    'host_host_id' => '2',
                    'contact_id' => '6'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM hostgroup_relation WHERE host_host_id IN (1,2)',
            array(
                array(
                    'hgr_id' => '1',
                    'hostgroup_hg_id' => '10',
                    'host_host_id' => '1'
                ),
                array(
                    'hgr_id' => '2',
                    'hostgroup_hg_id' => '20',
                    'host_host_id' => '2'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM host_service_relation WHERE (host_host_id IN (1,2)) OR (hostgroup_hg_id IN (10,20))',
            array(
                array(
                    'hsr_id' => '1',
                    'hostgroup_hg_id' => null,
                    'host_host_id' => '1',
                    'servicegroup_sg_id' => null,
                    'service_service_id' => '1'
                ),
                array(
                    'hsr_id' => '2',
                    'hostgroup_hg_id' => 20,
                    'host_host_id' => null,
                    'servicegroup_sg_id' => null,
                    'service_service_id' => '5'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM contact_service_relation WHERE service_service_id IN (1,5)',
            array(
                array(
                    'csr_id' => '1',
                    'service_service_id' => '10',
                    'contact_id' => '1'
                ),
                array(
                    'csr_id' => '2',
                    'service_service_id' => '20',
                    'contact_id' => '2'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM contact WHERE contact_id IN (2,6,1)',
            array(
                array(
                    'contact_id' => '1',
                    'contact_name' => 'toto'
                ),
                array(
                    'contact_id' => '6',
                    'contact_name' => 'tata'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM contactgroup_contact_relation WHERE contact_contact_id IN (1,6)',
            array(
                array(
                    'contact_contact_id' => '1',
                    'contactgroup_cg_id' => '5'
                ),
                array(
                    'contact_contact_id' => '6',
                    'contactgroup_cg_id' => '3'
                )
            )
        );

        self::$db->addResultSet(
            'SELECT * FROM contactgroup WHERE cg_id IN (5,3)',
            array(
                array(
                    'cg_id' => '3',
                    'cg_name' => 'group1'
                ),
                array(
                    'cg_id' => '5',
                    'cg_name' => 'group2'
                )
            )
        );

        $sql = self::$contact->generateSql();
        $this->assertEquals($sql, $expectedResult);
    }
}