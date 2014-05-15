<?php
/*
__PocketMine Plugin__
 name=Disguise
 description=Disguise as animals and stuff :)
 version=0.1.2
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
    $this->api->console->register("d", "Disguise as a mob", array($this, "command"));
    $this->d = array();
  }

  public function __destruct() {}
  public function command($cmd, $params, $issuer){
    if(!($issuer instanceof Player)) return "You are using it wrong :(";
    if(in_array($issuer->entity->eid, $this->d)) return $this->closeDisguise($issuer);
    else return $this->enableDisguise($issuer,$params[0]);
  }
  public function renderDisguises($data){
   foreach ($this->d as $eid => $type) {
    $e = $this->api->entity->get($eid);
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
  public function closeDisguise($p){
   unset($this->d[$p->eid]);
  }
  public function enableDisguise($p,$e){

        $pk = new AddMobPacket;
        $pk->eid = $p->entity->eid;
        $pk->type = 35; //Test with el creeper
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

        $this->d[$p->entity->eid] = 35;

  }
  public function sendPacket($p,$pk){
    foreach($this->api->player->getAll($p->level) as $i) if($i->eid != $p->eid) $i->dataPacket($pk);
  }
}
