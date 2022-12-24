<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Invoice styling -->
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td style="font-size: 40px">
                                {{ $title }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                Aplikasi Perhitungan<br />
                                Apriori, Moving Average, dan EOQ
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                @foreach ($table as $row)
                    <td>{{ $row['title'] }}</td>
                @endforeach
            </tr>

            @foreach ($resource as $datum)
                <tr class="item">
                    @foreach ($table as $row)
                        @if (is_array($datum->{$row['key']}))
                            @foreach ($datum->{$row['key']} as $value)
                                @if (isset($value['font']) && $value['font'] == 'bold')
                                    <span className="font-extrabold">{{ $value['word'] }}</span>;
                                @elseif (isset($value['font']) && $value['font'] == 'italic')
                                    <span className="italic">{{ $value['word'] }}</span>;
                                @elseif (isset($value['font']) && $value['font'] == 'underline')
                                    <span className="underline">{{ $value['word'] }}</span>;
                                @else
                                    <span>{{ $value['word'] }}</span>;
                                @endif
                            @endforeach
                        @else
                            <td>{{ $datum->{$row['key']} }}</td>
                        @endif
                    @endforeach

                </tr>
            @endforeach

        </table>
    </div>
</body>

</html>
