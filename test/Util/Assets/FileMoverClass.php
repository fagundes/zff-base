<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace ZffTest\Base\Util\Assets;

use Zff\Base\Util\FileMoverTrait;

/**
 * Class ConfigurableClass
 * @package ZffTest\Base\Util\Assets
 */
class FileMoverClass
{
    use FileMoverTrait;

    protected $filepathOne;
    protected $filepathOneInfo;
    protected $filepathTwo;
    protected $filepathTwoInfo;

    /**
     * @return mixed
     */
    public function getFilepathOne()
    {
        return $this->filepathOne;
    }

    /**
     * @param mixed $filepathOne
     */
    public function setFilepathOne($filepathOne)
    {
        $this->filepathOne = $filepathOne;
    }

    /**
     * @return mixed
     */
    public function getFilepathOneInfo()
    {
        return $this->filepathOneInfo;
    }

    /**
     * @param mixed $filepathOneInfo
     */
    public function setFilepathOneInfo($filepathOneInfo)
    {
        $this->filepathOneInfo = $filepathOneInfo;
    }

    /**
     * @return mixed
     */
    public function getFilepathTwo()
    {
        return $this->filepathTwo;
    }

    /**
     * @param mixed $filepathTwo
     */
    public function setFilepathTwo($filepathTwo)
    {
        $this->filepathTwo = $filepathTwo;
    }

    /**
     * @return mixed
     */
    public function getFilepathTwoInfo()
    {
        return $this->filepathTwoInfo;
    }

    /**
     * @param mixed $filepathTwoInfo
     */
    public function setFilepathTwoInfo($filepathTwoInfo)
    {
        $this->filepathTwoInfo = $filepathTwoInfo;
    }

    public function moveAllFiles()
    {
        $this->moveFile($this->filepathOne, $this->filepathOneInfo, 'test/data/upload');
        $this->moveFile($this->filepathTwo, $this->filepathTwoInfo, 'test/data/upload/otherplace');
    }

    public function removeAllFiles()
    {
        $this->removeFile($this->filepathOne);
        $this->removeFile($this->filepathTwo);
    }

    public function updateAllFiles()
    {
        $this->updateFile($this->filepathOne, $this->filepathOneInfo, 'test/data/upload');
        $this->updateFile($this->filepathTwo, $this->filepathTwoInfo, 'test/data/upload/otherplace');
    }
}
