<?php

namespace App\Base\Models;

use App\Base\Traits\Criteria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class BaseModel extends Model
{
    use Criteria;

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    protected $hidden = [
        'deleted_at'
    ];

    /**
     * 自动获取数据库表字段
     */
    public function setFillable()
    {
        $this->fillable = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->table);
    }

    /**
     * 过滤掉非数据库字段的数据
     * @param $data
     * @return array
     */
    public function filter($data)
    {
        if (empty($this->fillable)) {
            $this->setFillable();
        }
        $result = [];
        if (empty($data) || !is_array($data)) {
            return $result;
        }
        foreach ($this->fillable as $item) {
            if (isset($data[$item])) {
                $result[$item] = $data[$item];
            }
        }
        return $result;
    }


    /**
     * 覆盖父类方法 新建连接
     * @return \Illuminate\Database\Query\Builder|QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new Builder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    public function isJsonCastingField($field)
    {
        if (isset($this->casts[$field]) && $this->casts[$field] == 'array') {
            return true;
        }
        return false;
    }
}
