
@if(count($orders))
<div class="table-responsive">
    <table class="table table-hover">
    <thead>
        <tr>
        <th scope="col">Order Number</th>
        <th scope="col">Expected Delivery Date</th>
        <th scope="col">View</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders->take(10) as $key => $value)
        <tr>
        <td>#{{$value->order_number ?? ''}} </td>
        <td>{{getDateWithoutTimeFormate($value->expected_delivery_date)}}</td>
        <td><span data-id="{{Crypt::encrypt($value->id)}}" target="_blank" title='View Details' class='openOrderDetails text-default btn-label'><i class='fa fa-eye'></i></span></td>
        </tr>
        @endforeach
    </tbody>
    </table>
</div>

@else
<div class=container4>
    <h5>Orders are unavailable for this period.</h5>
</div>
@endif
        