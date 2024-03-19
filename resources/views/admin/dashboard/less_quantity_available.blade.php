
@if(count($productVariants))
<div class="table-responsive">
    <table class="table table-hover">
    <thead>
        <tr>
        <th scope="col">Name</th>
        <th scope="col">Stock</th>
        <th scope="col">View</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productVariants->take(10) as $key => $value)
       
        <tr>
        <td>{{$value->Product->name ?? ''}} ({{$value->quantity ?? '-'}} {{$value->unit ?? '-'}})</td>
        <td>{{$value->available_stock ?? '0'}}</td>
        <td><a href="{{route('admin.product.edit',$value->Product->uuid)}}" target="_blank" title='View Details' class='text-default btn-label'><i class='fa fa-eye'></i></a></td>
        </tr>
        @endforeach
    </tbody>
    </table>
</div>

@else
<div class=container4>
    <h5>Selling products are unavailable for this period.</h5>
</div>
@endif
        