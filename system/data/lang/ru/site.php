<?php

namespace system\data;

class lang
{
    static $gender = array("男", "女");
    static $race = array(
        0 => "人族",
        2 => "兽族",
        5 => "羽族",
        10 => "汐族"

    );
    static $cls = array(
        "武侠",
        "法师",
        "妖精",
        "妖兽",
        "羽灵",
        "羽芒",
        "刺客",
        "巫师",
        "剑灵",
        "魅灵",
        "月仙",
        "夜影",
    );

    static $level2 = array(0 => "筑基", 1 => "灵虚", 2 => "合和", 3 => "元婴", 4 => "空冥", 5 => "履霜", 6 => "渡劫", 67 => "寂灭", 8 => "大乘", 20 => "上仙", 21 => "真仙", 22 => "天仙", 30 => "狂魔", 31 => "魔圣", 32 => "魔尊");

static $max_ap = array(0=> "没有元气", 199 => "1颗真元", 299 => "2颗真元", 399 => "3颗真元");

static $yn = array("没有","死亡");

static $template = array(
    "visual" => "视图编辑"
);

static $notValidCharID = "不是有效的字符ID";

}