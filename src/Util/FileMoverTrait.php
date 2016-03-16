<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Util;

use Zend\Filter\File\Rename;

/**
 * MoveFileTrait
 *
 * @package ZffBase
 * @subpackage ZffBase_Util
 */
trait FileMoverTrait
{
    /**
     * @var Rename
     */
    private static $rename_filter;

    /**
     * @var string
     */
    protected $uploadDirectory = 'data/upload';

    /**
     * @return Rename
     */
    protected function getFileRenameFilter()
    {
        if (!self::$rename_filter) {
            self::$rename_filter = new Rename([]);
        }
        return self::$rename_filter;
    }

    /**
     * Move the $newFileSourceInfo from tmp path to $newFileDestPath.
     *
     * @param string      $oldFilePath
     * @param array       $newFileSourceInfo
     * @param string|null $newFileDestPath
     */
    protected function moveFile(&$oldFilePath, $newFileSourceInfo, $newFileDestPath = null)
    {
        if ($newFileDestPath === null) {
            $newFileDestPath = $this->uploadDirectory;
        }

        if (is_array($newFileSourceInfo)) {
            $this->getFileRenameFilter()->setFile(['target' => $newFileDestPath]);
            $oldFilePath = $this->getFileRenameFilter()->filter($newFileSourceInfo['tmp_name']);
        }
    }

    /**
     * Remove the given $filePath.
     *
     * @param string $filePath
     *
     * @return bool
     */
    protected function removeFile($filePath)
    {
        return unlink($filePath);
    }

    /**
     * Remove the given $oldFilePath
     * And move the $newFileSourceInfo from tmp path to $newFileDestPath.
     *
     * @param string      $oldFilePath
     * @param array       $newFileSourceInfo
     * @param string|null $newFileDestPath
     */
    protected function updateFile(&$oldFilePath, $newFileSourceInfo, $newFileDestPath = null)
    {
        if ($newFileDestPath === null) {
            $newFileDestPath = $this->uploadDirectory;
        }

        if (is_array($newFileSourceInfo)) {
            $this->removeFile($oldFilePath);
            $this->moveFile($oldFilePath, $newFileSourceInfo, $newFileDestPath);
        }
    }
}
