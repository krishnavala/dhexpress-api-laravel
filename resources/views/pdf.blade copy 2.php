<!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title></title>
    </head>
    <style>
    .container { width: 100%; display: block; margin: 0 auto; max-width: 1250px; padding: 0 20px; }
  body { margin: 0; display: block; padding: 0; }
  * { margin: 0;padding: 0; box-sizing: border-box; -webkit-box-sizing: border-box; }
  .pdf-section { width: 100%; display: inline-block; padding: 80px 20px; }
  .pdf-section .pdf { width: 100%; display: block; margin: 0 auto; max-width: 800px; }
  .pdf-section .pdf-file { width: 100%; display: inline-block; margin-bottom: 30px; }
  .pdf-section .pdf-file .logo { width: 100%; display: inline-block; }
  .pdf-section .pdf-file .logo img { max-width: 250px; object-fit: contain; vertical-align: top; }
  .pdf-section .customer-detail { width: 100%; display: inline-block; margin-top: 20px; padding: 20px; border: 1px solid #000; border-radius: 10px; }
  .pdf-section .customer-detail .detail { display: flex; align-items: flex-start; width: 100%; gap: 5px; margin-bottom: 16px; }
  .pdf-section .customer-detail .detail span { font-size: 20px; font-weight: normal; line-height: 30px; text-transform: capitalize; }
  .pdf-section .customer-detail .detail span.title { font-weight: 600; width: 100%; max-width: 90px; }
  .pdf-section .customer-detail .detail span.dot { font-weight: 600; }
  </style>
    <body>
      <div class="pdf-section">
        <div class="container">
          <div class="pdf">
            
            @foreach($customerList as $customer)
            <div class="pdf-file">
              <div class="logo">
                <!-- <img src="image/logo.jpg" alt="logo" /> -->
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
              </div>
              <div class="customer-detail">
                <div class="detail name">
                  <span class="title">customer</span>
                  <span class="dot">:</span>
                  <span class="text">{{ $customer->customerDetail->customer_name ?? ''}}
                  </span>
                </div>
                <div class="detail address">
                  <span class="title">address</span>
                  <span class="dot">:</span>
                  <span class="text"
                    >{!! $customer->customerDetail->address ? html_entity_decode($customer->customerDetail->address) : ''!!} </span
                  >
                </div>
                <div class="detail">
                  <span class="title">PinCode</span>
                  <span class="dot">:</span>
                  <span class="text">{{ $customer->customerDetail->pin_code ?? ''}}</span>
                </div>
                <div class="detail">
                  <span class="title">Invoice</span>
                  <span class="dot">:</span>
                  <span class="text">{{ $customer->customerDetail->invoice ?? ''}}</span>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </body>
  </html>
