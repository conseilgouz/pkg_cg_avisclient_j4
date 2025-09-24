<?php
/**
* CG Avis Client  - Joomla 4.x/5x/6.x package
* Package			: CG Avis Client
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class pkg_CGAvisClientInstallerScript
{
    private $min_joomla_version      = '4.0.0';
    private $min_php_version         = '7.4';
    private $name                    = 'CG Avis Client';
    private $exttype                 = 'package';
    private $extname                 = 'cg_avis_client';
    private $newlib_version	         = '';
    private $lang           = null;
    private $dir           = null;
    private $installerName = 'cgavisclientinstaller';

    public function __construct()
    {
        $this->dir = __DIR__;
    }

    public function preflight($type, $parent)
    {
        if (! $this->passMinimumJoomlaVersion()) {
            $this->uninstallInstaller();
            return false;
        }

        if (! $this->passMinimumPHPVersion()) {
            $this->uninstallInstaller();
            return false;
        }
    }

    public function postflight($type, $parent)
    {
        if (($type == 'install') || ($type == 'update')) { // remove obsolete dir/files
            $this->postinstall_cleanup();
        }
        if (!$this->checkLibrary('conseilgouz')) { // need library installation
            $ret = $this->installPackage('lib_conseilgouz');
            if ($ret) {
                Factory::getApplication()->enqueueMessage('ConseilGouz Library ' . $this->newlib_version . ' installed', 'notice');
            }
        }
        // delete obsolete version.php file
        $this->delete([
            JPATH_SITE . '/modules/mod_cg_avis_scroll/src/Field/VersionField.php',
            JPATH_SITE . '/modules/mod_cg_avis_scroll/src/Field/CgrangeField.php',
            JPATH_SITE . '/media/mod_cg_avis_scroll/js/cgrange.js',
            JPATH_SITE . '/media/mod_cg_avis_scroll/css/cgrange.css',
            JPATH_SITE . '/modules/mod_cg_avisclient/src/Field/VersionField.php',
            JPATH_SITE . '/modules/mod_cg_avisclient/src/Field/CgvariableField.php',
            JPATH_SITE . '/modules/mod_cg_avisclient/layouts/cgvariable.php',
        ]);

        return true;
    }
    private function postinstall_cleanup()
    {
        $obsloteFolders = ['assets'];
        // Remove plugins' files which load outside of the component. If any is not fully updated your site won't crash.
        foreach ($obsloteFolders as $folder) {
            $f = JPATH_SITE . '/modules/mod_'.$this->extname.'/' . $folder;

            if (!@file_exists($f) || !is_dir($f) || is_link($f)) {
                continue;
            }

            Folder::delete($f);
        }
        $obsloteFiles = [sprintf("%s/modules/mod_%s/helper.php", JPATH_SITE, $this->extname)];
        foreach ($obsloteFiles as $file) {
            if (@is_file($file)) {
                File::delete($file);
            }
        }
        $j = new Version();
        $version = $j->getShortVersion();
        $version_arr = explode('.', $version);
        if (($version_arr[0] == "4") || (($version_arr[0] == "3") && ($version_arr[1] == "10"))) {
            // Delete 3.9 and older language files
            $pluginname = "system_cgstyle";
            $langFiles = [
                sprintf("%s/language/en-GB/en-GB.mod_%s.ini", JPATH_SITE, $this->extname),
                sprintf("%s/language/en-GB/en-GB.mod_%s.sys.ini", JPATH_SITE, $this->extname),
                sprintf("%s/language/fr-FR/fr-FR.mod_%s.ini", JPATH_SITE, $this->extname),
                sprintf("%s/language/fr-FR/fr-FR.mod_%s.sys.ini", JPATH_SITE, $this->extname),
                sprintf("%s/language/en-GB/en-GB.plg_%s.ini", JPATH_ADMINISTRATOR, $pluginname),
                sprintf("%s/language/en-GB/en-GB.plg_%s.sys.ini", JPATH_ADMINISTRATOR, $pluginname),
                sprintf("%s/language/fr-FR/fr-FR.plg_%s.ini", JPATH_ADMINISTRATOR, $pluginname),
                sprintf("%s/language/fr-FR/fr-FR.plg_%s.sys.ini", JPATH_ADMINISTRATOR, $pluginname),
            ];
            foreach ($langFiles as $file) {
                if (@is_file($file)) {
                    File::delete($file);
                }
            }
        }
        // remove obsolete update sites
        $db	= Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->delete('#__update_sites')
            ->where($db->quoteName('location') . ' like "%432473037d.url-de-test.ws/%"');
        $db->setQuery($query);
        $db->execute();
        // Simple Isotope is now on Github
        $query = $db->createQuery()
            ->delete('#__update_sites')
            ->where($db->quoteName('location') . ' like "%conseilgouz.com/updates/pkg_cg_avis%"');
        $db->setQuery($query);
        $db->execute();

    }
    // Check if Joomla version passes minimum requirement
    private function passMinimumJoomlaVersion()
    {
        $j = new Version();
        $version = $j->getShortVersion();
        if (version_compare($version, $this->min_joomla_version, '<')) {
            Factory::getApplication()->enqueueMessage(
                'Incompatible Joomla version : found <strong>' . $version . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
                'error'
            );

            return false;
        }

        return true;
    }

    // Check if PHP version passes minimum requirement
    private function passMinimumPHPVersion()
    {

        if (version_compare(PHP_VERSION, $this->min_php_version, '<')) {
            Factory::getApplication()->enqueueMessage(
                'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
                'error'
            );
            return false;
        }

        return true;
    }
    private function checkLibrary($library)
    {
        $file = $this->dir.'/lib_conseilgouz/conseilgouz.xml';
        if (!is_file($file)) {// library not installed
            return false;
        }
        $xml = simplexml_load_file($file);
        $this->newlib_version = $xml->version;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $conditions = array(
             $db->qn('type') . ' = ' . $db->q('library'),
             $db->qn('element') . ' = ' . $db->quote($library)
            );
        $query = $db->getQuery(true)
                ->select('manifest_cache')
                ->from($db->quoteName('#__extensions'))
                ->where($conditions);
        $db->setQuery($query);
        $manif = $db->loadObject();
        if ($manif) {
            $manifest = json_decode($manif->manifest_cache);
            if ($manifest->version >= $this->newlib_version) { // compare versions
                return true; // library ok
            }
        }
        return false; // need library
    }
    private function installPackage($package)
    {
        $tmpInstaller = new Joomla\CMS\Installer\Installer();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $tmpInstaller->setDatabase($db);
        $installed = $tmpInstaller->install($this->dir . '/' . $package);
        return $installed;
    }
    
    private function uninstallInstaller()
    {
        if (! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
            return;
        }
        $this->delete([
            JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
            JPATH_PLUGINS . '/system/' . $this->installerName,
            sprintf("%s/modules/mod_%s/script.php", JPATH_SITE, $this->extname)
        ]);
        $db	= Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->delete('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        $db->execute();
        $cache = Factory::getContainer()->get(Joomla\CMS\Cache\CacheControllerFactoryInterface::class)->createCacheController();
        $cache->clean('_system');
    }
    public function delete($files = [])
    {
        foreach ($files as $file) {
            if (is_dir($file)) {
                Folder::delete($file);
            }

            if (is_file($file)) {
                File::delete($file);
            }
        }
    }

}
