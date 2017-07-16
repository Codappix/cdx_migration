<?php
namespace Codappix\CdxMigration\Tests\Migration\Model;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\CdxMigration\Tests\Fixtures\Migration\Model\NewRecordType;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ToTest extends TestCase
{
    /**
     * @test
     */
    public function modelIsInitializedThroughInjectedConfiguration()
    {
        $configuration = $this->getMockBuilder(ConfigurationManager::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
        $configuration->expects($this->once())
            ->method('getConfiguration')
            ->with(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'CdxMigration',
                'Migration'
            )
            ->will($this->returnValue([
                'model' => [
                    'RecordType' => [
                        'values' => [
                            'pid' => 10,
                            'hidden' => 1,
                        ],
                    ],
                ],
            ]));

        $subject = $this->getMockBuilder(NewRecordType::class)
            ->disableOriginalConstructor()
            ->setMethods(['applyTcaDefaults', 'getIdentifier'])
            ->getMockForAbstractClass();
        // We have to mock this, no way to test due to https://github.com/sebastianbergmann/phpunit-mock-objects/issues/295
        $subject->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('RecordType'));

        $subject->injectConfiguration($configuration);
        $this->assertSame(
            [10, 1],
            [$subject->get_pid(), $subject->get_hidden()],
            'Configured properties were not set.'
        );
    }

    /**
     * @test
     */
    public function modelIsInitializedThroughTca()
    {
        $configuration = $this->getMockBuilder(ConfigurationManager::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
        $configuration->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue([]));

        $subject = $this->getMockBuilder(NewRecordType::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTcaForTable', 'getBeUserUid', 'getIdentifier'])
            ->getMockForAbstractClass();
        $subject->expects($this->once())
            ->method('getBeUserUid')
            ->will($this->returnValue(2));
        // We have to mock this, no way to test due to https://github.com/sebastianbergmann/phpunit-mock-objects/issues/295
        $subject->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('RecordType'));
        $subject->expects($this->once())
            ->method('getTcaForTable')
            ->with('sys_categories')
            ->will($this->returnValue([
                'ctrl' => [
                    'tstamp' => 'tstampField',
                    'crdate' => 'crdateField',
                    'cruser_id' => 'cruserIdField',
                ],
            ]));

        $subject->injectConfiguration($configuration);
        $this->assertSame(
            [\time(), \time(), 2],
            [$subject->get_tstampField(), $subject->get_crdateField(), $subject->get_cruserIdField()],
            'TCA defaults are not set.'
        );
    }
}
