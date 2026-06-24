<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\HistoriqueMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class EnregistrementController extends Controller
{
    /**
     * Constructeur - middleware défini dans les routes ou via route::group
     */
    public function __construct()
    {
        // La méthode middleware n'est pas disponible comme ça dans Laravel 13
        // Les middlewares sont déjà gérés dans le fichier routes/web.php
        // Pas besoin de les redéfinir ici
    }

    /**
     * Dashboard des enregistrements
     */
    public function index()
    {
        // Statistiques des enregistrements
        $stats = [
            'total_enregistrements' => Patient::count(),
            'enregistrements_aujourdhui' => Patient::whereDate('created_at', today())->count(),
            'enregistrements_semaine' => Patient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'enregistrements_mois' => Patient::whereMonth('created_at', now()->month)->count(),
            'enregistrements_hier' => Patient::whereDate('created_at', today()->subDay())->count(),
        ];

        // Derniers enregistrements
        $recentEnregistrements = Patient::orderBy('created_at', 'desc')->limit(15)->get();

        // Graphique des 7 derniers jours
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = ucfirst($date->locale('fr')->isoFormat('ddd D MMM'));
            $chartData[] = Patient::whereDate('created_at', $date)->count();
        }

        // Top des jours d'enregistrement
        $topJours = Patient::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('receptionniste.enregistrements.index', compact('stats', 'recentEnregistrements', 'chartLabels', 'chartData', 'topJours'));
    }

    /**
     * Historique complet des enregistrements
     */
    public function historique(Request $request)
    {
        $search = $request->get('search');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');
        $type = $request->get('type');

        // Vérifier si la table HistoriqueMedical existe
        $historiquesExist = false;
        try {
            $query = DB::table('historique_medical');
            $historiquesExist = true;
        } catch (\Exception $e) {
            // Table n'existe pas, utiliser une collection vide
            $historiques = collect();
        }

        if ($historiquesExist) {
            $query = DB::table('historique_medical')
                ->leftJoin('patients', 'historique_medical.patient_id', '=', 'patients.id_patient')
                ->leftJoin('users', 'historique_medical.user_id', '=', 'users.id_user');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('historique_medical.action', 'LIKE', "%{$search}%")
                      ->orWhere('historique_medical.description', 'LIKE', "%{$search}%")
                      ->orWhere('historique_medical.table_concernee', 'LIKE', "%{$search}%");
                });
            }

            if ($dateDebut) {
                $query->whereDate('historique_medical.created_at', '>=', $dateDebut);
            }

            if ($dateFin) {
                $query->whereDate('historique_medical.created_at', '<=', $dateFin);
            }

            if ($type) {
                $query->where('historique_medical.table_concernee', $type);
            }

            $historiques = $query->orderBy('historique_medical.created_at', 'desc')
                ->select('historique_medical.*', 'patients.nom as patient_nom', 'patients.prenom as patient_prenom', 'users.name as user_name')
                ->paginate(20);

            $stats = [
                'total' => DB::table('historique_medical')->count(),
                'aujourdhui' => DB::table('historique_medical')->whereDate('created_at', today())->count(),
                'patients' => DB::table('historique_medical')->where('table_concernee', 'patients')->count(),
                'consultations' => DB::table('historique_medical')->where('table_concernee', 'consultations')->count(),
                'examens' => DB::table('historique_medical')->where('table_concernee', 'examens')->count(),
            ];
        } else {
            $historiques = collect();
            $stats = [
                'total' => 0,
                'aujourdhui' => 0,
                'patients' => 0,
                'consultations' => 0,
                'examens' => 0,
            ];
        }

        return view('receptionniste.enregistrements.historique', compact('historiques', 'stats', 'search', 'dateDebut', 'dateFin', 'type'));
    }

    /**
     * Générer un rapport d'enregistrement
     */
    public function rapport(Request $request)
    {
        $periode = $request->get('periode', 'mois');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        if ($dateDebut && $dateFin) {
            $debut = Carbon::parse($dateDebut)->startOfDay();
            $fin = Carbon::parse($dateFin)->endOfDay();
        } else {
            switch ($periode) {
                case 'jour':
                    $debut = Carbon::today()->startOfDay();
                    $fin = Carbon::today()->endOfDay();
                    break;
                case 'semaine':
                    $debut = Carbon::now()->startOfWeek()->startOfDay();
                    $fin = Carbon::now()->endOfWeek()->endOfDay();
                    break;
                case 'annee':
                    $debut = Carbon::now()->startOfYear()->startOfDay();
                    $fin = Carbon::now()->endOfYear()->endOfDay();
                    break;
                default:
                    $debut = Carbon::now()->startOfMonth()->startOfDay();
                    $fin = Carbon::now()->endOfMonth()->endOfDay();
                    break;
            }
        }

        // Données du rapport
        $rapport = [
            'periode' => [
                'debut' => $debut,
                'fin' => $fin,
                'libelle' => $this->getPeriodeLibelle($periode, $debut, $fin)
            ],
            'patients' => [
                'total' => Patient::whereBetween('created_at', [$debut, $fin])->count(),
                'hommes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'M')->count(),
                'femmes' => Patient::whereBetween('created_at', [$debut, $fin])->where('sexe', 'F')->count(),
                'par_jour' => Patient::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
                    ->whereBetween('created_at', [$debut, $fin])
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get(),
            ],
            'consultations' => [
                'total' => Consultation::whereBetween('created_at', [$debut, $fin])->count(),
                'par_jour' => Consultation::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
                    ->whereBetween('created_at', [$debut, $fin])
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get(),
            ],
            'examens' => [
                'total' => Examen::whereBetween('created_at', [$debut, $fin])->count(),
                'par_type' => Examen::select('type_examen', DB::raw('COUNT(*) as total'))
                    ->whereBetween('created_at', [$debut, $fin])
                    ->groupBy('type_examen')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->get(),
            ],
            'utilisateur' => auth()->user(),
            'date_generation' => now(),
        ];

        return view('receptionniste.enregistrements.rapport', compact('rapport', 'periode', 'dateDebut', 'dateFin'));
    }

    /**
     * Exporter les enregistrements en CSV
     */
    public function exportCSV(Request $request)
    {
        $type = $request->get('type', 'patients');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $query = $this->getExportQuery($type, $dateDebut, $dateFin);
        $data = $query->get();

        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // Entêtes CSV
        $headers = $this->getCSVHeaders($type);
        fputcsv($handle, $headers, ';');

        // Données CSV
        foreach ($data as $row) {
            $rowData = $this->formatCSVRow($row, $type);
            fputcsv($handle, $rowData, ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporter les enregistrements en Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'patients');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $query = $this->getExportQuery($type, $dateDebut, $dateFin);
        $data = $query->get();

        $html = $this->generateExcelHTML($data, $type);

        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.xls';

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporter en PDF (version impression)
     */
    public function exportPDF(Request $request)
    {
        $type = $request->get('type', 'patients');
        $dateDebut = $request->get('date_debut');
        $dateFin = $request->get('date_fin');

        $query = $this->getExportQuery($type, $dateDebut, $dateFin);
        $data = $query->get();

        return view('receptionniste.enregistrements.export-pdf', compact('data', 'type'));
    }

    /**
     * Obtenir la requête d'export selon le type
     */
    private function getExportQuery($type, $dateDebut = null, $dateFin = null)
    {
        switch ($type) {
            case 'patients':
                $query = Patient::query();
                break;
            case 'consultations':
                $query = Consultation::with(['patient', 'medecin']);
                break;
            case 'examens':
                $query = Examen::with(['patient']);
                break;
            default:
                $query = Patient::query();
                break;
        }

        if ($dateDebut) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin) {
            $query->whereDate('created_at', '<=', $dateFin);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Obtenir les entêtes CSV
     */
    private function getCSVHeaders($type)
    {
        switch ($type) {
            case 'patients':
                return ['ID', 'Numéro Fiche', 'Nom', 'Postnom', 'Prénom', 'Sexe', 'Date Naissance', 'Téléphone', 'Adresse', 'Date Création'];
            case 'consultations':
                return ['ID', 'Patient', 'Médecin', 'Date Consultation', 'Diagnostic', 'Prescription', 'Statut'];
            case 'examens':
                return ['ID', 'Patient', 'Type Examen', 'Date Examen', 'Résultat', 'Statut'];
            default:
                return ['ID', 'Nom', 'Date Création'];
        }
    }

    /**
     * Formater une ligne CSV
     */
    private function formatCSVRow($row, $type)
    {
        switch ($type) {
            case 'patients':
                return [
                    $row->id_patient ?? $row->id,
                    $row->numero_fiche ?? 'N/A',
                    $row->nom ?? 'N/A',
                    $row->postnom ?? 'N/A',
                    $row->prenom ?? 'N/A',
                    $row->sexe == 'M' ? 'Masculin' : 'Féminin',
                    isset($row->date_naissance) ? Carbon::parse($row->date_naissance)->format('d/m/Y') : 'N/A',
                    $row->telephone ?? 'N/A',
                    $row->adresse ?? 'N/A',
                    isset($row->created_at) ? $row->created_at->format('d/m/Y H:i') : 'N/A',
                ];
            case 'consultations':
                return [
                    $row->id_consultation ?? $row->id,
                    $row->patient->nom_complet ?? $row->patient->name ?? 'N/A',
                    $row->medecin->nom_complet ?? $row->medecin->name ?? 'N/A',
                    isset($row->date_consultation) ? Carbon::parse($row->date_consultation)->format('d/m/Y') : 'N/A',
                    $row->diagnostic ?? 'N/A',
                    $row->prescription ?? '-',
                    $row->statut ?? 'En attente',
                ];
            case 'examens':
                return [
                    $row->id_examen ?? $row->id,
                    $row->patient->nom_complet ?? $row->patient->name ?? 'N/A',
                    $row->type_examen ?? 'N/A',
                    isset($row->date_examen) ? Carbon::parse($row->date_examen)->format('d/m/Y') : 'N/A',
                    $row->resultat ?? 'En attente',
                    $row->statut ?? 'En attente',
                ];
            default:
                return [$row->id, $row->nom ?? 'N/A', isset($row->created_at) ? $row->created_at->format('d/m/Y') : 'N/A'];
        }
    }

    /**
     * Générer le HTML Excel
     */
    private function generateExcelHTML($data, $type)
    {
        $headers = $this->getCSVHeaders($type);
        $title = ucfirst($type) . ' - Hôpital Sendwe';
        $date = now()->format('d/m/Y H:i');

        $html = '<!DOCTYPE html>';
        $html .= '<html><head><meta charset="UTF-8"><title>' . $title . '</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
        $html .= 'h1 { color: #1a4d8c; }';
        $html .= 'table { border-collapse: collapse; width: 100%; margin-top: 20px; }';
        $html .= 'th { background: #1a4d8c; color: white; padding: 10px; border: 1px solid #ddd; }';
        $html .= 'td { padding: 8px; border: 1px solid #ddd; }';
        $html .= 'tr:nth-child(even) { background: #f9f9f9; }';
        $html .= '.footer { margin-top: 30px; font-size: 11px; color: #666; text-align: center; }';
        $html .= '</style></head><body>';
        $html .= '<h1>Hôpital Sendwe - Lubumbashi</h1>';
        $html .= '<h2>' . $title . '</h2>';
        $html .= '<p>Généré le : ' . $date . '</p>';
        $html .= '<p>Utilisateur : ' . (auth()->user()->nom_complet ?? auth()->user()->name) . '</p>';
        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($data as $row) {
            $rowData = $this->formatCSVRow($row, $type);
            $html .= '<tr>';
            foreach ($rowData as $cell) {
                $html .= '<td>' . htmlspecialchars((string)$cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<div class="footer">© ' . date('Y') . ' Hôpital Sendwe - Tous droits réservés</div>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Obtenir le libellé de la période
     */
    private function getPeriodeLibelle($periode, $debut, $fin)
    {
        if ($debut && $fin && $periode == 'custom') {
            return 'du ' . $debut->format('d/m/Y') . ' au ' . $fin->format('d/m/Y');
        }

        switch ($periode) {
            case 'jour':
                return 'du ' . $debut->format('d/m/Y');
            case 'semaine':
                return 'de la semaine du ' . $debut->format('d/m/Y') . ' au ' . $fin->format('d/m/Y');
            case 'annee':
                return 'de l\'année ' . $debut->format('Y');
            default:
                return 'du mois de ' . $debut->format('F Y');
        }
    }
}