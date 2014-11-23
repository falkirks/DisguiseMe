<?php
namespace disguiseme;

class MobStore{
    /** @var  DisguiseMe */
    private $plugin;
    private $mobs;
    public function __construct(DisguiseMe $plugin){
        $this->plugin = $plugin;
        $this->mobs = [];
        $mobs = json_decode(file_get_contents($this->plugin->getResourcePath() . "mobs.json"), true);
        foreach($mobs as $id => $names){
            foreach($names as $name) {
                $this->mobs[$name] = $id;
            }
        }
    }
    public function getMobId($name){
        if(isset($this->mobs[strtolower($name)])){
            return $this->mobs[strtolower($name)];
        }
        else{
            return false;
        }
    }
}