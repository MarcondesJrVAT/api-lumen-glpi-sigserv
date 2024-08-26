<?php

namespace App\Services;

use App\Models\Order;
use App\Services\GlpiService;
use Illuminate\Http\Request;

class OrderService
{
    protected Order $order;
    protected GlpiService $glpiService;

    public function __construct(Order $order, GlpiService $glpiService)
    {
        $this->order = $order;
        $this->glpiService = $glpiService;
    }

    public function filterGlpiData(array $data): array
    {
        $allowedFields = [
            'idusuario_registrou',
            'idcontrato',
            'idlocal',
            'solicitante',
            'idforma_contato',
            'descricao',
            'idarea_encaminhamento',
            'idsituacao_atendimento',
            'alias',
            'apelido_local',
            'data_previsao',
            'iduso',
            'idconta_contabil',
            'idunidade',
        ];
        return array_filter(
            $data,
            function ($key) use ($allowedFields) {
                return in_array($key, $allowedFields);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public function createOrder(array $data)
    {
        $order = $this->order->create($data);
        if (!$order->glpi_ticket_id) {
            $glpiData = $this->filterGlpiData($data);
            $glpiTicket = $this->glpiService->createTicket($glpiData);
            $order->glpi_ticket_id = $glpiTicket['id'];
            $order->save();
        }
        return $order;
    }

    public function updateOrder(Order $order, array $data)
    {
        $order->update($data);
        $glpiData = $this->filterGlpiData($data);
        if ($order->glpi_ticket_id) {
            $this->glpiService->updateTicket($order->glpi_ticket_id, $glpiData);
        } else {
            $glpiTicket = $this->glpiService->createTicket($glpiData);
            $order->glpi_ticket_id = $glpiTicket['id'];
            $order->save();
        }
        return $order;
    }

    public function deleteOrder(Order $order)
    {
        if ($order->glpi_ticket_id) {
            $this->glpiService->deleteTicket($order->glpi_ticket_id);
        }
        $order->delete();
    }
}
