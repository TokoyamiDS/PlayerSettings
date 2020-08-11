<?php

namespace pmmpshop\playersettings;

use pocketmine\Server;

use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\utils\TextFormat as T;

use onebone\economyapi\EconomyAPI;

use pmmpshop\playersettings\jojoe77777\FormAPI\FormAPI;
use pmmpshop\playersettings\jojoe77777\FormAPI\SimpleForm;
use pmmpshop\playersettings\jojoe77777\FormAPI\CustomForm;

class Main extends PluginBase implements Listener {
	
	public function onEnable(){
		$this->getserver()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(T::GREEN . " PlayerSettings Has started.!");
	}
	public function onDisable(){
		
		$this->getLogger()->info(T::RED . " PlayerSettings Has Disabled.!");
	}
	
	public function onCommand (CommandSender $sender, Command $cmd, string $label, array $args) : bool {
	    switch($cmd->getName()){
		    case "psui":
                $item = Item::get(397, 3, 64)->setCustomName("§l§ePlayerSettings");
                $sender->getInventory()->addItem($item);
	        break;
		
        }
	    return true;
    }
	
	public function onInteract(PlayerInteractEvent $ev){
		
        $player = $ev->getPlayer();
		$item = $ev->getItem();
		
		if ($item->getCustomName() == "§l§ePlayerSettings") {
            $this->openHomepage($player);
		}
	}
	
	public function openHomepage($player){
        $api = $this->getServer()->getPluginManager()->getplugin("FormAPI");
		$form = $api->createSimpleForm(function (Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
            switch($result){
                case 0:
                    $this->openFeed($player);
                    break;
				case 1:
                    $player->sendMessage("Soon...");
                    break;
				case 2:
                    $this->openFly($player);
                    break;
				case 3:
				    $this->openMoney($player);
                    break;
            }
			return false;
        });
        $form->setTitle("Player Settings");
        $form->setContent("Hi\nWhat you want?");
        $form->addButton("Health And Food\n§fTap To Open");
		$form->addButton("§eName\n§fTap To Open");
		$form->addButton("§aFly\n§fTap To Open");
		$form->addButton("§bSize\n§fTap To Open");
		$form->addButton("§eMoney\n§fTap To Open");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function openFeed(Player $player){
	    
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(function (Player $player, array $data = null){
		    
			if($data === null){
				return true;
			}
            $player->sendMessage("Your Health set to: " . $data[0]);
            $player->setHealth($data[0]);
            $player->sendMessage("Your Food set to: " . $data[1]);
            $player->setFood($data[1]);
            
		});
        $form->setTitle("Health And Food");
		$form->addSlider("Health", 1, 20, 2);
		$form->addSlider("Food", 1, 20, 2);
		$form->sendToPlayer($player);
		return $form;
    }
		
	public function openFly(Player $player){
	    
        $api = $this->getServer()->getPluginManager()->getplugin("FormAPI");
		$form = $api->createSimpleForm(function(Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
            switch($result) {
                case 0:
                    $player->sendMessage(T::GREEN . "Fly is On");
                    $player->addTitle(T::GREEN . "Fly", "On");
                    $player->setAllowFlight(true);
                    $player->setFlying(true);
                    break;
				case 1:
				    $player->sendMessage(T::RED . "Fly is Off");
                    $player->addTitle(T::RED . "Fly", "Off");
                    $player->setAllowFlight(false);
                    $player->setFlying(false);
				    break;
            }
			return false;
        });
        $form->setTitle("Fly");
        $form->setContent("§aOn §7or §cOff ?");
        $form->addButton("§aOn");
		$form->addButton("§cOff");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function openSize(Player $player){
		
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(function(Player $player, array $data = null){
			if($data[0] === null){
				return true;
			}
            $player->setScale($data[0]);
			$player->sendMessage("Your size set to: " . $data[0]);
        });
        $form->setTitle("Size");
		$form->addSlider("Size", 1, 10);
        $form->sendToPlayer($player);
		return $form;
    }
	
	public function openMoney(Player $player){
	    
        $api = $this->getServer()->getPluginManager()->getplugin("FormAPI");
		$form = $api->createSimpleForm(function(Player $player, int $data = null){
			$result = $data;
			if($result === null){
				return true;
			}
            switch($result) {
                case 0:
                    $mymoney = EconomyAPI::getInstance()->myMoney($player);
					$player->sendMessage("You have " . $mymoney . " money");
					$player->addTitle("Your Money: " . $mymoney . " $");
                    break;
				case 1:
				    $this->openpay($player);
				    break;
			    case 2:
				    $this->openPlayerMoney($player);
				    break;
            }
			return false;
        });
        $form->setTitle("Money");
        $form->addButton("MyMoney");
		$form->addButton("Pay");
		$form->addButton("Check Player Money");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function openPay(Player $player){
		
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(function(Player $player, array $data = null){
			if($data[0] === null){
				return true;
			}
            $this->getServer()->getCommandMap()->dispath($player, "pay " . $data[0] . " " . $data[1]);
        });
        $form->setTitle("Pay");
		$form->addInput("Player Name", "Enter player name here");
		$form->addInput("Money", "Enter Money here");
        $form->sendToPlayer($player);
		return $form;
    }
	
	public function openPlayerMoney(Player $player){
		
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(function(Player $player, array $data = null){
			if($data[0] === null){
				return true;
			}
            $this->getServer()->getCommandMap()->dispath($player, "seemoney " . $data[0]);
        });
        $form->setTitle("Check Player Money");
		$form->addInput("Player Name", "Enter player name here");
        $form->sendToPlayer($player);
		return $form;
    }
	
}
