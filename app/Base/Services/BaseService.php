<?php

namespace App\Base\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

abstract class BaseService
{

    protected $model;
    /**
     * 是否缓存数据
     * @var bool
     */
    protected $cache = false;
    /**
     * 缓存空间
     * @var string
     */
    protected $cacheBucket = '';

    /**
     * BaseService constructor.
     * @param BaseModel $model
     */
    public function __construct(\App\Base\Models\BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * DB:select对象转数组
     * @param $list
     * @return array
     */
    public function toArray(&$list) {
        $res = [];
        foreach ($list as &$v) {
            $res[] = (array)$v;
        }
        return $res;
    }

    /**
     * 获取url地址
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getBaseUrl()
    {
        return app()->make(Request::class)->root();
    }

    /**
     * 缓存key
     * @param $id
     * @return string
     */
    protected function getCacheId($id)
    {
        return $this->cacheBucket . '-' . md5($id);
    }

    /**
     * 获取绑定的model
     * @return \App\Base\Models\BaseModel|BaseModel
     */
    public function getModel(){
        return $this->model;
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->model->getConnection()->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->model->getConnection()->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->model->getConnection()->rollBack();
    }

    /**
     * 保存数据
     * @param $data
     * @return BaseModel
     */
    public function save($data)
    {
        $this->model = $this->model->newInstance();
        $data = $this->model->filter($data);
        foreach ($data as $key => $item) {
            $this->model->$key = $item;
        }
        $this->model->save();
        return $this->model;
    }

    /**
     * 批量保存数据
     * @param $data
     */
    public function saveAll($data)
    {
        if (empty($data)) {
            return;
        }
        //过滤字段数据
        foreach ($data as &$item) {
            $item = $this->model->filter($item);
//            self::save($item);
        }
        //更改为批量插入
        if($this->model->timestamps && $this->model::CREATED_AT && $this->model::UPDATED_AT) {
            $time = now();
            foreach ($data as &$item) {
                $item[$this->model::CREATED_AT] = $item[$this->model::UPDATED_AT] = $time;
                foreach ($item as $key => $itemValue) {
                    if ($this->model->isJsonCastingField($key) && is_array($itemValue)) {
                        $item[$key] = json_encode($itemValue);
                    }
                }
            }
        }
        $this->model->insert($data);
        //批量插入

//        $this->model->newInstance()->getConnection()->table($this->model->getTable())->insert($data);
    }

    /**
     * 根据主键判断保存或者更新
     * @param $data
     * @return BaseModel
     */
    public function saveOrUpdate($data)
    {
        $key = $this->model->getKeyName();
        if (isset($data[$key]) && !empty($data[$key])) {
            return $this->update($data[$key], $data);
        } else {
            return $this->save($data);
        }
    }

    /**
     * 详情
     * @param $id
     * @return BaseModel
     */
    public function show($id)
    {
        return $this->findOneById($id);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        if ($this->cache) {
            Cache::forget($this->getCacheId($id));
        }
        $res = $this->model->newInstance()->whereKey($id)->update($data);
        return $res;
    }

    /**
     * 根据多个条件更新数据
     * @param array $criteria
     * @param array $data
     * @return bool
     */
    public function updateBy($criteria, array $data)
    {
        if ($this->cache) {
            if (method_exists($this->model, 'runSoftDelete')) {
                $list = $this->model->newInstance()->buildQuery($criteria)->get();
            } else {
                $list = $this->model->newInstance()->buildQuery($criteria)->get();
            }
            foreach ($list as $item) {
                Cache::forget($this->getCacheId($item[$this->model->getKeyName()]));
            }
        }
        $data = $this->model->filter($data);
        if (method_exists($this->model, 'runSoftDelete')) {
            $res = $this->model->newInstance()->buildQuery($criteria)->update($data);
        } else {
            $res = $this->model->newInstance()->buildQuery($criteria)->update($data);
        }
        return $res;
    }

    /**
     * 列表查询
     * @param $data
     * @return Collection
     */
    public function list($data)
    {
        return $this->model->whereRaw($this->getCondition($data))->paginate($this->getPageSize($data));
    }

    /**
     * 获取查询条件
     * @param $data
     * @return string
     */
    protected function getCondition($data)
    {
        if (!is_array($data)) {
            return '';
        }
        $data = $this->model->filter($data);
        $condition = '1=1';
        foreach ($data as $key => $item) {
            $condition .= " and " . $key . "='" . $item . "'";
        }
        return $condition;
    }

    /**
     * 获取分页行数
     * @param $data
     * @return int
     */
    protected function getPageSize($data)
    {
        return $data['page_size']??config('app.app_rows');
    }

    /**
     * 删除数据
     * @param $id int|array
     * @return bool|null
     */
    public function delete($id)
    {
        if (is_array($id)) {
            return $this->deleteBy([
                $this->model->getKeyName() => [
                    ['in', $id]
                ]
            ]);
        } else {
            return $this->deleteBy([
                $this->model->getKeyName() => $id
            ]);
        }
    }

    /**
     * 根据条件删除数据
     * @param $criteria
     * @return bool|null
     */
    public function deleteBy($criteria)
    {
        if ($this->cache) {
            if (method_exists($this->model, 'runSoftDelete')) {
                $res = $this->model->newInstance()->buildQuery($criteria)->get();
            } else {
                $res = $this->model->newInstance()->buildQuery($criteria)->get();
            }
            foreach ($res as $item) {
                Cache::forget($this->getCacheId($item[$this->model->getKeyName()]));
            }
        }
        if (method_exists($this->model, 'runSoftDelete')) {
            return $this->model->newInstance()->buildQuery($criteria)->delete();
        } else {
            return $this->model->newInstance()->buildQuery($criteria)->delete();
        }
    }

    /**
     * 根据条件恢复数据
     * @param $criteria
     * @return bool|null
     */
    public function restoreBy($criteria)
    {
        if ($this->cache) {
            $res = $this->model->newInstance()->buildQuery($criteria)->get();
            foreach ($res as $item) {
                Cache::forget($this->getCacheId($item[$this->model->getKeyName()]));
            }
        }
        return $this->model->newInstance()->buildQuery($criteria)->restore();
    }

    /**
     * 启用
     * @param $id
     * @return bool|null
     */
    public function enabled($id)
    {
        if ($this->cache) {
            Cache::forget($this->getCacheId($id));
        }
        $model = $this->model->newInstance();
        return $model->whereKey($id)->restore();
    }

    /**
     * 禁用
     * @param $id
     * @return mixed
     */
    public function disabled($id)
    {
        if ($this->cache) {
            Cache::forget($this->getCacheId($id));
        }
        $model = $this->model->newInstance();
        return $model->whereKey($id)->update(['status' => $model::STATUS_DISABLED]);
    }

    /**
     * id获取详情
     * @param $id
     * @return BaseModel
     */
    public function findOneById($id, $fields = '*')
    {
        $model = $this->model->newInstance();
        if ($this->cache && $fields == '*') {
            $info = Cache::remember($this->getCacheId($id), config('cache.time'), function () use ($model, $id, $fields) {
                if (method_exists($model, 'runSoftDelete')) {
                    return $model->whereKey($id)->select(DB::raw($fields))->first();
                }else {
                    return $model->whereKey($id)->select(DB::raw($fields))->first();
                }
            });
        } else {
            if (method_exists($model, 'runSoftDelete')) {
                $info = $model->whereKey($id)->selectRaw($fields)->first();
            }else {
                $info = $model->whereKey($id)->select(DB::raw($fields))->first();
            }
        }
        return $info;
    }

    /**
     * 查找数据
     * @param array|string $criteria
     * @return BaseModel
     */
    public function findOneBy($criteria, $fields = '*')
    {
        return $this->model->newInstance()->buildQuery($criteria)->select(DB::raw($fields))->first();
    }

    /**
     * 查找数据
     * @param array|string $criteria
     * @return Collection
     */
    public function findBy($criteria, $fields = '*')
    {
        return $this->model->newInstance()->buildQuery($criteria)->select(DB::raw($fields))->get();
    }

    /**
     * 查询符合条件的行数
     * @param $criteria
     * @return int
     */
    public function count($criteria)
    {
        return $this->model->newInstance()->buildQuery($criteria)->count();
    }

    /**
     * sql原生查询
     * @param $sql
     * @return array
     */
    public function query($sql)
    {
        $data = $this->model->getConnection()->select($sql);
        return json_decode(json_encode($data), true);
    }

    /**
     * 字段唯一性性验证
     * 修改数据验证时请组装主键notIn条件语句,返回false时为存在重复
     * @param $field
     * @param $value
     * @param $criteria
     * @return bool
     */
    public function checkFieldUnique($field, $value, $criteria)
    {
        $collection = $this->model->newInstance()->buildQuery($criteria)->selectRaw($field)->get();
        if (empty($collection->toArray())) {
            return true;
        }
        $checkArray = array_column($collection->toArray(), $field);
        if (in_array($value, $checkArray, true)) {
            return false;
        }
        return true;
    }

    /**
     * Increment a column's value by a given amount.
     * @param $criteria
     * @param $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function incrementBy($criteria, $column, $amount = 1, array $extra = [])
    {
        return $this->model->newInstance()->buildQuery($criteria)->increment($column, $amount, $extra);
    }

    /**
     * Decrement a column's value by a given amount.
     * @param $criteria
     * @param $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    public function decrementBy($criteria, $column, $amount = 1, array $extra = [])
    {
        return $this->model->newInstance()->buildQuery($criteria)->decrement($column, $amount, $extra);
    }

    /**
     * 获取某一字段值
     * @param $field
     * @param $criteria
     * @return string|int|array
     */
    public function getFieldBy($field, $criteria)
    {
        $findOne = $this->findOneBy($criteria, $field);
        $findOne = $findOne ? $findOne->toArray() : [];
        return $findOne[$field]??'';
    }

    /**
     * 根据id获取某一个字段值
     * @param $field
     * @param $id
     * @return array|int|string
     */
    public function getFieldById($field, $id)
    {
        return $this->getFieldBy($field, [
            $this->model->getKeyName() => $id
        ]);
    }

    /**
     * 获取模型的table
     * @return string
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * 过滤数据库字段
     * @param $data
     * @return array
     */
    public function create($data)
    {
        return $this->model->filter($data);
    }

}