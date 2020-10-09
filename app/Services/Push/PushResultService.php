<?php

namespace App\Services\Push;

use App\Base\Services\BaseService;
use App\Model\Push\PushArticle;
use App\Model\Push\PushBuryingPoint;
use App\Model\Push\PushCount;
use Illuminate\Support\Facades\DB;

class PushResultService extends BaseService
{
    /**
     * @param $request
     * @return array
     */
    public function index($request)
    {
        set_time_limit(0);
        $pageLimit = isset($request->page_size) ? $request->page_size : 5;
        $title = $request->title ?: '';
        $is_export = $request->is_export ?:'';

        $push_result_id = PushCount::select('push_article_id')->distinct()->pluck('push_article_id');
        $data = PushArticle::whereIn('id',$push_result_id)->where(function($query) use ($request){
            if($request->title != ''){
                $query->where('title','like','%'.$request->title.'%');
            }
        })
            ->orderBy('push_time','desc');
        if(empty($is_export)){
            $data = $data->paginate($pageLimit);
        }else{
            $data = $data->get();
        }
        $result_id = $data->pluck('id');

        $result_data = PushCount::select('push_article_send_count.*','push_article.title','push_article.note_content','push_article.type')
            ->whereIn('push_article_id',$result_id)
            ->leftJoin('push_article','push_article_send_count.push_article_id','push_article.id')
            ->orderBy('push_article_send_count.push_article_id','desc')
            ->get();

        $list_data = [];
        $list_data_total = [];
        if(!$result_data->isEmpty()){
            foreach ($result_data->toArray() as $list) {
                $url = env('JAVA_API_URL','http://dev.npush-java.uheixia.com')."/api/task/searchErrInfo"."?pushChannel=".$list['push_channel'].'&pushArticleId='.$list['push_article_id'];
                $postCurl = postCurl($url);
                $postResult = json_decode($postCurl,true);
                $apiResult = [];
                if (isset($postResult['code']) && $postResult['code'] == 200) {
                    $apiResult['errTotal'] = $postResult['data']['errTotal'] ?? 0;
                    $apiResult['errRate'] = isset($apiResult['total']) && $apiResult['total'] ? number_format($apiResult['errTotal']/$apiResult['total'],2) : 0;
                    $apiResult['errMsg'] = $postResult['data']['msg'] ?? '';
                }
                $list_data[$list['push_article_id']][] = $list + $apiResult;
                $list_data_total[$list['push_article_id']]['id'] = $list['push_article_id'];
                $list_data_total[$list['push_article_id']]['title'] = $list['title'];
                $list_data_total[$list['push_article_id']]['note_content'] = $list['note_content'];
                $list_data_total[$list['push_article_id']]['type'] = $list['type'];
                @$list_data_total[$list['push_article_id']]['send_num'] += $list['send_num'];
                @$list_data_total[$list['push_article_id']]['get_num'] += $list['get_num'];
                @$list_data_total[$list['push_article_id']]['click_num'] += $list['click_num'];
                @$list_data_total[$list['push_article_id']]['send_num2'] += $list['send_num2'];
                @$list_data_total[$list['push_article_id']]['get_num2'] += $list['get_num2'];
                @$list_data_total[$list['push_article_id']]['click_num2'] += $list['click_num2'];
                @$list_data_total[$list['push_article_id']]['click_app_start'] += $list['click_app_start'];
                @$list_data_total[$list['push_article_id']]['click_open_detail'] += $list['click_open_detail'];
            }
        }
        $PushArticle = new PushArticle();
        $type_arr = $PushArticle->type_arr;
        $push_channel_arr = $PushArticle->push_channel_arr;
        if(!empty($is_export)){
            $view_link = 'export_index';
            $tName = '推送结果';
            $fName = $tName.'.xls';

            return view($view_link)
                ->with('data', $data)
                ->with('list_data',$list_data)
                ->with('list_data_total',$list_data_total)
                ->with('title', $title)
                ->with('tName',$tName)
                ->with('fName',$fName)
                ->with('type_arr',$type_arr)
                ->with('push_channel_arr',$push_channel_arr);
        }
        foreach ($list_data_total as $key => $item) {
            $list_data_total[$key]['channel_list'] = $list_data[$key];
        }

        $data = $data ? $data->toArray() : [];
        return [
            'current_page' => $data['current_page'],
            'data' => array_values($list_data_total),
            'last_page' => $data['last_page'],
            'total' => $data['total'],
        ];
    }

