<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications
     */
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('est_lue', false)
            ->update(['est_lue' => true]);
            
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Obtenir le nombre de notifications non lues
     * ✅ AJOUTER CETTE MÉTHODE
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('est_lue', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}