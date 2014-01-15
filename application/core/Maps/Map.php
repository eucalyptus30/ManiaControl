<?php

namespace ManiaControl\Maps;

use ManiaControl\Formatter;

/**
 * Map Class
 *
 * @author kremsy & steeffeen
 */
class Map {
	/**
	 * Public Properties
	 */
	public $index = -1;
	public $name = 'undefined';
	public $uid = '';
	public $fileName = '';
	public $environment = '';
	public $goldTime = -1;
	public $copperPrice = -1;
	public $mapType = '';
	public $mapStyle = '';
	public $nbCheckpoints = -1;
	/** @var MXMapInfo $mx */
	public $mx = null;
	public $authorLogin = '';
	public $authorNick = '';
	public $authorZone = '';
	public $authorEInfo = '';
	public $comment = '';
	public $titleUid = '';
	public $startTime = -1;
	public $lastUpdate = 0;

	/**
	 * Create a new Map Object from Rpc Data
	 *
	 * @param array $rpc_infos
	 * @internal param \ManiaControl\ManiaControl $maniaControl
	 */
	public function __construct($rpc_infos = null) {
		$this->startTime = time();

		if(!$rpc_infos) {
			return;
		}
		$this->name        = FORMATTER::stripDirtyCodes($rpc_infos['Name']);
		$this->uid         = $rpc_infos['UId'];
		$this->fileName    = $rpc_infos['FileName'];
		$this->authorLogin = $rpc_infos['Author'];
		$this->environment = $rpc_infos['Environnement'];
		$this->goldTime    = $rpc_infos['GoldTime'];
		$this->copperPrice = $rpc_infos['CopperPrice'];
		$this->mapType     = $rpc_infos['MapType'];
		$this->mapStyle    = $rpc_infos['MapStyle'];

		if(isset($rpc_infos['NbCheckpoints'])) {
			$this->nbCheckpoints = $rpc_infos['NbCheckpoints'];
		}

		$this->authorNick = $this->authorLogin;
	}

	/**
	 * Checks if a map Update is available
	 *
	 * @return bool
	 */
	public function updateAvailable() {

		if($this->mx != null && ($this->lastUpdate < strtotime($this->mx->updated) || $this->uid != $this->mx->uid)) {
			return true;
		} else {
			return false;
		}
	}
} 