<?php
namespace falkirks\disguiseme\listener;


use falkirks\disguiseme\DisguiseMe;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerLeaveListener implements Listener {
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
     * @priority LOW
     * @ignoreCancelled true
     *
     * @param PlayerQuitEvent $event
     */
    public function onPlayerLeave(PlayerQuitEvent $event){
        if(isset($this->api->getDisguiseManager()[$event->getPlayer()->getName()])){
            $disguise = $this->api->getDisguiseManager()[$event->getPlayer()->getName()];
            $disguise->hideFromAll();
            $disguise->end();
            unset($this->api->getDisguiseManager()[$event->getPlayer()->getName()]);
        }
    }

    /**
     * @return DisguiseMe
     */
    public function getApi(): DisguiseMe{
        return $this->api;
    }

}