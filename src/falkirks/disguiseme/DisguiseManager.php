<?php
namespace falkirks\disguiseme;

use falkirks\disguiseme\disguise\Disguise;
use pocketmine\Player;

class DisguiseManager implements \ArrayAccess, \IteratorAggregate, \Countable {
    private $sessions;
    private $api;

    /**
     * DisguiseManager constructor.
     * @param DisguiseMe $api
     */
    public function __construct(DisguiseMe $api){
        $this->api = $api;
        $this->sessions = new \SplObjectStorage();
    }


    public function getIterator(){
        return $this->sessions;
    }

    public function offsetExists($offset){
        $offset = $this->getPlayer($offset);

        return $this->sessions->contains($offset);
    }

    public function offsetGet($offset){
        $offset = $this->getPlayer($offset);

        return $this->sessions->offsetGet($offset);
    }

    public function offsetSet($offset, $value){
        $offset = $this->getPlayer($offset);

        if($value instanceof Disguise && $offset instanceof Player) {
            $this->sessions->attach($offset, $value);
            $value->start();
            $value->showToAll();
        }

    }

    public function offsetUnset($offset){
        $offset = $this->getPlayer($offset);

        if($this->sessions->offsetExists($offset)) {
            $disguise = $this->offsetGet($offset);
            $disguise->hideFromAll();
            $disguise->end();
            $this->sessions->detach($offset);
        }
    }

    public function count(){
        return $this->sessions->count();
    }

    public function add(Disguise $disguise){
        $this->offsetSet($disguise->getSubject(), $disguise);
    }

    /**
     * @return DisguiseMe
     */
    public function getApi(): DisguiseMe{
        return $this->api;
    }

    private function getPlayer($data){
        if($data instanceof Player){
            return $data;
        }
        if(is_numeric($data)){
            foreach($this->getApi()->getServer()->getOnlinePlayers() as $player){
                if($player->getId() === $data){
                    return $player;
                }
            }
            return null;
        }

        if(is_string($data)){
            return $this->getApi()->getServer()->getPlayer($data);
        }
    }



}