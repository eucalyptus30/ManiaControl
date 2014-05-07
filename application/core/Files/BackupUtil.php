<?php

namespace ManiaControl\Files;

use ManiaControl\ManiaControl;

/**
 * Backup Utility Class
 *
 * @author    ManiaControl Team <mail@maniacontrol.com>
 * @copyright 2014 ManiaControl Team
 * @license   http://www.gnu.org/licenses/ GNU General Public License, Version 3
 */
abstract class BackupUtil {
	/*
	 * Constants
	 */
	const FOLDER_NAME_BACKUP = 'backup/';

	/**
	 * Perform a Full Backup of ManiaControl
	 *
	 * @return bool
	 */
	public static function performFullBackup() {
		$backupFolder = self::getBackupFolder();
		if (!$backupFolder) {
			return false;
		}
		$backupFileName = $backupFolder . 'backup_' . ManiaControl::VERSION . '_' . date('y-m-d_H-i') . '_' . time() . '.zip';
		$backupZip      = new \ZipArchive();
		if ($backupZip->open($backupFileName, \ZipArchive::CREATE) !== true) {
			trigger_error("Couldn't create Backup Zip!");
			return false;
		}
		$excludes   = array('backup', 'logs', 'ManiaControl.log');
		$pathInfo   = pathInfo(ManiaControlDir);
		$parentPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR;
		$dirName    = $pathInfo['basename'];
		$backupZip->addEmptyDir($dirName);
		self::zipDirectory($backupZip, ManiaControlDir, strlen($parentPath), $excludes);
		return $backupZip->close();;
	}

	/**
	 * Get the Backup Folder Path and create it if necessary
	 *
	 * @return string
	 */
	private static function getBackupFolder() {
		$backupFolder = ManiaControlDir . self::FOLDER_NAME_BACKUP;
		if (!is_dir($backupFolder) && !mkdir($backupFolder)) {
			trigger_error("Couldn't create Backup Folder!");
			return false;
		}
		if (!is_writeable($backupFolder)) {
			trigger_error("ManiaControl doesn't have the necessary Writing Rights for the Backup Folder!");
			return false;
		}
		return $backupFolder;
	}

	/**
	 * Add a complete Directory to the ZipArchive
	 *
	 * @param \ZipArchive $zipArchive
	 * @param string      $folderName
	 * @param int         $prefixLength
	 * @param array       $excludes
	 * @return bool
	 */
	private static function zipDirectory(\ZipArchive &$zipArchive, $folderName, $prefixLength, array $excludes = array()) {
		$folderHandle = opendir($folderName);
		if (!is_resource($folderHandle)) {
			trigger_error("Couldn't open Folder '{$folderName}' for Backup!");
			return false;
		}
		while (false !== ($file = readdir($folderHandle))) {
			if (substr($file, 0, 1) === '.') {
				continue;
			}
			if (in_array($file, $excludes)) {
				continue;
			}
			$filePath  = $folderName . DIRECTORY_SEPARATOR . $file;
			$localPath = substr($filePath, $prefixLength);
			if (is_file($filePath)) {
				$zipArchive->addFile($filePath, $localPath);
				continue;
			}
			if (is_dir($filePath)) {
				$zipArchive->addEmptyDir($localPath);
				self::zipDirectory($zipArchive, $filePath, $prefixLength, $excludes);
				continue;
			}
		}
		closedir($folderHandle);
		return true;
	}

	/**
	 * Perform a Backup of the Plugins
	 *
	 * @return bool
	 */
	public static function performPluginsBackup() {
		$backupFolder = self::getBackupFolder();
		if (!$backupFolder) {
			return false;
		}
		$backupFileName = $backupFolder . 'backup_plugins_' . ManiaControl::VERSION . date('y-m-d_H-i') . '_' . time() . '.zip';
		$backupZip      = new \ZipArchive();
		if ($backupZip->open($backupFileName, \ZipArchive::CREATE) !== true) {
			trigger_error("Couldn't create Backup Zip!");
			return false;
		}
		$excludes   = array();
		$pathInfo   = pathInfo(ManiaControlDir . 'plugins');
		$parentPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR;
		$dirName    = $pathInfo['basename'];
		$backupZip->addEmptyDir($dirName);
		self::zipDirectory($backupZip, ManiaControlDir . 'plugins', strlen($parentPath), $excludes);
		return $backupZip->close();;
	}
}