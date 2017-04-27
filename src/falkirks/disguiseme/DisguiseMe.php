<?php
namespace falkirks\disguiseme;


use falkirks\disguiseme\disguise\Disguises;
use falkirks\disguiseme\disguise\GenericMobDisguise;
use falkirks\disguiseme\listener\PacketSendListener;
use falkirks\disguiseme\listener\PlayerLeaveListener;
use falkirks\disguiseme\command\DisguiseCommand;
use pocketmine\plugin\PluginBase;

class DisguiseMe extends PluginBase {
    /** @var  DisguiseCommand */
    private $disguiseCommand;
    /** @var  DisguiseManager */
    private $disguiseManager;
    /** @var  PlayerLeaveListener */
    private $playerLeaveListener;
    /** @var  PacketSendListener */
    private $packetSendListener;
    /** @var  Disguises */
    private $disguiseStore;

    public function onEnable(){
        $this->disguiseStore = new Disguises();
        $this->disguiseManager = new DisguiseManager($this);
        $this->disguiseCommand = new DisguiseCommand($this);
        $this->playerLeaveListener = new PlayerLeaveListener($this);
        $this->packetSendListener = new PacketSendListener($this);

        $this->disguiseStore->registerDisguise("id", GenericMobDisguise::class);

        $this->getServer()->getCommandMap()->register("disguiseme", $this->disguiseCommand);
        $this->getServer()->getPluginManager()->registerEvents($this->playerLeaveListener, $this);
        $this->getServer()->getPluginManager()->registerEvents($this->packetSendListener, $this);
    }

    /**
     * @return DisguiseCommand
     */
    public function getDisguiseCommand(): DisguiseCommand{
        return $this->disguiseCommand;
    }

    /**
     * @return DisguiseManager
     */
    public function getDisguiseManager(): DisguiseManager{
        return $this->disguiseManager;
    }

    /**
     * @return PlayerLeaveListener
     */
    public function getPlayerLeaveListener(): PlayerLeaveListener{
        return $this->playerLeaveListener;
    }

    /**
     * @return PacketSendListener
     */
    public function getPacketSendListener(): PacketSendListener{
        return $this->packetSendListener;
    }

    /**
     * @return Disguises
     */
    public function getDisguiseStore(): Disguises{
        return $this->disguiseStore;
    }





}