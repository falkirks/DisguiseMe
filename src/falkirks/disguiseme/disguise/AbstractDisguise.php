<?php
namespace falkirks\disguiseme\disguise;


use pocketmine\Player;

abstract class AbstractDisguise implements Disguise {
    /** @var  Player */
    protected $subject;
    /** @var  Player[] */
    protected $observers;

    /**
     * AbstractDisguise constructor.
     * @param Player $subject
     */
    public function __construct(Player $subject){
        $this->subject = $subject;
        $this->observers = [];
    }


    public function getSubject(): Player{
        return $this->subject;
    }

    public function start(){
    }

    public function isShownTo(Player $player): bool{
        return $player === $this->subject || array_search($player, $this->observers) !== false;
    }

    public function showToAll(){
        foreach ($this->getSubject()->getServer()->getOnlinePlayers() as $player){
            if($player->canSee($this->getSubject()) && !$player->hasPermission("disguiseme.exempt") && $player !== $this->getSubject() && !$this->isShownTo($player)){
                $this->showTo($player);
            }
        }
    }

    public function hideFromAll(){
        foreach ($this->getSubject()->getServer()->getOnlinePlayers() as $player){
            if($player->canSee($this->getSubject()) && !$player->hasPermission("disguiseme.exempt") && $player !== $this->getSubject() && $this->isShownTo($player)){
                $this->hideFrom($player);
            }
        }
    }

    public function showTo(Player $player){
       $this->observers[] = $player;
    }

    public function hideFrom(Player $player){
        unset($this->observers[array_search($player, $this->observers)]);
    }


    public function end(){
    }
}