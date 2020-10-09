<?php


namespace App\Base\Traits;


use Illuminate\Database\Eloquent\Model;

trait Criteria
{
    /**
     * 创建查询条件
     * @param $condition
     * @$conditionExample 有用到补充下例子
     * [
     *    'id' => [
     *        ['notIn', [1,2,3]]
     *     ],
     *     'score' => [
     *          ['>', 60]
     *      ]
     *     'create_time'    => [
     *          ['between',['2017-08-01','2017-08-30']
     *      ]
     *     'user_id' => 10338,
     *     '_string' => 'status=0 and type=1',
     *     'name' => [
     *         ['like', "huyunnan"]
     *     ]
     * ]
     * @return Model
     */
    public function buildQuery($condition)
    {
        if (is_string($condition)) {
            return $this->whereRaw($condition);
        }
        if (is_array($condition)) {
            $model = $this;
            foreach ($condition as $key => $item) {
                //laravel多条件写法 [ ['id','>',1],[...] ]
                if (is_int($key) && is_array($item)) {
                    $model = $model->where([$item]);
                    continue;
                }
                switch ($key) {
                    case '_string':
                        $model = $model->whereRaw($item);
                        break;
                    case '_null':
                        $model = $this->buildNullQuery($model, $item);
                        break;
                    case '_notNull':
                        $model = $this->buildNotNullQuery($model, $item);
                        break;
                    default:
                        if (!is_array($item)) {
                            $model = $model->where($key, $item);
                        } else {
                            $model = $this->buildItemQuery($model, $key, $item);
                        }
                }
            }
            return $model;
        }
        return $this;
    }

    /**
     * 查询条件
     * @param $model
     * @param $key
     * @param $query
     * @return Model|mixed
     */
    private function buildItemQuery($model, $key, $query)
    {
        foreach ($query as $index => $item) {
            if (count($item) < 2) {
                continue;
            }
            switch ($item[0]) {
                case 'in':
                    $model = $this->buildInQuery($model, $key, $item[1]);
                    break;
                case 'notIn':
                    $model = $this->buildNotInQuery($model, $key, $item[1]);
                    break;
                case 'like':
                    $model = $this->buildLikeQuery($model, $key, $item[1]);
                    break;
                case 'between':
                    $model = $this->buildBetweenQuery($model, $key, $item[1]);
                    break;
                case 'neq':
                    $model = $this->buildNotInQuery($model, $key, is_array($item[1])?$item[1]:[$item[1]]);
                    break;
                case '>':
                case '<':
                case '=':
                case '>=':
                case '<=':
                    $model = $model->where($key, $item[0], $item[1]);
                    break;
                default:
                    if (!is_array($item[1])) {
                        $model = $model->where($key, $item[0], $item[1]);
                    } else {
                        $model = $model->where($query);
                    }
                    break;
            }
            unset($query[$index]);
        }
        if (!empty($query) && count($query) >= 2) {
            $model = $model->where($key, $query[0], $query[0] == 'like' ? '%' . $query[1] . '%' : $query[1]);
        }
        return $model;
    }

    /**
     * in查询条件
     * @param $model
     * @param $key
     * @param $query
     * @return Model
     */
    private function buildInQuery($model, $key, $query)
    {
        if (is_array($query)) {
            $model = $model->whereIn($key, $query);
        } else {
            $model = $model->whereIn($key, [$query]);
        }
        return $model;
    }

    /**
     * not in
     * @param $model
     * @param $key
     * @param $query
     * @return mixed
     */
    private function buildNotInQuery($model, $key, $query)
    {
        if (is_array($query)) {
            $model = $model->whereNotIn($key, $query);
        } else {
            $model = $model->whereNotIn($key, [$query]);
        }
        return $model;
    }

    /**
     * null
     * @param $model
     * @param $query
     * @return mixed
     */
    private function buildNullQuery($model, $query)
    {
        if (is_array($query)) {
            foreach ($query as $item) {
                $model = $model->whereNull($item);
            }
        }
        return $model;
    }

    /**
     * not null
     * @param $model
     * @param $query
     * @return mixed
     */
    private function buildNotNullQuery($model, $query)
    {
        if (is_string($query)) {
            return $model->whereNotNull($query);
        }
        if (is_array($query)) {
            foreach ($query as $item) {
                $model = $model->whereNotNull($item);
            }
        }
        return $model;
    }

    /**
     * like
     * @param $model
     * @param $query
     * @return mixed
     */
    private function buildLikeQuery($model, $key, $query)
    {
        if (!is_array($query)) {
            $model = $model->where($key, 'like', '%' . $query . '%');
        }
        return $model;
    }

    /**
     * between
     * @param $model
     * @param $key
     * @param $query
     * @return mixed
     */
    private function buildBetweenQuery($model, $key, $query)
    {
        if (is_array($query)) {
            $model = $model->whereBetween($key, $query);
        }
        return $model;
    }

    public function joinRaw()
    {

    }
}