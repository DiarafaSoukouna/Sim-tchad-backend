<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;

class GenerateFile extends Controller
{
    public function generate(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'categorie_id' => 'nullable|exists:categories,id',
            'secteur_id' => 'nullable|exists:sectors,id',
            'type_produit_id' => 'nullable|exists:type_produits,id',
            'type' => 'required|string|in:categories,secteurs,types,tous',
            'type_file' => 'required|string|in:pdf,word'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $validation->errors(),
            ], 422);
        }

        // 🔥 QUERY
        $query = Product::with(['speculation.categorie.sectors']);

        // 🎯 FILTRES

        // Si le type est "tous", on ne filtre ni catégorie ni secteur ni type produit
        if ($request->type !== 'tous') {

            if ($request->type === 'categories' && $request->categorie_id) {
                $query->whereHas('speculation.categorie', function ($q) use ($request) {
                    $q->where('id', $request->categorie_id);
                });
            }

            if ($request->type === 'secteurs' && $request->secteur_id) {
                $query->whereHas('speculation.categorie.sectors', function ($q) use ($request) {
                    $q->where('id', $request->secteur_id);
                });
            }

            if ($request->type === 'types' && $request->type_produit_id) {
                $query->where('product_type_id', $request->type_produit_id);
            }
        }

        // 📅 FILTRE DATE
        // Appliqué uniquement si les deux dates sont fournies
        // Sinon on retourne tous les produits sans filtre de date
        if ($request->date_start && $request->date_end) {
            $query->whereBetween('production_date', [
                $request->date_start,
                $request->date_end
            ]);
        }

        $products = $query->get();


        // 📄 PDF
        if ($request->type_file === 'pdf') {
            $pdf = Pdf::loadView('pdf.produits', [
                'produits' => $products
            ]);

            return $pdf->download('rapport-produits-' . date('Y-m-d') . '.pdf');
        }

        // 📄 WORD
        if ($request->type_file === 'word') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $section->addText('Rapport Produits', ['bold' => true, 'size' => 16]);

            $table = $section->addTable();

            // Header
            $table->addRow();
            $table->addCell(2000)->addText('Nom');
            $table->addCell(2000)->addText('Code');
            $table->addCell(2000)->addText('Prix');
            $table->addCell(2000)->addText('Quantité');
            $table->addCell(2000)->addText('Origine');

            foreach ($products as $p) {
                $table->addRow();
                $table->addCell(2000)->addText($p->name);
                $table->addCell(2000)->addText($p->code);
                $table->addCell(2000)->addText($p->price);
                $table->addCell(2000)->addText($p->quantity);
                $table->addCell(2000)->addText($p->origin);
            }

            $fileName = 'rapport-produits.docx';
            $tempFile = tempnam(sys_get_temp_dir(), $fileName);

            $phpWord->save($tempFile, 'Word2007');

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }

        return response()->json([
            'message' => 'Erreur inattendue'
        ], 500);
    }
}