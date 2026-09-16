<?php

namespace App\Policies;

use App\Models\Femboy;
use App\Models\User;

class FemboyPolicy
{
    /**
     * Determinar si l'usuari és admin
     */
    public function esAdmin(User $user): bool
    {
        return $user->name === "admin";
    }

    /**
     * Determinar si l'usuari és propietari (té factory assignada)
     */
    public function esPropietari(User $user): bool
    {
        return $user->customer !== null && $user->customer->factory_id !== null;
    }

    /**
     * Determinar si l'usuari és només customer (sense factory)
     */
    public function esCustomer(User $user): bool
    {
        return $user->customer !== null && $user->customer->factory_id === null;
    }

    /**
     * Veure tots els femboys (només admin)
     */
    public function viewAll(User $user): bool
    {
        return $this->esAdmin($user);
    }

    /**
     * Veure femboys de la seva factory
     */
    public function viewFactory(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        return false;
    }

    /**
     * Veure només els seus femboys assignats
     */
    public function viewOwn(User $user, Femboy $femboy): bool
    {
        if ($this->esCustomer($user)) {
            return $user->customer->femboys->contains($femboy->id);
        }

        return false;
    }

    /**
     * Veure qualsevol femboy (combina totes les regles)
     */
    public function view(User $user, Femboy $femboy): bool
    {
        // Admin veu tots
        if ($this->esAdmin($user)) {
            return true;
        }

        // Propietari veu els de la seva factory
        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        // Customer veu només els seus
        if ($this->esCustomer($user)) {
            return $user->customer->femboys->contains($femboy->id);
        }

        //tothom por veure els disponibles
        if($femboy->estado === "Disponible"){
            return true;
        }

        return false;
    }

    /**
     * Veure la llista de femboys
     */
    public function viewAny(User $user): bool
    {
        return true; // Tothom autenticat pot veure la llista
    }

    /**
     * Crear un nou femboy
     */
    public function create(User $user): bool
    {
        return $this->esAdmin($user) || $this->esPropietari($user);
    }

    /**
     * Actualitzar un femboy
     */
    public function update(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        return false;
    }

    /**
     * Eliminar un femboy
     */
    public function delete(User $user, Femboy $femboy): bool
    {
        if ($this->esAdmin($user)) {
            return true;
        }

        if ($this->esPropietari($user)) {
            return $user->customer->factory_id === $femboy->factory_id;
        }

        return false;
    }
}
