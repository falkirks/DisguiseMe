<?php
namespace falkirks\disguiseme\disguise;


use falkirks\disguiseme\exception\InvalidDisguiseException;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\Player;

class GenericEntityDisguise extends AbstractDisguise {
    /** @var  int */
    protected $type;

    /**
     * GenericEntityDisguise constructor.
     * @param Player $subject
     * @param int $type
     * @throws InvalidDisguiseException
     */
    public function __construct(Player $subject, $type){
        parent::__construct($subject);

        if(!is_numeric($type) || $type < 0){
            throw new InvalidDisguiseException("That entity ID is invalid.");
        }

        $this->type = $type;
    }


    public function showTo(Player $player){
        if(!$this->isShownTo($player)) {
            $pk = new RemoveEntityPacket();
            $pk->eid = $this->getSubject()->getID();

            $pk2 = new AddEntityPacket();
            $pk2->eid = $this->getSubject()->getID();
            $pk2->type = $this->type;
            $pk2->x = $this->getSubject()->getX();
            $pk2->y = $this->getSubject()->getY();
            $pk2->z = $this->getSubject()->getZ();
            $pk2->pitch = $this->getSubject()->pitch;
            $pk2->yaw = $this->getSubject()->yaw;
            $pk2->metadata = [];

            $pk3 = new SetEntityMotionPacket();
            $pk3->entities = [
                [
                    $this->getSubject()->getID(),
                    $this->getSubject()->motionX,
                    $this->getSubject()->motionY,
                    $this->getSubject()->motionZ
                ]
            ];

            $player->dataPacket($pk);
            $player->dataPacket($pk2);
            $player->dataPacket($pk3);

            parent::showTo($player);
            return true;
        }
        return false;
    }

    public function hideFrom(Player $player){
        if($this->isShownTo($player)) {
            $this->getSubject()->despawnFrom($player);
            $this->getSubject()->spawnTo($player);
            parent::hideFrom($player);
            return true;
        }
        return false;
    }

    public function updatePositionFor(Player $target, MovePlayerPacket $movePlayerPacket){
        if($this->isShownTo($target)) {
            $pk = new MoveEntityPacket();
            $pk->x = $movePlayerPacket->x;
            $pk->y = $movePlayerPacket->y;
            $pk->z = $movePlayerPacket->z;
            $target->dataPacket($pk);
            return true;
        }
        return false;
    }

}