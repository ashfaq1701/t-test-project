<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Transfer as TransferResource;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransfersController extends Controller {
    public $transferRepository;

    public function __construct(TransferRepository $transferRepository) {
        $this->middleware('permission:get_transfers',
            ['only' => ['index', 'show']]);
        $this->middleware('permission:transfer_own_player|create_new_transfer',
            ['only' => ['store']]);
        $this->middleware('permission:edit_transfers|accept_transfer_player',
            ['only' => ['update']]);
        $this->middleware('permission:delete_transfers',
            ['only' => ['destroy']]);
        $this->transferRepository = $transferRepository;
    }

    public function index(Request $request) {
        $transfers = $this->transferRepository->searchTransfers($request);
        return TransferResource::collection($transfers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'asking_price' => 'required|numeric|min:1'
        ]);
        $transfer = $this->transferRepository->storeTransfer($request);
        return new TransferResource($transfer);
    }

    public function show($id)
    {
        $transfer = $this->transferRepository->getTransfer($id);
        return new TransferResource($transfer);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'player_id' => 'sometimes|required|exists:players,id',
            'asking_price' => 'sometimes|required|numeric|min:1'
        ]);
        $transfer = $this->transferRepository->updateTransfer($request, $id);
        return new TransferResource($transfer);
    }

    public function destroy($id)
    {
        return $this->transferRepository->deleteTransfer($id);
    }
}