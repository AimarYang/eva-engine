<?php
return array(
    'modules' => array(
        'Core',
        'User',
        'Blog',
        'File',
        'Scaffold',
        //'Engine',
        //'ZendDeveloperTools',
        //'BjyProfiler',
        //'Doctrine',
        //'DoctrineModule',
        //'DoctrineORMModule',
    ),
    'service_manager' => array(
        'use_defaults' => true,
        'factories'    => array(
            'ServiceListener' => 'Eva\Mvc\Service\ServiceListenerFactory',
            'translator' => 'Eva\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'protected_module_namespace' => array(
        'Admin', 'Api'   
    ),
    'use_module_template_path_stack' => true,
    'module_listener_options' => array( 
        'config_cache_enabled' => 0,
        'config_glob_paths'    => array(
            EVA_CONFIG_PATH . '/autoload/{,*.}{global,local}.config.php',
        ),
        'cache_dir'            => EVA_CONFIG_PATH . '/cache',
        'module_paths' => array(
            EVA_ROOT_PATH . '/depends',
            EVA_MODULE_PATH,
            EVA_ROOT_PATH . '/website',
        ),
    ),
);
