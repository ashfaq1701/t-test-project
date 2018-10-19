<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Team as TeamResource;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamsController extends Controller {
    public $teamRepository;

    public function __construct(TeamRepository $teamRepository) {
        $this->teamRepository = $teamRepository;
    }
}