<?php

namespace disguiseme;


use pocketmine\network\protocol\AddMobPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\Player;
use pocketmine\Server;

class DisguiseSession {
    private $p, $type;
    public function __construct(Player $player, $type){
        $this->p = $player;
        $this->type = $type;
        $this->startDisguise();
    }
    public function startDisguise(){
        $pk = new RemovePlayerPacket;
        $pk->eid = $this->p->getID();
        $pk->clientID = 0;

        $pk2 = new AddMobPacket;
        $pk2->eid = $this->p->getID();
        $pk2->type = $this->type;
        $pk2->x = $this->p->getX();
        $pk2->y = $this->p->getY();
        $pk2->z = $this->p->getY();
        $pk2->pitch = $this->p->pitch;
        $pk2->yaw = $this->p->yaw;
        $pk2->metadata = [];


        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($p->canSee($this->p) && !$p->hasPermission("disguiseme.exempt") && $p->getName() !== $this->p->getName()){
                $p->dataPacket($pk);
                $p->dataPacket($pk2);
            }
        }
    }
    public function despawnDisguise(){
        $this->p->despawnFromAll();

    }
    public function getType(){
        return $this->type;
    }
    public function getPlayer(){
        return $this->p;
    }
} 