<?php

namespace App\Repositories;

use App\Models\Player;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

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

    public function storeTransfer($request) {
        $data = $request->only(['player_id', 'asking_price']);
        $player = Player::find($data['player_id']);
        if (!empty($player->team_id)) {
            $data['placed_from_id'] = $player->team_id;
        }
        $transfer = Transfer::create($data);
        return $transfer;
    }

    public function updateTransfer($request, $id) {
        $currentUser = Auth::user();
        $data = $request->only(['player_id', 'asking_price', 'is_notified']);
        $transfer = Transfer::find($id);
        if ($currentUser->hasPermission('accept_transfer_player')) {
            if (!$request->has('is_notified')) {
                $team = $currentUser->team;
                if (empty($team)) {
                    throw new ValidationException('You do not have any team to buy this player');
                }
                if ($team->fund < $transfer->asking_price) {
                    throw new ValidationException('You do not have enough fund to buy this player');
                }
                $data['transferred_to_id'] = $team->id;

                if (!empty($transfer->placedFrom)) {
                    $transfer->placedFrom->fund = $transfer->placedFrom->fund + $transfer->asked_price;
                    $transfer->placedFrom->save();
                }

                $team->fund = $team->fund - $transfer->asked_price;
                $team->save();

                $player = $transfer->player;
                $player->team_id = $team->id;
                $player->save();

                $data['transfer_completed_at'] = Carbon::now()->format('Y-m-d H:i:s');
            }
        }
        $transfer->update($data);
        return $transfer;
    }

    public function deleteTransfer($id) {
        $transfer = Transfer::find($id);
        $transfer->delete();
        return '';
    }

    public function getTransfer($id) {
        $transfer = Transfer::find($id);
        return $transfer;
    }
}