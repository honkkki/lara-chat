<?php

/**
 * json格式返回
 *
 * @param int $code
 * @param null $msg
 * @param null $data
 * @return array
 */
function response_json($code, $msg = null, $data = null)
{
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
}

/**
 * 响应成功返回格式
 *
 * @param null $data
 * @return array
 */
function response_success($data = null)
{
    return response_json(0, 'success', $data);
}

/**
 * 响应失败返回格式
 *
 * @param null $msg
 * @return array
 */
function response_fail($msg = null)
{
    return response_json(-1, $msg, null);
}


/**
 * model保存数据
 *
 * @param $model
 * @param $data
 * @return mixed
 */
function model_save($model, $data)
{
    foreach ($data as $key => $item) {
        $model[$key] = $item;
    }

    $model->save();
    return $model;
}
