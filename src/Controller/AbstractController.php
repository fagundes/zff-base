<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Abstract Controller
 *
 * @package ZffBase
 * @subpackage ZffBase_Controller
 */
class AbstractController extends AbstractActionController
{

    /**
     * @var array
     */
    private $postedData;

    /**
     * @var array
     */
    protected $forms;

    /**
     * @var array
     */
    protected $services;

    /**
     * @return array list of forms's names
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @return array list of service's names
     */
    public function getServices()
    {
        return $this->services;
    }

    protected function getPostedData()
    {
        if (is_null($this->postedData)) {
            $this->postedData = array_merge(
                (array) $this->getRequest()->getPost(),
                (array) $this->getRequest()->getFiles()
            );
        }
        return $this->postedData;
    }

    /**
     * @param $htmlString
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function htmlResponse($htmlString)
    {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($htmlString);
        return $response;
    }

    /**
     * @param $jsonString
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function jsonResponse($jsonString)
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/json');
        $response->setStatusCode(200);
        $response->setContent($jsonString);
        return $response;
    }
}
