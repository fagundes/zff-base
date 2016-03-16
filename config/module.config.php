<?php

namespace Zff\Base;

if(!defined('CSS_DIR')) {
    define('CSS_DIR', __DIR__ . '/../view/public/css');
}

if(!defined('JS_DIR')) {
    define('JS_DIR', __DIR__ . '/../view/public/js');
}

return [
    'router'             => [
        'router_class' => Mvc\Router\ControllerRouteStack::class,
    ],
    'view_manager'       => [
        'template_path_stack' => [
            'zff-base' => __DIR__ . '/../view',
        ],
        'template_map'        => [
            //some commonly used partials/elements
            'element/message'    => __DIR__ . '/../view/base/element/message.phtml',
            'element/breadcrumb' => __DIR__ . '/../view/base/element/breadcrumb.phtml',
            'element/paginator'  => __DIR__ . '/../view/base/element/paginator.phtml',
        ],
    ],
    'asset_manager'      => [
        'resolver_configs' => [
            'map' => [
                //css
                'css/zff-base/twbs.css'  => CSS_DIR . '/twbs-extension.css',
                'css/zff-base/debug.css' => CSS_DIR . '/debug.css',
                //js
                'js/zff-base/twbs.js'    => JS_DIR . '/twbs-extension.js',
            ],
        ],
    ],
    'form_elements'      => [
        'invokables' => [
            'bstext'          => Form\Element\BsText::class,
            'bstextarea'      => Form\Element\BsTextarea::class,
            'bsfile'          => Form\Element\BsFile::class,
            'bscheckbox'      => Form\Element\BsCheckbox::class,
            'bsradio'         => Form\Element\BsRadio::class,
            'bsmulticheckbox' => Form\Element\BsMultiCheckbox::class,
            'bsselect'        => Form\Element\BsSelect::class,
            'bsobjectselect'  => Form\Element\BsObjectSelect::class,
            'bsobjectradio'   => Form\Element\BsObjectRadio::class,
        ],
    ],
    'validators' => [
        'invokables' => [
            'cpf' => Validator\Cpf::class,
            'cnpj' => Validator\Cnpj::class,
        ],
    ],
    'view_helpers'       => [
        'invokables' => [
            //bs form helpers
            'bsform'                 => Form\View\Helper\BsForm::class,
            'bsformrow'              => Form\View\Helper\BsFormRow::class,
            'bsformcollection'       => Form\View\Helper\BsFormCollection::class,
            //other form helpers
            'formactionbutton'       => Form\View\Helper\FormActionButton::class,
            'formmulticheckboxsplit' => Form\View\Helper\FormMultiCheckboxSplit::class,
            'formradiosplit'         => Form\View\Helper\FormRadioSplit::class,
            //escaper helpers
            'noescape'               => View\Helper\Escaper\NoEscape::class,
            //other helpers
            'getroute'               => View\Helper\GetRoute::class,
            'isroute'                => View\Helper\IsRoute::class,
            'link'                   => View\Helper\Link::class,
            'paginatorlink'          => View\Helper\PaginatorLink::class,
            'postlink'               => View\Helper\PostLink::class,
        ],
    ],
    'service_manager'    => [
        'invokables'         => [
            Service\Table\TableHandler::class => Service\Table\TableHandler::class,
        ],
        'abstract_factories' => [
            Form\FormAbstractFactory::class,
            Form\InputFilterAbstractFactory::class,
            Service\ServiceAbstractFactory::class,
            Controller\ControllerAbstractFactory::class,
        ],
    ],
];
