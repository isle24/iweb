<?php

namespace system\libs;

use system\libs\struct\GRoleData;
use system\libs\struct\roleBase;
use system\libs\struct\roleEquipment;
use system\libs\struct\roleForbid;
use system\libs\struct\roleForbids;
use system\libs\struct\roleItem;
use system\libs\struct\roleMeridian;
use system\libs\struct\rolePocket;
use system\libs\struct\roleProperty;
use system\libs\struct\roleStatus;
use system\libs\struct\roleStorehouse;
use system\libs\struct\roleTask;
use system\libs\struct\roleVarData;

if (!defined('IWEB')) {
    die("Error!");
}

class ArrayToXml
{

    static function toXML($data, $rootName = "role")
    {
        if (is_array($data) || is_object($data)) {
            $dom = new \DOMDocument("1.0");
            $root = $dom->appendChild($dom->createElement($rootName));

            foreach ($data as $key => $value) {
                $root2 = $root->appendChild($dom->createElement($key));
                if (is_object($value) || is_array($value)) {


                    foreach ($value as $key2 => $value2) {
                        if (is_object($value2) || is_array($value2)) {
                            $root3 = $root2->appendChild($dom->createElement($key2));

                            foreach ($value2 as $key3 => $value3) {
                                if (is_numeric($key3)) $key3 = "item";
                                if (is_object($value3) || is_array($value3)) {

                                    $root4 = $root3->appendChild($dom->createElement($key3));

                                    foreach ($value3 as $key4 => $value4) {
                                        if (is_numeric($key4)) $key4 = "forbid";
                                        if (is_object($value4) || is_array($value4)) {
                                            $root5 = $root4->appendChild($dom->createElement($key4));

                                            foreach ($value4 as $key5 => $value5) {
                                                if (is_numeric($key5)) $key5 = "item";
                                                $root5->appendChild($dom->createElement($key5))->appendChild($dom->createTextNode($value5));
                                            }
                                        } else
                                            $root4->appendChild($dom->createElement($key4))->appendChild($dom->createTextNode($value4));
                                    }

                                } else
                                    $root3->appendChild($dom->createElement($key3))->appendChild($dom->createTextNode($value3));
                            }

                        } else
                            $root2->appendChild($dom->createElement($key2))->appendChild($dom->createTextNode($value2));
                    }

                } else
                    $root->appendChild($dom->createElement($key)->appendChild($dom->createTextNode($value)));
            }
            $dom->formatOutput = true;
            return $dom->saveXML();
        } else {
            return $data;
        }
    }

