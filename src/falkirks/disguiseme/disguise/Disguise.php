<?php
namespace falkirks\disguiseme\disguise;


use pocketmine\event\player\PlayerEvent;
use pocketmine\level\Location;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;

interface Disguise{

    /**
     * Produces the Player who is the subject of this disguise.
     * @return Player
     */
    public function getSubject() : Player;

    /**
     * Initializes the disguise. Should not be shown to anyone.
     * @return mixed
     */
    public function start();

    /**
     * Returns true is the disguise is visible to $player, false otherwise
     * $this->isShownTo($this->getSubject()) === true
     * @param Player $player
     * @return bool
     */
    public function isShownTo(Player $player): bool;

    /**
     * Shows the disguise to a specifc player.
     * @param Player $player
     * @return mixed
     */
    public function showTo(Player $player);

    /**
     * Hides the disguise from a specific player.
     * @param Player $player
     * @return mixed
     */
    public function hideFrom(Player $player);

    /**
     * Show to all players.
     * @return mixed
     */
    public function showToAll();

    /**
     * Hide from all players.
     * @return mixed
     */
    public function hideFromAll();

    /**
     * Ends the disguise.
     * @return mixed
     */
    public function end();

    public function updatePositionFor(Player $target, MovePlayerPacket $movePlayerPacket);

}