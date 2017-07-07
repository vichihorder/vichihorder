
    @foreach($warehouse_list as $warehouse_list_item)
        @foreach($action_list as $action_list_item)
            <p>
                {{$action_list_item}} <strong>0</strong> kiện - {{$warehouse_list_item->name}} ({{$warehouse_list_item->code}})

            </p>
        @endforeach
        <br>

    @endforeach

