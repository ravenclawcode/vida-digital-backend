<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\SoapNote;

class SupervisorSoapController extends Controller
{
    public function index()
    {
        $soaps = SoapNote::with(['patient', 'counselor'])
            ->latest()
            ->get();

        return view('supervisor.soap.index', compact('soaps'));
    }

    public function destroy($id)
    {
        $soap = SoapNote::findOrFail($id);
        $soap->delete();

        return redirect()->back()->with('success', 'Catatan SOAP berhasil dihapus');
    }
}
