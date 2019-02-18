<?php

namespace Fromholdio\SystemLinks;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\View\TemplateGlobalProvider;

class SystemLinks implements TemplateGlobalProvider
{
    use Extensible;
    use Injectable;
    use Configurable;

    private static $links = [];

    public static function get_template_global_variables()
    {
        return [
            'SystemLink' => 'get_link'
        ];
    }

    public static function get_links()
    {
        $links = self::get_raw_links();
        if (!$links) {
            return null;
        }
        $linksList = ArrayList::create();
        foreach ($links as $link) {
            $linksList->push(self::convert_to_object($link));
        }
        return $linksList;
    }

    public static function get_link($key)
    {
        $link = self::get_raw_link($key);
        if (!$link) {
            return null;
        }
        return self::convert_to_object($link);
    }

    public static function get_map($value = 'title')
    {
        if ($value !== 'title' && $value !== 'url') {
            throw new \UnexpectedValueException(
                'SystemLinks::get_map() invalid $value argument passed. Expecting either `title` or `url`, '
                . 'received "' . $value . '".');
        }

        $links = self::get_raw_links();
        $map = [];
        foreach ($links as $key => $link) {
            $map[$key] = $link[$value];
        }
        return $map;
    }

    public static function get_raw_links()
    {
        $links = self::config()->get('links');
        if (!$links || count($links) < 1) {
            return null;
        }
        return $links;
    }

    public static function get_raw_link($key)
    {
        $links = self::get_raw_links();
        if (!isset($links[$key])) {
            return null;
        }
        return $links[$key];
    }

    private static function convert_to_object($link)
    {
        if (!isset($link['url'])) {
            throw new \UnexpectedValueException('SystemLinks::$links misconfiguration');
        }

        if (!isset($link['title'])) {
            throw new \UnexpectedValueException('SystemLinks::$links misconfiguration');
        }

        return ArrayData::create([
            'URL' => $link['url'],
            'Title' => $link['title']
        ]);
    }
}
