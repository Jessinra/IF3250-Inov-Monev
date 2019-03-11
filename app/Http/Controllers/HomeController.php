<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $auth = Auth::user();
        $this->redirectIfNotLoggedIn($auth);

        if ($auth->isUserDinas()) {
            return redirect('/dashboard/dinas');

        } else if ($auth->isUserKominfo()) {
            return redirect('/dashboard/kominfo');

        } else if ($auth->isAdmin()) {
            return redirect('/dashboard/admin');

        } else {
            return abort(404);
        }
    }
}
