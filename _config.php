<?php

use Fromholdio\SystemLinks\SystemLinks;
use SilverStripe\View\Parsers\ShortcodeParser;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;

call_user_func(function () {
    $module = ModuleLoader::inst()->getManifest()->getModule('fromholdio/silverstripe-systemlinks');
    TinyMCEConfig::get('cms')
        ->enablePlugins([
            'sslinksystem' => $module->getResource('client/dist/js/TinyMCE_sslink-system.js'),
        ]);
});


ShortcodeParser::get('default')->register(
    'system_link',
    [SystemLinks::class, 'link_shortcode_handler']
);
