<?php
namespace disguiseme;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\AddMobPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class DisguiseMe extends PluginBase implements Listener, CommandExecutor{
    public $e;
    public function onEnable(){
        $this->e = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("Loaded.");
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
        if(isset($args[1])){
            if($sender->hasPermission("disguiseme.other")){
                if(($p = $this->getServer()->getPlayer($args[1])) instanceof Player){
                    if($this->isDisguised($p->getID())){
                        $this->destroyDisguise($p->getID());
                        $sender->sendMessage("Disguise closed for " . $p->getName());
                        $p->sendMessage("Your disguise has been closed.");
                        return true;
                    }
                    else{
                        $s = new DisguiseSession($p, $args[0]);
                        $this->e[$p->getID()] = $s;
                        $sender->sendMessage("Disguise activated for " . $p->getName());
                        return true;
                    }
                }
                else{
                    $sender->sendMessage("Player not found.");
                    return true;
                }
            }
            else{
                $sender->sendMessage("You do not have permission to disguise others.");
                return true;
            }
        }
        else{
            if(isset($args[0]) && $sender instanceof Player){
                if($this->isDisguised($sender->getID())){
                    $this->destroyDisguise($sender->getID());
                    $sender->sendMessage("Disguise closed.");
                    return true;
                }
                else{
                    $s = new DisguiseSession($sender, $args[0]);
                    $this->e[$sender->getID()] = $s;
                    $sender->sendMessage("Disguise activated.");
                    return true;
                }
            }
            else{
                $sender->sendMessage("You need to specify disguise ID.");
                return true;
            }
        }
    }
    public function onPacketSend(DataPacketSendEvent $event){

        if(isset($event->getPacket()->eid)){
            if($this->isDisguised($event->getPacket()->eid) && !$event->getPlayer()->hasPermission("disguiseme.exempt")){
              if($event->getPacket() instanceof MovePlayerPacket){
                      $pk = new MoveEntityPacket;
                      $pk->entities = [[$event->getPacket()->eid, $event->getPacket()->x, $event->getPacket()->y, $event->getPacket()->z, $event->getPacket()->yaw, $event->getPacket()->pitch]];
                      $event->getPlayer()->dataPacket($pk);
                      $event->setCancelled();
              }
              elseif($event->getPacket() instanceof AddPlayerPacket){
                      $pk = new AddMobPacket;
                      $pk->eid = $event->getPacket()->eid;
                      $pk->type = $this->e[$event->getPacket()->eid]->getType();
                      $pk->x = $event->getPacket()->x;
                      $pk->y = $event->getPacket()->y;
                      $pk->z = $event->getPacket()->z;
                      $pk->pitch = $event->getPacket()->pitch;
                      $pk->yaw = $event->getPacket()->yaw;
                      $pk->metadata = $event->getPacket()->metadata;
                      $event->getPlayer()->dataPacket($pk);
                      $event->setCancelled();
              }
              elseif($event->getPacket() instanceof RemovePlayerPacket){
                      $pk = new RemoveEntityPacket;
                      $pk->eid = $event->getPacket()->eid;
                      $event->getPlayer()->dataPacket($pk);
                      $event->setCancelled();
              }
           }
        }
    }
    public function isDisguised($eid){
        return (isset($this->e[$eid]));
    }
    public function onQuit(PlayerQuitEvent $event){
        if($this->isDisguised($event->getPlayer()->getID())){
            $this->destroyDisguise($event->getPlayer()->getID());
        }
    }
    public function onDisable(){
        $this->getLogger()->info("Closing disguise sessions.");
        foreach($this->e as $eid => $s){
            $this->destroyDisguise($eid);
        }
    }
    public function destroyDisguise($i){
        if(isset($this->e[$i])){
            $this->e[$i]->despawnDisguise();
            $p = $this->e[$i]->getPlayer();
            unset($this->e[$i]);
            $p->spawnToAll();
        }
    }
}