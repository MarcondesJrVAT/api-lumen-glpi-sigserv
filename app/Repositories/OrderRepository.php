<?php
namespace App\Repositories;

use App\Models\Order;
use Validator;

class OrderRepository {
    public function create(array $data) {
        $validatedData = $this->validate($data);

        $order = new Order();
        $order->fill($validatedData);
        $order->save();
        return $order;
    }

    public function validate(array $data)
    {
        return Validator::make($data, [
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
        ])->validate();
    }
}
