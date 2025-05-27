<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Oferta</title>
    <style>
        @page {
            margin: 40px 35px 60px 35px; /* Margin to give space for footer */
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.6;
            padding-bottom: 250px; /* Zapewnia wystarczającą przestrzeń dla stopki */
            margin-top: 0px; /* Zapewnia miejsce na nagłówek */
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 5px;
            vertical-align: top;
        }

        .header-table img {
            max-height: 50px;
        }

        /* TABELA */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
            margin-left: auto;
            margin-right: auto;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .table td {
            background-color: #fafafa;
        }

        /* STOPKA */
        footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: left;
            font-size: 9px;
            padding: 10px 0;
            border-top: 1px solid black;
        }

        .footer-content {
            text-align: left;
            font-size: 9px;
        }

        .footer-content ol {
            margin: 0;
            padding-left: 20px;
        }

        .footer-content p, .footer-content ol {
            margin: 0;
            padding: 5px 0;
        }

        .footer-content .pagenum {
            text-align: center;
            font-size: 9px;
            padding-top: 10px;
        }

        /* Paginate Footer */
        .pagenum:before {
    content: "Strona " counter(page);
}
    </style>

</head>
<body>

<!-- STOPKA -->
<footer>
    <div class="footer-content">
        <p>Do podanych cen należy doliczyć podatek VAT w wysokości 23%.</p>
        <p>Przy zamówieniu prosimy o powołanie się na powyższy nr oferty.</p>
        <ol>
            <li>Termin realizacji: {{$pdfInfo['deliveryTime']}}</li>
            <li>W przypadku braku opakowań doliczamy koszt 2 pln/szt.</li>
            <li>Ważność oferty: {{$pdfInfo['offerValidity']}}</li>
            <li>Warunki płatności: {{$pdfInfo['paymentTerms']}}</li>
            <li>Transport: 21,90 zł (poniżej wartości zamówienia 1000 zł)</li>
            <li>Oferty w walucie euro rozliczane wg kursu BNP Paribas Bank z dnia faktury VAT</li>
        </ol>
        <p class="pagenum"></p>
    </div>
</footer>

<!-- HEADER -->
<table class="header-table">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <img src="{{ public_path('images/mastermet-logo.png') }}" style="max-height: 50px;"><br><br>
            <strong>Mastermet Sp. z o.o.</strong><br>
            ul. Międzyrzecka 3/1<br>
            50-421 Wrocław<br>
            NIP: 8971745244<br><br>
            regeneracja@mastermet.eu<br>
            tel. +48 881 707 222<br>
            tel. +48 533 946 422<br>
            www.mastermet.eu
        </td>
        <td style="width: 50%; text-align: center;">
            <div style="background-color: #f2f2f2; padding: 15px; border-radius: 6px;">
                <div style="font-size: 20px; font-weight: bold;">OFERTA HANDLOWA</div>
                <div style="margin-top: 10px;">
                    Nr oferty: <strong>{{ $offer->id }}/{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong><br>
                    Data wystawienia: <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong><br>
                    Dostawa/usługa: <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong>
                </div>
            </div>
            <br>
            <div style="text-align: left;">
                <strong>Nabywca:</strong><br>
                Firma: {{ $offer->customer->name }}<br>
                NIP: {{ $offer->customer->nip }}<br>
                Adres: {{ $offer->customer->address }}, {{ $offer->customer->city }}
            </div>
        </td>
    </tr>
</table>

<!-- TABELA -->
<table class="table">
    <thead>
        <tr>
            <th>Symbol</th>
            <th>Nazwa usługi</th>
            <th>Cena jednostkowa netto</th>
            @if($pdfInfo['displayDiscount'])
                <th>Cena po rabacie</th>
                <th>Rabat</th>
            @endif
            <th>Ilość</th>
            <th>Cena całkowita netto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($offerDetails as $detail)
            @php
                $cleanDescription = preg_replace('/\[.*?\]/', '', $detail->description);
                $basePrice = $pdfInfo['displayDiscount'] ? $detail->tool_net_price + $detail->coating_net_price : ($detail->tool_net_price + $detail->coating_net_price) * (1 - $detail->discount / 100);
                $discount = $detail->discount ?? 0;
                $discountedPrice = $basePrice * (1 - $discount / 100);
                $unitPrice = $pdfInfo['displayDiscount'] ? $discountedPrice : $basePrice; 
                $total = $unitPrice * $detail->quantity;
            @endphp
            <tr>
                <td>
                    {{ $detail->symbol }}
                </td>
                <td>{{ $detail->description }}</td>
                <td>{{ number_format($basePrice, 2) }} PLN</td>
                @if($pdfInfo['displayDiscount'])
                    <td>{{ number_format($discountedPrice, 2) }} PLN</td>
                    <td>{{ $discount }}%</td>
                @endif
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($total, 2) }} PLN</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $pdfInfo['displayDiscount'] ? 6 : 4 }}"></td>
            <td><strong>Kwota oferty:</strong> {{ number_format($offer->total_price, 2) }} PLN</td>
        </tr>
    </tfoot>
</table>



</body>
</html>
