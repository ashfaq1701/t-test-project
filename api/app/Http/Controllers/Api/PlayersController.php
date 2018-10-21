<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Player as PlayerResource;
use App\Repositories\PlayerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller {
    public $playerRepository;

    public function __construct(PlayerRepository $playerRepository) {
        $this->middleware('permission:get_players',
            ['only' => ['index']]);
        $this->middleware('permission:create_new_player',
            ['only' => ['store']]);
        $this->middleware('permission:edit_players|modify_player_role',
            ['only' => ['update']]);
        $this->middleware('permission:delete_players',
            ['only' => ['destroy']]);
        $this->playerRepository = $playerRepository;
    }

    public function index(Request $request) {
        if ($request->has('query')) {
            $players = $this->playerRepository->searchPlayers($request);
        } else {
            $players = $this->playerRepository->getAllPlayers($request);
        }
        return PlayerResource::collection($players);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'required|numeric',
            'price' => 'required|numeric|min:1',
            'country_id' => 'required|exists:countries,id',
            'team_id' => 'sometimes|required|exists:teams,id',
            'player_role_id' => 'required|exists:player_roles,id'
        ]);
        $player = $this->playerRepository->storePlayer($request);
        return new PlayerResource($player);
    }

    public function show($id)
    {
        $player = $this->playerRepository->getPlayer($id);
        return new PlayerResource($player);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'age' => 'sometimes|required|numeric',
            'price' => 'sometimes|required|numeric|min:1',
            'country_id' => 'sometimes|required|exists:countries,id',
            'team_id' => 'sometimes|required|exists:teams,id',
            'player_role_id' => 'sometimes|required|exists:player_roles,id'
        ]);
        $player = $this->playerRepository->updatePlayer($request, $id);
        return new PlayerResource($player);
    }

    public function destroy($id)
    {
        return $this->playerRepository->deletePlayer($id);
    }
}