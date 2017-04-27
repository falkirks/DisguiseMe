<?php
namespace falkirks\disguiseme\disguise;


use falkirks\disguiseme\exception\InvalidDisguiseException;
use pocketmine\Player;

class MobDisguise extends GenericEntityDisguise {
    private static $mobData = [
        "zombie" => 32,
        "creeper" => 33,
        "skeleton" => 34,
        "spider" => 35,
        "pig_zombie" => 36,
        "slime" => 37,
        "enderman" => 38,
        "silverfish" => 39,
        "cavespider" => 40,
        "ghast" => 41,
        "magmacube" => 42,
        "blaze" => 43,
        "zombie_villager" => 44,
        "witch" => 45,
        "skeleton.stray" => 46,
        "husk" => 47,
        "skeleton.wither" => 48,
        "guardian" => 49,
        "guardian.elder" => 50,
        "wither.boss" => 52,
        "dragon" => 53,
        "shulker" => 54,
        "endermite" => 55,
        "vindicator" => 57,
        "evocation_illager" => 104,
        "chicken" => 10,
        "cow" => 11,
        "pig" => 12,
        "sheep" => 13,
        "wolf" => 14,
        "villager" => 15,
        "mooshroom" => 16,
        "squid" => 17,
        "rabbit" => 18,
        "bat" => 19,
        "iron_golem" => 20,
        "snow_golem" => 21,
        "ocelot" => 22,
        "horse" => 23,
        "donkey" => 24,
        "mule" => 25,
        "skeleton_horse" => 26
    ];

    /**
     * MobDisguise constructor.
     */
    public function __construct(Player $player, $name){
        if(isset(self::$mobData[$name])){
            parent::__construct($player, self::$mobData[$name]);
        }
        else{
            throw new InvalidDisguiseException("There is not mob with that name.");
        }
    }


}