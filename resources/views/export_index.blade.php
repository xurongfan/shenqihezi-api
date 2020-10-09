@php
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header("Content-Disposition:attachment;filename={$fName}");
    header("Content-Transfer-Encoding:binary");
@endphp
<html>
<head>
    <title>{{ $tName }}</title>
</head>
<body>
<!-- <table border="1">
    <tr>
        <td colspan="55">
            {{ $tName }}
        </td>
    </tr>
</table> -->
<table border="1">
    <tr>
        <th>序号</th>
        <th>标题</th>
        <th>内容</th>
        <th>推送方式</th>
        <th>厂商通道</th>
        <th>厂商发送数</th>
        <th>发送数(备)</th>
        <th>厂商收到数</th>
        <th>收到数(备)</th>
        <th>厂商点击数</th>
        <th>点击数(备)</th>
        <th>点击启动APP数</th>
        <th>打开详情页</th>
        <th>厂家点开率</th>
        <th>点开率(备)</th>
    </tr>
    @if(!$data->isEmpty())
        @foreach($list_data as $id => $item)
            <tr>
                <td rowspan="{{ (count($item)+2) }}">{{ $id }}</td>
                <td style="width: 12%" rowspan="{{ (count($item)+2) }}">
                    {{ $list_data_total[$id]['title'] }}</td>
                <td style="width: 15%" rowspan="{{ (count($item)+2) }}">
                    {{ $list_data_total[$id]['note_content'] }}</td>
                <td rowspan="{{ (count($item)+2) }}">
                    {{ isset($type_arr[$list_data_total[$id]['type']])?$type_arr[$list_data_total[$id]['type']]:'' }}</td>
            </tr>
            <tr>
                <td>总计</td>
                <td>{{ $list_data_total[$id]['send_num'] }}</td>
                <td>{{ $list_data_total[$id]['send_num2'] }}</td>
                <td>{{ $list_data_total[$id]['get_num'] }}</td>
                <td>{{ $list_data_total[$id]['get_num2'] }}</td>
                <td>{{ $list_data_total[$id]['click_num'] }}</td>
                <td>{{ $list_data_total[$id]['click_num2'] }}</td>
                <td>{{ $list_data_total[$id]['click_app_start'] }}</td>
                <td>{{ $list_data_total[$id]['click_open_detail'] }}</td>
                <td>{{ sprintf('%.2f',$list_data_total[$id]['send_num']==0?0.00:$list_data_total[$id]['click_open_detail']/$list_data_total[$id]['send_num']*100) }}%</td>
                <td>{{ sprintf('%.2f',$list_data_total[$id]['send_num2']==0?0.00:$list_data_total[$id]['click_open_detail']/$list_data_total[$id]['send_num2']*100) }}%</td>
            </tr>
            @foreach($item as $list)
                <tr>
                    <td>
                        {{ isset($push_channel_arr[$list['push_channel']])?$push_channel_arr[$list['push_channel']]:$list['push_channel'] }}</td>
                    <td style="background-color:#e6e6e6">
                        {{ $list['send_num'] }}</td>
                    <td class='editTable change' data-str='send_num2' data-id="{{ $list['id'] }}">
                        {{ $list['send_num2'] }}</td>
                    <td style="background-color:#e6e6e6">
                        {{ $list['get_num'] }}</td>
                    <td class='editTable change' data-str='get_num2' data-id="{{ $list['id'] }}">
                        {{ $list['get_num2'] }}</td>
                    <td style="background-color:#e6e6e6">
                        {{ $list['click_num'] }}</td>
                    <td class='editTable change' data-str='click_num2' data-id="{{ $list['id'] }}">
                        {{ $list['click_num2'] }}</td>
                    <td class='editTable change' data-str='click_app_start' data-id="{{ $list['id'] }}">
                        {{ $list['click_app_start'] }}</td>
                    <td class='editTable change' data-str='click_open_detail' data-id="{{ $list['id'] }}">
                        {{ $list['click_open_detail'] }}</td>
                    <td style="background-color:#e6e6e6">
                        {{ sprintf('%.2f',$list['send_num']==0?0.00:$list['click_open_detail']/$list['send_num']*100) }}%</td>
                    <td>
                        {{ sprintf('%.2f',$list['send_num2']==0?0.00:$list['click_open_detail']/$list['send_num2']*100) }}%</td>
                </tr>
            @endforeach
        @endforeach
    @else
        <tr>
            <td colspan="14">无数据</td>
        </tr>
    @endif
</table>
</body>

</html>

