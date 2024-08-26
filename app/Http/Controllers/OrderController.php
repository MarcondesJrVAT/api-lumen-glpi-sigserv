<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): LengthAwarePaginator
    {
        return DB::table('os')
            ->select([
                'idos as id',
                'descricao as description',
                'data_previsao as created_at',
                'data_conclusao_os as updated_at'
            ])
            ->orderByDesc('idos')->paginate(100);
    }

    public function update(UpdateOrderRequest $request, int $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order = $this->orderService->updateOrder($order, $request->validated());
            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ordem não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar ordem'], 500);
        }
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar ordem'], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ordem não encontrada'], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->glpi_ticket_id) {
                $this->orderService->glpiService->deleteTicket($order->glpi_ticket_id);
            }
            $order->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar ordem'], 500);
        }
    }
}
