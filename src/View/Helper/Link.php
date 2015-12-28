<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

/**
 * Link
 * Cria a tag de ancora 'A', de ocordo com os itens passados.
 *
 * @package ZffBase
 * @subpackage ZffBase_Helper
 */
class Link extends AbstractHtmlElement
{

    protected $escapeHtmlHelper;

    public function __invoke($title, $url, $options = [])
    {
        return $this->link($title, $url, $options);
    }

    protected function link($title, $url, $options)
    {
        $options['href'] = is_string($url) ? $url : $this->_url($url);
        return $this->linkHtml($title, $options);
    }

    protected function _url($url)
    {
        $urlHelper = $this->view->plugin('Url');

        if (!isset($url['params'])) {
            $url['params'] = [];
        }

        return $urlHelper($url['route'], $url['params']);
    }

    protected function linkHtml($title, $attribs)
    {
        $escaper = $this->escapeHtmlHelper ? : $this->view->plugin('escapeHtml');
        return '<a' . $this->htmlAttribs($attribs) . '>'
                . $escaper($title)
                . '</a>';
    }

    public function setEscapeHtmlHelper($escapeHtmlHelper)
    {
        $this->escapeHtmlHelper = $escapeHtmlHelper;
    }
}
