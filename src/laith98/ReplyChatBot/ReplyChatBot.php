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
 
use pocketmine\{Player, Server};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config, TextFormat as TF};
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

/**
 * The main class for ReplyChatBot infrastructure.
 *
 * @package laith98\ReplyChatBot
 */
class ReplyChatBot extends PluginBase
{
	/** @var array */
	public $files = [
		"Messages.yml",
		"Config.yml"
	];
	
	public function onEnable(): void{
		$this->saveFiles();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}
	
   /**
	* @parm CommandSender $sender
	* @parm Command $cmd
	* @parm string $commandLabel
	* @parm array $args
	* @return bool
	*/
	public function onCommand(CommandSender $sender, Command $cmd, string $commandLabel, array $args): bool
    {
		if(strtolower($cmd->getName()) == "bot"){
			if(!$sender instanceof Player){
				$sender->sendMessage(TF::RED . "Cannot use the command here.");
				return true;
			}
			if(!$sender->hasPermission("replybot.command.use")){
				$sender->sendMessage(TF::RED . "you don't have permission to use the command.");
				return true;
			}
			
			if(!isset($args[0])){
				$sender->sendMessage(TF::RED . "Usage: /bot help");
				return true;
			}
			
			if(strtolower($args[0]) == "help"){
				$sender->sendMessage(TF::YELLOW . "/bot add|remove|info|list|off|on");
				return true;
			}
			
			if(strtolower($args[0]) == "add"){
				if(!isset($args[1]) || !isset($args[2]) || isset($args[3])){
					$sender->sendMessage(TF::YELLOW . "/bot add <key> <reply>");
					return true;
				}
				if($this->addRep($args[1], $args[2])){
					$sender->sendMessage(TF::YELLOW . "Sucessfully add '" . $args[1] . "'!");
					return true;
				}
				$sender->sendMessage(TF::RED . "Cannot add '" . $args[1] . "',Alredy exists!");
				return true;
			}
			
			if(strtolower($args[0]) == "remove"){
				if(!isset($args[1])){
					$sender->sendMessage(TF::YELLOW . "/bot remove <key>");
					return true;
				}
				if($this->rmRep($args[1])){
					$sender->sendMessage(TF::YELLOW . "Sucessfully remove '" . $args[1] . "'!");
					return true;
				}
				$sender->sendMessage(TF::RED . "Cannot remove '" . $args[1] . "', not exists!");
				return true;
			}
			
			if(strtolower($args[0]) == "info"){
				$sender->sendMessage(TF::RED . "Plugin created by laith98[Laith Youtuber]\nYT: Laith Youtuber\nDiscord: Laith.97#8167\nFacebook: Laith A Al Haddad");
				return true;
			}
			
			if(strtolower($args[0]) == "off"){
				if($this->getFromConfig("plugin_enable") == "off"){
					$sender->sendMessage(TF::RED . "Cannot turn off the plugin, Alredy Off");
					return true;
				}
				$cfg = new Config($this->getDataFolder() . $this->files[1], Config::YAML);
				$cfg->set("plugin_enable", "off");
				$cfg->save();
				$sender->sendMessage(TF::YELLOW . "Sucessfully set off");
				return true;
			}
			
			if(strtolower($args[0]) == "on"){
				if($this->getFromConfig("plugin_enable") == "on"){
					$sender->sendMessage(TF::RED . "Cannot turn on the plugin, Alredy On");
					return true;
				}
				$cfg = new Config($this->getDataFolder() . $this->files[1], Config::YAML);
				$cfg->set("plugin_enable", "on");
				$cfg->save();
				$sender->sendMessage(TF::YELLOW . "Sucessfully set on");
				return true;
			}
			
			if(strtolower($args[0]) == "list"){
				if(isset($args[1])){
					$sender->sendMessage(TF::RED . "Usage: /bot list");
					return true;
				}
				$path = new Config($this->getDataFolder() . $this->files[0], Config::YAML);
				$all = $path->getAll();
				$sender->sendMessage(TF::YELLOW . "ReplyChatBot list:");
				foreach($all as $key => $reply){
					$sender->sendMessage(TF::YELLOW . $key . " => " . $reply);
				}
				return true;
			}
			
			if(!in_array($args[0], array('list', 'add', 'remove', 'info', 'off', 'on'))){
				$sender->sendMessage(TF::RED . "Usage: /bot help");
				return true;
			}
		}
		return false;
	}
	
	public function saveFiles(){
		foreach($this->files as $file){
			if(!is_file($this->getDataFolder() . $file)){
				$this->saveResource($file);
			}
		}
	}
	
	public function addRep($key, $reply): bool{
		$m = new Config($this->getDataFolder() . $this->files[0], Config::YAML);
		if(!$m->get($key)){
			$m->set($key, $reply);
			$m->save();
			if(!$m->save() || !$m->get($key)){
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function rmRep($key): bool{
		$m = new Config($this->getDataFolder() . $this->files[0], Config::YAML);
		if($m->get($key)){
			$m->remove($key);
			$m->save();
			if(!$m->save() || $m->get($key)){
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function getFromConfig($item){
		$cfg = new Config($this->getDataFolder() . $this->files[1], Config::YAML);
		if($cfg->get($item)){
			return $cfg->get($item);
		}
		return null;
	}
	
	public function getMessage($m){
		$path = new Config($this->getDataFolder() . $this->files[0], Config::YAML);
		$all = $path->getAll();
		foreach($all as $key => $reply){
			$st = false;
			$ar = explode(" ", $m);
			foreach($ar as $i){
				if($i == $key){
					$st = true;
				}
			}
			if(strtolower($key) == strtolower($m) || $st){
				return $reply;
			}
		}
		return null;
	}
}
