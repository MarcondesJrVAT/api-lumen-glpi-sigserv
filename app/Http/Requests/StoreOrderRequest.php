<?php

namespace App\Http\Requests;

class StoreOrderRequest
{
    public function rules()
    {

        return [
            'idusuario_registrou' => 'required|integer',
            'idcontrato' => 'required|integer',
            'idlocal' => 'required|integer',
            'solicitante' => 'required|string',
            'idforma_contato' => 'required|integer',
            'descricao' => 'required|string',
            'idarea_encaminhamento' => 'required|integer',
            'idsituacao_atendimento' => 'required|integer',
            'alias' => 'required|string',
            'apelido_local' => 'required|string',
            'data_previsao' => 'required|date',
            'iduso' => 'required|integer',
            'idconta_contabil' => 'required|integer',
            'idunidade' => 'required|integer',
        ];
    }
}
