<?php

namespace AntiBuild;

use pocketmine\utils\TextFormat as MT;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player; 
use pocketmine\server; 
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\event\block\BlockPlaceEvent;


class AntiBuild extends PluginBase implements Listener
{
	private $config;
	
	public function onEnable()
	{
		if (!file_exists($this->getDataFolder()))
		{
            @mkdir($this->getDataFolder(), true);
        }
		$this->getLogger()->info(MT::BLUE . "[MineTox]" . MT::AQUA . " AntiBuild" . MT::BLUE . " has been loaded!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array(
		"banned-items" 		=> array(), 
		"ops-can-use-items" => true,
		"msg-item-use" => "[AntiBuild] You dont have permissions to place this block/item!"
		));
	}
	
	public function playerBlockPlace(BlockPlaceEvent $event) 
	{
            $id = $event->getBlock()->getID();  
			$player = $event->getPlayer();
			if($id == in_array($id, $this->config->get("banned-items"))) 
			{
				if($player->isOp())
				{
					if(!$this->config->get("ops-can-use-items") == true)
					{
					$player->sendMessage($this->config->get("msg-item-use"));
					$event->setCancelled();
					}
				} else {
				$player->sendMessage($this->config->get("msg-item-use"));
				$event->setCancelled();
				}
			}
	}	
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
        switch($cmd->getName())
		{
		case "banitem": 
		$config = $this->config->getAll();
        $items = $config["banned-items"];
			if(isset($args[0]))
			{
				if($args[0] == "add")
				{
					if(isset($args[1]))
					{
						$banid = $args[1];
						if(is_numeric($banid))
						{
							if(!in_array($banid, $items))
							{
								if(!is_array($items))
								{
									$items = array($banid);
								}else{
									$items[] = $banid;
									$sender->sendMessage("[AntiBuild] You have successfully banned BlockID:$banid ");
									$config["banned-items"] = $items;
									$this->config->setAll($config);
									$this->config->save();
								}
							}else{
								$sender->sendMessage("[AntiBuild] This Block/Item is already banned");
							}           
						}
					}else{
					$sender->sendMessage("[AntiBuild] Usage: /banitem <add/remove> <id>");
					}
				}
				if($args[0] == "remove")
				{
					if(isset($args[1]))
					{
						$banid = $args[1];
						if(is_numeric($banid))
						{
							if(in_array($banid, $items))
							{
								$key = array_search($banid, $items);
								unset($items[$key]);
								$sender->sendMessage("[AntiBuild] You have successfully Un-Banned BlockID:$banid ");
								$config["banned-items"] = $items;
								$this->config->setAll($config);
								$this->config->save();
							}else{
							$sender->sendMessage("[AntiBuild] This Block/Item is not banned");
							}
						}
					}else{
					$sender->sendMessage("[AntiBuild] Usage: /banitem <add/remove> <id>");
					}
				}
			}else{
			$sender->sendMessage("[AntiBuild] Usage: /banitem <add/remove> <id>");
			}
		}
	}
}
