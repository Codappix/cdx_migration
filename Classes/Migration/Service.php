<?php
namespace Codappix\CdxMigration\Migration;

/*
 * Copyright (C) 2018  Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\CdxMigration\Migration\Model;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Contains helper methods for recurring migration tasks.
 */
class Service
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(LogManager $logManager, ObjectManagerInterface $objectManager)
    {
        $this->logger = $logManager->getLogger(__CLASS__);
        $this->objectManager = $objectManager;
    }

    /**
     * Migrates a file from file system into FAL.
     *
     * @param string $oldFilePath The current file path in filesystem to file.
     * @param string $newFilePath The new path, will be mapped via FAL.
     * @param string $targetField The field containing the reference after migration.
     * @param Model\To $targetModel The instance of the model which has the $targetField.
     */
    public function migrateFile($oldFilePath, $newFilePath, $targetField, Model\To $targetModel)
    {
        if (!is_file($oldFilePath)) {
            $this->logger->error(sprintf('No old file found at %s.', $oldFilePath), [$oldFilePath]);
            return;
        }

        $storage = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()
            ->getDefaultStorage();

        GeneralUtility::mkdir_deep(dirname($newFilePath));
        $targetFolder = $storage->getRootLevelFolder();
        $newFilePath = str_replace(PATH_site, '', $newFilePath);
        $newFilePath = str_replace('fileadmin/', '', $newFilePath);
        $newFilePath = str_replace('user_upload/', '', $newFilePath);
        foreach (explode('/', dirname($newFilePath)) as $subPath) {
            $targetFolder = $targetFolder->getSubfolder($subPath);
        }
        $newFile = $storage->addFile($oldFilePath, $targetFolder, basename($oldFilePath));

        $fileReference = $this->objectManager->get(Model\FileReference::class, [
            'uid_local' => $newFile->getUid(),
            'uid_foreign' => $targetModel->uid,
            'tablenames' => $targetModel::$table_name,
            'fieldname' => $targetField,
            'table_local' => 'sys_file',
        ]);
        $fileReference->save();

        $targetModel->$targetField = 1;
        $targetModel->save();

        $this->logger->info(sprintf('Added old file "%s".', $oldFilePath));
    }
}
