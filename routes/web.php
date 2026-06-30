<?php

// ==================== IMPORTS ====================
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\PasswordChangeController;

use App\Http\Controllers\Gestionnaire\DashboardController as GestionnaireDashboardController;
use App\Http\Controllers\Gestionnaire\UserController;
use App\Http\Controllers\Gestionnaire\RoleController;
use App\Http\Controllers\Gestionnaire\StatistiqueController;

use App\Http\Controllers\Medecin\DashboardController as MedecinDashboardController;
use App\Http\Controllers\Medecin\PatientController as MedecinPatientController;
use App\Http\Controllers\Medecin\ConsultationController;
use App\Http\Controllers\Medecin\MaladieController;
use App\Http\Controllers\Medecin\ExamenResultatController;
use App\Http\Controllers\Medecin\BonAnalyseController as MedecinBonAnalyseController;

use App\Http\Controllers\Receptionniste\DashboardController as ReceptionnisteDashboardController;
use App\Http\Controllers\Receptionniste\PatientController as ReceptionnistePatientController;
use App\Http\Controllers\Receptionniste\EnregistrementController;
use App\Http\Controllers\Receptionniste\RapportController as ReceptionnisteRapportController;
use App\Http\Controllers\Receptionniste\FichierPatientController;
use App\Http\Controllers\Receptionniste\ExamenController as ReceptionnisteExamenController;
use App\Http\Controllers\Receptionniste\BonAnalyseController as ReceptionnisteBonAnalyseController;

use App\Http\Controllers\Infirmier\DashboardController as InfirmierDashboardController;
use App\Http\Controllers\Infirmier\PatientController as InfirmierPatientController;
use App\Http\Controllers\Infirmier\BilanController as InfirmierBilanController;

use App\Http\Controllers\Laborantin\DashboardController as LaborantinDashboardController;
use App\Http\Controllers\Laborantin\ExamenController as LaborantinExamenController;
use App\Http\Controllers\Laborantin\StatistiqueController as LaborantinStatistiqueController;
use App\Http\Controllers\Laborantin\BonAnalyseController as LaborantinBonAnalyseController;

