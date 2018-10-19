<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PlayerRole as PlayerRoleResource;
use App\Repositories\PlayerRoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayerRolesController extends Controller {
    public $playerRoleRepository;

    public function __construct(PlayerRoleRepository $playerRoleRepository) {
        $this->playerRoleRepository = $playerRoleRepository;
    }

    public function index() {
        $playerRoles = $this->playerRoleRepository->getAllPlayerRoles();
        return PlayerRoleResource::collection($playerRoles);
    }
}