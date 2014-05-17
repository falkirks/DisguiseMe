<?php
/*
__PocketMine Plugin__
 name=Disguise
 description=Disguise as animals and stuff :)
 version=0.0.1
 author=Falk
 class=disguiseMe
 apiversion=10,11,12,13
 */
class disguiseMe implements Plugin {
  private $api, $path, $d;
  public function __construct(ServerAPI $api, $server = false) {
    $this->api = $api;
  }

  public function init() {
    $this->api->addHandler("player.spawn", array($this, "renderDisguises"), 50);
    $this->api->addHandler("player.quit", array($this, "purgeDisguise"), 50);
    $this->api->console->register("d", "Disguise as a mob", array($this, "command"));
    $this->d = array();
  }

  public function __destruct() {}
  public function command($cmd, $params, $issuer){
    if(!($issuer instanceof Player)) return "[Disguise] You can only diguise while in game.";
    if(isset($this->d[$issuer->entity->eid])) return $this->closeDisguise($issuer);
    if(isset($params[0])) return $this->enableDisguise($issuer,$params[0]);
    return "Usage: /d <ID>";
  }
  public function renderDisguises($data){
   foreach ($this->d as $eid => $type) {
        if(($e = $this->api->entity->get($eid)) == false) continue;
        if($e->level !== $data->entity->level) continue;
        $pk = new AddMobPacket;
        $pk->eid = $eid;
        $pk->type = $type; 
        $pk->x = $e->x;
        $pk->y = $e->y;
        $pk->z = $e->z;
        $pk->yaw = $e->yaw;
        $pk->pitch = $e->pitch;
        $pk->metadata = array();       
        $data->dataPacket($pk);

        $pk = new SetEntityMotionPacket;
        $pk->eid = $eid;
        $pk->speedX = $e->speedX;
        $pk->speedY = $e->speedY;
        $pk->speedZ = $e->speedZ;
        $data->dataPacket($pk);
   }
  }
  public function purgeDisguise($p){
    if(isset($this->d[$p->entity->eid])){
        unset($this->d[$p->entity->eid]);
        $pk = new RemoveEntityPacket;
        $pk->eid = $p->entity->eid;
        $this->sendPacket($p->entity,$pk);
    }
  }
  public function closeDisguise($p){
        $p->sendChat("[Disguise] Disguise closing, you will be kicked.");
        $p->close("Closing Disguise");

  }
  public function enableDisguise($p,$e){
        $pk = new AddMobPacket;
        $pk->eid = $p->entity->eid;
        $pk->type = $e; //Test with el creeper
        $pk->x = $p->entity->x;
        $pk->y = $p->entity->y;
        $pk->z = $p->entity->z;
        $pk->yaw = $p->entity->yaw;
        $pk->pitch = $p->entity->pitch;
        $pk->metadata = array();       
        $this->sendPacket($p->entity,$pk);

        $pk = new SetEntityMotionPacket;
        $pk->eid = $p->entity->eid;
        $pk->speedX = $p->entity->speedX;
        $pk->speedY = $p->entity->speedY;
        $pk->speedZ = $p->entity->speedZ;
        $this->sendPacket($p->entity,$pk);

        $this->d[$p->entity->eid] = $e;
        return "[Disguise] Disguise enabled.";

  }
  public function sendPacket($p,$pk){
    foreach($this->api->player->getAll($p->level) as $i) if($i->eid != $p->eid) $i->dataPacket($pk);
  }
}
