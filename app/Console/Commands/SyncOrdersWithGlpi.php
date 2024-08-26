<?php

namespace App\Console\Commands;


use App\Models\Order;
use App\Services\GlpiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncOrdersWithGlpi extends Command
{
    protected $signature = 'sync:orders-with-glpi';
    protected $description = 'Sincronizar ordens de serviço existentes com GLPI';
    protected GlpiService $glpiService;
    protected bool $stopSync = false;

    public function __construct(GlpiService $glpiService)
    {
        parent::__construct();
        $this->glpiService = $glpiService;
    }

    public function handle()
    {
        ini_set('memory_limit', '2G');

        $this->info('Iniciando sincronização');

        $orders = Order::whereNull('glpi_ticket_id')->chunk(1000, function ($orders) {
            foreach ($orders as $order) {
                $glpiTicket = $this->glpiService->createTicket($order->toArray());
                if ($this->stopSync) {
                    $this->info('Sincronização interrompida');

                    return false;
                }

                if (isset($glpiTicket['id'])) {
                    $order->glpi_ticket_id = $glpiTicket['id'];
                    $order->save();
                    $this->info("Ordem de serviço #{$order->idos} sincronizada com GLPI");
                }else{
                    $this->error("Ordem de serviço #{$order->idos} não sincronizada com GLPI");
                    Log::info("Ordem de serviço #{$order->idos} não sincronizada com GLPI", $glpiTicket);
                }
            }
        });

        $this->info('Sincronização concluída');
    }

    public function stopSync(): void
    {
        $this->stopSync = true;
    }
}
