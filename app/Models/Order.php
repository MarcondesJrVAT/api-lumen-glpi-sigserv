<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $table = 'os';

    protected $primaryKey = 'idos';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idos',
        'idusuario_registrou',
        'idcontrato',
        'idlocal',
        'solicitante',
        'idforma_contato',
        'descricao',
        'old_idocorrencia_cliente',
        'resolvi_atraves_roteiro',
        'sugestao_deslocamento',
        'observacoes',
        'idarea_encaminhamento',
        'idsituacao_atendimento',
        'registro_retroativo',
        'alias',
        'apelido_local',
        'data_previsao',
        'os_terceiros',
        'pesq_resolveu',
        'pesq_nota',
        'pesq_motivo',
        'data_conclusao_os',
        'pesq_anted',
        'pesq_pesquisado',
        'data_pesquisa_os',
        'local_descricao',
        'local_idcomunidade',
        'solicitacao_cliente',
        'kit_entregue',
        'idescola_destino',
        'idmotivo',
        'vsat_origem',
        'vsat_destino',
        'login_destino',
        'validou_inventario',
        'tipo_os',
        'idos_atendimento',
        'iduso',
        'emergencia_compras',
        'idconta_contabil',
        'data_hora_sla',
        'idunidade',
        'idprod_aula',
        'idos_atendimento_vinculada',
        'idos_demanda_vinculada',
        'idalias_local_estoque_origem',
        'idlogin_cliente',
        'idsituacao_atendimento_sub',
        'idconta_pfisica_tecnico',
        'data_previsao_tecnico',
        'idalias_local_estoque_destino_os_compra',
        'idsetor_os_compra',
        'glpi_ticket_id',
    ];

    protected $dates = [
        'data_hora_abertura_os',
        'data_previsao',
        'data_conclusao_os',
        'data_pesquisa_os',
        'data_hora_sla',
        'data_previsao_tecnico'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function userRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idusuario_registrou', 'idusuario');
    }
}
