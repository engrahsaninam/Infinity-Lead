<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subscription->cid }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .company-name {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: right
        }

        .section-title {
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .info-table tr{
            vertical-align: top!important;
        }
        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px 10px;
        }

        .items-table th{
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background: #f5f5f5;
        }
        .text-right{
            text-align: right!important;
        }
    </style>
</head>

<body>
    <div class="company-name">
        Infinity Lead
    </div>
    <div class="company-name" style="text-align: left;">
        Invoice
    </div>

    <div class="section-title">Billing Information</div>

    <table style="width:100%">
        <tr style="vertical-align: top!Important;">
            <td style="width: 50%;">
                <table class="info-table">
                    <tr>
                        <td><strong>First Name:</strong></td>
                        <td>{{ $billing->first_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Name:</strong></td>
                        <td>{{ $billing->last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $billing->email ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $billing->phone ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>{{ $billing->address ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Country:</strong></td>
                        <td>{{ $billing->country ?? '' }}</td>
                    </tr>
                </table>

            </td>
            <td style="width: 50%;">
                <table class="info-table">
                    <tr>
                        <td><strong>Invoice#:</strong></td>
                        <td>{{ $subscription->cid  }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created at:</strong></td>
                        <td>{{ $subscription->start ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Due Date:</strong></td>
                        <td>{{ $subscription->end ?? '' }}</td>
                    </tr>
                   
                </table>

            </td>
        </tr>
    </table>

    <div class="section-title">Purchase Details</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="70%">Item</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p>Plan: {{ $subscription->plan->name }}</p>
                    <p>Credits: {{$subscription->plan->credits}}</p>
                </td>
                <td class="text-right">{{ $subscription->price  }} {{ $subscription->code  }}</td>
            </tr>
            <tr style="border-top: 1px solid #ccc">
                <td></td>
                <td class="text-right" style="padding-top:10px;">
                    <b>Total:</b> {{ $subscription->price  }} {{ $subscription->code  }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>