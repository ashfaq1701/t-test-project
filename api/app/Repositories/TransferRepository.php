<?php

namespace App\Repositories;

use App\Models\Transfer;

class TransferRepository {
    public function searchTransfers($request) {
        $query = Transfer::query();
        if ($request->has('country')) {
            $query = $query->whereHas('player', function($innerQuery) use($request) {
                $innerQuery->where('country_id', '=', $request->input('country'));
            });
        }
        if ($request->has('team')) {
            $query = $query->whereHas('player', function ($innerQuery) use($request) {
               $innerQuery->where('team_id', '=', $request->input('team'));
            });
        }
        if ($request->has('team_name')) {
            $query = $query->whereHas('player.team', function ($innerQuery) use($request) {
                $innerQuery->where('name', 'LIKE', $request->input('team_name') . '%');
            });
        }
        if ($request->has('player_name')) {
            $query = $query->whereHas('player', function($innerQuery) use($request) {
               $innerQuery->where('first_name', 'LIKE', $request->input('player_name') . '%')
                   ->orWhere('last_name', 'LIKE', $request->input('player_name') . '%');
            });
        }
        if ($request->has('min_price')) {
            $query = $query->where('asking_price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query = $query->where('asking_price', '<=', $request->input('max_price'));
        }
        if (!($request->has('type') && ($request->input('type') == 'include_completed'))) {
            $query = $query->whereNull('transfer_completed_at')
                ->whereNull('transferred_to_id');
        }
        if ($request->has('page')) {
            return $query->paginate();
        }
        else {
            return $query->get();
        }
    }
}