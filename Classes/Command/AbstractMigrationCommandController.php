<?php
namespace Codappix\CdxMigration\Command;

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Provides basic implementation to ease migrations using ActiveRecord.
 */
abstract class AbstractMigrationCommandController extends CommandController
{
    /**
     * @var array
     */
    protected $t3Connection = [];

    /**
     * @var array
     */
    protected $oldConnection = [];

    public function injectConnectionPool(ConnectionPool $connectionPool) : AbstractMigrationCommandController
    {
        // TODO: Adjust how to fetch connection, use default?!
        $connection = $connectionPool->getConnectionForTable('sys_category');
        $this->t3Connection = [
            'username' => $connection->getUsername(),
            'password' => $connection->getPassword(),
            'host' => $connection->getHost(),
            'db' => $connection->getDatabase(),
        ];

        return $this;
    }

    public function injectOldDb(ConfigurationManagerInterface $configurationManager) : AbstractMigrationCommandController
    {
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'CdxMigration',
            'Migration'
        );

        if ($settings && isset($settings['dbConnection']['old'])) {
            $this->oldConnection = $settings['dbConnection']['old'];
        }

        return $this;
    }

    /**
     * Initialize ActiveRecord database connections.
     */
    protected function initDbConnection()
    {
        if ($this->oldConnection === []) {
            $this->oldConnection = [
                'username' => $this->output->ask('Old db username:'),
                'password' => $this->output->askHiddenResponse('Old db password:'),
                'host' => $this->output->ask('Old db host:'),
                'db' => $this->output->ask('Old db name:'),
            ];
        }

        $extensionPath = ExtensionManagementUtility::extPath(
            GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName())
        );

        \ActiveRecord\Config::initialize(function ($configuration) use ($extensionPath) {
            $configuration->set_model_directory($extensionPath);
            $configuration->set_connections([
                'from' => sprintf(
                    'mysql://%s:%s@%s/%s',
                    $this->oldConnection['username'],
                    $this->oldConnection['password'],
                    $this->oldConnection['host'],
                    $this->oldConnection['db']
                ),
                'typo3' => sprintf(
                    'mysql://%s:%s@%s/%s',
                    $this->t3Connection['username'],
                    $this->t3Connection['password'],
                    $this->t3Connection['host'],
                    $this->t3Connection['db']
                ),
            ]);
        });
    }
}
