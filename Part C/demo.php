<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Warrior\PaladinFactory;
use App\Contracts\CharacterFactory;

function renderCharacter(CharacterFactory $factory): void
{
    $weapon = $factory->createWeapon();
    $armor = $factory->createArmor();
    $ability = $factory->createAbility();

    echo $weapon->use() . PHP_EOL;
    echo $armor->equip() . PHP_EOL;
    echo $ability->activate() . PHP_EOL;
    echo PHP_EOL;
}

echo "=== Paladin Class Output ===" . PHP_EOL;
renderCharacter(new PaladinFactory());