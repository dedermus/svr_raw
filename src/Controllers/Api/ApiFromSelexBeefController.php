<?php

namespace Svr\Raw\Controllers\Api;

use Illuminate\Routing\Controller;

use Svr\Raw\Models\FromSelexBeef;
use Illuminate\Http\Request;

class ApiFromSelexBeefController extends Controller
{
    /**
     * Создание новой записи.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $record = FromSelexBeef::createRaw($request);

        return response()->json($record, 201);
    }

    /**
     * Обновление существующей записи.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $record = FromSelexBeef::findOrFail($id);
        $record->updateRaw($request);

        return response()->json($record);
    }

    /**
     * Получение списка записей с пагинацией.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15); // Количество записей на странице по умолчанию
        $records = FromSelexBeef::paginate($perPage);

        return response()->json($records);
    }
}
