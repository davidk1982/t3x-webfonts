<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Utilities;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class InstallationManager
{
    protected const CONFIG_FILE = 'tx_webfonts_installed.json';

    protected static $config;
    protected $CONFIG_DIR;
    protected $FONT_DIR;

    public function __construct()
    {
        $fileadminAbsolute = rtrim(Environment::getPublicPath(), "/") . '/' . trim($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'], "/") . "/";
        $this->CONFIG_DIR = $fileadminAbsolute . "tx_webfonts/"; // with ending /
        $this->FONT_DIR = $fileadminAbsolute . "tx_webfonts/fonts/"; // with ending /

        GeneralUtility::mkdir_deep($this->CONFIG_DIR);
        GeneralUtility::mkdir_deep($this->FONT_DIR);

        if (!file_exists($this->CONFIG_DIR . self::CONFIG_FILE)) {
            file_put_contents($this->CONFIG_DIR . self::CONFIG_FILE, json_encode([]));
        }

        self::$config = array_values(json_decode(file_get_contents($this->CONFIG_DIR . self::CONFIG_FILE), true));
    }

    final public function getInstalledFonts(): array
    {
        return self::$config;
    }

    function deleteFont($fontId, $provider)
    {
        if (!$this->hasInstalled($fontId, $provider)) {
            return;
        }

        $this->deleteFontImpl($fontId, $provider);

        foreach (self::$config as $k => $font) {
            if ($font['id'] === $fontId && $font['provider'] === $provider) {
                unset(self::$config[$k]);
                break;
            }
        }

        $this->saveConfig();
    }


    public function installFont($font, $provider, $variants, $subsets)
    {
        $this->deleteFont($font, $provider);
        $this->installFontImpl($font, $provider, $variants, $subsets);
        $this->saveConfig();
        $this->createCssImportFile($font, $provider, $variants);
    }

    private function saveConfig()
    {
        file_put_contents($this->CONFIG_DIR . self::CONFIG_FILE, json_encode(array_values(self::$config)));
    }

    public function installDetails($fontId, $provider)
    {
        foreach (self::$config as $j) {
            if ($j['id'] === $fontId && $j['provider'] == $provider) {
                return $j;
            }
        }
        return null;
    }

    abstract protected function deleteFontImpl($fontId, $provider);

    abstract protected function installFontImpl($font, $provider, $variants, $subsets);

    abstract protected function createCssImportFile($fontId, $provider, $variants);

    abstract public function hasInstalled($font): bool;

}
