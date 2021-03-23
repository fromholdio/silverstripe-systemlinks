<?php

namespace Fromholdio\SystemLinks;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;
use SilverStripe\SiteConfig\SiteConfig;
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
        $links = static::get_raw_links();
        if (!$links) {
            return null;
        }
        $linksList = ArrayList::create();
        foreach ($links as $link) {
            $linksList->push(static::convert_to_object($link));
        }
        return $linksList;
    }

    public static function get_link($key)
    {
        $link = static::get_raw_link($key);
        if (!$link) {
            return null;
        }
        return static::convert_to_object($link);
    }

    public static function get_map($value = 'title')
    {
        if ($value !== 'title' && $value !== 'url') {
            throw new \UnexpectedValueException(
                'SystemLinks::get_map() invalid $value argument passed. Expecting either `title` or `url`, '
                . 'received "' . $value . '".');
        }

        $links = static::get_raw_links();
        $map = [];
        foreach ($links as $key => $link) {
            $map[$key] = $link[$value];
        }
        return $map;
    }

    public static function get_raw_links()
    {
        $links = static::config()->get('links');
        if (!$links || count($links) < 1) {
            return null;
        }
        foreach ($links as $key => $link) {
            $links[$key]['url'] = self::process_url($link['url']);
        }
        return $links;
    }

    public static function get_raw_link($key)
    {
        $links = static::get_raw_links();
        if (!isset($links[$key])) {
            return null;
        }
        $link = $links[$key];
        $link['url'] = self::process_url($link['url']);
        return $link;
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

    private static function process_url($url)
    {
        if ($url === '$Login') {
            $url = self::login_url();
        }
        else if ($url === '$Logout') {
            $url = self::logout_url();
        }
        else if ($url === '$LostPassword') {
            $url = self::lost_password_url();
        }
        return $url;
    }

    private static function login_url()
    {
        return Security::login_url();
    }

    private static function lost_password_url()
    {
        return Security::lost_password_url();
    }

    private static function logout_url()
    {
        $url = Security::logout_url();
        $url = SecurityToken::inst()->addToUrl($url);

        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig && $siteConfig->hasMethod('getSystemLinksLogoutRedirectURL')) {
            $redirectURL = $siteConfig->getSystemLinksLogoutRedirectURL();
            if (is_string($url) && !empty($url)) {
                $url = Controller::join_links($url, '?BackURL=' . urlencode($redirectURL));
            }
        }
        return $url;
    }
}
