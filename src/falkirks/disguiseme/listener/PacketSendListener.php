<?php
namespace falkirks\disguiseme\listener;


use falkirks\disguiseme\DisguiseMe;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;

class PacketSendListener implements Listener {
    /** @var  DisguiseMe */
    private $api;

    /**
     * CreationListener constructor.
     * @param DisguiseMe $api
     */
    public function __construct(DisguiseMe $api){
        $this->api = $api;
    }

    /**
     * @priority HIGH
     * @ignoreCancelled true
     *
     * @param DataPacketSendEvent $event
     */
    public function onDataPacketSend(DataPacketSendEvent $event){
        switch (get_class($event->getPacket())){
            case AddPlayerPacket::class:
                if(isset($this->getApi()->getDisguiseManager()[$event->getPacket()->eid])){
                    if(!$event->getPlayer()->hasPermission("disguiseme.exempt")){
                        if ($this->getApi()->getDisguiseManager()[$event->getPacket()->eid]->showTo($event->getPlayer())) {
                            $event->setCancelled();
                        }
                    }
                }
                break;
            case MovePlayerPacket::class:
                if(isset($this->getApi()->getDisguiseManager()[$event->getPacket()->eid])){
                    if ($this->getApi()->getDisguiseManager()[$event->getPacket()->eid]->updatePositionFor($event->getPlayer(), $event->getPacket())) {
                        $event->setCancelled();
                    }
                }
                break;

        }
    }

    /**
     * @return DisguiseMe
     */
    public function getApi(): DisguiseMe{
        return $this->api;
    }

}