// ============================================================
// PAGE D'ACCUEIL
// ============================================================
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ============================================================
// AUTHENTIFICATION
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// ============================================================
// DÉCONNEXION
// ============================================================
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ============================================================
// ROUTES PROTÉGÉES
// ============================================================
Route::middleware(['auth'])->group(function () {

    // Changement de mot de passe
    Route::get('/change-password', [PasswordChangeController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.update');

    // Notifications
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Dashboard principal (redirection)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== GESTIONNAIRE ====================
    Route::prefix('gestionnaire')->name('gestionnaire.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/dashboard', [GestionnaireDashboardController::class, 'index'])->name('dashboard');
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::prefix('statistiques')->name('statistiques.')->group(function () {
                Route::get('/', [StatistiqueController::class, 'index'])->name('index');
                Route::get('/consultations', [StatistiqueController::class, 'consultations'])->name('consultations');
                Route::get('/patients', [StatistiqueController::class, 'patients'])->name('patients');
                Route::get('/examens', [StatistiqueController::class, 'examens'])->name('examens');
            });
        });

    // ==================== MÉDECIN ====================
    Route::prefix('medecin')->name('medecin.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/dashboard', [MedecinDashboardController::class, 'index'])->name('dashboard');
            Route::prefix('patients')->name('patients.')->group(function () {
                Route::get('/', [MedecinPatientController::class, 'index'])->name('index');
                Route::get('/search', [MedecinPatientController::class, 'search'])->name('search');
                Route::get('/{patient}', [MedecinPatientController::class, 'show'])->name('show');
                Route::get('/{patient}/historique', [MedecinPatientController::class, 'historique'])->name('historique');
            });
            Route::resource('consultations', ConsultationController::class);
            Route::post('consultations/{consultation}/ordonner-examens', [ConsultationController::class, 'ordonnerExamens'])->name('consultations.ordonner-examens');
            Route::resource('maladies', MaladieController::class);
            Route::prefix('examens')->name('examens.')->group(function () {
                Route::get('/', [ExamenResultatController::class, 'index'])->name('index');
                Route::get('/{examen}', [ExamenResultatController::class, 'show'])->name('show');
                Route::get('/{examen}/print', [ExamenResultatController::class, 'imprimer'])->name('print');
            });
            Route::prefix('bons-analyse')->name('bons-analyse.')->group(function () {
                Route::get('/', [MedecinBonAnalyseController::class, 'index'])->name('index');
                Route::get('/create/{patient?}', [MedecinBonAnalyseController::class, 'create'])->name('create');
                Route::post('/', [MedecinBonAnalyseController::class, 'store'])->name('store');
                Route::get('/{bon}', [MedecinBonAnalyseController::class, 'show'])->name('show');
                Route::get('/{bon}/print', [MedecinBonAnalyseController::class, 'print'])->name('print');
                Route::get('/patient/{patient}/print', [MedecinBonAnalyseController::class, 'printByPatient'])->name('print-by-patient');
            });
        });

    // ==================== RÉCEPTIONNISTE ====================
    Route::prefix('receptionniste')->name('receptionniste.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/dashboard', [ReceptionnisteDashboardController::class, 'index'])->name('dashboard');
            Route::resource('patients', ReceptionnistePatientController::class);
            Route::get('patients/search', [ReceptionnisteDashboardController::class, 'search'])->name('patients.search');
            Route::prefix('rapports')->name('rapports.')->group(function () {
                Route::get('/', [ReceptionnisteRapportController::class, 'index'])->name('index');
                Route::get('/historique', [ReceptionnisteRapportController::class, 'historique'])->name('historique');
                Route::get('/generer', [ReceptionnisteRapportController::class, 'genererRapport'])->name('generer');
                Route::get('/apercu', [ReceptionnisteRapportController::class, 'apercu'])->name('apercu');
                Route::get('/export-pdf', [ReceptionnisteRapportController::class, 'exportPDF'])->name('export-pdf');
            });
            Route::prefix('fichiers')->name('fichiers.')->group(function () {
                Route::get('/patient/{patient}', [FichierPatientController::class, 'index'])->name('index');
                Route::get('/patient/{patient}/create', [FichierPatientController::class, 'create'])->name('create');
                Route::post('/patient/{patient}', [FichierPatientController::class, 'store'])->name('store');
                Route::get('/{fichier}', [FichierPatientController::class, 'show'])->name('show');
                Route::get('/{fichier}/download', [FichierPatientController::class, 'download'])->name('download');
                Route::delete('/{fichier}', [FichierPatientController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('examens')->name('examens.')->group(function () {
                Route::get('/', [ReceptionnisteExamenController::class, 'index'])->name('index');
                Route::get('/{examen}', [ReceptionnisteExamenController::class, 'show'])->name('show');
                Route::get('/{examen}/print', [ReceptionnisteExamenController::class, 'print'])->name('print');
            });
            Route::prefix('enregistrements')->name('enregistrements.')->group(function () {
                Route::get('/', [EnregistrementController::class, 'index'])->name('index');
                Route::get('/historique', [EnregistrementController::class, 'historique'])->name('historique');
                Route::get('/rapport', [EnregistrementController::class, 'rapport'])->name('rapport');
                Route::get('/export/csv', [EnregistrementController::class, 'exportCSV'])->name('export-csv');
                Route::get('/export/excel', [EnregistrementController::class, 'exportExcel'])->name('export-excel');
                Route::get('/export/pdf', [EnregistrementController::class, 'exportPDF'])->name('export-pdf');
            });
            Route::prefix('bons-analyse')->name('bons-analyse.')->group(function () {
                Route::get('/', [ReceptionnisteBonAnalyseController::class, 'index'])->name('index');
                Route::get('/{bon}', [ReceptionnisteBonAnalyseController::class, 'show'])->name('show');
                Route::get('/{bon}/print', [ReceptionnisteBonAnalyseController::class, 'print'])->name('print');
            });
        });

    // ==================== INFIRMIER ====================
    Route::prefix('infirmier')->name('infirmier.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/dashboard', [InfirmierDashboardController::class, 'index'])->name('dashboard');
            
            Route::prefix('patients')->name('patients.')->group(function () {
                Route::get('/attente', [InfirmierPatientController::class, 'attente'])->name('attente');
                Route::get('/{patient}/bilan', [InfirmierPatientController::class, 'bilanForm'])->name('bilan');
                Route::post('/{patient}/bilan', [InfirmierPatientController::class, 'storeBilan'])->name('store-bilan');
                Route::get('/{patient}', [InfirmierPatientController::class, 'show'])->name('show');
            });
            
            Route::prefix('bilans')->name('bilans.')->group(function () {
                Route::get('/', [InfirmierBilanController::class, 'index'])->name('index');
                Route::get('/{bilan}', [InfirmierBilanController::class, 'show'])->name('show');
                Route::get('/{bilan}/print', [InfirmierBilanController::class, 'print'])->name('print');
            });
        });

    // ==================== LABORANTIN ====================
    Route::prefix('laborantin')->name('laborantin.')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/dashboard', [LaborantinDashboardController::class, 'index'])->name('dashboard');
            
            // Examens
            Route::resource('examens', LaborantinExamenController::class);
            Route::get('examens/en-attente', [LaborantinExamenController::class, 'enAttente'])->name('examens.attente');
            Route::get('examens/{examen}/resultat', [LaborantinExamenController::class, 'saisirResultat'])->name('examens.resultat');
            Route::put('examens/{examen}/resultat', [LaborantinExamenController::class, 'updateResultat'])->name('examens.update-resultat');
            Route::get('examens/{examen}/analyse', [LaborantinExamenController::class, 'analyse'])->name('examens.analyse');
            Route::get('examens/{examen}/print', [LaborantinExamenController::class, 'imprimer'])->name('examens.print');
            
            // Bons d'analyse
            Route::prefix('bons-analyse')->name('bons-analyse.')->group(function () {
                Route::get('/', [LaborantinBonAnalyseController::class, 'index'])->name('index');
                Route::get('/{bon}', [LaborantinBonAnalyseController::class, 'show'])->name('show');
                Route::get('/{bon}/prelever', [LaborantinBonAnalyseController::class, 'preleverForm'])->name('prelever-form');
                Route::post('/{bon}/prelever', [LaborantinBonAnalyseController::class, 'prelever'])->name('prelever');
                Route::get('/{bon}/resultats', [LaborantinBonAnalyseController::class, 'saisirResultatsForm'])->name('resultats-form');
                Route::post('/{bon}/resultats', [LaborantinBonAnalyseController::class, 'saisirResultats'])->name('resultats');
                Route::put('/{bon}/retourner', [LaborantinBonAnalyseController::class, 'retourner'])->name('retourner');
                Route::get('/{bon}/print', [LaborantinBonAnalyseController::class, 'print'])->name('print');
            });
            
            // Statistiques
            Route::prefix('statistiques')->name('statistiques.')->group(function () {
                Route::get('/', [LaborantinStatistiqueController::class, 'index'])->name('index');
                Route::get('/par-type', [LaborantinStatistiqueController::class, 'parType'])->name('par-type');
                Route::get('/par-periode', [LaborantinStatistiqueController::class, 'parPeriode'])->name('par-periode');
                Route::get('/export-pdf', [LaborantinStatistiqueController::class, 'exportPDF'])->name('export-pdf');
            });
        });

}); // ← FERMETURE DU GROUPE AUTH

require __DIR__ . '/auth.php';