<table class="table card-table">
    <tbody>
    @foreach($statistic as $s)
        <tr>
            <td>{{$s['name']}}</td>
            <td class="text-right">
                <span class="text-danger"><span class="lines" data-money="{{$s['money']}}">{{$s['money']}}</span>đ</span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>