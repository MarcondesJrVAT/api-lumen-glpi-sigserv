<?php

namespace Tests;

use App\Repositories\OrderRepository;
use PhpUnit\Framework\TestCase;
use App\Services\GlpiService;
use App\Services\OrderService;
use App\Models\Order;


class OrderServiceTest extends TestCase
{
    protected $orderService;
    protected $glpiServiceMock;
    protected $orderMock;

    public function setUp(): void
    {
        $this->orderMock = $this->createMock(OrderRepository::class);
        $this->glpiServiceMock = $this->createMock(GlpiService::class);
        $this->orderService = new OrderService(new Order(), $this->glpiServiceMock);
    }

    public function testCreateOrder()
    {
        $data = [
            'idusuario_registrou' => 1,
            'idcontrato' => 1,
            'idlocal' => 1,
            'solicitante' => 'John Doe',
            'idforma_contato' => 1,
            'descricao' => 'Description',
            'idarea_encaminhamento' => 1,
            'idsituacao_atendimento' => 1,
            'alias' => 'Alias',
            'apelido_local' => 'Local',
            'data_previsao' => '2021-01-01',
            'iduso' => 1,
            'idconta_contabil' => 1,
            'idunidade' => 1,
        ];

        $this->orderMock->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn(new Order(['glpi_ticket_id' => null]));

        $this->glpiServiceMock->expects($this->once())
            ->method('createTicket')
            ->with($data)
            ->willReturn(['id' => 123]);

        $order = $this->orderService->createOrder($data);

        $this->assertEquals(123, $order->glpi_ticket_id);
    }

    public function testUpdateOrder()
    {
        $order = new Order();
        $data = [
            'idusuario_registrou' => 1,
            'idcontrato' => 1,
            'idlocal' => 1,
            'solicitante' => 'John Doe',
            'idforma_contato' => 1,
            'descricao' => 'Description',
            'idarea_encaminhamento' => 1,
            'idsituacao_atendimento' => 1,
            'alias' => 'Alias',
            'apelido_local' => 'Local',
            'data_previsao' => '2021-01-01',
            'iduso' => 1,
            'idconta_contabil' => 1,
            'idunidade' => 1,
        ];

        $this->orderMock->expects($this->once())
            ->method('update')
            ->with($order, $data)
            ->willReturn(true);

        $result = $this->orderService->updateOrder($order, $data);

        $this->assertTrue($result);
    }
}
