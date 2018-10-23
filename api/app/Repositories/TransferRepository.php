<?php

namespace App\Repositories;

use App\Models\Player;
use App\Models\Transfer;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;

class TransferRepository {
    public function searchTransfers($request) {
        $currentUser = Auth::user();
        $query = Transfer::query();
        if ($request->has('country') && !empty($request->input('country'))) {
            $query = $query->whereHas('player', function($innerQuery) use($request) {
                $innerQuery->where('country_id', '=', $request->input('country'));
            });
        }
        if ($request->has('team') && !empty($request->input('team'))) {
            $query = $query->whereHas('player', function ($innerQuery) use($request) {
               $innerQuery->where('team_id', '=', $request->input('team'));
            });
        }
        if ($request->has('team_name') && !empty($request->input('team_name'))) {
            $query = $query->whereHas('player.team', function ($innerQuery) use($request) {
                $innerQuery->where('name', 'LIKE', $request->input('team_name') . '%');
            });
        }
        if ($request->has('player_name') && !empty($request->input('player_name'))) {
            $query = $query->whereHas('player', function($innerQuery) use($request) {
                $nameParts = explode(' ', $request->input('player_name'));
                $innerQuery->where('first_name', 'LIKE', $nameParts[0] . '%');
                if (count($nameParts) > 1) {
                    $innerQuery->orWhere('last_name', 'LIKE', $nameParts[1] . '%');
                }
            });
        }
        if ($request->has('player_role') && !empty($request->input('player_role'))) {
            $query = $query->whereHas('player', function($innerQuery) use($request) {
                $innerQuery->where('player_role_id', '=', $request->input('player_role'));
            });
        }
        if ($request->has('min_price') && !empty($request->input('min_price'))) {
            $query = $query->where('asking_price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price') && !empty($request->input('max_price'))) {
            $query = $query->where('asking_price', '<=', $request->input('max_price'));
        }
        if ($request->has('type') && !empty($request->input('type'))) {
            if ($request->input('type') == 'completed') {
                $query = $query->whereNotNull('transfer_completed_at')
                    ->whereNotNull('transferred_to_id');
            } else if ($request->input('type') == 'incomplete') {
                $query = $query->whereNull('transfer_completed_at')
                    ->whereNull('transferred_to_id');
            }
        }
        if ($request->has('not_notified') && (intval($request->input('not_notified')) == 1)) {
            $query = $query->where(function($innerQuery) {
                $innerQuery->whereNull('is_notified')
                    ->orWhere('is_notified', '=', 0);
            });
            $query->whereHas('placedFrom', function($innerQuery) use($currentUser) {
                $innerQuery->where('user_id', '=', $currentUser->id);
            });
        }
        if ($request->has('page')) {
            return $query->paginate(10);
        }
        else {
            return $query->get();
        }
    }

    public function storeTransfer($request) {
        $currentUser = Auth::user();
        $data = $request->only(['player_id', 'asking_price']);
        $player = Player::find($data['player_id']);
        if ($currentUser->hasPermissionTo('transfer_own_player') && $currentUser->hasRole('owner')) {
            if (empty($currentUser->team)) {
                throw new ValidationException('You do not have any team and you can not transfer player ' .
                    'owned by others.');
            }
            if ($player->team_id != $currentUser->team->id) {
                throw new ValidationException('You can not transfer this player because he ' .
                    'is not owned by you.');
            }
        }
        $activeTransferCount = Transfer::query()->where('player_id', '=', $player->id)
            ->whereNull('transfer_completed_at')
            ->whereNull('transferred_to_id')
            ->count();
        if ($activeTransferCount > 0) {
            throw new ValidationException('This player is already in transfer list');
        }
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
        if ($currentUser->hasPermissionTo('accept_transfer_player')) {
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
                    $transfer->placedFrom->fund = $transfer->placedFrom->fund + $transfer->asking_price;
                    $transfer->placedFrom->save();
                }

                $team->fund = $team->fund - $transfer->asking_price;
                $team->save();

                $player = $transfer->player;
                $player->team_id = $team->id;
                $player->price = $transfer->asking_price + ($this->getIncreaseFactor() / 100) * $transfer->asking_price;
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

    public function getIncreaseFactor() {
        return rand(10, 100);
    }
}