@extends('emails.layout.emailapp')
@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr>
        <td class="p30-15" style="padding: 50px  30px 0px 30px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="text2 pb15"
                        style="color:#585858; font-family:'Montserrat', sans-serif; font-size:14px; line-height:20px; padding-bottom: 10px;">
                        Dear Customer<br>
                        {!! $data['name'] !!},<br>
                        <br>
                            <p>
                                We are sorry to inform you that we are unable to process your prescription right now. <br>
                                Your prescription has been declined.<br>
                            </p>
                        <p>
                            <b> Declined reason: </b>
                            {!! $data['reject_reason'] !!}<br>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection