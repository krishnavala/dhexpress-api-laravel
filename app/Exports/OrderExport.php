<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExport implements FromCollection,WithHeadings, ShouldAutoSize,WithEvents
{
    public $status,$start_date,$end_date;

    public function __construct($status, $start_date, $end_date)
    {
        $this->status = $status;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $orderContent = Order::with('orderUser:id,name')
        ->with('orderProducts:id,order_id,product_id,product_variant_id,quantity',
        'orderProducts.product:id,name', 'orderProducts.productVariant:id,quantity,unit_id')
        ->select('id','order_number','total','sub_total','delivery_charge','discount_price','total_item','status_id','created_at','user_type','created_by','order_type_id')
        ->whereHas('orderUser', function($q) {
            $q->select('name','id');
        });
        $orderContent = $orderContent->orderBy('orders.created_at', 'desc');

        if (!empty($this->status)) {
            $status = $this->status;
            $orderContent = $orderContent->where('status_id',$status);
        }
        if(!empty($this->start_date) && !empty($this->end_date)){
            $orderContent =$orderContent->whereDate("created_at",'>=',$this->start_date)->whereDate("created_at",'<=',$this->end_date);
        }

        $orderContent = $orderContent->get()->makeHidden(['created_by','users.id','id']);  
        $csvData = [];
        if (count($orderContent)) {
            foreach ($orderContent as $okey => $order) { 
                if ($order->status_id ==OrderStatus::PENDING) {
                    $status = __("general.pending");
                }elseif ($order->status_id ==OrderStatus::REJECT) {
                    $status = __("general.declined");
                }elseif ($order->status_id ==OrderStatus::ACCEPT) {
                    $status = __("general.accept");
                }elseif ($order->status_id ==OrderStatus::CANCEL) {
                    $status = __("general.cancel");
                }elseif ($order->status_id ==OrderStatus::INPROGRESS) {
                    $status = __("general.Inprogress");
                }elseif ($order->status_id ==OrderStatus::DELIVERED) {
                    $status = __("general.delivered");
                }  
                 //Order type:
                 if ($order->order_type_id ==OrderType::NORMAL) {
                    $orderType = __("general.order_type.normal");
                }elseif ($order->order_type_id ==OrderType::SUGGESTION_REQUIRED) {
                    $orderType = __("general.order_type.suggestion");
                }elseif ($order->order_type_id ==OrderType::PRESCRIPTION_REQUIRED) {
                    $orderType = __("general.order_type.prescription");
                }else{
                    $orderType = __("general.order_type.from_prescription");
                }        
                if($order && $order->orderProducts)
                {
                    $orderProductInfo = $order->orderProducts;         
                    foreach ($orderProductInfo as $key => $orderProduct) { 
                        $orderNumber = '#'.$order->order_number;
                        $totalPrice =$order->total ?? 0;
                        $subTotalPrice =$order->sub_total ?? 0;
                        $shippingPrice =$order->delivery_charge ?? 0;
                        $discountPrice =$order->discount_price ?? 0;
                        $totalItem =$order->total_item ?? 0;
                        $createdAt = getDateFormate($order->created_at);
                        $customerName = $order->orderUser->name ?? "-";
                        $productName =$orderProduct->product->name ?? '-';
                        $productQuantity = $orderProduct->quantity ?? "0";
                        $productVariationName = $orderProduct->productVariant->quantity."-".$orderProduct->productVariant->unit;
                        if($key != 0){
                            $orderNumber = '';
                            $totalItem = '';
                            $totalPrice = '';
                            $subTotalPrice = '';
                            $shippingPrice = '';
                            $discountPrice = '';
                            $status = '';
                            $createdAt = '';
                            $customerName = '';
                        }
                        $csvData[$okey][$key]['order_number'] =$orderNumber;
                        $csvData[$okey][$key]['product_name'] = $productName;
                        $csvData[$okey][$key]['product_variation'] = $productVariationName;
                        $csvData[$okey][$key]['product_quantity'] = $productQuantity;
                        $csvData[$okey][$key]['sub_total_price'] =$subTotalPrice;
                        $csvData[$okey][$key]['shipping_price'] =$shippingPrice;
                        $csvData[$okey][$key]['discount_price'] =$discountPrice;
                        $csvData[$okey][$key]['total_price'] =$totalPrice;
                        $csvData[$okey][$key]['total_item'] =  $totalItem;
                        $csvData[$okey][$key]['created_at'] =  $createdAt;
                        $csvData[$okey][$key]['order_type'] =  $orderType;
                        $csvData[$okey][$key]['status'] =  $status;
                        $csvData[$okey][$key]['customer_name'] =  $customerName;
                    } 
                }
            }
        }
        return collect($csvData);
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Product Name',
            'Product Variation',
            'Quantity',
            'SubTotal (P)',
            'Shipping (P)',
            'Discount (P)',
            'Total Price (P)',
            'Total Item',
            'Order Date',
            'Order Type',
            'Status',
            'Customer Name',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                
                $cellRange = 'A1:L1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
