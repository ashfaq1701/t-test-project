<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Transfer as TransferResource;
use App\Repositories\TransferRepository;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransfersController extends Controller {
    public $transferRepository;

    public function __construct(TransferRepository $transferRepository) {
        $this->transferRepository = $transferRepository;
    }

    public function index(Request $request) {
        $transfers = $this->transferRepository->searchTransfers($request);
        return TransferResource::collection($transfers);
    }
}