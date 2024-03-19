<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Title Here</title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    .logo {
      display: inline-block;
      width: 100%;
      padding-bottom: 10px;
    }

    .logo img {
      max-width: 250px;
      display: block;
      width: 100%;
      margin: 0;
    }

    .pdf-section {
      margin: 0 0 30px;
      padding: 25px;
      border: 1px solid #000;
      border-radius: 10px;
      text-transform: capitalize;
      display: block;
    }

    .detail {
      margin-bottom: 12px;
      display: flex;
    }

    .title {
      font-weight: 600;
      font-size: 21px;
      line-height: 30px;
      min-width: 100px;
    }

    .dot {
      font-weight: 600;
      font-size: 20px;
      line-height: 30px;
    }

    .text {
      font-size: 20px;
      font-weight: normal;
      line-height: 30px;
      text-transform: capitalize;
    }
  </style>
</head>

<body>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tbody>
    @foreach($customerList as $customer)
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">{{ $customer->customerDetail->customer_name ?? ''}}</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">{!! $customer->customerDetail->address ? html_entity_decode($customer->customerDetail->address) : ''!!} {{ $customer->customerDetail->pin_code ?? ''}}</span>
          </div>
          <div class="detail">
            <span class="title">Contact No.</span>
            <span class="dot">:</span>
            <span class="text">{{ $customer->customerDetail->invoice ?? ''}}</span>
          </div>
          <div class="detail">
            <span class="title">Remarks</span>
            <span class="dot">:</span>
            <span class="text">{!! $customer->customerDetail->remark ? html_entity_decode($customer->customerDetail->remark) : ''!!} </span>
          </div>
        </td>
      </tr>
      @endforeach
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>
      <tr>
        <td class="logo" colspan="2">
          <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </td>
        <td colspan="2" class="pdf-section">
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
          <div class="detail">
            <span class="title">address</span>
            <span class="dot">:</span>
            <span class="text">A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061A - 4 Ratnadeep Avenue Infront Of Satadhar Char Rasta BRTS Station, Ghatlodia - 380061</span>
          </div>
          <div class="detail">
            <span class="title">customer</span>
            <span class="dot">:</span>
            <span class="text">Well Worx Pvt. Ltd.</span>
          </div>
        </td>
      </tr>

    </tbody>
  </table>
</body>

</html>