<?php

namespace App\Enums;

enum EstadoFemboy: string
{
    case DISPONIBLE = 'Disponible';
    case RESERVADO = 'Reservado';
    case NO_DISPONIBLE = 'No Disponible';

    //obtenir tots els valors possibles. Tambe podries fer EstadoFemboy::cases()
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }



    //aquestes funcions son totalment innecessàries, les poso eprque mira. Però no cal que els feu en els vostres enums
    public function color(): string
    {
        return match($this) {
            self::DISPONIBLE => 'success',
            self::RESERVADO => 'warning',
            self::NO_DISPONIBLE => 'danger',
        };
    }


    public function emoji(): string
    {
        return match($this) {
            self::DISPONIBLE => '✅',
            self::RESERVADO => '⏳',
            self::NO_DISPONIBLE => '❌',
        };
    }
}
