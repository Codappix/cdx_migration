<?php
namespace Codappix\CdxMigration\Migration\Model;

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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

abstract class To extends \ActiveRecord\Model
{
    public static $connection = 'typo3';
    public static $primary_key = 'uid';

    public function injectConfiguration(ConfigurationManagerInterface $configurationManager) : To
    {
        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'CdxMigration',
            'Migration'
        );

        $identifer = $this->getIdentifier();
        if ($settings && isset($settings['model'][$identifer]['values'])) {
            $this->setConfiguredProperties($settings['model'][$identifer]['values']);
        }

        $this->applyTcaDefaults();

        return $this;
    }

    /**
     * Applies the given properties to model.
     *
     * Structure:
     *      name => value
     */
    protected function setConfiguredProperties(array $properties) : void
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Returns identifier used for configuration, based on class name.
     */
    protected function getIdentifier() : string
    {
        $model = get_class($this);
        $identifer = substr($model, strrpos($model, '\\') + 1);
        $identifer = str_replace('New', '', $identifer);

        return strtolower($identifer);
    }

    /**
     * Checks TCA configuration and applies default values, e.g. crdate.
     */
    protected function applyTcaDefaults() : void
    {
        $tca = $this->getTcaForTable(static::$table_name);

        if (isset($tca['ctrl']['tstamp'])) {
            $field = $tca['ctrl']['tstamp'];
            $this->$field = \time();
        }

        if (isset($tca['ctrl']['crdate'])) {
            $field = $tca['ctrl']['crdate'];
            $this->$field = \time();
        }

        if (isset($tca['ctrl']['cruser_id'])) {
            $field = $tca['ctrl']['cruser_id'];
            $this->$field = $this->getBeUserUid();
        }
    }

    protected function getTcaForTable(string $table) : array
    {
        return $GLOBALS['TCA'][$table] ?: [];
    }

    protected function getBeUserUid() : int
    {
        return $GLOBALS['BE_USER']->user['uid'];
    }
}
