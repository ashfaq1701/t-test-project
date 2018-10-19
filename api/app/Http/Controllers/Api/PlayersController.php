<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Player as PlayerResource;
use App\Repositories\PlayerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller {
    public $playerRepository;

    public function __construct(PlayerRepository $playerRepository) {
        $this->playerRepository = $playerRepository;
    }
}