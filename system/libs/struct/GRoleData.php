<?php

namespace system\libs\struct;

use system\data\config;
use system\libs\stream;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class GRoleData
{

    // public $opcode = NULL;
    // public $len = NULL;
    // public $localsid = NULL;
    // public $retcode = NULL;
    public $base = NULL;
    public $status = NULL;
    public $pocket = NULL;
    public $equipment = NULL;
    public $storehouse = NULL;
    public $task = NULL;

    //get char info

    function getBase($octetToInt = true)
    {
        $base = new roleBase();
        $base->version = stream::readByte();
        $base->id = stream::readInt32();
        $base->name = stream::readString();
        $base->race = stream::readInt32();
        $base->cls = stream::readInt32();
        $base->gender = stream::readByte();
        $base->custom_data = stream::readOctets(true);
       // system::debug($this->getCustomData($base->custom_data));
        //$base->custom_data = 0;
        $base->config_data = stream::readOctets($octetToInt);
        $base->custom_stamp = stream::readInt32();
        $base->status = stream::readByte();
        $base->delete_time = stream::readInt32();
        $base->create_time = stream::readInt32();
        $base->lastlogin_time = stream::readInt32();
        $base->forbid = $this->getForbids();
        $base->help_states = stream::readOctets($octetToInt);
        $base->spouse = stream::readInt32();
        $base->userid = stream::readInt32();
        $base->cross_data = stream::readOctets($octetToInt);
        $base->reserved2_ = stream::readOctets($octetToInt);
        $base->reserved3 = stream::readOctets($octetToInt);
        $base->reserved4 = stream::readOctets($octetToInt);
        return $base;
    }

    function getStatus($octetToInt = true)
    {
        $status = new roleStatus();
        $status->version = stream::readByte();
        $status->level = stream::readInt32();
        $status->level2 = stream::readInt32();
        $status->exp = stream::readInt32();
        $status->sp = stream::readInt32();
        $status->pp = stream::readInt32();
        $status->hp = stream::readInt32();
        $status->mp = stream::readInt32();
        $status->posx = stream::readSingle();
        $status->posy = stream::readSingle();
        $status->posz = stream::readSingle();
        $status->worldtag = stream::readInt32();
        $status->invader_state = stream::readInt32();
        $status->invader_time = stream::readInt32();
        $status->pariah_time = stream::readInt32();
        $status->reputation = stream::readInt32();
        $status->custom_status = stream::readOctets($octetToInt);
        $status->filter_data = stream::readOctets($octetToInt);
        $status->charactermode = stream::readOctets($octetToInt);
        $status->instancekeylist = stream::readOctets($octetToInt);
        $status->dbltime_expire = stream::readInt32();
        $status->dbltime_mode = stream::readInt32();
        $status->dbltime_begin = stream::readInt32();
        $status->dbltime_used = stream::readInt32();
        $status->dbltime_max = stream::readInt32();
        $status->time_used = stream::readInt32();
        $status->dbltime_data = stream::readOctets($octetToInt);
        $status->storesize = stream::readInt16();
        $status->petcorral = stream::readOctets($octetToInt);
        $status->property = $this->getProperty(stream::readOctets());
        $status->var_data = $this->getVarData(stream::readOctets());
        $status->skills = stream::readOctets($octetToInt);
        $status->storehousepasswd = stream::readOctets($octetToInt);
        $status->waypointlist = stream::readOctets($octetToInt);
        $status->coolingtime = stream::readOctets($octetToInt);
        $status->npc_relation = stream::readOctets($octetToInt);
        $status->multi_exp_ctrl = stream::readOctets($octetToInt);
        $status->storage_task = stream::readOctets($octetToInt);
        $status->faction_contrib = stream::readOctets($octetToInt);
        $status->force_data = stream::readOctets($octetToInt);
        $status->online_award = stream::readOctets($octetToInt);
        $status->profit_time_data = stream::readOctets($octetToInt);
        $status->country_data = stream::readOctets($octetToInt);
        $status->king_data = stream::readOctets($octetToInt);
        $status->meridian_data = $this->getMeridian(stream::readOctets());
        $status->extraprop = stream::readOctets($octetToInt);
        $status->title_data = stream::readOctets($octetToInt);
        $status->reincarnation_data = stream::readOctets($octetToInt);
        $status->realm_data = stream::readOctets($octetToInt);
        $status->reserved2 = stream::readByte();
        $status->reserved3 = stream::readByte();
        return $status;
    }

    function getPocket($octetToInt = true)
    {
        $pocket = new rolePocket();
        $pocket->capacity = stream::readInt32();
        $pocket->timestamp = stream::readInt32();
        $pocket->money = stream::readInt32();
        $count = stream::readCUint32();
        for ($i = 0; $i < $count; $i++)
            $pocket->items[$i] = $this->getItems($octetToInt);
        $pocket->reserved1 = stream::readInt32();
        $pocket->reserved2 = stream::readInt32();
        return $pocket;
    }

    function getEquipment($octetToInt = true)
    {
        $equipment = new roleEquipment();
        $count = stream::readCUint32();
        for ($e = 0; $e < $count; $e++) {
            $equipment->items[$e] = $this->getItems($octetToInt);
        }
        return $equipment;
    }

    function getStorehouse($octetToInt = true)
    {
        $storehouse = new roleStorehouse();
        $storehouse->capacity = stream::readInt32();
        $storehouse->money = stream::readInt32();
        $count = stream::readCUint32();
        for ($s = 0; $s < $count; $s++) {
            $storehouse->items[$s] = $this->getItems($octetToInt);
        }
        $storehouse->size1 = stream::readByte();
        $storehouse->size2 = stream::readByte();
        $count = stream::readCUint32();
        for ($d = 0; $d < $count; $d++) {
            $storehouse->dress[$d] = $this->getItems($octetToInt);
        }
        $count = stream::readCUint32();
        for ($m = 0; $m < $count; $m++) {
            $storehouse->material[$m] = $this->getItems($octetToInt);
        }
        $storehouse->size3 = stream::readByte();
        $count = stream::readCUint32();
        for ($g = 0; $g < $count; $g++) {
            $storehouse->generalcard[$g] = $this->getItems($octetToInt);
        }
        $storehouse->reserved = stream::readInt16();
        return $storehouse;
    }

    function getTask($octetToInt = true)
    {
        $task = new roleTask();
        $task->task_data = stream::readOctets($octetToInt);
        $task->task_complete = stream::readOctets($octetToInt);
        $task->task_finishtime = stream::readOctets($octetToInt);
        $count = stream::readCUint32();
        for ($t = 0; $t < $count; $t++) {
            $task->task_inventory[$t] = $this->getItems($octetToInt);
        }
        return $task;
    }

    function getListRoles($id)
    {
        stream::writeInt32(2147483648);
        stream::writeInt32($id);
        stream::pack(0xD49);
        $roles = new userRoles();
        if (stream::Send(config::$serverIP, config::$dbPort)) {
            stream::$p = 2;
            stream::readCUint32();
            stream::readInt32();
            stream::readInt32();
            $roles->count = stream::readByte();
            $roles->roles = array();

            for ($i = 0; $i < $roles->count; $i++) {
                $roles->roles[$i] = new userRole();
                $roles->roles[$i]->id = stream::readInt32();
                $roles->roles[$i]->name = stream::readString();
            }
        }
        return $roles;
    }

    function getRoleFaction($id){

        stream::$p = 0;
        stream::writeInt32($id,true, -1, 1);
        stream::pack(0x11FF);
        stream::Send(config::$serverIP, config::$dbPort);

        $role = new roleFaction();
        $role->opcode = stream::readCUint32();
        $role->status = stream::readCUint32();
        $role->unc1 = stream::readInt32();
        $role->unc2 = stream::readInt32();
        $role->rid = stream::readInt32();
        $role->name = stream::readString();
        $role->fid = stream::readInt32();
        $role->cls = stream::readByte();
        $role->race = stream::readByte();
        $role->delayexpel = stream::readOctets(true);
        $role->extend = stream::readOctets(true);
        $role->title = stream::readString();

        return $role;
    }
    
    function getRoleFactionInfo(){

        stream::$p = 0;
        stream::writeInt32(1024, true, -1, 1);
        stream::pack(0x11FE);
        stream::Send(config::$serverIP,  config::$dbPort);

        $role = new roleFactionInfo();
        $role->opcode = stream::readCUint32();
        $role->status = stream::readCUint32();
        $role->unc1 = stream::readInt32();
        $role->unc2 = stream::readInt32();
        $role->fid = stream::readInt32();
        $role->name = stream::readString();
        $role->level = stream::readByte();
        $role->master_id = stream::readInt32();
        $role->master_role = stream::readByte();
        $role->count = stream::readCUint32();
        for ($i = 0; $role->count > $i; $i++){
            $role->members[$i] = new factionMember();
            $role->members[$i]->rid = stream::readInt32();
            $role->members[$i]->role = stream::readByte();
        }
        $role->announce = stream::readString();
        $role->sysinfo = stream::readOctets(true);
        stream::$p = 0;

        return $role;
    }

    //functions
    function getItems($octetToInt = true)
    {
        $item = new roleItem();
        $item->id = stream::readInt32();
        $item->pos = stream::readInt32();
        $item->count = stream::readInt32();
        $item->max_count = stream::readInt32();
        $item->data = stream::readOctets($octetToInt);
        $item->proctype = stream::readInt32();
        $item->expire_date = stream::readInt32();
        $item->guid1 = stream::readInt32();
        $item->guid2 = stream::readInt32();
        $item->mask = stream::readInt32();
        return $item;
    }

    function getForbid()
    {
        $forbid = new roleForbid();
        $forbid->type = stream::readByte();
        $forbid->time = stream::readInt32();
        $forbid->createtime = stream::readInt32();
        $forbid->reason = stream::readString();
        return $forbid;
    }

    function getForbids()
    {
        $forbids = new roleForbids();
        $forbids->count = stream::readCUint32();
        $forbids->forbids = array();
        for ($i = 0; $i < $forbids->count; $i++) {
            $forbids->forbids[$i] = $this->getForbid();
        }
        return $forbids;
    }

    function getMeridian($data)
    {
        $meridian = new roleMeridian();
        stream::putRead($data);
        $meridian->lvl = stream::readInt32();
        $meridian->life_dot = stream::readInt32();
        $meridian->die_dot = stream::readInt32();
        $meridian->free_up = stream::readInt32();
        $meridian->paid_up = stream::readInt32();
        $meridian->login_time = stream::readInt32();
        $meridian->login_days = stream::readInt32();
        $meridian->trigrams_map1 = stream::readInt32();
        $meridian->trigrams_map2 = stream::readInt32();
        $meridian->trigrams_map3 = stream::readInt32();
        $meridian->reserved1 = stream::readInt32();
        $meridian->reserved2 = stream::readInt32();
        $meridian->reserved3 = stream::readInt32();
        $meridian->reserved4 = stream::readInt32();
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        return $meridian;
    }

    function getVarData($data)
    {
        $varData = new roleVarData();
        stream::putRead($data);
        $varData->version = stream::readInt32(false);
        $varData->pk_count = stream::readInt32(false);
        $varData->pvp_cooldown = stream::readInt32(false);
        $varData->pvp_flag = stream::readByte();
        $varData->dead_flag = stream::readByte();
        $varData->is_drop = stream::readByte();
        $varData->resurrect_state = stream::readByte();
        $varData->resurrect_exp_reduce = stream::readSingle(false);
        $varData->instance_hash_key1 = stream::readInt32(false);
        $varData->instance_hash_key2 = stream::readInt32(false);
        $varData->trashbox_size = stream::readInt32(false);
        $varData->last_instance_timestamp = stream::readInt32(false);
        $varData->last_instance_tag = stream::readInt32(false);
        $varData->last_instance_pos_x = stream::readSingle(false);
        $varData->last_instance_pos_y = stream::readSingle(false);
        $varData->last_instance_pos_z = stream::readSingle(false);
        $varData->dir = stream::readInt32(false);
        $varData->resurrect_hp_factor = stream::readSingle(false);
        $varData->resurrect_mp_factor = stream::readSingle(false);
        if (config::$version == 153) {
            $varData->instance_reenter = stream::readInt32(false);
            $varData->last_world_type = stream::readInt32(false);
            $varData->last_logout_pos_x = stream::readSingle(false);
            $varData->last_logout_pos_y = stream::readSingle(false);
            $varData->last_logout_pos_z = stream::readSingle(false);
        }

        stream::putRead(stream::$readData_copy, stream::$p_copy);
        return $varData;
    }

    function getProperty($data)
    {
        $property = new roleProperty();
        stream::putRead($data);
        $property->vitality = stream::readInt32(false);
        $property->energy = stream::readInt32(false);
        $property->strength = stream::readInt32(false);
        $property->agility = stream::readInt32(false);
        $property->max_hp = stream::readInt32(false);
        $property->max_mp = stream::readInt32(false);
        $property->hp_gen = stream::readInt32(false);
        $property->mp_gen = stream::readInt32(false);
        $property->walk_speed = stream::readSingle(false);
        $property->run_speed = stream::readSingle(false);
        $property->swim_speed = stream::readSingle(false);
        $property->flight_speed = stream::readSingle(false);
        $property->attack = stream::readInt32(false);
        $property->damage_low = stream::readInt32(false);
        $property->damage_high = stream::readInt32(false);
        $property->attack_speed = stream::readInt32(false);
        $property->attack_range = stream::readSingle(false);
        $property->addon_damage_low = array(
            "a_l_d_metal" => stream::readInt32(false),
            "a_l_d_tree" => stream::readInt32(false),
            "a_l_d_water" => stream::readInt32(false),
            "a_l_d_fire" => stream::readInt32(false),
            "a_l_d_ground" => stream::readInt32(false)
        );
        $property->addon_damage_high = array(
            "a_h_d_metal" => stream::readInt32(false),
            "a_h_d_tree" => stream::readInt32(false),
            "a_h_d_water" => stream::readInt32(false),
            "a_h_d_fire" => stream::readInt32(false),
            "a_h_d_ground" => stream::readInt32(false)
        );
        $property->damage_magic_low = stream::readInt32(false);
        $property->damage_magic_high = stream::readInt32(false);
        $property->resistance = array(
            "protection_from_metal" => stream::readInt32(false),
            "protection_from_tree" => stream::readInt32(false),
            "protection_from_water" => stream::readInt32(false),
            "protection_from_fire" => stream::readInt32(false),
            "protection_from_ground" => stream::readInt32(false)
        );
        $property->defense = stream::readInt32(false);
        $property->armor = stream::readInt32(false);
        $property->max_ap = stream::readInt32(false);
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        return $property;

    }

    function getCustomData($data){
        $customData = new roleCustomData();
        stream::putRead($data);
        $customData->Header = stream::readInt32();
        //[3Parts]
        $customData->scaleUp = stream::readByte();
        $customData->scaleMiddle = stream::readByte();
        $customData->scaleDown = stream::readByte();
        //[BlendFace]
        $customData->idFaceShape1 =stream::readInt16();
        $customData->idFaceShape2 =stream::readInt16();
        $customData->blendFaceShape =stream::readInt16();
        //[Face]
        $customData->scaleFaceH = stream::readByte();
        $customData->scaleFaceV = stream::readByte();
        $customData->idFaceTex = stream::readInt16();
        //[Forehead]
        $customData->offsetForeheadH = stream::readInt16();
        $customData->offsetForeheadV = stream::readByte();
        $customData->offsetForeheadZ = stream::readByte();
        $customData->rotateForehead = stream::readByte();
        $customData->scaleForehead = stream::readByte();
        //YokeBone
        $customData->offsetYokeBoneH = stream::readByte();
        $customData->offsetYokeBoneV = stream::readByte();
        $customData->offsetYokeBoneZ = stream::readByte();
        $customData->rotateYokeBone = stream::readByte();
        $customData->scaleYokeBone = stream::readByte();
        //[Cheek]
        $customData->offsetCheekH = stream::readByte();
        $customData->offsetCheekV = stream::readByte();
        $customData->offsetCheekZ = stream::readByte();
        $customData->scaleCheek = stream::readByte();
        //[Chain]
        $customData->offsetChainV = stream::readByte();
        $customData->offsetChainZ = stream::readByte();
        $customData->rotateChain = stream::readByte();
        $customData->scaleChainH = stream::readByte();
        //[Jaw]
        $customData->offsetJawH = stream::readByte();
        $customData->offsetJawV = stream::readByte();
        $customData->offsetJawZ = stream::readByte();
        $customData->scaleJawSpecial = stream::readByte();
        $customData->scaleJawH = stream::readByte();
        $customData->scaleJawV = stream::readByte();
        //[Eye]
        $customData->idEyeShape = stream::readByte();
        $customData->idEyeBaseTex = stream::readInt16();
        $customData->idEyeHighTex = stream::readInt16();
        $customData->idThirdEye =stream::readByte();
        $customData->idEyeBallTex = stream::readInt16();
        $customData->scaleEyeH =stream::readByte();
        $customData->scaleEyeV =stream::readByte();
        $customData->rotateEye =stream::readByte();
        $customData->offsetEyeH =stream::readByte();
        $customData->offsetEyeV =stream::readByte();
        $customData->offseteyeZ =stream::readByte();
        $customData->scaleEyeBall =stream::readByte();
        $customData->scaleEyeH2 =stream::readByte();
        $customData->scaleEyeV2 =stream::readByte();
        $customData->rotateEye2 =stream::readByte();
        $customData->offsetEyeH2 =stream::readByte();
        $customData->offsetEyeV2 =stream::readByte();
        $customData->offseteyeZ2 =stream::readByte();
        $customData->scaleEyeBall2=stream::readByte();
        //[Brow]
        $customData->idBrowTex = stream::readInt16();
        $customData->idBrowShape = stream::readInt16();
        $customData->scaleBrowH =stream::readByte();
        $customData->scaleBrowV =stream::readByte();
        $customData->rotateBrow =stream::readByte();
        $customData->offsetBrowH =stream::readByte();
        $customData->offsetBrowV =stream::readByte();
        $customData->offsetBrowZ =stream::readByte();
        $customData->scaleBrowH2 =stream::readByte();
        $customData->scaleBrowV2 =stream::readByte();
        $customData->rotateBrow2 =stream::readByte();
        $customData->offsetBrowH2 =stream::readByte();
        $customData->offsetBrowV2 =stream::readByte();
        $customData->offsetBrowZ2 =stream::readByte();
        //[Nose]
        $customData->idNoseTex =stream::readInt16();
        $customData->idNoseTipShape =stream::readInt16();
        $customData->scaleNoseTipH =stream::readByte();
        $customData->scaleNoseTipV =stream::readByte();
        $customData->offsetNoseTipV =stream::readByte();
        $customData->scaleNoseTipZ =stream::readByte();
        $customData->idNoseBridgeShap = stream::readInt16();
        $customData->scaleBridgeTipH =stream::readByte();
        $customData->offsetBridgeTipZ =stream::readByte();
        //[Mouth]
        $customData->idMouthUpLipLine =stream::readInt16();
        $customData->idMouthMidLipLine =stream::readInt16();
        $customData->idMouthDownLipLine =stream::readInt16();
        $customData->thickUpLip =stream::readByte();
        $customData->thickDownLip =stream::readByte();
        $customData->offsetMouthV =stream::readByte();
        $customData->offsetMOuthZ =stream::readByte();
        $customData->idMouthTex =stream::readInt16();
        $customData->scaleMouthH =stream::readByte();
        $customData->scaleMouthH2 =stream::readByte();
        $customData->offsetCornerOfMouthSpecial = stream::readByte();
        $customData->offsetCornerOfMouthSpecial2 = stream::readByte();
        //[Ear]
        $customData->idEarShape = stream::readInt16();
        $customData->scaleEar = stream::readByte();
        $customData->offsetEarV = stream::readByte();
        //[Hair]
        $customData->idHairModel = stream::readInt16();
        $customData->idHairTex =stream::readInt16();
        //[Moustache]
        $customData->idMoustacheTex = stream::readInt16();
        $customData->idMoustacheSkin = stream::readInt16();
        $customData->idGoateeTex = stream::readInt16();
        //[Faling]
        $customData->idFalingSkin =stream::readInt16();
        $customData->idFalingTex = stream::readInt16();
        //unk
        $customData->bodyID = stream::readInt16();
        //[Color]
        $customData->colorFace =stream::readInt32();
        $customData->colorEye =stream::readInt32();
        $customData->colorBrow =stream::readInt32();
        $customData->colorMouth =stream::readInt32();
        $customData->colorHair =stream::readInt32();
        $customData->colorEyeBall =stream::readInt32();
        $customData->colorMoustache =stream::readInt32();
        $customData->Unknown2 =stream::readInt32();
        $customData->Unknown3 =stream::readInt32();

        $customData->colorBody = stream::readInt32();
        $customData->headScale = stream::readByte();
        $customData->upScale = stream::readByte();
        $customData->waistScal = stream::readByte();
        $customData->armWidth =stream::readByte();
        $customData->legWidth =stream::readByte();

        $customData->Unknown4 = stream::readByte();
        $customData->Unknown5 = stream::readByte();
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        return $customData;
    }

    //put char info

    function putBase($data, $octetToData)
    {
        stream::writeByte($data->base->version);
        stream::writeInt32($data->base->id);
        stream::writeString($data->base->name);
        stream::writeInt32($data->base->race);
        stream::writeInt32($data->base->cls);
        stream::writeByte($data->base->gender);
        stream::writeOctets($data->base->custom_data, $octetToData);
        stream::writeOctets($data->base->config_data, $octetToData);
        stream::writeInt32($data->base->custom_stamp);
        stream::writeByte($data->base->status);
        stream::writeInt32($data->base->delete_time);
        stream::writeInt32($data->base->create_time);
        stream::writeInt32($data->base->lastlogin_time);
        $this->putForbids($data->base->forbid);
        stream::writeOctets($data->base->help_states, $octetToData);
        stream::writeInt32($data->base->spouse);
        stream::writeInt32($data->base->userid);
        stream::writeOctets($data->base->cross_data, $octetToData);
        stream::writeOctets($data->base->reserved2_, $octetToData);
        stream::writeOctets($data->base->reserved3, $octetToData);
        stream::writeOctets($data->base->reserved4, $octetToData);
    }

    function putStatus($data, $octetToData)
    {
        stream::writeByte($data->status->version);
        stream::writeInt32($data->status->level);
        stream::writeInt32($data->status->level2);
        stream::writeInt32($data->status->exp);
        stream::writeInt32($data->status->sp);
        stream::writeInt32($data->status->pp);
        stream::writeInt32($data->status->hp);
        stream::writeInt32($data->status->mp);
        stream::writeSingle($data->status->posx);
        stream::writeSingle($data->status->posy);
        stream::writeSingle($data->status->posz);
        stream::writeInt32($data->status->worldtag);
        stream::writeInt32($data->status->invader_state);
        stream::writeInt32($data->status->invader_time);
        stream::writeInt32($data->status->pariah_time);
        stream::writeInt32($data->status->reputation);
        stream::writeOctets($data->status->custom_status, $octetToData);
        stream::writeOctets($data->status->filter_data, $octetToData);
        stream::writeOctets($data->status->charactermode, $octetToData);
        stream::writeOctets($data->status->instancekeylist, $octetToData);
        stream::writeInt32($data->status->dbltime_expire);
        stream::writeInt32($data->status->dbltime_mode);
        stream::writeInt32($data->status->dbltime_begin);
        stream::writeInt32($data->status->dbltime_used);
        stream::writeInt32($data->status->dbltime_max);
        stream::writeInt32($data->status->time_used);
        stream::writeOctets($data->status->dbltime_data, $octetToData);
        stream::writeInt16($data->status->storesize, $octetToData);
        stream::writeOctets($data->status->petcorral, $octetToData);
        stream::writeOctets($this->putProperty($data->status->property));
        stream::writeOctets($this->putVarData($data->status->var_data));
        stream::writeOctets($data->status->skills, $octetToData);
        stream::writeOctets($data->status->storehousepasswd, $octetToData);
        stream::writeOctets($data->status->waypointlist, $octetToData);
        stream::writeOctets($data->status->coolingtime, $octetToData);
        stream::writeOctets($data->status->npc_relation, $octetToData);
        stream::writeOctets($data->status->multi_exp_ctrl, $octetToData);
        stream::writeOctets($data->status->storage_task, $octetToData);
        stream::writeOctets($data->status->faction_contrib, $octetToData);
        stream::writeOctets($data->status->force_data, $octetToData);
        stream::writeOctets($data->status->online_award, $octetToData);
        stream::writeOctets($data->status->profit_time_data, $octetToData);
        stream::writeOctets($data->status->country_data, $octetToData);
        stream::writeOctets($data->status->king_data, $octetToData);
        stream::writeOctets($this->putMeridian($data->status->meridian_data));
        stream::writeOctets($data->status->extraprop, $octetToData);
        stream::writeOctets($data->status->title_data, $octetToData);
        stream::writeOctets($data->status->reincarnation_data, $octetToData);
        stream::writeOctets($data->status->realm_data, $octetToData);
        stream::writeByte($data->status->reserved2);
        stream::writeByte($data->status->reserved3);
    }

    function putPocket($data, $octetToData)
    {
        stream::writeInt32($data->pocket->capacity);
        stream::writeInt32($data->pocket->timestamp);
        stream::writeInt32($data->pocket->money);
        if (is_array($data->pocket->items)) {
            stream::$writeData .= stream::cuint(count($data->pocket->items));
            foreach ($data->pocket->items as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
        stream::writeInt32($data->pocket->reserved1);
        stream::writeInt32($data->pocket->reserved2);
    }

    function putEquipment($data, $octetToData)
    {
        if (is_array($data->equipment->items) > 0) {
            stream::$writeData .= stream::cuint(count($data->equipment->items));
            foreach ($data->equipment->items as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
    }

    function putStorehouse($data, $octetToData)
    {
        stream::writeInt32($data->storehouse->capacity);
        stream::writeInt32($data->storehouse->money);
        if (is_array($data->storehouse->items) > 0) {
            stream::$writeData .= stream::cuint(count($data->storehouse->items));
            foreach ($data->storehouse->items as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
        stream::writeByte($data->storehouse->size1);
        stream::writeByte($data->storehouse->size2);
        if (is_array($data->storehouse->dress) > 0) {
            stream::$writeData .= stream::cuint(count($data->storehouse->dress));
            foreach ($data->storehouse->dress as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
        if (is_array($data->storehouse->material) > 0) {
            stream::$writeData .= stream::cuint(count($data->storehouse->material));
            foreach ($data->storehouse->material as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
        stream::writeByte($data->storehouse->size3);
        if (is_array($data->storehouse->generalcard) > 0) {
            stream::$writeData .= stream::cuint(count($data->storehouse->generalcard));
            foreach ($data->storehouse->generalcard as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);
        stream::writeInt16($data->storehouse->reserved);

    }

    function putTask($data, $octetToData)
    {
        stream::writeOctets($data->task->task_data, $octetToData);
        stream::writeOctets($data->task->task_complete, $octetToData);
        stream::writeOctets($data->task->task_finishtime, $octetToData);
        if (is_array($data->task->task_inventory)) {
            stream::$writeData .= stream::cuint(count($data->task->task_inventory));
            foreach ($data->task->task_inventory as $item) {
                stream::writeInt32($item->id);
                stream::writeInt32($item->pos);
                stream::writeInt32($item->count);
                stream::writeInt32($item->max_count);
                stream::writeOctets($item->data, $octetToData);
                stream::writeInt32($item->proctype);
                stream::writeInt32($item->expire_date);
                stream::writeInt32($item->guid1);
                stream::writeInt32($item->guid2);
                stream::writeInt32($item->mask);
            }
        } else
            stream::$writeData .= stream::cuint(0);

    }

    //functions
    function putProperty($property)
    {
        stream::putWrite("");
        stream::writeInt32($property->vitality, false);
        stream::writeInt32($property->energy, false);
        stream::writeInt32($property->strength, false);
        stream::writeInt32($property->agility, false);
        stream::writeInt32($property->max_hp, false);
        stream::writeInt32($property->max_mp, false);
        stream::writeInt32($property->hp_gen, false);
        stream::writeInt32($property->mp_gen, false);
        stream::writeSingle($property->walk_speed, false);
        stream::writeSingle($property->run_speed, false);
        stream::writeSingle($property->swim_speed, false);
        stream::writeSingle($property->flight_speed, false);
        stream::writeInt32($property->attack, false);
        stream::writeInt32($property->damage_low, false);
        stream::writeInt32($property->damage_high, false);
        stream::writeInt32($property->attack_speed, false);
        stream::writeSingle($property->attack_range, false);
        foreach ($property->addon_damage_low as $key => $value) {
            stream::writeInt32($value, false);
        }
        foreach ($property->addon_damage_high as $key1 => $value1) {
            stream::writeInt32($value1, false);
        }
        stream::writeInt32($property->damage_magic_low, false);
        stream::writeInt32($property->damage_magic_high, false);
        foreach ($property->resistance as $key2 => $value2) {
            stream::writeInt32($value2, false);
        }
        stream::writeInt32($property->defense, false);
        stream::writeInt32($property->armor, false);
        stream::writeInt32($property->max_ap, false);

        stream::putWrite(stream::$writeData_copy);
        return stream::$writeData_copy;
    }

    function putVarData($varData)
    {
        stream::putWrite("");
        stream::writeInt32($varData->version, false);
        stream::writeInt32($varData->pk_count, false);
        stream::writeInt32($varData->pvp_cooldown, false);
        stream::writeByte($varData->pvp_flag);
        stream::writeByte($varData->dead_flag);
        stream::writeByte($varData->is_drop);
        stream::writeByte($varData->resurrect_state);
        stream::writeSingle($varData->resurrect_exp_reduce, false);
        stream::writeInt32($varData->instance_hash_key1, false);
        stream::writeInt32($varData->instance_hash_key2, false);
        stream::writeInt32($varData->trashbox_size, false);
        stream::writeInt32($varData->last_instance_timestamp, false);
        stream::writeInt32($varData->last_instance_tag, false);
        stream::writeSingle($varData->last_instance_pos_x, false);
        stream::writeSingle($varData->last_instance_pos_y, false);
        stream::writeSingle($varData->last_instance_pos_z, false);
        stream::writeInt32($varData->dir, false);
        stream::writeSingle($varData->resurrect_hp_factor, false);
        stream::writeSingle($varData->resurrect_mp_factor, false);
        if (config::$version == 153) {
            stream::writeInt32($varData->instance_reenter, false);
            stream::writeInt32($varData->last_world_type, false);
            stream::writeSingle($varData->last_logout_pos_x, false);
            stream::writeSingle($varData->last_logout_pos_y, false);
            stream::writeSingle($varData->last_logout_pos_z, false);
        }
        stream::putWrite(stream::$writeData_copy);
        return stream::$writeData_copy;
    }

    function putMeridian($meridian)
    {
        stream::putWrite("");
        stream::writeInt32($meridian->lvl);
        stream::writeInt32($meridian->life_dot);
        stream::writeInt32($meridian->die_dot);
        stream::writeInt32($meridian->free_up);
        stream::writeInt32($meridian->paid_up);
        stream::writeInt32($meridian->login_time);
        stream::writeInt32($meridian->login_days);
        stream::writeInt32($meridian->trigrams_map1);
        stream::writeInt32($meridian->trigrams_map2);
        stream::writeInt32($meridian->trigrams_map3);
        stream::writeInt32($meridian->reserved1);
        stream::writeInt32($meridian->reserved2);
        stream::writeInt32($meridian->reserved3);
        stream::writeInt32($meridian->reserved4);
        stream::putWrite(stream::$writeData_copy);
        return stream::$writeData_copy;

    }

    function putForbid($forbid)
    {
        stream::writeByte($forbid->type);
        stream::writeInt32($forbid->time);
        stream::writeInt32($forbid->createtime);
        stream::writeString($forbid->reason);
    }

    function putForbids($data)
    {
        stream::writeByte(count($data->forbids));
        foreach ($data->forbids as $i => $val) {
            $this->putForbid($data->forbids[$i]);
        }
    }

}