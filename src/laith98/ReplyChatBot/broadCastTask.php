<?php

namespace laith98\ReplyChatBot;

/*  
 *  A plugin for PocketMine-MP that will reply chat messages.
 *  
 *  Copyright (C) 2020 LaithYoutuber
 *	
 *  Plugin By LaithYT, Gihhub:                                                                           
 *	 _           _ _   _    ___   ___  _____             
 *	| |         (_) | | |  / _ \ / _ \|  __ \            
 *	| |     __ _ _| |_| |_| (_) | (_) | |  | | _____   __
 *	| |    / _` | | __| '_ \__, |> _ <| |  | |/ _ \ \ / /
 *	| |___| (_| | | |_| | | |/ /| (_) | |__| |  __/\ V / 
 *	|______\__,_|_|\__|_| |_/_/  \___/|_____/ \___| \_/  
 *		
 *	Youtube: Laith Youtuber
 *	Facebook: Laith A Al Haddad
 *	Discord: Laith.97#8167
 *	Gihhub: Laith98Dev
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 	
 */

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;

class broadCastTask extends Task{
	
	/** @var ReplyChatBot   */
	private $plugin;
	
	/** $var string         */
	private $message;
	
	/** $var Player|null    */
	private $player;
	
	/**
     * broadCastTask constructor.
     * 
     * @param ReplyChatBot $plugin
     */
	public function __construct(ReplyChatBot $plugin, $message, $player = null){
		$this->plugin = $plugin;
		$this->message = $message;
		$this->player = $player;
	}
	
	public function onRun(int $currentTick): void
	{
		$player = $this->player;
		$msg = $this->message;
		if(($r = $this->plugin->getMessage($msg))!== null && $player instanceof Player){
			$this->plugin->getServer()->broadcastMessage(str_replace(["{PLAYER}", "{PREFIX}", "{MESSAGE}"], [$player->getName(), $this->plugin->getFromConfig("Prefix"), $r], $this->plugin->getFromConfig("Reply.Message")));
		}
	}
}
