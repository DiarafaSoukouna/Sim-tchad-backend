<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Produits - SIM Tchad</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap');

        :root {
            --color-primary: #0f172a;
            --color-secondary: #334155;
            --color-accent: #c9a227;
            --color-accent-light: #f5f0e1;
            --color-background: #fafafa;
            --color-surface: #ffffff;
            --color-border: #e2e8f0;
            --color-text-primary: #0f172a;
            --color-text-secondary: #64748b;
            --color-text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-background);
            color: var(--color-text-primary);
            font-size: 11px;
            line-height: 1.6;
        }

        .page {
            width: 100%;
            background: var(--color-surface);
        }

        .header {
            background: linear-gradient(135deg, var(--color-primary), #1e293b);
            color: white;
            padding: 30px 40px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-container {
            width: 70px;
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .logo {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .header-text h1 {
            font-size: 18px;
            margin-top: 5px;
            color : var(--color-accent);
        }

        .header-text h2 {
            font-size: 12px;
            color: var(--color-accent);
            text-transform: uppercase;
        }

        .content {
            padding: 30px 40px;
        }

        .table-container {
            border: 1px solid var(--color-border);
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f1f5f9;
            padding: 12px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--color-border);
            text-align: left;
        }

        tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--color-border);
            vertical-align: middle;
        }

        .photo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .no-photo {
            width: 50px;
            height: 50px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .product-name {
            font-weight: 600;
        }

        .product-code {
            font-size: 10px;
            color: var(--color-text-muted);
        }

        .price {
            font-weight: 700;
        }

        .quantity {
            text-align: center;
            font-weight: 600;
        }

        .footer {
            padding: 20px 40px;
            background: #f8fafc;
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="{{ public_path('logo.jpg') }}" class="logo">
            </div>
            <div class="header-text">
                <h1>Système d'Information sur les Marchés du Tchad</h1>
                <h2>Rapport des Produits</h2>
                <small style="color: var(--color-accent);">Généré le {{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Qté</th>
                        <th>Dates</th>
                        <th>Origine</th>
                    </tr>
                </thead>

                <tbody>
                    @if($produits->isEmpty())
                        <tr>
                            <td colspan="6" style="text-align:center;padding:20px;">
                                Aucun produit disponible pour cette période
                            </td>
                        </tr>
                    @else
                        @foreach($produits as $p)
                        <tr>

                            <!-- PHOTO -->
                            <td>
                                @if($p->photo)
                                    <img src="{{ public_path('storage/'.$p->photo) }}" class="photo">
                                @else
                                    <div class="no-photo">—</div>
                                @endif
                            </td>

                            <!-- PRODUIT -->
                            <td>
                                <div class="product-name">{{ $p->name }}</div>
                                <div class="product-code">{{ $p->code }}</div>
                            </td>

                            <!-- PRIX -->
                            <td class="price">
                                {{ number_format($p->price, 0, ',', ' ') }} FCFA
                            </td>

                            <!-- QUANTITE -->
                            <td class="quantity">
                                {{ $p->quantity }}
                            </td>

                            <!-- DATES -->
                            <td>
                                Prod: {{ \Carbon\Carbon::parse($p->production_date)->format('d/m/Y') }} <br>
                                Ajout: {{ $p->created_at?->format('d/m/Y') }}
                            </td>

                            <!-- ORIGINE -->
                            <td>
                                {{ $p->origin }}
                            </td>

                        </tr>
                        @endforeach
                    @endif
                </tbody>

            </table>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>Total produits: {{ count($produits) }}</div>
        <div>SIM Tchad</div>
    </div>

</div>

</body>
</html>