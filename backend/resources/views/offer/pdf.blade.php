<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oferta</title>
    <style>

           @page {
        margin: 40px 35px 20px 35px; /* Dostosowanie marginesów */
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 8px;
        text-align: left;
    }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: 700;
        }
        .table td {
            background-color: #fafafa;
        }
        .header-table {
        width: 100%;
        border-collapse: collapse;
        }
        .header-info {
            text-align: right;
        }
        .info {
            font-size: 12px;
        }
        
        .customer {
            font-size: 13px;
        }

    </style>
</head>
<body>
<table class="header-table" style="width: 100%; border-collapse: collapse;">
    <tr>
        <td class="info" style="width: 50%; vertical-align: top;">
            <strong>Mastermet Sp. z o.o.</strong><br>
            ul. Międzyrzeck 3/1<br>
            50-421 Wrocław<br><br>
            tel. kom. +48 536 980 972<br>
            regeneracja@mastermet.eu<br>
            www.mastermet.eu<br><br><br>
            <img src="{{ storage_path('app/public/images/mastermet-logo.png') }}" alt="Logo Mastermet">
        </td>
        <td class="header-info" style="width: 50%; vertical-align: top; text-align: left;">
            <span class="info">NIP: 8971745244<<br>
            REGON: 020817441<br>
            KRS 0000312613 SR Wrocław-Farbryczna</span><br><br>
            OFERTA NR: {{ $offer->id }}/{{ \Carbon\Carbon::now()->format('d/m/Y') }}<br>
            Data: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span><br>
            Firma: {{ $offer->customer->name }}<br>
            NIP: {{ $offer->customer->nip }}<br>
            Adres: {{ $offer->customer->city }}, {{ $offer->customer->address }}</span>
        </td>
    </tr>
</table>
   
    <table class="table">
        <thead>
            <tr>
                <th class="min-w-[600px]">Symbol</th>
                <th>Nazwa usługi</th>
                <th>Cena jednostkowa netto</th>
                <th>Ilość</th>
                <th>Cena całkowita netto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offerDetails as $detail)
                <tr>
                <td>
                    {{ $detail->toolGeometry->toolType->tool_type_name }} 
                    D{{ $detail->toolGeometry->diameter }}  
                    Z-{{ $detail->toolGeometry->flutes_number }}
                    
                    @if($detail->radius != 0)
                        R{{ $detail->radius }}
                    @endif
                </td>
                <td>{{ $detail->description }}</td>
                    <td>{{ $detail->tool_net_price +  $detail->coating_net_price}} PLN</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ ($detail->tool_net_price + $detail->coating_net_price) *  $detail->quantity}} PLN</td>
                </tr>
            @endforeach
        </tbody>
        <tr>
            <td style="visibility: hidden"></td>
            <td style="visibility: hidden"></td>
            <td style="visibility: hidden"></td>
            <td style="visibility: hidden"></td>
            <td> 
            <strong>Kwota oferty:</strong> {{ $offer->total_price }} PLN
            </td>
        </tr>
    </table>
<div class="footer">
    <p>Do podanych cen należy doliczyć podatek VAT w wysokości 23%.</p>
    <p>Przy zamówieniu prosimy o powołanie się na powyższy nr oferty.</p>
    <ol>
        <li>Termin realizacji: 12-15 dni roboczych</li>
        <li>Ważność oferty: 7 dni</li>
        <li>W przypadku braku opakowań: 2zł/szt</li>
        <li>Warunki płatności: przelew 14 dni</li>
        <li>Transport: 21,90 zł (poniżej wartości zamówienia 1000 zł)</li>
        <li>Oferty w walucie euro rozliczane wg aktualnego kursu sprzedaży dewiz BNP Paribas Bank z dnia wystawienia faktury VAT</li>
    </ol>
</div>
</body>
</html>
