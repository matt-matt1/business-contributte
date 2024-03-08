<?php

namespace App\Presenters;

use App\Objects\LanguagesAttributesObject;
use App\Objects\LanguagesDomainObject;
use App\Objects\LanguagesLocaleObject;
use App\Objects\LanguagesObject;
use Nette;


final class LanguagesPresenter extends Nette\Application\UI\Presenter
{
    private $path = 'lang';
    private $domain = 'locale';
    private $default = 'en';

    public function __construct(
//        private DirectoryProvider $directoryProvider,
    )
    {
//        $this->template->appDir = $appDir;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($dir)
    {
        $this->path = $dir;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getLocaleFilesObject()
    {
        $loc = dirname(__DIR__). '/'. $this->path. '/*.neon';
        $files = glob($loc);
        $domains = array();
//        $domains = new LanguagesObject();
        foreach ($files as $file) {
            $f = basename($file, '.neon');
            list($domain, $code) = explode('.', $f);
            $lang = new LanguagesAttributesObject($f. '.neon');
            if ($code == $this->default)
                $lang->default = true;
            $wrap = new LanguagesLocaleObject($code, $lang);
            $myDomain = new LanguagesDomainObject($domain, $wrap);
            $domains[] = new LanguagesObject($myDomain);
//            $domains[$domain][$code] = $lang;
        }
//        return new LanguagesObject();
        return $domains;
    }
    public function NOgetLocaleFilesArray()
    {
        $loc = dirname(__DIR__). '/'. $this->path. '/*.neon';
        $files = glob($loc);
        $out = array();
        $domains = array();
        foreach ($files as $file) {
            $f = basename($file, '.neon');
            list($domain, $code) = explode('.', $f);
            $lang = array('path' => $f. '.neon');
            if ($code == $this->default)
                $lang['default'] = true;
            $domains[$domain][$code] = $lang;
        }
        return $domains;
    }

    public function getLocaleFilesArray($domain)
    {
        $locales = array();
        $loc = dirname(__DIR__). '/'. $this->path. '/'. $this->domain. '.*.neon';
        $files = glob($loc);
        foreach ($files as $file) {
            $f = basename($file, '.neon');
            $code = str_replace($this->domain. '.', '', $f);
//            list($domain, $code) = explode('.', $f);
            $lang = array('path' => $f. '.neon');
            if ($code == $this->default)
                $lang['default'] = true;
            $locales[$code] = $lang;
//            $domains[$domain][$code] = $lang;
        }
        return $locales;
//        return $domains;
    }

    public function beforeRender()
    {
        $this->template->locales = $this->getLocaleFilesArray($this->domain);
        $this->template->domain = $this->domain;
        $this->template->default = $this->default;
        $this->template->skip = true;
        if (isset($_COOKIE[$this->domain]))
            $this->template->cookie = $_COOKIE[$this->domain];
        if (isset($_SESSION[$this->domain]))
            $this->template->session = $_SESSION[$this->domain];
        if (isset($_SERVER['Accept-Language']) && in_array($this->domain, $_SERVER['Accept-Language']))
            $this->template->header = $_SERVER['Accept-Language'];
        if (isset($_GET[$this->domain]))
            $this->template->querystring = $_GET[$this->domain];
    }
}