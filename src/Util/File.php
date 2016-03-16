<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Util;

use Zff\Base\Exception;

/**
 * File
 *
 * @package ZffBase
 * @subpackage ZffBase_Util
 */
class File
{

    /**
     * Recursive Modes
     */
    const LEAVES_ONLY     = 0;
    const SELF_FIRST      = 1;
    const CHILD_FIRST     = 2;
    const CATCH_GET_CHILD = 16;

    public static function rmdirRecursive($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        static::walkDirRecursive($dir, function ($file) {
            if ('.' === $file->getBasename() || '..' === $file->getBasename()) {
                return true;
            }
            if ($file->isDir()) {
                return rmdir($file->getPathname());
            }
            return unlink($file->getPathname());
        });
    }

    protected static function walkDirRecursive($dir, $callable, $mode = self::CHILD_FIRST)
    {


        $it  = new \RecursiveDirectoryIterator($dir);
        $iit = new \RecursiveIteratorIterator($it, self::CHILD_FIRST);
        foreach ($iit as $file) {
            $callable($file);
        }
        $callable(new \SplFileInfo($dir));
    }

    public static function includeFile($path, $filename)
    {
        $path = $path . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            throw new Exception\RuntimeException(sprintf('Arquivo "%s" não encontrado', $path));
        }
        ob_clean();
        flush();
        set_time_limit(0);
        readfile($path);
        exit(0);
    }

    public static function openFile($path, $filename, $displayFilename = false, $disposition = 'inline')
    {
        $path            = $path . DIRECTORY_SEPARATOR . $filename;
        $displayFilename = $displayFilename ? : $filename;
        if (!file_exists($path)) {
            throw new Exception\RuntimeException(sprintf('Arquivo "%s" não encontrado', $path));
        }

        $finfo = new \finfo();
        $ctype = $finfo->file($path, FILEINFO_MIME);
        $filesize = filesize($path);

        header('Accept-Ranges: bytes');
        header("Content-Type: $ctype");
        header("Content-Length: $filesize");
        header("Content-Disposition: $disposition; filename=\"$displayFilename\"");

        ob_clean();
        flush();
        set_time_limit(0);
        readfile($path);
        exit;
    }
}
