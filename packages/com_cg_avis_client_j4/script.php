<?php
/**
* CG Avis Client Component  - Joomla 4.x/5.x Component 
* Version			: 2.1.1
* Package			: CG Avis Client
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class com_cgavisclientInstallerScript
{
	private $min_joomla_version      = '4.0';
	private $min_php_version         = '7.4';
	private $name                    = 'CG Avis Client';
	private $exttype                 = 'component';
	private $extname                 = 'cgavisclient';
	private $previous_version        = '';
	private $dir           = null;
	private $lang = null;
	private $installerName = 'cgavisclientinstaller';
	public function __construct()
	{
		$this->dir = __DIR__;
	}
    function preflight($type, $parent)
    {

		if ( ! $this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();

			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();

			return false;
		}
		// To prevent installer from running twice if installing multiple extensions
		if ( ! file_exists($this->dir . '/' . $this->installerName . '.xml'))
		{
			return true;
		}
	
    }
    
    function postflight($type, $parent)
    {
	// check previous version com_spavisclient 
	// 1. check if old version exists
		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->createQuery()
			->select('*')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_spavisclient"')
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$old = $db->loadObjectList();
	// 2. check if spavisclient contains info.
		try {
		  $query = $db->createQuery()
		  ->select('*')
		  ->from('#__spavisclient');
		  $db->setQuery($query);
		  $avis = $db->loadObjectList();
		} catch (\Exception $e) {
		    $avis = array();
        }
		if (count($old) && count($avis)) { //info in #_spavisclient
		    try{
    // 3. insert spavisclient into new table
		        $query = $db->createQuery();
				foreach($avis as $oneavis) {
		          $result = $db->insertObject('#__cgavisclient', $oneavis);
		        }
    // 4. delete old table cg_resa
                $query = $db->setQuery('DROP TABLE #__spavisclient' );
                $db->execute();
    // 5. delete old version from extensions list, assets
                $query = $db->createQuery()
                ->delete('#__schemas')
                ->where($db->quoteName('extension_id') . ' = '.$old[0]->extension_id);
                $db->setQuery($query);
                $result = $db->execute();
                $query = $db->createQuery()
                ->delete('#__update_sites_extensions')
                ->where($db->quoteName('extension_id') . ' = '.$old[0]->extension_id);
                $db->setQuery($query);
                $result = $db->execute();
                $query = $db->createQuery()
		        ->delete('#__extensions')
		        ->where($db->quoteName('element') . ' = "com_spavisclient"')
		        ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		        $db->setQuery($query);
		        $result = $db->execute();
		        $query = $db->createQuery()
		        ->delete('#__assets')
		        ->where($db->quoteName('name') . ' = "com_spavisclient"');
		        $db->setQuery($query);
		        $result = $db->execute();
		        $query = $db->createQuery()
		        ->delete('#__session')
		        ->where($db->quoteName('data') . ' like "%com_spavisclient%"');
		        $db->setQuery($query);
		        $result = $db->execute();
    // 6. delete system menus 
		        $query = $db->createQuery()
		        ->delete('#__menu')
		        ->where($db->quoteName('link') . ' like "%com_spavisclient%"')
		        ->where($db->quoteName('menutype') . ' = ' . $db->quote('main'));
		        $db->setQuery($query);
		        $result = $db->execute();
    // 7. update old menus to new menus		
		        $query = $db->createQuery()
		        ->update('#__menu')
		        ->set('link = REPLACE(link,"com_spavisclient","com_cgavisclient")')
		        ->where($db->quoteName('menutype') . ' <> ' . $db->quote('main').' AND link like "%com_spavisclient%"');
		        $db->setQuery($query);
		        $result = $db->execute();
    // 7. update categories		
		        $query = $db->createQuery()
		        ->update('#__categories')
		        ->set('extension = "com_cgavisclient"')
		        ->where('extension = "com_spavisclient"');
		        $db->setQuery($query);
		        $result = $db->execute();
    // 8. delete old components directories		        
		    } catch (\Exception $e) {
		        $avis = array();
		    }
		}
	// Uninstall this installer
		if (($type=='install') || ($type == 'update')) { // remove obsolete dir/files
			$this->postinstall_cleanup();
		}
		$this->uninstallInstaller();

		return true;
    }
	private function postinstall_cleanup() {
		$obsloteFolders = ['/administrator/components/com_spavisclient', '/components/com_spavisclient'];
		// Remove plugins' files which load outside of the component. If any is not fully updated your site won't crash.
		foreach ($obsloteFolders as $folder)
		{
			$f = JPATH_SITE . $folder;

			if (!@file_exists($f) || !is_dir($f) || is_link($f))
			{
				continue;
			}

			Folder::delete($f);
		}
		$old = "com_spavisclient";
		$langFiles = [
			sprintf("%s/language/en-GB/en-GB.%s.ini", JPATH_SITE, $old),
			sprintf("%s/language/en-GB/en-GB.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/language/fr-FR/fr-FR.%s.ini", JPATH_SITE, $old),
			sprintf("%s/language/fr-FR/fr-FR.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/en-GB/en-GB.%s.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/en-GB/en-GB.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/fr-FR/fr-FR.%s.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/fr-FR/fr-FR.%s.sys.ini", JPATH_SITE, $old),
		];
		foreach ($langFiles as $file) {
			if (@is_file($file)) {
				File::delete($file);
			}
		}
	}
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		if (version_compare(JVERSION, $this->min_joomla_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Incompatible Joomla version : found <strong>' . JVERSION . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
					'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
				'error'
			);
			return false;
		}

		return true;
	}
	
	private function uninstallInstaller()
	{
		if ( ! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
			return;
		}
		$this->delete([
			JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
			JPATH_PLUGINS . '/system/' . $this->installerName,
		]);
		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->createQuery()
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		Factory::getApplication()->getCache()->clean('_system');
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