<?php

namespace App\Repositories;

use App\Models\Player;

class PlayerRepository {
    public function getQuery($request) {
        $query = Player::query();
        if ($request->has('team')) {
            $query = $query->where('team_id', '=', $request->input('team'));
        }
        if ($request->has('team_name')) {
            $query = $query->whereHas('team', function($innerQuery) use($request) {
               $innerQuery->where('name', 'LIKE', $request->input('team_name') . '%');
            });
        }
        if ($request->has('country')) {
            $query = $query->where('country_id', '=', $request->input('country'));
        }
        if ($request->has('player_type')) {
            $query = $query->where('player_type_id', '=', $request->input('player_type'));
        }
        if ($request->has('min_price')) {
            $query = $query->where('price','>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query = $query->where('price', '<=', $request->input('max_price'));
        }
        return $query;
    }

    public function searchPlayers($request) {
        $query = $this->getQuery($request);
        $query = $query->where(function($innerQuery) use ($request) {
           $innerQuery->where('first_name', 'LIKE', $request->input('query') . '%')
               ->orWhere('last_name', 'LIKE', $request->input('query') . '%');
        });
        if ($request->has('page')) {
            $players = $query->paginate();
        } else {
            $players = $query->get();
        }
        return $players;
    }

    public function getAllPlayers($request) {
        $query = $this->getQuery($request);
        if ($request->has('page')) {
            return $query->paginate();
        } else {
            return $query->get();
        }
    }

    public function storePlayer($request) {
        $data = $request->only(['first_name', 'last_name', 'age', 'price', 'country_id', 'team_id', 'player_role_id']);
        $player = Player::create($data);
        return $player;
    }

    public function updatePlayer($request, $id) {
        $player = Player::find($id);
        $data = $request->only(['first_name', 'last_name', 'age', 'price', 'country_id', 'team_id', 'player_role_id']);
        $player->update($data);
        return $player;
    }

    public function deletePlayer($id) {
        if (is_object($id)) {
            $player = $id;
        } else {
            $player = Player::find($id);
        }
        $player->transfers()->delete();
        $player->delete();
        return '';
    }

    public function getPlayer($id) {
        $player = Player::find($id);
        return $player;
    }
}