    /**
     * 定时数据
     * @return string
     */
    public function auto($request = [])
    {
        set_time_limit(0);
        echo "推送结果-START...";
        $create_date = date("Y-m-d H:i:s");
        if(isset($request['set_date'])){
            $create_date = $request['set_date'];
        }
//        else{
//            if(date("i")%30!=0) return 'false';
//        }
        if(date("H:i")=='00:00'){
            $start = date("Y-m-d 00:00:01",strtotime("$create_date -2 day"));
            $end   = date("Y-m-d 23:59:59",strtotime("$create_date -1 day"));
        }else{
            $start = date("Y-m-d 00:00:01",strtotime("$create_date -1 day"));
            $end   = date("Y-m-d H:i:s",strtotime($create_date));
        }

        $push_id = PushArticle::whereBetween('push_time',[$start,$end])->pluck('id');

        if(empty($push_id)) return '无任务';

        //推送结果表任务汇总
        $PushResult = $this->model->newInstance()->whereIn('push_article_id',$push_id)->get()->toArray();

        foreach ($PushResult as $list) {
            if(empty($list['business_id'])||empty($list['app_package'])) continue;
            $data = [];
            //VIVO接入
            if($list['push_channel']=='VIVO'){
                $url = env('JAVA_API_URL','http://dev.npush-java.uheixia.com')."/api/task/searchByTaskId/VIVO/".$list['app_package']."?taskId=".$list['business_id'];
                $postCurl = postCurl($url);
                $postResult = json_decode($postCurl,true);

                if($postResult['code']==200){
                    $data = [];
                    $data['send_num'] = isset($postResult['data'][0]['send'])?$postResult['data'][0]['send']:0;
                    $data['get_num']  = isset($postResult['data'][0]['receive'])?$postResult['data'][0]['receive']:0;
                    $data['click_num']= isset($postResult['data'][0]['click'])?$postResult['data'][0]['click']:0;
                }

            }
            //友盟接入
            if($list['push_channel']=='YouMeng'){
                $url = env('JAVA_API_URL','http://dev.npush-java.uheixia.com')."/api/task/searchByTaskId/YouMeng/".$list['app_package']."?taskId=".$list['business_id'];

                $postCurl = postCurl($url);
                $postResult = json_decode($postCurl,true);

                if($postResult['code']==200 && $postResult['data']['ret']=='SUCCESS' && isset($postResult['data']['data']['status']) && $postResult['data']['data']['status']==2){
                    //安卓
                    if($list['app_package'] =='com.mg.xyvideo'){
                        $data['send_num'] = isset($postResult['data']['data']['sent_count'])?$postResult['data']['data']['sent_count']:0;
                        $data['get_num']  = isset($postResult['data']['data']['sent_count'])?$postResult['data']['data']['sent_count']:0;
                        $data['click_num']= isset($postResult['data']['data']['open_count'])?$postResult['data']['data']['open_count']:0;
                        //IOS
                    }else if($list['app_package'] == 'com.mg.westVideo'){
                        $data['send_num'] = isset($postResult['data']['data']['total_count'])?$postResult['data']['data']['total_count']:0;
                        $data['get_num']  = isset($postResult['data']['data']['sent_count'])?$postResult['data']['data']['sent_count']:0;
                        $data['click_num']= isset($postResult['data']['data']['open_count'])?$postResult['data']['data']['open_count']:0;
                    }
                }
            }
            //友盟接入
            if(!empty($data)){
                $this->model->newInstance()->where('business_id',$list['business_id'])->update($data);
            }
        }

        //第三方汇总数据至PushCount
        $PushResult = $this->model->newInstance()->select(DB::Raw("push_article_id,push_channel,sum(send_num)as send_num,sum(get_num)as get_num,sum(click_num)as click_num"))
            ->whereIn('push_article_id',$push_id)
            ->groupBy('push_article_id','push_channel')
            ->get()->toArray();
        foreach ($PushResult as $list) {
            $where = ['push_article_id'=>$list['push_article_id'],'push_channel'=>$list['push_channel']];
            $data = ['send_num'=>$list['send_num'],'get_num'=>$list['get_num'],'click_num'=>$list['click_num']];
            PushCount::query()->updateOrCreate($where,$data);
        }

        //推送埋点表数据汇总
        $XyBuryingPointPush = PushBuryingPoint::query()->select(DB::Raw("push_id,count(id)as num,push_channel,action_id"))
            ->whereIn('push_id',$push_id)
            ->groupBy('push_channel','action_id')
            ->get()->toArray();

        if($XyBuryingPointPush){
            foreach ($XyBuryingPointPush as $list) {
                $str = $list['action_id']=='push_click'?'click_app_start':'click_open_detail';
                $data = [];
                $data[$str] = $list['num'];
                PushCount::where(['push_article_id'=>$list['push_id'],'push_channel'=>$list['push_channel']])
                    ->update($data);
            }
        }

        echo "\r\nSUCCESS";
    }
}