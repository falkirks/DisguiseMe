<?php

namespace falkirks\disguiseme\command;


use falkirks\disguiseme\DisguiseMe;
use falkirks\disguiseme\exception\InvalidDisguiseException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class DisguiseCommand extends Command implements PluginIdentifiableCommand {
    /** @var DisguiseMe  */
    protected $api;
    public function __construct(DisguiseMe $api){
        parent::__construct("d", "Disguise as a mob!", "/d <type> <data> [player]");
        $this->api = $api;
    }
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(isset($args[2])){
            if($sender->hasPermission("disguiseme.other")) {
                $player = $this->getApi()->getServer()->getPlayer($args[2]);
                if($player instanceof Player){
                    $this->startDisguise($args[0], explode(":", $args[1]), $player, $sender);
                }
                else{
                    $sender->sendMessage(TextFormat::RED . "Player doesn't exist." . TextFormat::RESET);
                }
            }
            else{
                $sender->sendMessage(TextFormat::RED . "You don't have permission." . TextFormat::RESET);
                return true;
            }
        }
        else if(isset($args[1])){
            if($sender->hasPermission("disguiseme.other") && substr($args[1], 0, 1) == "-" && strlen($args[1]) > 1 && ($p = $this->getApi()->getServer()->getPlayer(substr($args[1], 1))) instanceof Player){
                $this->startDisguise($args[0], [], $p, $sender);
            }
            else if($sender->hasPermission("disguiseme.disguise") && $sender instanceof Player) {
                $this->startDisguise($args[0], explode(":", $args[1]), $sender, $sender);
            }
            else{
                $sender->sendMessage(TextFormat::RED . "You don't have permission." . TextFormat::RESET);
                return true;
            }
        }
        else if(isset($args[0])){
            if($sender->hasPermission("disguiseme.other") && ($player = $this->getApi()->getServer()->getPlayer($args[0])) instanceof Player) {
                $this->endDisguise($player, $sender);
            }
            else if($sender->hasPermission("disguiseme.disguise") && $this->getApi()->getDisguiseStore()->disguiseExists($this->getDisguiseName($args[0])) && $sender instanceof Player){
                $this->startDisguise($this->getDisguiseName($args[0]), [], $sender, $sender);
            }
            else{
                $sender->sendMessage(TextFormat::RED . "Failed to find valid target to execute on." . TextFormat::RESET);
                return true;
            }
        }
        else{
            if($sender instanceof Player && $this->getApi()->getDisguiseManager()->offsetExists($sender)){
                $this->endDisguise($sender, $sender);
            }
            else {
                $sender->sendMessage($this->getUsage());
            }
            return true;
        }

        return true;
    }

    private function getDisguiseName($string){
        if(substr($string, 0, 1) === "-"){
            return substr($string, 1);
        }
        else{
            return $string;
        }
    }

    private function endDisguise(Player $player, CommandSender $sender){
        if($this->getApi()->getDisguiseManager()->offsetExists($player)) {
            $this->getApi()->getDisguiseManager()->offsetUnset($player);
            $sender->sendMessage(TextFormat::BLUE . "Disguise closed." . TextFormat::RESET);
        }
        else{
            $sender->sendMessage(TextFormat::RED . "Player not disguised." . TextFormat::RESET);
        }
    }

    private function startDisguise(string $name, array $args, Player $player, CommandSender $sender){
        $this->getApi()->getDisguiseManager()->offsetUnset($player);
        try {
            $d = $this->getApi()->getDisguiseStore()->makeDisguise($name, $player, $args);
            if ($d != null) {
                $this->getApi()->getDisguiseManager()->add($d);
                $sender->sendMessage(TextFormat::GREEN . "Disguise activited." . TextFormat::RESET);
            } else {
                $sender->sendMessage(TextFormat::RED . "Bad disguise." . TextFormat::RESET);
            }
        }
        catch(InvalidDisguiseException $disguiseException){
            $sender->sendMessage(TextFormat::RED . $disguiseException->getMessage() . TextFormat::RESET);
        }
    }

    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(): Plugin{
        return $this->api;
    }

    /**
     * @return DisguiseMe
     */
    public function getApi(): DisguiseMe{
        return $this->api;
    }


}