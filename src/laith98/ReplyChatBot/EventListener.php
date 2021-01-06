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
 *	Discord: Laith.97#0001
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

use pocketmine\event\player\PlayerChatEvent;

use pocketmine\utils\TextFormat as TF;
use pocketmine\event\Listener;
use pocketmine\{Player, Server};

class EventListener implements Listener {
	
	/** @var ReplyChatBot */
	private $plugin;
	
	/**
     * EventListener constructor.
     * 
     * @param ReplyChatBot $plugin
     */
	public function __construct(ReplyChatBot $plugin){
		$this->plugin = $plugin;
	}
	
	public function onChat(PlayerChatEvent $event): void{
		$player = $event->getPlayer();
		$msg = $event->getMessage();
		if($this->plugin->getFromConfig("plugin_enable") !== "on")return;
		$time = $this->plugin->getFromConfig("sendMessage.time");
		$this->plugin->getScheduler()->scheduleDelayedTask(new broadCastTask($this->plugin, $msg, $player), $time * 20);
	}
}
