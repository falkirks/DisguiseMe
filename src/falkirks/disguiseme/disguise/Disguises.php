<?php
namespace falkirks\disguiseme\disguise;


use pocketmine\Player;

class Disguises{
    private $disguises;

    /**
     * Disguises constructor.
     */
    public function __construct(){
        $this->disguises = [];
    }

    public function registerDisguise($name, string $class){
        if(is_subclass_of($class, Disguise::class)){
            $this->disguises[$name] = $class;
        }
    }

    public function makeDisguise($name, Player $player, $params = []){
        if(isset($this->disguises[$name])) {
            try {
                $class = $this->disguises[$name];
                return new $class($player, ...$params);
            }
            catch(\Exception $exception){
                return null;
            }
        }
        else{
            return null;
        }
    }

    public function disguiseExists($name){
        return isset($this->disguises[$name]);
    }
}