    static function fromXML($data)
    {

        $data = simplexml_load_string($data);
        $role = new GRoleData();

        foreach ($data as $child => $items) {
            switch ($child) {

                case "base":
                    $role->base = new roleBase();
                    $i = 0;
                    foreach ($items as $key => $value) {
                        if ($key == "forbid") {
                            $forbid = new roleForbids();
                            foreach ($value as $key1 => $value1) {
                                if ($key1 == "forbids") {
                                    if (count($value1) > 0) {
                                        foreach ($value1 as $key2 => $value2) {
                                            $forbids = "";
                                            foreach ($value2 as $key3 => $value3) {
                                                $forbids = new roleForbid();
                                                $forbids->$key3 = (string)$value3;
                                            }
                                            $forbid->forbids[$i] = $forbids;
                                            $i++;
                                        }
                                    }
                                } else
                                    $forbid->$key1 = (string)$value1;
                            }
                            $role->base->$key = $forbid;

                        } else
                            $role->base->$key = (string)$value;
                    }

                    break;

                case "status":
                    $role->status = new roleStatus();
                    foreach ($items as $key => $value) {

                        if ($key == "meridian_data") {
                            $meridian = new roleMeridian();
                            foreach ($value as $key2 => $value2) {
                                $meridian->$key2 = (string)$value2;
                            }
                            $role->status->$key = $meridian;
                        } elseif ($key == "var_data") {
                            $var_data = new roleVarData();
                            foreach ($value as $key3 => $value3) {
                                $var_data->$key3 = (string)$value3;
                            }
                            $role->status->$key = $var_data;
                        } elseif ($key == "property") {
                            $property = new roleProperty();
                            foreach ($value as $key4 => $value4) {
                                if ($key4 == "addon_damage_low") {
                                    foreach ($value4 as $key5 => $value5) {
                                        $property->addon_damage_low[$key5] = (string)$value5;
                                    }
                                } else if ($key4 == "addon_damage_high") {
                                    foreach ($value4 as $key5 => $value5) {
                                        $property->addon_damage_high[$key5] = (string)$value5;
                                    }
                                } else if ($key4 == "resistance") {
                                    foreach ($value4 as $key5 => $value5) {
                                        $property->resistance[$key5] = (string)$value5;
                                    }
                                } else
                                    $property->$key4 = (string)$value4;
                            }
                            $role->status->$key = $property;
                        } else
                            $role->status->$key = (string)$value;
                    }
                    break;

                case "pocket":
                    $role->pocket = new rolePocket();
                    foreach ($items as $key => $value) {
                        if ($key == "items") {
                            $i = 0;
                            foreach ($value as $key1 => $value1) {
                                $itemPocket = new roleItem();
                                foreach ($value1 as $key2 => $value2) {
                                    $itemPocket->$key2 = (string)$value2[0];
                                }
                                $role->pocket->items[$i] = $itemPocket;
                                $i++;
                            }
                        } else
                            $role->pocket->$key = (string)$value;
                    }
                    break;

                case "equipment":
                    $role->equipment = new roleEquipment();
                    foreach ($items as $key => $value) {
                        if ($key == "items") {
                            $i = 0;
                            foreach ($value as $key1 => $value1) {
                                $itemEquipment = new roleItem();
                                foreach ($value1 as $key2 => $value2) {
                                    $itemEquipment->$key2 = (string)$value2[0];
                                }
                                $role->equipment->items[$i] = $itemEquipment;
                                $i++;
                            }
                        } else
                            $role->equipment->$key = (string)$value;
                    }
                    break;
                case "storehouse":
                    $role->storehouse = new roleStorehouse();
                    foreach ($items as $key => $value) {
                        if ($key == "items") {
                            $i = 0;
                            foreach ($value as $keyI1 => $valueI1) {
                                $itemStorehouseI = new roleItem();
                                foreach ($valueI1 as $keyI2 => $valueI2) {
                                    $itemStorehouseI->$keyI2 = (string)$valueI2[0];
                                }
                                $role->storehouse->items[$i] = $itemStorehouseI;
                                $i++;
                            }
                        } elseif ($key == "dress") {
                            $i = 0;
                            foreach ($value as $keyD1 => $valueD1) {
                                $itemStorehouseD = new roleItem();
                                foreach ($valueD1 as $keyD2 => $valueD2) {
                                    $itemStorehouseD->$keyD2 = (string)$valueD2[0];
                                }
                                $role->storehouse->dress[$i] = $itemStorehouseD;
                                $i++;
                            }
                        } elseif ($key == "material") {
                            $i = 0;
                            foreach ($value as $keyM1 => $valueM1) {
                                $itemStorehouseM = new roleItem();
                                foreach ($valueM1 as $keyM2 => $valueM2) {
                                    $itemStorehouseM->$keyM2 = (string)$valueM2[0];
                                }
                                $role->storehouse->material[$i] = $itemStorehouseM;
                                $i++;
                            }
                        } elseif ($key == "generalcard") {
                            $i = 0;
                            foreach ($value as $keyG1 => $valueG1) {
                                $itemStorehouseG = new roleItem();
                                foreach ($valueG1 as $keyG2 => $valueG2) {
                                    $itemStorehouseG->$keyG2 = (string)$valueG2[0];
                                }
                                $role->storehouse->generalcard[$i] = $itemStorehouseG;
                                $i++;
                            }
                        } else
                            $role->storehouse->$key = (string)$value;
                    }
                    break;
                case "task":
                    $role->task = new roleTask();
                    foreach ($items as $key => $value) {
                        if ($key == "task_inventory") {
                            $i = 0;
                            foreach ($value as $key1 => $value1) {
                                $itemTask = new roleItem();
                                foreach ($value1 as $key2 => $value2) {
                                    $itemTask->$key2 = (string)$value2[0];
                                }
                                $role->task->task_inventory[$i] = $itemTask;
                                $i++;
                            }
                        } else
                            $role->task->$key = (string)$value;
                    }
                    break;

            }

        }

        return $role;
    }
}
